<?php
/**
 * The Template for displaying all single posts.
 *
 * @package Cryout Creations
 * @subpackage tempera
 * @since tempera 0.5
 */

get_header();

require_once("regioni_e_zone/regioni_zone_utils.php");

?>

		<?php if(isset($_GET['iscriviti'])): ?>
		<script type="text/javascript">
			alert("Iscrizione!");
			<?php 
				wp_redirect('http://ansa.it'); 
				exit(); 
			?>
		</script>
		<?php endif; ?>

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

					<!-- BEGIN DREAMLAND SPECIFIC -->
						<?php  
							$r = get_post_meta($post->ID, '_regione', 1); 
							$z = get_post_meta($post->ID, '_zona', 1);
						?>
						<style>
						.limite-sfida {
							font-size:14pt;
							color:green;
							padding: 5px 0 5px 0;
							display: inline;
						}

						.icons {
							float:left;
						}

						.iscrizione-button {
							border:2px solid red;
							float: left;
							color: red;
							margin: 5px;
							font-size: 20pt;
							padding: 5px;
						}
						.locus {
							color: blue;
						}
						</style>
						<div>
						<?php if ($r == 'CM_NAZ') : ?>
							<div class="limite-sfida" >SFIDA APERTA A TUTTI!</div>
						<?php else : ?>
							<div class="limite-sfida" >Sfida limitata alla regione 
							<span class="locus"><?php echo(get_nome_regione_by_code($r)); ?></span></div>
							<?php if ($z != 'A1' && $z != '-- TUTTE LE ZONE --') : ?>
								<div class="limite-sfida" >e alla zona 
								<span class="locus"><?php echo(get_nome_zona_by_code($z)); ?></span>
								</div>
							<?php endif; ?>
						<?php endif; ?>
						</div>
						<?php 
							$l = array('Dal ' => '_start', 'Al ' => '_end');
							$p = array('_year', '_month', '_day', '_hour', '_minute');
							foreach($l as $k => $v){
								$data = array();
								foreach ($p as $key => $value) {
									// echo "cerco post meta " . $v . $value;
									$data[$value] = get_post_meta($post->ID, $v . $value);
								}
								?>
								<div class="limite" style="float:left;border:2px solid; margin:5px; padding:5px;">
								<?php echo $k ?> <?php echo $data['_day'][0] ?>-<?php echo $data['_month'][0] ?>-<?php echo $data['_year'][0] ?>
								alle <?php echo $data['_hour'][0] ?>:<?php echo $data['_minute'][0] ?>.
								</div>
								<?php
							}

						$terms = wp_get_object_terms($post->ID, 'tipologiesfide');
				        $icons = array();
				        $captions = array();
				        $has_shield = False;
				        $has_dragon = False;
				        $has_castle = False;
				        $has_pharo = False;
				        $has_world = False;

				        if($terms && ! is_wp_error($terms)){
				            foreach ($terms as $term_key => $term_value) {
				                switch ($term_value->name) {
				                    case 'Avventura':
				                    	if($has_castle){
				                    		break;
				                    	}
				                    	$has_castle = True;
				                        array_push($icons, array(
				                            'src' => 'http://returntodreamland.agesci.org/blog/wp-content/uploads/2014/10/5.png',
				                            'caption' => $term_value->name
				                            )
				                        );                        
				                        break;
				                    case 'Originalita':
				                    	if($has_pharo){
				                    		break;
				                    	}
				                    	$has_pharo = True;
				                        array_push($icons, array(
				                            'src' => 'http://returntodreamland.agesci.org/blog/wp-content/uploads/2014/10/3.png',
				                            'caption' => $term_value->name
				                            )
				                        );
				                        
				                        break;
				                    case 'Grande Impresa':
				                    	if($has_dragon){
				                    		break;
				                    	}
				                    	$has_dragon = True;
				                        array_push($icons, array(
				                            'src' => 'http://returntodreamland.agesci.org/blog/wp-content/uploads/2014/10/1.png',
				                            'caption' => $term_value->name
				                            )
				                        );
				                        break;
				                    case 'Traccia nel Mondo':
				                    	if($has_world){
				                    		break;
				                    	}
				                    	$has_world = True;
				                        array_push($icons, array(
				                            'src' => 'http://returntodreamland.agesci.org/blog/wp-content/uploads/2014/10/2.png',
				                            'caption' => $term_value->name
				                            )
				                        );
				                        break;        
				                    case 'Grande Sfida':
				                    case 'Sfida Speciale':
				                        break;
				                    default:
				                        if($has_shield)
				                            break;
				                        $has_shield = True;
				                        array_push($icons, array(
				                            'src' => 'http://returntodreamland.agesci.org/blog/wp-content/uploads/2014/10/6.png',
				                            'caption' => 'Altro'
				                            )
				                        );
				                        break;
				                }
				            }
				        }
				        ?>
				        <div class="icons">
				        <?php
				        foreach ($icons as $icon) {
				        	$sfida_html = "";
			                $sfida_html = $sfida_html . '<img alt="'. $icon['caption'] . '" '
			                . 'title="'. $icon['caption'] . '"'
			                .' style="height:35px;margin:5px 5px -5px 5px;" src="'. $icon['src'] . '" \>';
			                echo $sfida_html;
			            }

						?>
						</div>
						<?php if(/* is_alive() && */ is_sfida_for_me($post)): ?>
						<div class="iscrizione-button">
							<a href="?iscriviti">ISCRIVITI</a>
						</div>
						<?php else: ?>
							<?php 
							$is_iscritto = get_user_meta(get_current_user_id(), '_iscrizioni');
							if($is_iscritto && in_array($post->ID, $is_iscritto)){ ?>
							<div class="iscrizione-button">
								ISCRITTO
							</div>
							<?php } ?>
		 				<?php endif; ?>
						<!-- END DREAMLAND SPECIFIC -->

					<div class="entry-content">
						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'tempera' ), 'after' => '</span></div>' ) ); ?>
					</div><!-- .entry-content -->

<?php if ( get_the_author_meta( 'description' ) ) : // If a user has filled out their description, show a bio on their entries  ?>
					<div id="entry-author-info">
						<div id="author-avatar">
							<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'tempera_author_bio_avatar_size', 60 ) ); ?>
						</div><!-- #author-avatar -->
						<div id="author-description">
							<h2><?php echo esc_attr( get_the_author() ); ?></h2>
							<?php the_author_meta( 'description' ); ?>
							<div id="author-link">
								<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
									<?php printf( __( 'View all posts by ','tempera').'%s <span class="meta-nav">&rarr;</span>', get_the_author() ); ?>
								</a>
							</div><!-- #author-link	-->
						</div><!-- #author-description -->
					</div><!-- #entry-author-info -->
<?php endif; ?>

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
