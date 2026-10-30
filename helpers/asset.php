<?php

/**
 * Render a versioned CSS or JS tag for an asset in a Kirby plugin.
 * Accepts either a plugin ID (andre/site) or its directory name (andre).
 */
function pureAsset(string $plugin, string $path, string $type): string
{
    if (!in_array($type, ['css', 'js'], true)) {
        throw new InvalidArgumentException('Unsupported asset type');
    }

    $path = ltrim($path, '/');
    if ($path === '' || str_contains($path, '..') || str_contains($path, '\\')) {
        throw new InvalidArgumentException('Invalid asset path');
    }

    $plugins = kirby()->plugins();
    $match = $plugins[$plugin] ?? null;
    $id = $plugin;

    if ($match === null) {
        foreach ($plugins as $candidateId => $candidate) {
            if (basename($candidate->root()) === $plugin) {
                $match = $candidate;
                $id = $candidateId;
                break;
            }
        }
    }

    if ($match === null) {
        return '';
    }

    $source = $match->root() . '/assets/' . $path;
    if (!is_file($source)) {
        return '';
    }

    $url = url('media/plugins/' . $id . '/' . $path);
    $hash = substr(hash_file('sha256', $source), 0, 12);
    $url .= (str_contains($url, '?') ? '&' : '?') . 'v=' . $hash;

    return $type === 'css' ? css($url) : js($url);
}

function pureCss(string $plugin, string $path): string
{
    return pureAsset($plugin, $path, 'css');
}

function pureJs(string $plugin, string $path): string
{
    return pureAsset($plugin, $path, 'js');
}
