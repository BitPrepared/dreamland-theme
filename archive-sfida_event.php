<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Cryout Creations
 * @subpackage Tempera
 * @since Tempera 1.0
 */

get_header();

require_once("regioni_e_zone/regioni_zone_utils.php");

?>

		<section id="container" class="<?php echo tempera_get_layout_class(); ?>">
			<div id="content" role="main">
			<?php cryout_before_content_hook(); ?>
			
			<?php if ( have_posts() ) : ?>
				<?php 
					if(isset($_GET['regione'])){
						$filtro_regione = filter_var($_GET['regione'], FILTER_SANITIZE_STRING);
					}
				?>
				<header class="page-header">
					<h1 class="page-title">
						<?php if ( is_day() ) : ?>
							<?php printf( __( 'Daily Archives: %s', 'tempera' ), '<span>' . get_the_date() . '</span>' ); ?>
						<?php elseif ( is_month() ) : ?>
							<?php printf( __( 'Monthly Archives: %s', 'tempera' ), '<span>' . get_the_date( _x( 'F Y', 'monthly archives date format', 'tempera' ) ) . '</span>' ); ?>
						<?php elseif ( is_year() ) : ?>
							<?php printf( __( 'Yearly Archives: %s', 'tempera' ), '<span>' . get_the_date( _x( 'Y', 'yearly archives date format', 'tempera' ) ) . '</span>' ); ?>
						<?php else : ?>
							<?php _e( 'Le sfide pubblicate', 'tempera' ); ?>
						<?php endif; ?>
						<?php if(isset($filtro_regione)): ?>
							<?php _e('per la regione ' . ucwords(get_nome_regione_by_code($filtro_regione))); ?>
						<?php endif; ?>
					</h1>
				</header>

				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>

					<?php
						/* Include the Post-Format-specific template for the content.
						 * If you want to overload this in a child theme then include a file
						 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
						 */
						if(isset($filtro_regione)){
							$p_r = get_post_meta($post->ID, '_regione');
							if($filtro_regione === $p_r[0] && is_sfida_alive($post)){
						 		get_template_part( 'content/content', get_post_format() );
						 	}
						} else {
							get_template_part( 'content/content', get_post_format() );
						}
					?>

				<?php endwhile; ?>

			<?php if($tempera_pagination=="Enable") tempera_pagination(); else tempera_content_nav( 'nav-below' ); ?>

			<?php else : ?>

				<article id="post-0" class="post no-results not-found">
					<header class="entry-header">
						<h1 class="entry-title"><?php _e( 'Nothing Found', 'tempera' ); ?></h1>
					</header><!-- .entry-header -->

					<div class="entry-content">
						<p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'tempera' ); ?></p>
						<?php get_search_form(); ?>
					</div><!-- .entry-content -->
				</article><!-- #post-0 -->

			<?php endif; ?>
			
			<?php cryout_after_content_hook(); ?>
			</div><!-- #content -->
		<?php tempera_get_sidebar(); ?>
		</section><!-- #primary -->


<?php get_footer(); ?>