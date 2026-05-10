<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
<?php
	elegant_description();
	elegant_keywords();
	elegant_canonical();

	/**
	 * Fires in the head, before {@see wp_head()} is called.
	 *
	 * @since 1.0
	 */
	do_action( 'et_head_meta' );
?>

	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

	<script type="text/javascript">
		document.documentElement.className = 'js';
	</script>

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php
	wp_body_open();

	$product_tour_enabled = et_builder_is_product_tour_enabled();
	$page_container_style = $product_tour_enabled ? ' style="padding-top: 0px;"' : '';
	$asset_uri            = get_stylesheet_directory_uri() . '/assets';
	$asset_path           = get_stylesheet_directory() . '/assets';
	$logo_path            = $asset_path . '/img/bluetab-logo.svg';
	$logo_uri             = $asset_uri . '/img/bluetab-logo.svg';
	$offering_image_path  = $asset_path . '/img/submenu-01.webp';
	$offering_image_uri   = $asset_uri . '/img/submenu-01.webp';
	$culture_image_path   = $asset_path . '/img/submenu-02.webp';
	$culture_image_uri    = $asset_uri . '/img/submenu-02.webp';
	?>
	<div id="page-container"<?php echo et_core_intentionally_unescaped( $page_container_style, 'fixed_string' ); ?>>
