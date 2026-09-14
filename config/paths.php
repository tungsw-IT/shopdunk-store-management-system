<?php

/** URL prefix when the project is served at /, /Web, or another subdirectory. */
function app_base_url(): string
{
    $root = str_replace('\\', '/', dirname(__DIR__));
    $scriptFile = $_SERVER['SCRIPT_FILENAME'] ?? '';
    $scriptFile = str_replace('\\', '/', realpath($scriptFile) ?: $scriptFile);
    $scriptUrl = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');

    if (strncasecmp($scriptFile, $root . '/', strlen($root) + 1) !== 0) {
        return '';
    }

    $relativeScript = substr($scriptFile, strlen($root));
    $urlSuffix = substr($scriptUrl, -strlen($relativeScript));
    $matchesScript = PHP_OS_FAMILY === 'Windows'
        ? strcasecmp($urlSuffix, $relativeScript) === 0
        : $urlSuffix === $relativeScript;
    if ($relativeScript === '' || !$matchesScript) {
        return '';
    }

    return rtrim(substr($scriptUrl, 0, -strlen($relativeScript)), '/');
}

function app_url(string $path = ''): string
{
    return app_base_url() . '/' . ltrim($path, '/');
}
