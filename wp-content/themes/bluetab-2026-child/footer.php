<?php
if ( et_theme_builder_overrides_layout( ET_THEME_BUILDER_HEADER_LAYOUT_POST_TYPE ) || et_theme_builder_overrides_layout( ET_THEME_BUILDER_FOOTER_LAYOUT_POST_TYPE ) ) {
	/**
	 * In Frontend Builder app window, wrapper markup around main content is opened on
	 * `et_before_main_content` and must always be closed on `et_after_main_content`.
	 */
	if ( et_core_is_fb_enabled() ) {
		do_action( 'et_after_main_content' );
	}

	return;
}

/**
 * Fires after the main content, before the footer is output.
 *
 * @since 3.10
 */
do_action( 'et_after_main_content' );

if ( 'on' === et_get_option( 'divi_back_to_top', 'false' ) ) :
	?>

	<span class="et_pb_scroll_top et-pb-icon"></span>

	<?php
endif;

if ( ! is_page_template( 'page-template-blank.php' ) ) :
	$current_year = current_time( 'Y' );
	$footer_logo_path = get_stylesheet_directory() . '/assets/img/bluetab-logo-white.svg';
	$footer_logo_uri  = get_stylesheet_directory_uri() . '/assets/img/bluetab-logo-white.svg';
	?>

			<footer class="bt-site-footer" role="contentinfo">
				<div class="bt-site-footer__inner">
					<div class="bt-site-footer__main">
						<div class="bt-site-footer__brand">
							<a class="bt-site-footer__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Bluetab home', 'bluetab-2026-child' ); ?>">
								<?php if ( file_exists( $footer_logo_path ) ) : ?>
									<img class="bt-site-footer__logo-img" src="<?php echo esc_url( $footer_logo_uri ); ?>" alt="<?php esc_attr_e( 'Bluetab, an IBM Company', 'bluetab-2026-child' ); ?>">
								<?php else : ?>
									<?php /* Logo asset pending: add assets/img/bluetab-logo-white.svg when the final logo is available. */ ?>
									<span>/bluetab</span>
									<span class="bt-site-footer__company">an IBM Company</span>
								<?php endif; ?>
							</a>
							<p class="bt-site-footer__description">
								Expertos en arquitectura de datos e IA.<br>
								Transformamos datos en capacidades reales de negocio.
							</p>
							<a class="bt-site-footer__social-link" href="https://www.linkedin.com/company/bluetab/" aria-label="<?php esc_attr_e( 'LinkedIn de Bluetab', 'bluetab-2026-child' ); ?>">in</a>
						</div>

						<nav class="bt-site-footer__nav" aria-label="<?php esc_attr_e( 'Footer navigation', 'bluetab-2026-child' ); ?>">
							<div class="bt-site-footer__nav-column">
								<h2 class="bt-site-footer__nav-title">Qué hacemos</h2>
								<ul class="bt-site-footer__nav-list">
									<li><a href="<?php echo esc_url( home_url( '/data-strategy' ) ); ?>">Data Strategy</a></li>
									<li><a href="<?php echo esc_url( home_url( '/data-readiness' ) ); ?>">Data Readiness</a></li>
									<li><a href="<?php echo esc_url( home_url( '/data-ai-products' ) ); ?>">Data &amp; AI Products</a></li>
									<li><a href="<?php echo esc_url( home_url( '/fastcapture' ) ); ?>">Fastcapture</a></li>
									<li><a href="<?php echo esc_url( home_url( '/spark-tune' ) ); ?>">Spark Tune</a></li>
									<li><a href="<?php echo esc_url( home_url( '/truedat' ) ); ?>">Truedat</a></li>
									<li><a href="<?php echo esc_url( home_url( '/puria' ) ); ?>">PurIA</a></li>
								</ul>
							</div>

							<div class="bt-site-footer__nav-column">
								<h2 class="bt-site-footer__nav-title">Quiénes somos</h2>
								<ul class="bt-site-footer__nav-list">
									<li><a href="<?php echo esc_url( home_url( '/quienes-somos' ) ); ?>">Historia</a></li>
									<li><a href="<?php echo esc_url( home_url( '/valores-y-cultura' ) ); ?>">Valores y cultura</a></li>
									<li><a href="<?php echo esc_url( home_url( '/dei' ) ); ?>">DEI</a></li>
									<li><a href="<?php echo esc_url( home_url( '/esg' ) ); ?>">ESG</a></li>
								</ul>
							</div>

							<div class="bt-site-footer__nav-column">
								<h2 class="bt-site-footer__nav-title">Hablemos</h2>
								<ul class="bt-site-footer__nav-list">
									<li><a href="<?php echo esc_url( home_url( '/unete-a-bluetab' ) ); ?>">Únete</a></li>
									<li><a href="<?php echo esc_url( home_url( '/blog' ) ); ?>">Blog</a></li>
								</ul>
							</div>
						</nav>
					</div>

					<div class="bt-site-footer__bottom">
						<p class="bt-site-footer__copyright">&copy; <?php echo esc_html( $current_year ); ?> Bluetab. Todos los derechos reservados.</p>
						<ul class="bt-site-footer__legal-list">
							<li><a href="<?php echo esc_url( home_url( '/politica-de-privacidad' ) ); ?>">Política de privacidad</a></li>
							<li><a href="<?php echo esc_url( home_url( '/cookies' ) ); ?>">Cookies</a></li>
						</ul>
					</div>
				</div>
			</footer>
		</div>

<?php endif; // ! is_page_template( 'page-template-blank.php' ) ?>

	</div>

	<?php wp_footer(); ?>
</body>
</html>
