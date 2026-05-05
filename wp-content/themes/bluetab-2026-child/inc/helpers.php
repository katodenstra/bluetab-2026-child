<?php

if (!defined('ABSPATH')) {
    exit;
}

function bluetab_asset_uri($path = '')
{
    return get_stylesheet_directory_uri() . '/assets/' . ltrim($path, '/');
}

function bluetab_asset_path($path = '')
{
    return get_stylesheet_directory() . '/assets/' . ltrim($path, '/');
}

function bluetab_theme_uri($path = '')
{
    return get_stylesheet_directory_uri() . '/' . ltrim($path, '/');
}

function bluetab_theme_path($path = '')
{
    return get_stylesheet_directory() . '/' . ltrim($path, '/');
}