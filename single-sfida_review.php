<?php
/**
 * The Template for displaying all single posts.
 *
 * @package Cryout Creations
 * @subpackage tempera
 * @since tempera 0.5
 */

get_header();?>

		<section id="container" class="<?php echo tempera_get_layout_class(); ?>">
			<div id="content" role="main">
			<?php cryout_before_content_hook(); ?>
			
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h1 class="entry-title"><?php the_title(); ?></h1>
					<?php cryout_post_title_hook(); ?>
					<div class="entry-meta">
						<?php tempera_posted_on(); cryout_post_meta_hook(); ?>
					</div><!-- .entry-meta -->

					<div class="entry-content">
						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'tempera' ), 'after' => '</span></div>' ) ); ?>
					</div><!-- .entry-content -->
					
					<footer class="entry-meta">
						<?php tempera_posted_in(); ?>
						<?php edit_post_link( __( 'Edit', 'tempera' ), '<span class="edit-link"><i class="icon-edit icon-metas"></i> ', '</span>' ); cryout_post_footer_hook(); ?>
					</footer><!-- .entry-meta -->
				</div><!-- #post-## -->

				<div id="nav-below" class="navigation">
					<div class="nav-previous"><?php previous_post_link( '%link', '<i class="meta-nav-prev"></i> %title' ); ?></div>
					<div class="nav-next"><?php next_post_link( '%link', '%title <i class="meta-nav-next"></i>' ); ?></div>
				</div><!-- #nav-below -->

				<?php comments_template( '', true ); ?>

<?php endwhile; // end of the loop. ?>

			<?php cryout_after_content_hook(); ?>
			</div><!-- #content -->
	<?php tempera_get_sidebar(); ?>
		</section><!-- #container -->

<?php get_footer(); ?>
