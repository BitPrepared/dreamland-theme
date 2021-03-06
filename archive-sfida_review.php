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

get_header(); ?>

		<section id="container" class="<?php echo tempera_get_layout_class(); ?>">
			<div id="content" role="main">
			<?php cryout_before_content_hook(); ?>

            <?php if (! is_user_logged_in()): ?>
            <div class="bs-callout bs-callout-danger">
            <h4>Attenzione</h4>
            Devi essere autenticato per poter leggere il contenuto dei racconti! Se non hai effettuato l'accesso visita
            la <a href="<?php echo wp_login_url(); ?>">pagina di login</a>.
            </div>
            <?php endif; ?>
            <header class="page-header">
				<h1 class="page-title">
					Cerca fra i racconti
				</h1>
			</header>
				<p>Scrivi il testo che vuoi cercare nel titolo dei racconti (Per esempio il nome di una sfida).</p>
				<div class="bs-callout bs-callout-info">
					<h4>Usa i <b>tag</b> per cercare nei racconti</h4>
					Puoi cercare i nomi delle squadriglie, dei gruppi, delle zone e delle regioni utilizzando i tag.
					Insieme alla tua ricerca inserisci '#' (premi i tasti 'Alt' e 'à' contemporaneamente) e poi il nome del tag.
					Per esempio puoi cercare: 'pinocchio #roma-1' oppure '#toscana'.
				</div>
				<form method="get" id="searchform" action="<?= site_url(); ?>">
					<input type="text" value="Cerca" name="s" id="s"
					   onblur="if (this.value == '') {this.value = 'Cerca';}" onfocus="if (this.value == 'Cerca') {this.value = '';}">
					<input type="hidden" name="post_type" value="sfida_review">
					<input type="hidden" name="tag" id="tags-in">
				</form>
				<script>
					function get_tags (from) {
						var t = jQuery(from).val();
						var regexp = /#[^\s]*[\s]?/g;
						var ret = []; var tmparr;
						jQuery(from).val(t.replace(regexp, ""));
						while((tmparr = regexp.exec(t)) !== null) {
							ret.push(tmparr[0]);
						}
						return ret;
					}

					function mytrim(s){
						return s.trim().replace('#','');
					}

					jQuery(document).ready(function(){
						jQuery('#searchform').submit(function(){
							var tags = get_tags('#s');
							jQuery('#tags-in').val(tags.map(mytrim).join(','));
						});
					});
				</script>

			<header class="page-header">
				<h1 class="page-title">
					I tag più usati
				</h1>
			</header>
				<?php wp_tag_cloud(); ?>

			<header class="page-header">
				<h1 class="page-title">
					Gli ultimi racconti condivisi
				</h1>
			</header>

			<?php if ( have_posts() ) : ?>

				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>

					<?php
						/* Include the Post-Format-specific template for the content.
						 * If you want to overload this in a child theme then include a file
						 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
						 */
						get_template_part( 'content/content', get_post_format() );
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
						<?php
						//get_search_form();
						?>
					</div><!-- .entry-content -->
				</article><!-- #post-0 -->

			<?php endif; ?>
			
			<?php cryout_after_content_hook(); ?>
			</div><!-- #content -->
		<?php tempera_get_sidebar(); ?>
		</section><!-- #primary -->


<?php get_footer(); ?>
