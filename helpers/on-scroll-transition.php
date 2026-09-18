<?php

if (!function_exists('pureOnScrollAttribute')) {
    function pureOnScrollAttribute(bool $enabled = true): string
    {
        if (!$enabled) {
            return '';
        }

        $effect = site()->onScrollTransitions()->or('none')->value();

        if ($effect === 'none') {
            return '';
        }

        return 'data-on-scroll-transition="' . esc($effect) . '"';
    }
}
