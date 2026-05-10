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

function bluetab_get_acf_image_url($image)
{
    if (empty($image)) {
        return '';
    }

    if (is_array($image) && !empty($image['url'])) {
        return $image['url'];
    }

    if (is_numeric($image)) {
        return wp_get_attachment_image_url((int) $image, 'full') ?: '';
    }

    if (is_string($image)) {
        return $image;
    }

    return '';
}

function bluetab_accelerators_shortcode()
{
    if (!function_exists('get_field')) {
        return '';
    }

    $section_title = get_field('section_title') ?: '';
    $section_intro = get_field('section_intro') ?: '';
    $section_image = bluetab_get_acf_image_url(get_field('section_image'));

    $assets = [];

    for ($i = 1; $i <= 4; $i++) {
        $logo = get_field('asset_' . $i . '_logo');
        $title = get_field('asset_' . $i . '_title') ?: '';
        $text = get_field('asset_' . $i . '_text') ?: '';
        $url = get_field('asset_' . $i . '_url') ?: '';

        if ($title === '' && $text === '' && $url === '' && empty($logo)) {
            continue;
        }

        $assets[] = [
            'logo' => bluetab_get_acf_image_url($logo),
            'title' => $title,
            'text' => $text,
            'url' => $url,
        ];
    }

    if ($section_title === '' && $section_intro === '' && empty($assets) && $section_image === '') {
        return '';
    }

    ob_start();
    ?>
    <section class="bt-section bt-section--dark bt-accelerators" aria-labelledby="bt-accelerators-title">
        <div class="bt-container">
            <?php if ($section_title !== ''): ?>
                <h2 id="bt-accelerators-title" class="bt-type-h3 bt-accelerators__title">
                    <?php echo esc_html($section_title); ?>
                </h2>
            <?php endif; ?>

            <?php if ($section_intro !== ''): ?>
                <p class="bt-type-h5 bt-accelerators__intro">
                    <?php echo esc_html($section_intro); ?>
                </p>
            <?php endif; ?>

            <?php if (!empty($assets) || $section_image !== ''): ?>
                <div class="bt-accelerators__grid">
                    <?php if (!empty($assets)): ?>
                        <div class="bt-accelerators__list">
                            <?php foreach ($assets as $asset): ?>
                                <article class="bt-asset-card">
                                    <?php if ($asset['logo'] !== ''): ?>
                                        <div class="bt-asset-card__media">
                                            <img src="<?php echo esc_url($asset['logo']); ?>" alt="">
                                        </div>
                                    <?php endif; ?>

                                    <div class="bt-asset-card__content">
                                        <?php if ($asset['title'] !== ''): ?>
                                            <h4 class="bt-type-h4 bt-asset-card__title">
                                                <?php echo esc_html($asset['title']); ?>
                                            </h4>
                                        <?php endif; ?>

                                        <?php if ($asset['text'] !== ''): ?>
                                            <p class="bt-type-p bt-asset-card__text">
                                                <?php echo esc_html($asset['text']); ?>
                                            </p>
                                        <?php endif; ?>

                                        <?php if ($asset['url'] !== ''): ?>
                                            <a class="bt-card-link bt-card-link--blue" href="<?php echo esc_url($asset['url']); ?>"
                                                aria-label="<?php echo esc_attr('Explorar' . ($asset['title'] !== '' ? ': ' . $asset['title'] : '')); ?>">
                                                <span>Explorar</span>
                                                <span class="material-symbols-rounded" aria-hidden="true">arrow_forward</span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($section_image !== ''): ?>
                        <div class="bt-accelerators__media">
                            <img src="<?php echo esc_url($section_image); ?>" alt="">
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

add_shortcode('bt_hero_acf', 'bluetab_hero_acf_shortcode');
add_shortcode('bt_hero', 'bluetab_hero_shortcode');
add_shortcode('bt_solution_card', 'bluetab_solution_card_shortcode');
add_shortcode('bt_accelerators', 'bluetab_accelerators_shortcode');