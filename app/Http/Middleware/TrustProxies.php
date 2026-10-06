<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * '*' because the only thing that can reach this app is dookwebsite's
     * nginx. The container publishes no ports - its compose file sets
     * network_mode: "container:dookwebsite-app-1", so port 8001 exists only
     * inside that container's network namespace and nothing else can connect.
     * There is no untrusted path by which a forged X-Forwarded-* could arrive.
     *
     * It has to be set for the CMS at blog.dook.bigfat.ai to work at all.
     * nginx terminates TLS and proxies plain HTTP here; with no trusted proxy
     * Laravel ignores X-Forwarded-Proto, decides the request is http, and
     * builds http:// into form actions and redirects on an https page. The
     * login POST is then blocked as mixed content, and the redirect after it
     * is bounced to dook.bigfat.ai by the port-80 rule in docker/nginx.conf.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies = '*';

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
