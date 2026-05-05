<?php

if (!defined('ABSPATH')) {
    exit;
}

function bluetab_child_theme_setup()
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');

    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);

    register_nav_menus([
        'primary' => __('Primary Menu', 'bluetab-2026-child'),
        'footer' => __('Footer Menu', 'bluetab-2026-child'),
    ]);
}

add_action('after_setup_theme', 'bluetab_child_theme_setup');