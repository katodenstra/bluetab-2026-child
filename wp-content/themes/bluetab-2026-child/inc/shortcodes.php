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

function bluetab_get_solution_variant($variant)
{
    $variant = bluetab_normalize_shortcode_variant($variant, ['strategy', 'readiness', 'products']);

    $variants = [
        'strategy' => [
            'page_class' => 'bt-solution-page--strategy',
            'card_class' => 'bt-solution-card--blue',
            'wave_class' => 'bt-solution-wave--blue',
        ],
        'readiness' => [
            'page_class' => 'bt-solution-page--readiness',
            'card_class' => 'bt-solution-card--purple',
            'wave_class' => 'bt-solution-wave--purple',
        ],
        'products' => [
            'page_class' => 'bt-solution-page--products',
            'card_class' => 'bt-solution-card--orange',
            'wave_class' => 'bt-solution-wave--orange',
        ],
    ];

    return $variants[$variant];
}

function bluetab_solution_wave_markup($variant = 'strategy')
{
    $variant_config = bluetab_get_solution_variant($variant);

    ob_start();
    ?>
    <div class="bt-solution-wave <?php echo esc_attr($variant_config['wave_class']); ?> js-bt-solution-wave"
        data-wave-variant="<?php echo esc_attr($variant); ?>" aria-hidden="true">
        <canvas class="bt-solution-wave__canvas"></canvas>
    </div>
    <?php
    return ob_get_clean();
}

function bluetab_solution_card_shortcode($atts)
{
    $atts = shortcode_atts([
        'title' => '',
        'text' => '',
        'icon' => '',
        'variant' => 'strategy',
        'heading_level' => 3,
    ], $atts, 'bt_solution_card');

    $title = trim($atts['title']);
    $text = trim($atts['text']);
    $icon = trim($atts['icon']);
    $variant_config = bluetab_get_solution_variant($atts['variant']);
    $heading_level = bluetab_normalize_heading_level($atts['heading_level']);
    $heading_tag = 'h' . $heading_level;

    if ($title === '' && $text === '' && $icon === '') {
        return '';
    }

    ob_start();
    ?>
    <article class="bt-solution-card <?php echo esc_attr($variant_config['card_class']); ?>">
        <?php if ($icon !== ''): ?>
            <span class="bt-solution-card__icon material-symbols-rounded" aria-hidden="true">
                <?php echo esc_html($icon); ?>
            </span>
        <?php endif; ?>

        <?php if ($title !== ''): ?>
            <<?php echo esc_html($heading_tag); ?> class="bt-solution-card__title">
                <?php echo esc_html($title); ?>
            </<?php echo esc_html($heading_tag); ?>>
        <?php endif; ?>

        <?php if ($text !== ''): ?>
            <p class="bt-solution-card__text">
                <?php echo esc_html($text); ?>
            </p>
        <?php endif; ?>
    </article>
    <?php
    return ob_get_clean();
}

