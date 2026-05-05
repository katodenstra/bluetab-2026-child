<?php

if (!defined('ABSPATH')) {
    exit;
}

$args = wp_parse_args($args ?? [], [
    'variant' => 'generic',
    'eyebrow' => '',
    'title' => '',
    'text' => '',
    'primary_label' => 'Hablemos',
    'primary_url' => '/contacto',
    'secondary_label' => '',
    'secondary_url' => '',
]);

$is_home = $args['variant'] === 'home';

$hero_classes = [
    'bt-hero',
    $is_home ? 'bt-hero--home js-bt-mesh' : 'bt-hero--inner js-bt-wave',
];
?>

<section class="<?php echo esc_attr(implode(' ', $hero_classes)); ?>" <?php if (!$is_home): ?>
        data-wave-variant="<?php echo esc_attr($args['variant']); ?>" <?php endif; ?>>
    <div class="bt-hero__content">
        <div class="<?php echo esc_attr($is_home ? 'bt-hero__panel' : 'bt-hero__body'); ?>">
            <?php if (!empty($args['eyebrow'])): ?>
                <p class="bt-hero__eyebrow"><?php echo esc_html($args['eyebrow']); ?></p>
            <?php endif; ?>

            <?php if (!empty($args['title'])): ?>
                <h1 class="bt-hero__title"><?php echo esc_html($args['title']); ?></h1>
            <?php endif; ?>

            <?php if (!empty($args['text'])): ?>
                <p class="bt-hero__text"><?php echo esc_html($args['text']); ?></p>
            <?php endif; ?>

            <div class="bt-hero__actions">
                <a class="bt-button bt-button--primary" href="<?php echo esc_url($args['primary_url']); ?>">
                    <?php echo esc_html($args['primary_label']); ?>
                </a>

                <?php if (!empty($args['secondary_label']) && !empty($args['secondary_url'])): ?>
                    <a class="bt-button bt-button--secondary" href="<?php echo esc_url($args['secondary_url']); ?>">
                        <?php echo esc_html($args['secondary_label']); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>