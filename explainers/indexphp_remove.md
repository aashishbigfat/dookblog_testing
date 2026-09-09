# Old `/index.php/` blog links cause a redirect loop

**Status:** Plan only — nothing in the code has been changed yet. This document describes the problem and the proposed fix for review/sign-off before anyone implements it.

**Note on location:** This issue and its fix live entirely in the `dookwebsite` codebase (`public/.htaccess`), not in `dookblog_server`. This file was placed in `dookblog_server/explainers/` on request.

---

## Issue

Visiting a URL shaped like the old-style pattern below currently **never loads** — it loops forever and the browser eventually gives up with a "too many redirects" error:

```
https://www.dookinternational.com/index.php/blog/kiev-pechersk-lavra-kiev
```

Confirmed live on production by directly testing the URL — it bounces back and forth endlessly between the version with a trailing slash and the version without one, and never actually reaches the blog post.

### Why it happens (plain-language version)

Two separate pieces of the routing logic each have a rule about trailing slashes (`/` at the end of a URL), and they disagree with each other on this specific URL shape:

1. **A rule in `public/.htaccess`** says: *"If a URL ends in `/`, and it doesn't start with `/blog/`, remove the `/`."* Since this URL starts with `/index.php/blog/` (not literally `/blog/`), the rule doesn't recognize it as a blog URL — so it keeps stripping the trailing slash off.
2. **A rule in the Laravel app** (`EnsureTrailingSlash.php`) says: *"If this is a blog post URL, make sure it ends in `/`."* Laravel is smart enough to see past the `index.php` part and recognize this as a blog post — so it keeps adding the trailing slash back on.

One side keeps removing the slash, the other keeps adding it back. Neither ever wins, so the visitor is stuck bouncing between the two forms forever and never reaches the actual page.

### Where the URL pattern comes from

Nothing in the current app generates links shaped like `/index.php/blog/...` — `routes/web.php` only ever produces clean links like `/blog/kiev-pechersk-lavra-kiev/`. This pattern is a leftover from an older, pre-Laravel version of the site (this exact `index.php/controller/method` style is the classic default URL format of older PHP frameworks). It shows up today only when an **old bookmark, an old backlink from another site, or a stale search-engine result** sends someone to it — it's an incoming request the app has no control over, not something it produces itself.

### Who this affects

Anyone arriving via an old/stale link to a blog post — a real visitor, or a search engine crawler trying to re-index the page. Right now they all hit the same dead end.

---

## Solution

Add **one new rule** to `dookwebsite/public/.htaccess`, placed just above the existing "Remove index.php" rule. It detects `/index.php/` followed by anything, and redirects straight to the clean version of the URL, dropping `index.php` entirely:

```apache
RewriteCond %{THE_REQUEST} \s/index\.php/(.*)\s [NC]
RewriteRule ^ /%1 [R=301,L]
```

No PHP or Laravel code needs to change — this is a single addition to the existing server rewrite-rules file.

---

## What this solution exactly solves

- **Removes the source of the disagreement**, rather than picking a side. Once `index.php` is stripped out *before* the two conflicting rules ever see the URL, they stop disagreeing — there's nothing left for them to fight over.
- **Old links start working again.** `/index.php/blog/kiev-.../` becomes `/blog/kiev-.../` in one redirect, then the existing rules cleanly add the trailing slash in a second redirect, landing on the real blog post. Two clean hops, no loop.
- **Fixes this for the whole site, not just blog links.** Since the new rule isn't blog-specific, it also protects against the same kind of loop on any other page if an old `/index.php/...`-style link to it exists.

## What this solution does **not** do

- Does not touch any PHP or Laravel code — `EnsureTrailingSlash.php` and the routes stay exactly as they are.
- Does not change how normal `/blog/...` links behave today — those already work correctly and are unaffected.
- Does not touch `dookblog_server` at all — this issue and fix are 100% inside `dookwebsite`.
- Does not fix a separate, unrelated bug also found during this investigation: `dookwebsite/bootstrap/app.php` is missing an import (`use ... HttpException;`), which means the sitewide "redirect broken links to the homepage" safety net may itself error out on a genuine 404 (e.g., a blog slug that no longer exists). That's a distinct issue from this redirect loop and would need its own separate fix.

---

## Effort

One file (`dookwebsite/public/.htaccess`), roughly 3 lines added. No deployment changes, no database changes, no changes to `dookblog_server`.
