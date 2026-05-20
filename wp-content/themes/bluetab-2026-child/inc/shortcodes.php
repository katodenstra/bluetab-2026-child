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
    $variant = bluetab_normalize_shortcode_variant($variant, ['strategy', 'readiness', 'products', 'generic']);

    $variants = [
        'generic' => [
            'page_class' => 'bt-solution-page--generic',
            'card_class' => 'bt-solution-card--blue',
            'wave_class' => 'bt-solution-wave--blue',
        ],
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
        'logo' => '',
        'logo_alt' => '',
        'heading_level' => 1,
        'show_wave' => 'true',
    ], $atts, 'bt_solution_hero');

    $title = trim($atts['title']);
    $text = trim($atts['text']);
    $logo = trim($atts['logo']);
    $logo_alt = trim($atts['logo_alt']);
    $heading_level = absint($atts['heading_level']);

    if ($heading_level < 1 || $heading_level > 2) {
        $heading_level = 1;
    }

    $heading_tag = 'h' . $heading_level;
    $variant_config = bluetab_get_solution_variant($atts['variant']);
    $show_wave = filter_var($atts['show_wave'], FILTER_VALIDATE_BOOLEAN);
    $title_id = 'bt-solution-hero-title-' . wp_rand(1000, 9999);

    if ($title === '' && $text === '' && $logo === '') {
        return '';
    }

    ob_start();
    ?>
    <section class="bt-solution-hero <?php echo esc_attr($variant_config['page_class']); ?>" <?php echo ($title !== '' || $logo !== '') ? 'aria-labelledby="' . esc_attr($title_id) . '"' : ''; ?>>
        <?php if ($show_wave): ?>
            <?php echo bluetab_solution_wave_markup($atts['variant']); ?>
        <?php endif; ?>

        <div class="bt-solution-hero__inner">
            <div class="bt-solution-hero__content">
                <?php if ($title !== '' || $logo !== ''): ?>
                    <<?php echo esc_html($heading_tag); ?> id="<?php echo esc_attr($title_id); ?>" class="bt-type-h2 bt-solution-hero__title <?php echo $logo !== '' ? 'bt-solution-hero__title--logo' : ''; ?>">
                        <?php if ($logo !== ''): ?>
                            <?php if ($title !== ''): ?>
                                <span class="bt-sr-only"><?php echo esc_html($title); ?></span>
                            <?php endif; ?>
                            <img class="bt-solution-hero__logo" src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr($logo_alt !== '' ? $logo_alt : $title); ?>" loading="eager">
                        <?php else: ?>
                            <?php echo esc_html($title); ?>
                        <?php endif; ?>
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

function bluetab_get_history_timeline_fallback_items()
{
    return [
        [
            'year' => '2005',
            'title' => 'Nace Bluetab',
            'text' => 'Bluetab inicia su historia con una visión clara: construir soluciones de datos sólidas, útiles y orientadas a negocio.',
            'image' => '',
            'image_alt' => '',
        ],
        [
            'year' => '2008',
            'title' => 'Primeros grandes proyectos',
            'text' => 'El equipo consolida una forma de trabajar cercana, técnica y pragmática junto a clientes que necesitaban escalar sus capacidades de datos.',
            'image' => '',
            'image_alt' => '',
        ],
        [
            'year' => '2010',
            'title' => 'Especialización en datos',
            'text' => 'Bluetab refuerza su foco en arquitecturas, gobierno y plataformas de datos para acompañar transformaciones más ambiciosas.',
            'image' => '',
            'image_alt' => '',
        ],
        [
            'year' => '2012',
            'title' => 'Crecimiento del equipo',
            'text' => 'La compañía amplía talento, capacidades y presencia para responder a nuevos retos tecnológicos y de negocio.',
            'image' => '',
            'image_alt' => '',
        ],
        [
            'year' => '2014',
            'title' => 'Nuevas capacidades cloud',
            'text' => 'Bluetab incorpora nuevas prácticas de modernización tecnológica para diseñar plataformas más flexibles, robustas y preparadas para escalar.',
            'image' => '',
            'image_alt' => '',
        ],
        [
            'year' => '2015',
            'title' => 'Impulso internacional',
            'text' => 'La compañía acompaña a organizaciones globales y fortalece su experiencia en entornos complejos, regulados y multiculturales.',
            'image' => '',
            'image_alt' => '',
        ],
        [
            'year' => '2017',
            'title' => 'Data como ventaja competitiva',
            'text' => 'Bluetab ayuda a sus clientes a convertir el dato en una capacidad estructural para tomar mejores decisiones y acelerar su negocio.',
            'image' => '',
            'image_alt' => '',
        ],
        [
            'year' => '2021',
            'title' => 'Una nueva etapa',
            'text' => 'Bluetab entra en una fase de mayor escala, combinando cultura técnica, delivery especializado y visión estratégica.',
            'image' => '',
            'image_alt' => '',
        ],
        [
            'year' => '2023',
            'title' => 'Evolución hacia IA',
            'text' => 'El avance de la inteligencia artificial abre nuevas oportunidades para diseñar soluciones de datos más inteligentes, automatizadas y accionables.',
            'image' => '',
            'image_alt' => '',
        ],
        [
            'year' => '2025',
            'title' => 'Lanzamiento de purIA',
            'text' => 'Bluetab impulsa una nueva etapa con el lanzamiento de purIA, apostando por capacidades multiagente y soluciones más avanzadas para migración y gestión de datos.',
            'image' => '',
            'image_alt' => '',
        ],
        [
            'year' => '2026',
            'title' => 'El futuro se construye con datos',
            'text' => 'Bluetab sigue evolucionando su propuesta para ayudar a las organizaciones a crear valor real con datos, cloud e inteligencia artificial.',
            'image' => '',
            'image_alt' => '',
        ],
    ];
}

