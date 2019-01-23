<?php

namespace App\Classes;

class Helpers
{
    /**
     * Extract host and domain from url
     * @param string $url
     * @return string
     */
    public static function extractDomain(string $url): string
    {
        if (empty($url)) {
            return $url;
        }
        $urlParams = parse_url($url);
        if ($urlParams === false) {
            return $url;
        }
        $scheme = array_get($urlParams, 'scheme', '');
        $host = array_get($urlParams, 'host', '');
        $path = array_get($urlParams, 'path', '');
        $domain = ($scheme) ? "{$scheme}://" : '';
        $domain .= $host;
        if (!$host && $path) {
            $domain .= $path;
        }
        return $domain;
    }
}