# syntax=docker/dockerfile:1
#
# dookblog (blog.dookinternational.com) - Laravel 8 admin CMS, plus the JSON
# API that dookwebsite's BlogController fetches server-side.
#
# Built to run as a SECOND container on the same Compute Engine VM that
# already hosts dookwebsite, so this image is shaped around what that VM
# already provides:
#
#   - It does NOT terminate TLS and does NOT bind 443. dookwebsite's
#     container already owns host ports 80 and 443 (docker-compose.prod.yml)
#     and terminates Let's Encrypt TLS itself. This one serves plain HTTP on
#     container port 80, to be published on another host port and fronted
#     later. Wiring that up is a separate step - not done here.
#   - The database is the same private-IP Cloud SQL host (192.168.4.7,
#     database `dookblog`), reachable only through the VM's OpenVPN tunnel.
#     That tunnel already exists for dookwebsite; this image needs nothing
#     extra for it.
#   - No Redis. This app is CACHE_DRIVER=file / SESSION_DRIVER=file /
#     QUEUE_CONNECTION=sync, so it adds nothing to the Redis plan's 30-client
#     budget that dookwebsite's php-fpm pool is sized against.
#
# Everything the container needs is in this one file - nginx, php-fpm,
# supervisor and entrypoint config are written inline rather than COPYed from
# a docker/ directory, so adding the blog touches nothing else in this repo.
# (dookwebsite splits these into docker/*; this can be refactored to match.)
#
# No Node build stage. Laravel Mix is configured in webpack.mix.js, but the
# views never call mix() - they reference asset('css/app.css'),
# asset('js/app.js') and asset('assets/...'), all of which are already
# COMPILED AND COMMITTED under public/. There is no mix-manifest.json to
# satisfy. Running `npm run production` here would add a toolchain and
# minutes to every build to regenerate files the repo already ships.
#
# No application source file is modified to produce this image.


# ---------------------------------------------------------------------
# Stage 1: PHP dependencies (no dev packages)
# ---------------------------------------------------------------------
# Dependencies are installed before the source is copied so that editing a
# controller does not invalidate the composer layer - only a change to
# composer.json/composer.lock does.
#
# --no-scripts skips composer.json's post-autoload-dump hook
# (`artisan package:discover`). Unlike dookwebsite this app does not touch
# the database during bootstrap, so it would actually succeed here - but
# discovery is still deferred to container start, where the real runtime
# extension set is present rather than the composer image's.
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --prefer-dist \
        --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative


# ---------------------------------------------------------------------
# Stage 2: runtime
# ---------------------------------------------------------------------
# PHP 8.2 to match the dookwebsite image already running on this VM - one
# PHP version to reason about on the box, and 8.2 still receives security
# fixes (8.1 stopped in Dec 2025). It satisfies this app's own composer
# constraint of ^7.3|^8.0.
#
# The caveat, stated plainly: laravel/framework v8.83.27 predates PHP 8.2 and
# was never officially certified against it (Laravel 8 lists support up to
# 8.1). What that produces is E_DEPRECATED notices - chiefly dynamic property
# creation - not fatals, and they are filtered out of the log in the php.ini
# below. If something does misbehave in a way that traces back to the PHP
# version, changing 8.2 to 8.1 on the next line is the whole fix.
FROM php:8.2-fpm-bookworm AS runtime