function bluetab_normalize_history_timeline_items($items)
{
    if (!is_array($items)) {
        return [];
    }

    $normalized_items = [];

    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }

        $year = isset($item['year']) ? sanitize_text_field((string) $item['year']) : '';
        $title = isset($item['title']) ? sanitize_text_field((string) $item['title']) : '';
        $text = isset($item['text']) ? sanitize_textarea_field((string) $item['text']) : '';
        $image = isset($item['image']) ? esc_url_raw((string) $item['image']) : '';
        $image_alt = isset($item['image_alt']) ? sanitize_text_field((string) $item['image_alt']) : '';

        if ($year === '' || ($title === '' && $text === '' && $image === '')) {
            continue;
        }

        $normalized_items[] = [
            'year' => $year,
            'title' => $title,
            'text' => $text,
            'image' => $image,
            'image_alt' => $image_alt,
        ];
    }

    usort($normalized_items, static function ($item_a, $item_b) {
        return intval($item_a['year']) <=> intval($item_b['year']);
    });

    return $normalized_items;
}

function bluetab_get_history_timeline_items()
{
    $items = [];

    if (function_exists('get_field')) {
        $raw_items = get_field('bt_history_timeline_items');

        if (is_string($raw_items) && trim($raw_items) !== '') {
            $decoded_items = json_decode($raw_items, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $items = bluetab_normalize_history_timeline_items($decoded_items);
            }
        }
    }

    if (empty($items)) {
        $items = bluetab_normalize_history_timeline_items(bluetab_get_history_timeline_fallback_items());
    }

    return $items;
}

function bluetab_history_timeline_shortcode()
{
    $items = bluetab_get_history_timeline_items();

    if (empty($items)) {
        return '';
    }

    $instance_id = 'bt-history-timeline-' . wp_rand(1000, 9999);
    $initial_item = $items[0];
    $json_items = wp_json_encode($items, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

    ob_start();
    ?>
    <section class="bt-history-timeline" data-bt-history-timeline aria-labelledby="<?php echo esc_attr($instance_id); ?>-heading">
        <div class="bt-history-timeline__inner">
            <h2 id="<?php echo esc_attr($instance_id); ?>-heading" class="bt-type-h2 bt-history-timeline__heading">
                Nuestra historia
            </h2>

            <div class="bt-history-timeline__layout">
                <aside class="bt-history-timeline__nav" aria-label="Años de la historia de Bluetab">
                    <button class="bt-history-timeline__control bt-history-timeline__control--prev" type="button" aria-label="Ver año anterior">
                        <span class="material-symbols-rounded" aria-hidden="true">keyboard_arrow_up</span>
                    </button>

                    <ul class="bt-history-timeline__years">
                        <?php foreach ($items as $index => $item): ?>
                            <?php
                            $distance = abs($index);
                            $distance_class = 'is-far';

                            if ($distance === 0) {
                                $distance_class = 'is-active';
                            } elseif ($distance === 1) {
                                $distance_class = 'is-near';
                            } elseif ($distance === 2) {
                                $distance_class = 'is-mid';
                            }

                            $is_initially_visible = $index < 6;
                            ?>
                            <li class="bt-history-timeline__year-item" <?php echo !$is_initially_visible ? 'hidden' : ''; ?>>
                                <button class="bt-history-timeline__year-button <?php echo esc_attr($distance_class); ?>"
                                    type="button"
                                    data-bt-history-index="<?php echo esc_attr((string) $index); ?>"
                                    <?php echo !$is_initially_visible ? 'hidden' : ''; ?>
                                    <?php echo $index === 0 ? 'aria-current="true"' : ''; ?>>
                                    <span class="bt-history-timeline__year-dot" aria-hidden="true"></span>
                                    <span class="bt-history-timeline__year-label"><?php echo esc_html($item['year']); ?></span>
                                </button>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <button class="bt-history-timeline__control bt-history-timeline__control--next" type="button" aria-label="Ver año siguiente">
                        <span class="material-symbols-rounded" aria-hidden="true">keyboard_arrow_down</span>
                    </button>
                </aside>

                <article class="bt-history-timeline__content" aria-live="polite">
                    <?php if ($initial_item['title'] !== ''): ?>
                        <h3 class="bt-history-timeline__title"><?php echo esc_html($initial_item['title']); ?></h3>
                    <?php endif; ?>

                    <div class="bt-history-timeline__body">
                        <div class="bt-history-timeline__copy">
                            <?php if ($initial_item['text'] !== ''): ?>
                                <p class="bt-history-timeline__text"><?php echo esc_html($initial_item['text']); ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="bt-history-timeline__media" <?php echo $initial_item['image'] === '' ? 'hidden' : ''; ?>>
                            <?php if ($initial_item['image'] !== ''): ?>
                                <img src="<?php echo esc_url($initial_item['image']); ?>" alt="<?php echo esc_attr($initial_item['image_alt']); ?>" loading="lazy">
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            </div>

            <script type="application/json" class="bt-history-timeline__data">
                <?php echo $json_items ? $json_items : '[]'; ?>
            </script>
        </div>
    </section>
    <?php
    return ob_get_clean();
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
                <h2 id="<?php echo esc_attr($title_id); ?>" class="bt-type-h3 bt-success-cases__title">
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
add_shortcode('bt_history_timeline', 'bluetab_history_timeline_shortcode');
