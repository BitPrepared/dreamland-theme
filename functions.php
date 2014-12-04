<?php

// require_once("regioni_e_zone/regione_zone_utils.php");

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

// API: sfida_permalink?iscritto&secret=XXXX
function iscrizione_sfida_completata(){
	if(is_single() && get_post_type() == 'sfida_event' && isset($_GET['iscritto'])){
		if(!isset($_GET['secret'])){
			return;
		}
		
		$s = filter_var( $_GET['secret'], FILTER_STRING);
		
		if($s !== $sfide_api_secret){
			return;
		}

		// salva iscrizione completata
		add_user_meta(wp_get_current_user_ID(), '_iscrizioni', get_the_ID(), False);

	}
}
add_action('wp_head', 'iscrizione_sfida_completata');

// API: sfida_permalink?iscriviti
function richiedi_iscrizione_sfida(){
	if(is_single() && get_post_type() === 'sfida_event' && isset($_GET['iscriviti'])){
		
		if(!is_user_logged_in()){
			wp_redirect("wp-login.php");
			exit();
		}

		$user = wp_get_current_user();
		
		if(! $user instanceof WP_User){
			wp_redirect("wp-login.php");
			exit();
		}

		// login_portal( $user->user_login, $user );
		
		$post = get_post();

		if(!is_sfida_for_me($post)){
			wp_die("Non puoi partecipare a questa sfida", "Sfida a partecipazione limitata", array('back_link' => True));
			return;
		}

		// controlla se non è già iscritto
		$is_iscritto = get_user_meta($user, '_iscrizioni');
		if($is_iscritto && in_array($post->ID, $is_iscritto)){
			wp_die("Sei già iscritto a questa sfida", "Sfida a partecipazione limitata", array('back_link' => True));
			exit();
		}

		$u_p = get_user_meta($user, 'punteggio');
		$ncomponenti_p = get_user_meta($user, 'numerocomponenti');
		$nspecialita_p = get_user_meta($user, 'nspecialita');
		$nbrevetti_p = get_user_meta($user, 'nbrevetti');
		
		$_SESSION['sfide'] = array(
			'sfida_url' => post_permalink($post->ID),
			'sfida_id' => $post->ID,
			'punteggio_attuale' => ($u_p) ? reset($u_p) : $u_p,
			'numero_componenti' => ($ncomponenti_p) ? reset($ncomponenti_p) : $ncomponenti_p,
			'numero_specialita' => ($nspecialita_p) ? reset($nspecialita_p) : $nspecialita_p,
			'numero_brevetti' => ($nbrevetti_p) ? reset($nbrevetti_p) : $nbrevetti_p
		);

		_log('Richiesta iscrizione per evento '.$post->ID.' da parte dello user '.$user->ID);

		$url = site_url('../portal/sfide/iscrizione/'. $post->ID);

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

?>