# gd + exif are why this list differs from dookwebsite's: intervention/image
# 2.7 has no published config/image.php, so it defaults to the GD driver, and
# every post / profile / summernote upload runs through Image::make()->save().
# Without gd the CMS still boots and the API still answers - image uploads are
# what break, at runtime, inside a request. install-php-extensions handles
# gd's jpeg/webp/freetype configure flags, which are easy to get subtly wrong
# by hand.
RUN apt-get update && apt-get install -y --no-install-recommends \
        nginx \
        supervisor \
        curl \
    && curl -sSLf -o /usr/local/bin/install-php-extensions \
        https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
    && chmod +x /usr/local/bin/install-php-extensions \
    && install-php-extensions gd exif pdo_mysql opcache \
    && rm -rf /var/lib/apt/lists/* /tmp/pear

WORKDIR /var/www/html

# Source first, then the built vendor/ on top. The order matters: if a host
# vendor/ ever reaches the build context (a stale .dockerignore, a build run
# from an unexpected directory), copying source last would silently overwrite
# the dependency set this image just resolved with whatever the dev box
# happened to have.
COPY . .
COPY --from=vendor /app/vendor ./vendor

# --- php.ini -----------------------------------------------------------
COPY <<'PHPINI' /usr/local/etc/php/conf.d/zz-app.ini
; Sized for an image-processing CMS sharing an 8 GB VM with dookwebsite.
;
; memory_limit is 512M rather than the 2024M dookwebsite inherited from its
; cPanel host: Image::make() holds a decoded bitmap in memory (roughly
; width x height x 4 bytes), so even a 24-megapixel upload needs ~100M and
; 512M covers that several times over. It also bounds the blast radius -
; with pm.max_children = 5 the worst case is ~2.5G, on a box whose other
; container is already allowed 6G.
memory_limit = 512M

; Upload ceiling. The app validates nothing on the way in (no mimes: or max:
; rules in PostController or ProfileController), so this and nginx's
; client_max_body_size are the only limits that exist. Keep the two in step -
; a mismatch fails as either a confusing 413 or a silently empty $_FILES.
upload_max_filesize = 64M
post_max_size = 64M

max_execution_time = 300
max_input_time = 300
max_input_vars = 3000

expose_php = Off
date.timezone = UTC

; Laravel 8 on PHP 8.2 emits dynamic-property deprecations that are noise,
; not signal, and would otherwise fill the log on every request. Real errors,
; warnings and notices are still reported.
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
log_errors = On
display_errors = Off

opcache.enable = 1
opcache.enable_cli = 0
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 16
opcache.max_accelerated_files = 20000
opcache.validate_timestamps = 1
opcache.revalidate_freq = 2
PHPINI

# --- php-fpm pool ------------------------------------------------------
COPY <<'FPMPOOL' /usr/local/etc/php-fpm.d/zz-www.conf
; Overrides the base image's www pool (loaded after it, alphabetically).
;
; Small on purpose. This is an internal CMS used by a handful of editors,
; plus five API endpoints called server-side by dookwebsite - not a public
; front end. It shares a 2-vCPU / 8 GB VM with dookwebsite, which is already
; allowed 6 GB, so this pool is sized to stay out of its way.
;
; 'ondemand' means idle editors cost nothing: workers spawn on traffic and
; are reaped after process_idle_timeout.
[www]
user = www-data
group = www-data

; 9001, NOT the conventional 9000.
;
; This container shares dookwebsite's network namespace, which shares the
; whole port space - not just port 80. dookwebsite's php-fpm already holds
; 127.0.0.1:9000, so binding it here fails with "Address already in use" and
; supervisor gives up after three retries.
;
; The failure mode is genuinely nasty rather than obvious: nginx still starts,
; and its fastcgi_pass to 9000 then reaches DOOKWEBSITE'S php-fpm, which
; happily executes ITS OWN /var/www/html/public/index.php - the two images use
; the same path. Every blog request is then served by the wrong application,
; which returns plausible-looking 200s and 302s instead of an error. Hit this
; exact bug on the first deploy.
listen = 127.0.0.1:9001

pm = ondemand
pm.max_children = 5
pm.process_idle_timeout = 30s
pm.max_requests = 500

; Surface worker errors in the container log instead of swallowing them.
catch_workers_output = yes
decorate_workers_output = no
php_admin_value[error_log] = /proc/self/fd/2
php_admin_flag[log_errors] = on

; Load-bearing, exactly as in dookwebsite. The entrypoint runs
; `artisan config:cache`, after which Laravel stops reading .env - so the
; bare env('FireBase') calls in
; app/Http/Controllers/NotificationController.php and
; app/Traits/FireBaseNotification.php resolve from the worker's real process
; environment or not at all. clear_env = yes would strip them and push
; notifications would post with an empty authorization key.
;
; Worth knowing separately: FireBase is not defined in .env today, so that
; value is already null. Not fixed here - this image only packages the app
; as it stands.
clear_env = no
FPMPOOL

# --- nginx site --------------------------------------------------------
COPY <<'NGINXCONF' /etc/nginx/sites-available/default
# Serves the Laravel front controller out of public/.
#
# nginx never reads .htaccess, so public/.htaccess's rules are restated here.
# That file is what Apache serves this app with in production today, and the
# trailing-slash rule below is a faithful translation rather than an
# improvement: the sibling app hit a genuine infinite redirect loop when an
# .htaccess rule and application middleware disagreed about trailing slashes,
# so this is a place to match production exactly rather than tidy it up.
#
# No TLS here by design - see the Dockerfile header. Nothing sets
# `fastcgi_param HTTPS on` either, because that would be a lie if this
# container is ever reached directly over http. If it later sits behind a
# TLS-terminating reverse proxy, that proxy's X-Forwarded-* headers arrive as
# HTTP_X_FORWARDED_* through fastcgi_params automatically, and it is
# Laravel's TrustProxies middleware - not this file - that has to be taught
# to trust them.

# --- Public origin for image URLs, for /api/* requests only ---------------
#
# ApiBlogController returns ABSOLUTE image URLs built from the address the API
# was called on:
#
#     $value->image = url('') . '/images/posts/' . $value->image;
#
# url() is request-based - it reads the Host header, not APP_URL. dookwebsite
# calls this API at 127.0.0.1:8001 and drops the result straight into
# <img src="{{$posts->image}}">, so without this the reader's BROWSER would
# try to load every post image from its own machine at 127.0.0.1:8001.
#
# These maps make the API - and only the API - report the site's own public
# origin, so blog images are served from the same domain as the page that
# embeds them:
#
#     https://dook.bigfat.ai/images/posts/<file>
#
# That path is not in dookwebsite's docroot; dookwebsite's nginx proxies
# ^~ /images/posts/ to 127.0.0.1:8001, which reaches THIS container because
# the two share a network namespace. Both halves must ship together - point
# this at dook.bigfat.ai before that proxy exists and every featured image
# 404s.
#
# This deliberately does NOT use the legacy blog host. It would work (that
# host serves the same files from the same database), but it would leave this
# deployment quietly depending on the old production server for its images.
#
# Images inside post BODIES are a separate matter and are not fixed by this:
# summernoteImage() writes absolute URLs into the stored post HTML at upload
# time, so they point at whichever host the CMS was on. Redirecting those
# means rewriting content in the database.
#
# HTTPS matters as much as the host: the page is served over https, so http://
# image URLs would be blocked as mixed content even if the host were right.
#
# Scoped to /api/ deliberately. The CMS keeps its real host, so an editor
# reaching the admin UI does not get form actions and redirects pointing at
# the production domain - which would silently act against the live site.
#
# The honest fix is making that base URL a config value in the application
# instead of deriving it from the request. This changes no PHP.
map $request_uri $blog_public_host {
    default   $http_host;
    ~^/api/   dook.bigfat.ai;
}
map $request_uri $blog_public_https {
    default   "";
    ~^/api/   on;
}

server {
    # 8001, not 80. This container shares dookwebsite's network namespace so
    # that its hardcoded 127.0.0.1:8001 resolves here - which also means port
    # 80 in that namespace already belongs to dookwebsite's nginx, and binding
    # it would fail outright.
    listen 8001 default_server;
    server_name _;
    root /var/www/html/public;
    index index.php;

    # Must stay in step with upload_max_filesize / post_max_size in php.ini.
    client_max_body_size 64M;

    # Emit RELATIVE Location headers ("/login" rather than
    # "http://host:80/login"). With the default absolute_redirect on, nginx
    # rebuilds the URL from its own listening port and scheme - so the
    # trailing-slash 301 below answered "http://localhost/login" when reached
    # on :8081, and behind a TLS-terminating proxy it would answer http://
    # for a request that arrived as https://, downgrading the scheme on every
    # redirect. Relative Location headers are resolved by the browser against
    # whatever scheme, host and port it actually used, which is correct in all
    # three cases: direct, port-mapped, and proxied. Verified by reproducing
    # the bad header before setting this.
    absolute_redirect off;

    access_log /dev/stdout;
    error_log  /dev/stderr warn;

    gzip on;
    gzip_vary on;
    gzip_types
        text/plain text/css text/xml text/javascript
        application/json application/javascript application/x-javascript
        application/xml application/rss+xml application/xhtml+xml
        image/svg+xml font/woff font/woff2;

    # .htaccess: "Redirect Trailing Slashes If Not A Folder".
    # The `.+` before the slash is what stops "/" itself from matching and
    # redirecting to an empty URI. Directories losing their trailing slash
    # costs nothing here - listings are off either way (Options -Indexes).
    location ~ ^(?<no_slash>.+)/$ {
        return 301 $no_slash$is_args$args;
    }

    # Uploads are written straight into public/images/ (PostController and
    # ProfileController call Image::make()->save(public_path(...))), and
    # summernoteImage() keeps the CLIENT'S original file extension. Refusing
    # to hand anything under that tree to php-fpm means an upload can never
    # become executable code, whatever it is named. Must precede the generic
    # \.php$ block below - nginx takes the first matching regex location.
    location ~ ^/images/.*\.php$ {
        deny all;
    }

    # .env, .git and friends, if ever requested directly.
    location ~ /\. {
        deny all;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        # 9001 - this container's own php-fpm. 9000 in this shared network
        # namespace is dookwebsite's php-fpm, which would execute dookwebsite's
        # index.php against blog URLs. See the pool config for the full story.
        fastcgi_pass 127.0.0.1:9001;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        # Must come AFTER `include fastcgi_params`, which sets HTTP_HOST and
        # HTTPS itself - these override those values, and only differ from
        # them for /api/ requests (see the maps at the top of this file).
        fastcgi_param HTTP_HOST $blog_public_host;
        fastcgi_param HTTPS     $blog_public_https;
        # Image processing on a large upload is the slow path here.
        fastcgi_read_timeout 300;
    }

    location ~* \.(css|js|jpg|jpeg|png|gif|webp|svg|ico|woff|woff2|ttf|otf)$ {
        try_files $uri =404;
        expires 30d;
        add_header Cache-Control "public";
        access_log off;
    }
}
NGINXCONF

# --- supervisor --------------------------------------------------------
COPY <<'SUPERVISORD' /etc/supervisor/conf.d/supervisord.conf
; nginx and php-fpm side by side in one container, matching how dookwebsite
; runs on this VM - one unit to start, stop and restart per site.
;
; Both log to the container's stdout/stderr, so whatever logging driver the
; VM gives this container forwards them on.

[supervisord]
nodaemon=true
user=root
logfile=/dev/stdout
logfile_maxbytes=0
pidfile=/var/run/supervisord.pid

[program:php-fpm]
command=php-fpm -F
autostart=true
autorestart=true
priority=5
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0

[program:nginx]
command=nginx -g "daemon off;"
autostart=true
autorestart=true
priority=10
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
SUPERVISORD

# --- entrypoint --------------------------------------------------------
COPY --chmod=755 <<'ENTRYPOINT_SH' /usr/local/bin/entrypoint.sh
#!/usr/bin/env bash
# Prepare the Laravel app, then hand off to CMD (supervisord).
set -euo pipefail

cd /var/www/html

if [ ! -f vendor/autoload.php ]; then
    echo "[entrypoint] ERROR: vendor/autoload.php is missing from the image." >&2
    exit 1
fi

# Fail loudly and immediately rather than serving 500s on every request:
# Laravel cannot decrypt sessions or cookies without this.
if [ -z "${APP_KEY:-}" ]; then
    echo "[entrypoint] ERROR: APP_KEY is not set in the container environment." >&2
    exit 1
fi

# .dockerignore keeps the host's compiled caches out of the build context,
# but a build run from an unusual context could still carry them in. They are
# regenerated below, so clearing costs nothing and removes a class of "works
# on my machine" failure where a cached file references a dev-only package.
rm -f bootstrap/cache/*.php

# Laravel fatals at boot if these are missing, and git does not track empty
# directories - so a clean clone genuinely does not have all of them.
echo "[entrypoint] Ensuring writable directories exist..."
mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    storage/app/public \
    bootstrap/cache

# Upload targets. The controllers do call mkdir() themselves, but with mode
# 777 written as a DECIMAL literal rather than 0777 - which is not the
# permission anyone intended. Better that these already exist, owned
# correctly, so that path is never taken.
mkdir -p \
    public/images/posts \
    public/images/cropedimages \
    public/images/post-media \
    public/images/profile \
    public/images/summernote

# php-fpm's master runs as root but its WORKERS run as www-data, and the
# workers are what handle requests - so anything they must write has to be
# theirs. (dookwebsite learned this the expensive way with a root-owned GCS
# key: `docker exec` tests passed while every real request failed.)
chown -R www-data:www-data storage bootstrap/cache public/images
chmod -R ug+rwX storage bootstrap/cache

# Deferred from the build stage, where the runtime extension set was not yet
# present.
echo "[entrypoint] Discovering packages..."
php artisan package:discover --ansi

# Rebuilt at start rather than baked into the image: config values only
# become real process environment variables at runtime (via the container's
# env_file), so caching at build time would freeze in empty values.
echo "[entrypoint] Caching config/views..."
php artisan config:clear >/dev/null
php artisan config:cache
php artisan view:cache

# NOTE: `php artisan route:cache` is deliberately NOT run.
#
# routes/web.php line 24 registers a CLOSURE:
#
#     Route::get('/', function () { return view('auth.login'); });
#
# Laravel 8 cannot serialise closure routes - route:cache aborts with
# "Unable to prepare route [/] for serialization. Uses Closure." Running it
# here would kill the container at startup, not merely fail to optimise it.
#
# (dookwebsite skips route:cache too, for an unrelated reason: its route
# table is built from a database query. Same omission, different cause.)

echo "[entrypoint] Ready. Starting: $*"
exec "$@"
ENTRYPOINT_SH

EXPOSE 8001

# Hits the login page - a closure returning a view - so this proves nginx,
# php-fpm and a full Laravel boot all work WITHOUT touching the database.
# That is deliberate: the DB lives behind the VM's OpenVPN tunnel, and a
# health check that failed whenever the tunnel dropped would have Docker
# restart-loop a container that is doing nothing wrong.
HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD curl -fsS -o /dev/null http://127.0.0.1:8001/ || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
