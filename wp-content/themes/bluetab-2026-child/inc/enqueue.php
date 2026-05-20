<?php

if (!defined('ABSPATH')) {
    exit;
}

function bluetab_child_asset_version($relative_path)
{
    $file_path = get_stylesheet_directory() . '/' . ltrim($relative_path, '/');

    return file_exists($file_path) ? filemtime($file_path) : '0.1.0';
}

function bluetab_child_enqueue_assets()
{
    wp_enqueue_style(
        'divi-parent-style',
        get_template_directory_uri() . '/style.css',
        [],
        wp_get_theme(get_template())->get('Version')
    );

    wp_enqueue_style(
        'bluetab-google-fonts',
        'https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0&family=Titillium+Web:wght@400;600;700;900&display=swap',
        [],
        null
    );

    wp_enqueue_style(
        'bluetab-tokens',
        get_stylesheet_directory_uri() . '/assets/css/tokens.css',
        ['divi-parent-style', 'bluetab-google-fonts'],
        bluetab_child_asset_version('assets/css/tokens.css')
    );

    wp_enqueue_style(
        'bluetab-layout',
        get_stylesheet_directory_uri() . '/assets/css/layout.css',
        ['bluetab-tokens'],
        bluetab_child_asset_version('assets/css/layout.css')
    );

    wp_enqueue_style(
        'bluetab-components',
        get_stylesheet_directory_uri() . '/assets/css/components.css',
        ['bluetab-layout'],
        bluetab_child_asset_version('assets/css/components.css')
    );

    wp_enqueue_style(
        'bluetab-utilities',
        get_stylesheet_directory_uri() . '/assets/css/utilities.css',
        ['bluetab-components'],
        bluetab_child_asset_version('assets/css/utilities.css')
    );

    wp_enqueue_script(
        'bluetab-waves',
        get_stylesheet_directory_uri() . '/assets/js/animations/waves.js',
        [],
        bluetab_child_asset_version('assets/js/animations/waves.js'),
        true
    );

    wp_enqueue_script(
        'bluetab-mesh',
        get_stylesheet_directory_uri() . '/assets/js/animations/mesh.js',
        [],
        bluetab_child_asset_version('assets/js/animations/mesh.js'),
        true
    );

    wp_enqueue_script(
        'bluetab-history-timeline',
        get_stylesheet_directory_uri() . '/assets/js/components/history-timeline.js',
        [],
        bluetab_child_asset_version('assets/js/components/history-timeline.js'),
        true
    );

    wp_enqueue_script(
        'bluetab-main',
        get_stylesheet_directory_uri() . '/assets/js/main.js',
        ['bluetab-waves', 'bluetab-mesh', 'bluetab-history-timeline'],
        bluetab_child_asset_version('assets/js/main.js'),
        true
    );

}

add_action('wp_enqueue_scripts', 'bluetab_child_enqueue_assets');
