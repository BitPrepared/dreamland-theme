<?php

// require_once("regioni_e_zone/regione_zone_utils.php");

function add_bootstrap_style()
{
    // Register the style like this for a theme:
    wp_register_style( 'bootstrap-style', get_template_directory_uri() . '/styles/bootstrap.min.css' );

    // For either a plugin or a theme, you can then enqueue the style:
    wp_enqueue_style( 'bootstrap-style' );
}
add_action( 'wp_enqueue_scripts', 'add_bootstrap_style' );

if(!function_exists('_log')){
  function _log( $message ) {
    if( WP_DEBUG === true ){
      if( is_array( $message ) || is_object( $message ) ){
        error_log( print_r( $message, true ) );
      } else {
        error_log( $message );
      }
    }
  }
}

function child_override(){
	// override
	remove_action('cryout_before_content_hook' , 'tempera_above_widget');
	add_action ('cryout_before_content_hook','my_tempera_above_widget');
};
// add hook for overload function
add_action( 'after_setup_theme', 'child_override' );

function my_tempera_above_widget() {
 if ( is_active_sidebar( 'above-content-widget-area' ) && is_home() ) { ?>
	<ul class="yoyo">
		<?php dynamic_sidebar( 'above-content-widget-area' ); ?>
	</ul>
<?php } }

// API: sfida_permalink?completa
// ESEMPIO: http://returntodreamland.agesci.org/blog/sfida_event/viaggio-nel-tempo/?completa
function completa_sfida(){
	global $current_user;
	$post = get_post();

	if(is_single() && get_post_type() == 'sfida_event' && isset($_GET['completa'])){
		_log("Landing su completamento della sfida " . get_the_ID() . " per utente " . $current_user->ID);

		if(isset($_SESSION['wordpress']['user_id']) 
			&& $_SESSION['wordpress']['user_id'] == $current_user->ID ){
			// salva iscrizione completata
			_log("Completata la sfida " . get_the_ID() . " per utente " . $current_user->ID);
			
			$newpost = rtd_completa_sfida(get_the_ID(), $current_user->ID);

			$_SESSION['portal'] = array();

			_log('Redirect al nuovo post ' . $newpost . ', utente ' . $current_user->ID 
				. "( " . get_edit_post_link($newpost) . " )");

			wp_redirect(get_edit_post_link($newpost));
			exit;
		} else {
            _log(var_export($_SESSION,true));
        }

		wp_redirect(post_permalink(get_the_ID()));
		exit;
	}
}
add_action('wp_head', 'iscrizione_sfida_completata');

// API: sfida_permalink?iscritto
// ESEMPIO: http://returntodreamland.agesci.org/blog/sfida_event/viaggio-nel-tempo/?iscritto
function iscrizione_sfida_completata(){
	global $current_user;

	if(is_single() && get_post_type() == 'sfida_event' && isset($_GET['iscritto'])){
		_log("Landing su completamento iscrizione sfida " . get_the_ID() . " per utente " . $current_user->ID);

		if(isset($_SESSION['wordpress']['user_id']) 
			&& $_SESSION['wordpress']['user_id'] == $current_user->ID() ){
			// salva iscrizione completata
			_log("Completata iscrizione sfida " . get_the_ID() . " per utente " . $current_user->ID);
			add_user_meta($current_user->ID, '_iscrizioni', get_the_ID(), False);
			add_user_meta($current_user->ID,'_iscrizione_'.get_the_ID(), StatusIscrizione::Richiesta, True);
			$_SESSION['portal'] = array();
		} else {
            _log(var_export($_SESSION,true));
        }

		wp_redirect(post_permalink(get_the_ID()));
	}
}
add_action('wp_head', 'iscrizione_sfida_completata');

function diiscrizione_sfida(){
	global $current_user;

	if(is_single() && get_post_type() == 'sfida_event' && isset($_GET['disiscrivi'])){
		_log("Landing su disiscrizione sfida " . get_the_ID() . " per utente " . $current_user->ID);

		disiscriviti();
	}
}
add_action('wp_head', 'diiscrizione_sfida');

function disiscriviti(){
	global $current_user;

    rtd_disiscrivi_utente_da_sfida(get_the_ID(), $current_user->ID);

	wp_redirect(post_permalink(get_the_ID()));

}

// API: sfida_permalink?iscriviti
// ESEMPIO: http://returntodreamland.agesci.org/blog/sfida_event/viaggio-nel-tempo/?iscriviti
function richiedi_iscrizione_sfida(){
	global $current_user;

	if(is_single() && get_post_type() === 'sfida_event' && isset($_GET['iscriviti'])){
		
		if(!is_user_logged_in()){
			wp_die("Solo gli Esploratori o Guide registrati possono iscriversi alle sfide.");
			exit();
		}

		$user_id = $current_user->ID;
		
		if($user_id == 0){
			wp_redirect("wp-login.php");
			exit();
		}

		// login_portal( $user->user_login, $user );
		
		$post = get_post();

		if(!is_sfida_for_me($post)){
			wp_die("Non puoi partecipare a questa sfida.", "Sfida a partecipazione limitata", array('back_link' => True));
			return;
		}

		// controlla se non è già iscritto

		if(is_sfida_subscribed($post)){
			wp_die("Sei già iscritto a questa sfida.", "Sfida a partecipazione limitata", array('back_link' => True));
			exit();
		}

		$u_p = get_user_meta($user_id, 'punteggio');

		$ncomponenti_p = get_user_meta($user_id, 'numerocomponenti');
		$nspecialita_p = get_user_meta($user_id, 'nspecialita');
		$nbrevetti_p = get_user_meta($user_id, 'nbrevetti');
		
		$_SESSION['sfide'] = array(
			'sfida_url' => post_permalink($post->ID),
			'sfida_titolo' => get_the_title($post->ID),
			'sfida_id' => $post->ID,
			'sfidaspeciale' => is_sfida_speciale($post),
            'categoria' => get_elenco_categorie_sfida($post),
			'punteggio_attuale' => ($u_p) ? reset($u_p) : $u_p,
			'numero_componenti' => ($ncomponenti_p) ? reset($ncomponenti_p) : $ncomponenti_p,
			'numero_specialita' => ($nspecialita_p) ? reset($nspecialita_p) : $nspecialita_p,
			'numero_brevetti' => ($nbrevetti_p) ? reset($nbrevetti_p) : $nbrevetti_p
		);

		_log('Richiesta iscrizione per evento '.$post->ID.' da parte dello user '.$current_user->ID);

		$url = site_url('../portal/api/sfide/iscrizione/'. $post->ID);

		_log('Redirect to '.$url);

		wp_redirect($url);
		exit();
	}
}
add_action('wp_head', 'richiedi_iscrizione_sfida');

function no_nopaging($query) {
	if (is_post_type_archive('sfida_event')) {
		$query->set('nopaging', 1);
	}
}

add_action('parse_query', 'no_nopaging');

//// @see http://codex.wordpress.org/Template_Hierarchy
//// @see http://codex.wordpress.org/images/9/96/wp-template-hierarchy.jpg
function custom_single_template($single_template) {
   global $post;

    $newPath = dirname( __FILE__ ) . '/single-'.$post->post_type.'.php';

    if ( file_exists($newPath) ) {
        $single_template = dirname( __FILE__ ) . '/single-'.$post->post_type.'.php';
    }

   return $single_template;
}

add_filter('single_template', 'custom_single_template');


?>
