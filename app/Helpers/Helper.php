<?php

if (!function_exists('img_url')) {
    /**
     * Absolute URL for an uploaded image, built from config('images.base_url').
     *
     * Takes the path as it is stored on disk and in the bucket, including the
     * tree: img_url('images/posts/' . $post->image).
     *
     * Replaces url('') . '/images/posts/' . $file, which had two problems this
     * fixes. It read the request's Host header, so the URL depended on who
     * called the API. And it did no percent-encoding, which the ~170 files
     * whose names contain a curly apostrophe or an en dash need - a raw U+2019
     * in a URL is not something every client handles the same way, and the CDN
     * matches on the encoded form.
     *
     * Values that are already absolute are returned untouched, so a post whose
     * stored image is a full URL keeps working. An empty path yields an empty
     * string rather than a link to the bare host.
     */
    function img_url($path)
    {
        $path = ltrim(trim((string) $path), '/');

        if ($path === '') {
            return '';
        }

        if (preg_match('#^(https?:)?//#i', $path)) {
            return $path;
        }

        $segments = array_filter(explode('/', $path), 'strlen');
        $encoded  = implode('/', array_map('rawurlencode', $segments));

        return rtrim(config('images.base_url'), '/') . '/' . $encoded;
    }
}
