<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image base URL
    |--------------------------------------------------------------------------
    |
    | Host that serves uploaded images. Every image URL this app emits - the
    | API's featured images and the CMS's own <img> tags - is built from this
    | value by img_url().
    |
    | It points at the CDN in front of gs://dookblog, which holds the same
    | trees this repo used to ship in public/: images/posts, images/post-media,
    | images/cropedimages, images/profile, images/summernote and
    | wp-content/uploads, at exactly those paths.
    |
    | Setting it to this app's own origin restores the previous behaviour of
    | serving the files off local disk, which is what makes the cutover
    | reversible in one env value. That only works while those files are still
    | in the image; once they are removed from the repo, this must stay
    | pointed at the bucket.
    |
    | Deliberately NOT derived from the request, which is what
    | url('') . '/images/posts/' did. That read the Host header, so the URL
    | depended on who called the API - dookwebsite calling over loopback got
    | image URLs pointing at 127.0.0.1, and the nginx config had to rewrite
    | the Host header for /api/ requests to paper over it. With a config value
    | the answer is the same for every caller and that rewrite can go.
    |
    */

    'base_url' => env('IMAGE_BASE_URL', 'https://img.dook.bigfat.ai'),

];
