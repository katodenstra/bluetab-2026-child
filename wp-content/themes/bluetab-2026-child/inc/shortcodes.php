<?php

if (!defined('ABSPATH')) {
    exit;
}

function bluetab_normalize_shortcode_variant($variant, $allowed_variants = [])
{
    $variant = sanitize_html_class($variant);

    if (empty($allowed_variants)) {
        return $variant;
    }

    return in_array($variant, $allowed_variants, true) ? $variant : $allowed_variants[0];
}

function bluetab_normalize_heading_level($level, $default = 3)
{
    $level = absint($level);

    if ($level < 2 || $level > 6) {
        return $default;
    }

    return $level;
}

function bluetab_solution_card_shortcode($atts)
{
    $atts = shortcode_atts([
        'title' => '',
        'eyebrow' => '',
        'text' => '',
        'url' => '#',
        'link_label' => 'Explorar',
        'variant' => 'blue',
        'glow' => 'false',
        'heading_level' => 3,
    ], $atts, 'bt_solution_card');

    $title = trim($atts['title']);
    $eyebrow = trim($atts['eyebrow']);
    $text = trim($atts['text']);
    $url = trim($atts['url']);
    $link_label = trim($atts['link_label']);
    $variant = bluetab_normalize_shortcode_variant($atts['variant'], ['blue', 'purple', 'orange']);
    $heading_level = bluetab_normalize_heading_level($atts['heading_level']);
    $heading_tag = 'h' . $heading_level;
    $has_glow = filter_var($atts['glow'], FILTER_VALIDATE_BOOLEAN);

    $card_classes = [
        'bt-card',
        'bt-card--' . $variant,
    ];

    if ($has_glow) {
        $card_classes[] = 'bt-card--glow';
    }

    ob_start();
    ?>
    <article class="<?php echo esc_attr(implode(' ', $card_classes)); ?>">
        <div class="bt-card__icon" aria-hidden="true"></div>

        <?php if ($title !== ''): ?>
            <<?php echo esc_html($heading_tag); ?> class="bt-card__title">
                <?php echo esc_html($title); ?>
            </<?php echo esc_html($heading_tag); ?>>
        <?php endif; ?>

        <?php if ($eyebrow !== ''): ?>
            <p class="bt-card__eyebrow">
                <?php echo esc_html($eyebrow); ?>
            </p>
        <?php endif; ?>

        <?php if ($text !== ''): ?>
            <p class="bt-card__text">
                <?php echo esc_html($text); ?>
            </p>
        <?php endif; ?>

        <?php if ($url !== '' && $link_label !== ''): ?>
            <a class="bt-card__link" href="<?php echo esc_url($url); ?>"
                aria-label="<?php echo esc_attr($link_label . ($title !== '' ? ': ' . $title : '')); ?>">
                <?php echo esc_html($link_label); ?>
                <span aria-hidden="true">→</span>
            </a>
        <?php endif; ?>
    </article>
    <?php
    return ob_get_clean();
}

function bluetab_hero_shortcode($atts)
{
    $atts = shortcode_atts([
        'variant' => 'generic',
        'eyebrow' => '',
        'title' => '',
        'text' => '',
        'primary_label' => 'Hablemos',
        'primary_url' => '/contacto',
        'secondary_label' => '',
        'secondary_url' => '',
    ], $atts, 'bt_hero');

    ob_start();

    get_template_part('template-parts/sections/hero-waves', null, $atts);

    return ob_get_clean();
}

function bluetab_hero_acf_shortcode()
{
    if (!function_exists('get_field')) {
        return '';
    }

    $title = get_field('hero_title') ?: '';
    $text = get_field('hero_text') ?: '';
    $cta_label = get_field('hero_cta_label') ?: 'Hablemos';
    $cta_url = get_field('hero_cta_url') ?: '/contacto';
    $variant = get_field('hero_variant') ?: 'home';

    ob_start();

    get_template_part('template-parts/sections/hero-waves', null, [
        'variant' => $variant,
        'title' => $title,
        'text' => $text,
        'primary_label' => $cta_label,
        'primary_url' => $cta_url,
    ]);

    return ob_get_clean();
}

add_shortcode('bt_hero_acf', 'bluetab_hero_acf_shortcode');
add_shortcode('bt_hero', 'bluetab_hero_shortcode');
add_shortcode('bt_solution_card', 'bluetab_solution_card_shortcode');