function bluetab_solution_hero_shortcode($atts)
{
    $atts = shortcode_atts([
        'variant' => 'strategy',
        'title' => '',
        'text' => '',
        'heading_level' => 1,
        'show_wave' => 'true',
    ], $atts, 'bt_solution_hero');

    $title = trim($atts['title']);
    $text = trim($atts['text']);
    $heading_level = absint($atts['heading_level']);

    if ($heading_level < 1 || $heading_level > 2) {
        $heading_level = 1;
    }

    $heading_tag = 'h' . $heading_level;
    $variant_config = bluetab_get_solution_variant($atts['variant']);
    $show_wave = filter_var($atts['show_wave'], FILTER_VALIDATE_BOOLEAN);
    $title_id = 'bt-solution-hero-title-' . wp_rand(1000, 9999);

    if ($title === '' && $text === '') {
        return '';
    }

    ob_start();
    ?>
    <section class="bt-solution-hero <?php echo esc_attr($variant_config['page_class']); ?>" <?php echo $title !== '' ? 'aria-labelledby="' . esc_attr($title_id) . '"' : ''; ?>>
        <?php if ($show_wave): ?>
            <?php echo bluetab_solution_wave_markup($atts['variant']); ?>
        <?php endif; ?>

        <div class="bt-solution-hero__inner">
            <div class="bt-solution-hero__content">
                <?php if ($title !== ''): ?>
                    <<?php echo esc_html($heading_tag); ?> id="<?php echo esc_attr($title_id); ?>" class="bt-type-h2
                bt-solution-hero__title">
                        <?php echo esc_html($title); ?>
                    </<?php echo esc_html($heading_tag); ?>>
                <?php endif; ?>

                <?php if ($text !== ''): ?>
                    <p class="bt-solution-hero__intro">
                        <?php echo esc_html($text); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

function bluetab_solution_section_header_shortcode($atts)
{
    $atts = shortcode_atts([
        'title' => '',
        'text' => '',
        'heading_level' => 2,
    ], $atts, 'bt_solution_section_header');

    $title = trim($atts['title']);
    $text = trim($atts['text']);
    $heading_level = bluetab_normalize_heading_level($atts['heading_level'], 2);
    $heading_tag = 'h' . $heading_level;

    if ($title === '' && $text === '') {
        return '';
    }

    ob_start();
    ?>
    <header class="bt-solution-section__header">
        <?php if ($title !== ''): ?>
            <<?php echo esc_html($heading_tag); ?> class="bt-type-h3 bt-solution-section__title">
                <?php echo esc_html($title); ?>
            </<?php echo esc_html($heading_tag); ?>>
        <?php endif; ?>

        <?php if ($text !== ''): ?>
            <p class="bt-solution-section__intro">
                <?php echo esc_html($text); ?>
            </p>
        <?php endif; ?>
    </header>
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

function bluetab_get_theme_asset_url_if_exists($relative_path)
{
    $relative_path = ltrim($relative_path, '/');
    $absolute_path = get_stylesheet_directory() . '/' . $relative_path;

    if (!file_exists($absolute_path)) {
        return '';
    }

    return get_stylesheet_directory_uri() . '/' . $relative_path;
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

function bluetab_success_cases_shortcode($atts)
{
    $default_image_1 = bluetab_get_theme_asset_url_if_exists('assets/img/success-case-1.webp');
    $default_image_2 = bluetab_get_theme_asset_url_if_exists('assets/img/success-case-2.webp');
    $default_image_3 = bluetab_get_theme_asset_url_if_exists('assets/img/success-case-3.webp');

    $atts = shortcode_atts([
        'title' => 'Casos de éxito',
        'intro' => 'Proyectos estratégicos que han transformado capacidades de datos en ventajas competitivas reales.',

        'case_1_eyebrow' => 'Servicios',
        'case_1_title' => 'Gobierno del dato en el sector de los servicios de Contact Center',
        'case_1_text' => 'El sector de los servicios de Contact Center es un mercado en continua transformación con unas expectativas de crecimiento.',
        'case_1_url' => '#',
        'case_1_image' => $default_image_1,
        'case_1_image_alt' => '',

        'case_2_eyebrow' => 'Cadena de suministro',
        'case_2_title' => 'Optimización de los procesos de la Cadena de Suministro',
        'case_2_text' => 'En su proceso de optimización continua ha emprendido un importante proceso de transformación digital.',
        'case_2_url' => '#',
        'case_2_image' => $default_image_2,
        'case_2_image_alt' => '',

        'case_3_eyebrow' => 'Seguros',
        'case_3_title' => 'Calidad del dato de la información de seguros',
        'case_3_text' => 'Bluetab impulsó la transformación digital de una gran aseguradora global, optimizando la calidad de datos y automatizando procesos.',
        'case_3_url' => '#',
        'case_3_image' => $default_image_3,
        'case_3_image_alt' => '',

        'link_label' => 'Explorar',
    ], $atts, 'bt_success_cases');

    $title = trim($atts['title']);
    $intro = trim($atts['intro']);
    $link_label = trim($atts['link_label']);

    $cases = [];

    for ($i = 1; $i <= 3; $i++) {
        $eyebrow = trim($atts['case_' . $i . '_eyebrow']);
        $case_title = trim($atts['case_' . $i . '_title']);
        $text = trim($atts['case_' . $i . '_text']);
        $url = trim($atts['case_' . $i . '_url']);
        $image = trim($atts['case_' . $i . '_image']);
        $image_alt = trim($atts['case_' . $i . '_image_alt']);

        if ($eyebrow === '' && $case_title === '' && $text === '' && $url === '' && $image === '') {
            continue;
        }

        $cases[] = [
            'eyebrow' => $eyebrow,
            'title' => $case_title,
            'text' => $text,
            'url' => $url,
            'image' => $image,
            'image_alt' => $image_alt,
        ];
    }

    if ($title === '' && $intro === '' && empty($cases)) {
        return '';
    }

    $title_id = 'bt-success-cases-title-' . wp_rand(1000, 9999);

    ob_start();
    ?>
    <section class="bt-success-cases" <?php echo $title !== '' ? 'aria-labelledby="' . esc_attr($title_id) . '"' : ''; ?>>
        <div class="bt-success-cases__inner">
            <?php if ($title !== ''): ?>
                <h2 id="<?php echo esc_attr($title_id); ?>" class="bt-type-h4 bt-success-cases__title">
                    <?php echo esc_html($title); ?>
                </h2>
            <?php endif; ?>

            <?php if ($intro !== ''): ?>
                <p class="bt-type-p bt-success-cases__intro">
                    <?php echo esc_html($intro); ?>
                </p>
            <?php endif; ?>

            <?php if (!empty($cases)): ?>
                <div class="bt-success-cases__grid">
                    <?php foreach ($cases as $case): ?>
                        <article class="bt-success-card">
                            <div class="bt-success-card__content">
                                <?php if ($case['eyebrow'] !== ''): ?>
                                    <p class="bt-success-card__eyebrow">
                                        <?php echo esc_html($case['eyebrow']); ?>
                                    </p>
                                <?php endif; ?>

                                <?php if ($case['image'] !== ''): ?>
                                    <div class="bt-success-card__media">
                                        <img src="<?php echo esc_url($case['image']); ?>"
                                            alt="<?php echo esc_attr($case['image_alt']); ?>">
                                    </div>
                                <?php endif; ?>

                                <?php if ($case['title'] !== ''): ?>
                                    <h3 class="bt-success-card__title">
                                        <?php echo esc_html($case['title']); ?>
                                    </h3>
                                <?php endif; ?>

                                <?php if ($case['text'] !== ''): ?>
                                    <p class="bt-success-card__text">
                                        <?php echo esc_html($case['text']); ?>
                                    </p>
                                <?php endif; ?>

                                <?php if ($case['url'] !== '' && $link_label !== ''): ?>
                                    <a class="bt-success-card__link" href="<?php echo esc_url($case['url']); ?>"
                                        aria-label="<?php echo esc_attr($link_label . ($case['title'] !== '' ? ': ' . $case['title'] : '')); ?>">
                                        <span><?php echo esc_html($link_label); ?></span>
                                        <span class="material-symbols-rounded" aria-hidden="true">arrow_forward</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

add_shortcode('bt_solution_hero', 'bluetab_solution_hero_shortcode');
add_shortcode('bt_solution_section_header', 'bluetab_solution_section_header_shortcode');
add_shortcode('bt_hero_acf', 'bluetab_hero_acf_shortcode');
add_shortcode('bt_hero', 'bluetab_hero_shortcode');
add_shortcode('bt_solution_card', 'bluetab_solution_card_shortcode');
add_shortcode('bt_success_cases', 'bluetab_success_cases_shortcode');
add_shortcode('bt_accelerators', 'bluetab_accelerators_shortcode');