<?php
if ( $product_tour_enabled || is_page_template( 'page-template-blank.php' ) ) {
	return;
}
?>

	<header class="bt-site-header" role="banner">
		<nav class="bt-nav" aria-label="<?php esc_attr_e( 'Main navigation', 'bluetab-2026-child' ); ?>">
			<div class="bt-nav__inner">
				<a class="bt-nav__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Bluetab home', 'bluetab-2026-child' ); ?>">
					<?php if ( file_exists( $logo_path ) ) : ?>
						<img class="bt-nav__logo" src="<?php echo esc_url( $logo_uri ); ?>" alt="<?php esc_attr_e( 'Bluetab, an IBM Company', 'bluetab-2026-child' ); ?>">
					<?php else : ?>
						<?php /* Logo asset pending: add assets/img/bluetab-logo.svg when the final logo is available. */ ?>
						<span class="bt-nav__logo-text">/bluetab</span>
						<span class="bt-nav__logo-subtext">an IBM Company</span>
					<?php endif; ?>
				</a>

				<ul class="bt-nav__menu bt-nav__menu--primary">
					<li class="bt-nav__item">
						<a class="bt-nav__link" href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
					</li>

					<li class="bt-nav__item bt-nav__item--has-mega">
						<button class="bt-nav__link" type="button" aria-haspopup="true" aria-expanded="false">Qué hacemos</button>
						<div class="bt-mega-menu">
							<div class="bt-mega-menu__inner bt-mega-menu__inner--offering">
								<div class="bt-mega-menu__intro">
									<p class="bt-type-p">Creamos y ejecutamos soluciones transformadoras que potencian decisiones estratégicas, optimizan operaciones y generan resultados de alto impacto.</p>
								</div>

								<div class="bt-mega-menu__divider" aria-hidden="true"></div>

								<div class="bt-mega-menu__section">
									<h2 class="bt-mega-menu__heading">Offering</h2>
									<ul class="bt-mega-menu__list">
										<li><a class="bt-mega-menu__link bt-type-p" href="<?php echo esc_url( home_url( '/data-strategy' ) ); ?>">Data Strategy</a></li>
										<li><a class="bt-mega-menu__link bt-type-p" href="<?php echo esc_url( home_url( '/data-readiness' ) ); ?>">Data Readiness</a></li>
										<li><a class="bt-mega-menu__link bt-type-p" href="<?php echo esc_url( home_url( '/data-ai-products' ) ); ?>">Data &amp; AI Products</a></li>
									</ul>
								</div>

								<div class="bt-mega-menu__section">
									<h2 class="bt-mega-menu__heading">Nuestros assets</h2>
									<ul class="bt-mega-menu__list">
										<li><a class="bt-mega-menu__link bt-type-p" href="<?php echo esc_url( home_url( '/fastcapture' ) ); ?>">Fastcapture</a></li>
										<li><a class="bt-mega-menu__link bt-type-p" href="<?php echo esc_url( home_url( '/spark-tune' ) ); ?>">Spark Tune</a></li>
										<li><a class="bt-mega-menu__link bt-type-p" href="<?php echo esc_url( home_url( '/truedat' ) ); ?>">Truedat</a></li>
										<li><a class="bt-mega-menu__link bt-type-p" href="<?php echo esc_url( home_url( '/puria' ) ); ?>">PurIA</a></li>
									</ul>
								</div>

								<div class="bt-mega-menu__divider" aria-hidden="true"></div>

								<?php if ( file_exists( $offering_image_path ) ) : ?>
									<figure class="bt-mega-menu__media">
										<img src="<?php echo esc_url( $offering_image_uri ); ?>" alt="<?php esc_attr_e( 'Persona trabajando frente a monitores en una oficina de Bluetab', 'bluetab-2026-child' ); ?>">
									</figure>
								<?php else : ?>
									<?php /* Menu image pending: add assets/img/submenu-01.webp when the final image is available. */ ?>
								<?php endif; ?>
							</div>
						</div>
					</li>

					<li class="bt-nav__item bt-nav__item--has-mega">
						<button class="bt-nav__link" type="button" aria-haspopup="true" aria-expanded="false">Quiénes somos</button>
						<div class="bt-mega-menu">
							<div class="bt-mega-menu__inner bt-mega-menu__inner--culture">
								<div class="bt-mega-menu__intro">
									<p class="bt-type-p">Hemos construido una cultura empresarial que atrae a los mejores expertos. Valoramos el conocimiento, la experiencia y el trabajo bien hecho.</p>
								</div>

								<div class="bt-mega-menu__divider" aria-hidden="true"></div>

								<div class="bt-mega-menu__section">
									<h2 class="bt-mega-menu__heading">Nuestra cultura</h2>
									<ul class="bt-mega-menu__list">
										<li><a class="bt-mega-menu__link bt-type-p" href="<?php echo esc_url( home_url( '/quienes-somos' ) ); ?>">Bluetab hoy</a></li>
										<li><a class="bt-mega-menu__link bt-type-p" href="<?php echo esc_url( home_url( '/valores-y-cultura' ) ); ?>">Valores y cultura</a></li>
										<li><a class="bt-mega-menu__link bt-type-p" href="<?php echo esc_url( home_url( '/historia' ) ); ?>">Historia</a></li>
										<li><a class="bt-mega-menu__link bt-type-p" href="<?php echo esc_url( home_url( '/dei' ) ); ?>">DEI</a></li>
										<li><a class="bt-mega-menu__link bt-type-p" href="<?php echo esc_url( home_url( '/esg' ) ); ?>">ESG</a></li>
									</ul>
								</div>

								<div class="bt-mega-menu__divider" aria-hidden="true"></div>

								<?php if ( file_exists( $culture_image_path ) ) : ?>
									<figure class="bt-mega-menu__media">
										<img src="<?php echo esc_url( $culture_image_uri ); ?>" alt="<?php esc_attr_e( 'Presentación interna con equipo de Bluetab en oficina', 'bluetab-2026-child' ); ?>">
									</figure>
								<?php else : ?>
									<?php /* Menu image pending: add assets/img/submenu-02.webp when the final image is available. */ ?>
								<?php endif; ?>
							</div>
						</div>
					</li>

					<li class="bt-nav__item">
						<a class="bt-nav__link" href="<?php echo esc_url( home_url( '/blog' ) ); ?>">Blog</a>
					</li>
				</ul>

				<ul class="bt-nav__menu bt-nav__actions">
					<li class="bt-nav__item">
						<a class="bt-nav__link" href="<?php echo esc_url( home_url( '/unete-a-bluetab' ) ); ?>">Únete a Bluetab</a>
					</li>
					<li class="bt-nav__item">
						<a class="bt-nav__link bt-nav__link--primary" href="<?php echo esc_url( home_url( '/contacto' ) ); ?>">Hablemos</a>
					</li>
				</ul>
			</div>
		</nav>
	</header>

	<div id="et-main-area">
<?php
/**
 * Fires after the header, before the main content is output.
 *
 * @since 3.10
 */
do_action( 'et_before_main_content' );
