<?php
function my_theme_enqueue_styles() { 
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}

require_once('functions_shipping.php');
require_once('functions_quotation.php');
require_once('functions_product.php');
require_once('functions_construction_sites.php');

add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );


add_action( 'woocommerce_single_product_summary', 'woocommerce_total_product_price', 31 );
function woocommerce_total_product_price() { //This function display a product total price
    global $woocommerce, $product;
    echo sprintf('<div class="product_total_price">%s %s</div>',__('Total ','woocommerce'),'<span class="price_total">'.$product->get_price().' €</span>');
    ?>
        <script>
            jQuery(function($){
                var price = <?php echo $product->get_price(); ?>,
                    currency = ' €';

                $('[name=quantity]').change(function(){
                    if (!(this.value < 1)) {
                        var product_total = parseFloat(price * this.value);
                        $('.product_total_price .price_total').html( product_total.toFixed(2) + currency);
                    }
                });
            });
        </script>
    <?php
}

add_filter( 'woocommerce_registration_redirect', 'custom_redirection_after_registration', 10, 1 );
function custom_redirection_after_registration( $redirection_url ){
    // Change the redirection Url
    $redirection_url = get_home_url(); // Home page
    return $redirection_url; // Always return something
}

//Formulaire d'inscription
add_shortcode( 'wc_reg_form_bbloomer2', 'bbloomer_separate_registration_form2' );
    
function bbloomer_separate_registration_form2() {
   	ob_clean();
	ob_start();
	//if ( is_admin() ) return;
   if ( is_user_logged_in() ) {
	   echo "Il semble que vous soyez déjà connecté, vous pouvez retourner à l'acceuil en cliquant <a href='https://koncrete.fr'>ici</a>";
	    return ob_get_clean();
	   //Doesn't work 
   };
   $allowed_html = array('a' => array('href' => array(),));
 
   // NOTE: THE FOLLOWING <FORM></FORM> IS COPIED FROM woocommerce\templates\myaccount\form-login.php
   // IF WOOCOMMERCE RELEASES AN UPDATE TO THAT TEMPLATE, YOU MUST CHANGE THIS ACCORDINGLY
 
   do_action( 'woocommerce_before_customer_login_form' );
   ?>

   <h1>
      Vous voici prêt à créer votre compte Koncrete !
   </h1>
  <div class="woocommerce">
   <div class="u-columns" id="customer_register">
      <div class="u-column col-1">
		  <div class="tab create_account">
			  <button id="tab_individual" class="tablinks selected" onclick="open_form('individual')">Je suis un particulier</button>
			  <button id="tab_professional" class="tablinks" onclick="open_form('professional')">Je suis un pro</button>
		  </div>
		  <form method="post" id="individual" class="woocommerce-form woocommerce-form-register register active" <?php do_action( 'woocommerce_register_form_tag' ); ?>>
 		  <?php do_action( 'woocommerce_register_form_start_individual' ); ?>
			  
 		  <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
 			  <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
              	  <label for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?> <span class="required">*</span></label>
              	  <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
              </p>
 		  <?php endif; ?>
			  
		  <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		  	  <label for="reg_email"><?php esc_html_e( 'Adresse mail', 'woocommerce' ); ?><span class="required">*</span></label>
			  <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
		  </p>
			  
 		  <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
					<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" />
				</p>
		  <?php else : ?>
          	  <p><?php esc_html_e( 'Un mot de passe va être envoyé sur votre boite mail.', 'woocommerce' ); ?></p>
		  <?php endif; ?>
 
          <?php do_action( 'woocommerce_register_form' ); ?>
          <p class="form-row">
          	  <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
              	  <input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Se souvenir de moi', 'woocommerce' ); ?></span>
			  </label>
          </p>        
          <p class="form-row">
          	  <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__newsletter">
              	  <input class="woocommerce-form__input woocommerce-form__input-checkbox" name="newsletter" type="checkbox" id="newsletter" value="register" /> <span><?php esc_html_e( 'S\'inscrire à la newsletter', 'woocommerce' ); ?></span>
              </label>
          </p>		  
		  <p class="form-row" id="CGU">
          	  <?php esc_html_e( 'En vous créant un compte, vous acceptez les Conditions Générales d\'Utilisation.', 'woocommerce' ); ?>
          </p>
          <div id="button_connect">
          	  <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
              <button type="submit" class="woocommerce-button button woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Créer un compte', 'woocommerce' ); ?></button>
          </div>
          <?php do_action( 'woocommerce_register_form_end' ); ?>
		  </form>
		  
		  
		  
		  
		  <form method="post" id="professional" class="woocommerce-form woocommerce-form-register register disabled" <?php do_action( 'woocommerce_register_form_tag' ); ?>>
 		  <?php do_action( 'woocommerce_register_form_start_professional' ); ?>
 		  <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
 			  <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
              	  <label for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?> <span class="required">*</span></label>
              	  <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
              </p>
 		  <?php endif; ?>
			  
		  <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		  	  <label for="reg_email"><?php esc_html_e( 'Adresse mail', 'woocommerce' ); ?><span class="required">*</span></label>
			  <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
		  </p>
			  
 		  <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
					<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" />
				</p>
		  <?php else : ?>
          	  <p><?php esc_html_e( 'Un mot de passe va être envoyé sur votre boite mail.', 'woocommerce' ); ?></p>
		  <?php endif; ?>
 
          <?php do_action( 'woocommerce_register_form' ); ?>
          <p class="form-row">
          	  <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
              	  <input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Se souvenir de moi', 'woocommerce' ); ?></span>
			  </label>
          </p>        
          <p class="form-row">
          	  <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__newsletter">
              	  <input class="woocommerce-form__input woocommerce-form__input-checkbox" name="newsletter" type="checkbox" id="newsletter" value="register" /> <span><?php esc_html_e( 'S\'inscrire à la newsletter', 'woocommerce' ); ?></span>
              </label>
          </p>		  
		  <p class="form-row" id="CGU">
          	  <?php esc_html_e( 'En vous créant un compte, vous acceptez les Conditions Générales d\'Utilisation.', 'woocommerce' ); ?>
          </p>
          <div id="button_connect">
          	  <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
              <button type="submit" class="woocommerce-button button woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Créer un compte', 'woocommerce' ); ?></button>
          </div>
          <?php do_action( 'woocommerce_register_form_end' ); ?>
		  </form>
		  
		  <script>
			  function open_form(tab_name){
				  tabs_form = document.getElementsByClassName("woocommerce-form-register");
				  for (i = 0; i < tabs_form.length; i++) {
					  tabs_form[i].classList.remove("active");
					  tabs_form[i].classList.add("disabled");
 				  }	 
				  
				  document.getElementById("tab_individual").classList.remove("selected");
				  document.getElementById("tab_professional").classList.remove("selected");	
				  document.getElementById("tab_"+tab_name).classList.add("selected");
				
				  document.getElementById(tab_name).classList.add("active");
				  document.getElementById(tab_name).classList.remove("disabled");
				  return;
			  }
		  </script>
		  
		  <p class="row_connect"><?php printf(
          	  wp_kses( __( '<a href="%1$s">Vous êtes déjà inscrit ? Cliquez ici pour vous connecter !</a> ', 'woocommerce' ), $allowed_html ),
              esc_url( 'https://koncretedotfr.wpcomstaging.com/mon-compte/')
           );?>
          </p>
  	  </div>
  </div>
  </div>
<?php 
	
	return ob_get_clean();
}

add_filter( 'woocommerce_min_password_strength',function () {
	return 3; 
	}
, 10 );

//Mon compte : affichage menu
function woocommerce_account_navigation() {
		global $wp;
		if ( ! empty( $wp->query_vars ) ) {
			foreach ( $wp->query_vars as $key => $value ) {
				// Ignore pagename param.
				if ( 'pagename' === $key ) {
					continue;
				}

				if ( has_action( 'woocommerce_account_' . $key . '_endpoint' ) ) {
					wc_get_template( 'woocommerce/myaccount/navigation.php' );
					return;
				}
			}
		}
		// No endpoint found? Default to dashboard.
		return;		
	}

function woocommerce_account_content() {
	global $wp;
	if ( ! empty( $wp->query_vars ) ) {
		foreach ( $wp->query_vars as $key => $value ) {
			// Ignore pagename param.
			if ( 'pagename' === $key ) {
				continue;
			}
			if ( has_action( 'woocommerce_account_' . $key . '_endpoint' ) ) {
				?>
				<div class="woocommerce-MyAccount-content">
					<?php
								do_action( 'woocommerce_account_' . $key . '_endpoint', $value );
					?>
				</div>
				<?php
				return;
			}
		}
	}
	// No endpoint found? Default to dashboard.
	wc_get_template(
		'woocommerce/myaccount/dashboard.php',
		array(
			'current_user' => get_user_by( 'id', get_current_user_id() ),
		)
	);
}

//Mon compte : identification page actuelle					
function checkCurrentPage($endpoint){
	global $wp;
	if (esc_url( wc_get_account_endpoint_url( $endpoint ) ) == home_url( $wp->request )."/") {
		echo "selected";
	}
	else{
		echo "not_selected";
	}
}

// Ajout champs prénoms et noms dans formulaire d'inscription
function wooc_extra_register_fields_individual() {?>
       <p class="form-row form-row-first">
       <label for="reg_billing_first_name"><?php _e( 'Prénom', 'woocommerce' ); ?><span class="required">*</span></label>
       <input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" />
       </p>
       <p class="form-row form-row-last">
       <label for="reg_billing_last_name"><?php _e( 'Nom', 'woocommerce' ); ?><span class="required">*</span></label>
       <input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) esc_attr_e( $_POST['billing_last_name'] ); ?>" />
       </p>
       <div class="clear"></div>
       <?php
 }

// Ajout champs nom, prénom, nom de l'entreprise, phone, siret, numéro de TVA, addresse de facturation

function wooc_extra_register_fields_professional() {?>
     	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide ">
       <label for="reg_billing_company"><?php _e( 'Nom de l\'entreprise', 'woocommerce' ); ?><span class="required">*</span></label>
       <input type="text" class="input-text" name="billing_company" id="reg_billing_company" value="<?php if ( ! empty( $_POST['billing_company'] ) ) esc_attr_e( $_POST['billing_company'] ); ?>" />
       </p>
		<p class="form-row form-row-first">
       <label for="reg_billing_first_name"><?php _e( 'Prénom', 'woocommerce' ); ?><span class="required">*</span></label>
       <input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" />
       </p>
       <p class="form-row form-row-last">
       <label for="reg_billing_last_name"><?php _e( 'Nom', 'woocommerce' ); ?><span class="required">*</span></label>
       <input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) esc_attr_e( $_POST['billing_last_name'] ); ?>" />
       </p>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide ">
       <label for="reg_billing_phone"><?php _e( 'Téléphone', 'woocommerce' ); ?><span class="required">*</span></label>
       <input type="text" class="input-text" name="billing_phone" id="reg_billing_phone" value="<?php if ( ! empty( $_POST['billing_phone'] ) ) esc_attr_e( $_POST['billing_phone'] ); ?>" placeholder="06 12 34 56 78"/>
       </p>    
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide ">
       <label for="reg_billing_company_SIRET"><?php _e( 'Numero de SIRET', 'woocommerce' ); ?><span class="required">*</span></label>
       <input type="text" class="input-text" name="billing_company_SIRET" id="reg_billing_company_SIRET" value="<?php if ( ! empty( $_POST['billing_company_SIRET'] ) ) esc_attr_e( $_POST['billing_company_SIRET'] ); ?>" placeholder="123 456 789 01234"/>
       </p>     	
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide ">
       <label for="reg_billing_company_VAT"><?php _e( 'Numero de TVA', 'woocommerce' ); ?><span class="required">*</span></label>
       <input type="text" class="input-text" name="billing_company_VAT" id="reg_billing_company_VAT" value="<?php if ( ! empty( $_POST['billing_company_VAT'] ) ) esc_attr_e( $_POST['billing_company_VAT'] ); ?>" placeholder="FR01234567890"/>
       </p>
       <div class="clear"></div>
       <?php
 }


function wooc_save_extra_register_fields( $customer_id ) {
    if ( isset( $_POST['billing_phone'] ) ) {
                 // Phone input filed which is used in WooCommerce
                 update_user_meta( $customer_id, 'billing_phone', sanitize_text_field( $_POST['billing_phone'] ) );
          }
      if ( isset( $_POST['billing_first_name'] ) ) {
             //First name field which is by default
             update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
             // First name field which is used in WooCommerce
             update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
      }
      if ( isset( $_POST['billing_last_name'] ) ) {
             // Last name field which is by default
             update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
             // Last name field which is used in WooCommerce
             update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
      }
	  if ( isset( $_POST['billing_company'] ) ) {
             update_user_meta( $customer_id, 'billing_company', sanitize_text_field( $_POST['billing_company'] ) );
      }
	  if ( isset( $_POST['billing_company_SIRET'] ) ) {
             update_user_meta( $customer_id, 'billing_company_SIRET', sanitize_text_field( $_POST['billing_company_SIRET'] ) );

      }
	  if ( isset( $_POST['billing_company_VAT'] ) ) {
             // Last name field which is by default
             update_user_meta( $customer_id, 'billing_company_VAT', sanitize_text_field( $_POST['billing_company_VAT'] ) );
      }
}



add_action( 'woocommerce_created_customer', 'wooc_save_extra_register_fields' );
add_action( 'woocommerce_register_form_start_individual', 'wooc_extra_register_fields_individual' );
add_action( 'woocommerce_register_form_start_professional', 'wooc_extra_register_fields_professional' );


//Mon compte : ajouts endpoints
add_action('init', function() {
	add_rewrite_endpoint('mes-devis', EP_ROOT | EP_PAGES);
	add_rewrite_endpoint('favoris', EP_ROOT | EP_PAGES);
	add_rewrite_endpoint('suivi-livraison', EP_ROOT | EP_PAGES);
	add_rewrite_endpoint('mes-chantiers', EP_ROOT | EP_PAGES);

	
});

add_filter( 'query_vars', function($vars) {
	$vars[] = 'mes-devis';
	return $vars;
}, 0 );

add_filter( 'query_vars', function($vars) {
	$vars[] = 'mes-chantiers';
	return $vars;
}, 0 );

add_filter( 'query_vars', function($vars) {
	$vars[] = 'favoris';
	return $vars;
}, 0 );


add_filter( 'query_vars', function($vars) {
	$vars[] = 'suivi-livraison';
	return $vars;
}, 0 );

	
//Mon compte : ajout endpoints au menu

add_filter ( 'woocommerce_account_menu_items', 'add_links' );
function add_links( $menu_links ){

	// we will hook "anyuniquetext123" later
	$new = array( 'mes-devis' => 'Mes devis', 'suivi-livraison' => 'Suivi de mes livraisons', 'mes-chantiers'=>'Mes chantiers'); 
	$new2 = array('favoris' => 'Mes favoris');
	$new_orders = array('orders' => 'Mes commandes');

	$new3 = array('payment-methods' => 'Mes moyens de paiement');
	
	$new_edit_account = array('edit-account' => 'Mes informations personnelles');
	$user_logout = array('customer-logout'=> 'Deconnexion');
	   
	// array_slice() is good when you want to add an element between the other ones
	$menu_links =  array_slice( $menu_links, 0, 2, true ) 
	+ $new 
	+ $new2
	+ $new_edit_account
	+ $new3
	+ array_slice( $menu_links, 1,NULL);
    
	return $menu_links;
 
 
}



add_action( 'woocommerce_account_mes-devis_endpoint', function () {

	$quotes_array = get_quotes_by_user_id(get_current_user_id());
	setlocale(LC_MONETARY,"fr_FR");
	$fmt = numfmt_create( 'fr_FR', NumberFormatter::CURRENCY );
	?>
			<h1>Mes devis</h1>
			<br>
			Pour demander un devis, vous devez vous connecter avec votre compte et avoir une commande de plus de 1 500,00€. Nous répondrons à votre demande de devis dans les plus bref délais pas email.
			<br>
			<?php foreach($quotes_array as $quote_content) {
					str_replace("/","",$quote_content->quote_cart);
					$quote_content_formated = json_decode($quote_content->quote_cart ); 
					?> <div class="quote-list quote-list-<?php echo $quote_content->status ?>">
					<?php
					echo "Devis n°".$quote_content->ID." - ".date("d/m/Y",strtotime($quote_content->created_at));
					if($quote_content->status == 'done'){
						echo " - Traité";
					}
					$product_count = 1;
					$total = 0;
					?> <div class="cart_item cart_item_header">  
						<div class="product_count"> N° </div>
						<div class="product_name"> Produit </div>
						<div class="product_quantity"> Qtt </div>
						<div class="product_subtotal"> Sous-total </div>
					   </div> <?php
					foreach($quote_content_formated as $quote_cart_content){ 
						?> <div class="cart_item"> 
						<div class="product_count"><?php echo $product_count."  -  "; ?> </div> 					
						<div class="product_name"><?php echo $quote_cart_content->name; ?> </div> 
						<div class="product_quantity"> <?php echo $quote_cart_content->quantity; ?> </div> 
						<div class="product_subtotal"> <?php echo numfmt_format_currency($fmt,$quote_cart_content->line_subtotal,"EUR"); ?></div> 
					</div> <?php
						$product_count = $product_count + 1;
						$total = $total + $quote_cart_content->line_subtotal;														   
					}
				?> <div class="quote_total"> <?php echo "Total : ".numfmt_format_currency($fmt,$total,"EUR");?></div>
				</div> <?php
				}
				?>
		<?php
} );



add_action( 'woocommerce_account_suivi-livraison_endpoint', function () {
	?>
		<div class="woocommerce-MyAccount-content">
			<h1>Vos suivi de livraison seront disponibles ici très bientôt</h1>
		</div>
		<?php
} );

add_action( 'woocommerce_account_favoris_endpoint', function () {
	?>
		<div class="woocommerce-MyAccount-content">
			<h1>Vos favoris seront disponibles ici très bientôt</h1>
		</div>
		<?php
} );

//Mon compte : suppression endpoints
add_filter ( 'woocommerce_account_menu_items', 'remove_my_account_links' );
function remove_my_account_links( $menu_links ){
	
	unset( $menu_links['edit-address'] ); // Addresses
	//unset( $menu_links['dashboard'] ); // Remove Dashboard
	//unset( $menu_links['payment-methods'] ); // Remove Payment Methods
	//unset( $menu_links['orders'] ); // Remove Orders
	unset( $menu_links['downloads'] ); // Disable Downloads
	//unset( $menu_links['edit-account'] ); // Remove Account details tab
	//unset( $menu_links['customer-logout'] ); // Remove Logout link
	return $menu_links;
}

//Deconnexion sans confirmation
add_action( 'template_redirect', 'logout_confirmation' );

function logout_confirmation() {
    global $wp;
    if ( isset( $wp->query_vars['customer-logout'] ) ) {
	    wp_destroy_current_session();
   		wp_logout();
    	wp_redirect(site_url()); 
        wp_redirect( str_replace( '&amp;', '&', wp_logout_url( wc_get_page_permalink( 'myaccount' ) ) ) );
        exit;
    }
}

//Mon compte : changer infos personnelles.
add_filter('woocommerce_save_account_details_required_fields', 'wc_save_account_details_required_fields' );
function wc_save_account_details_required_fields( $required_fields ){
    unset( $required_fields['account_display_name'] );
    return $required_fields;
}

add_action('after_setup_theme','remove_admin_bar');

function remove_admin_bar(){
	if(!current_user_can('administrator') && !is_admin()){
		show_admin_bar(false);
	}
}
	
add_shortcode('redirect_if_user_is_not_logged','function_redirect_if_user_is_not_logged');

function function_redirect_if_user_is_not_logged(){
	if( !is_user_logged_in() ){
		header('Location: https:\/\/koncrete.fr');
		die();
	}
}
	
add_action('init','custom_login');
function custom_login(){
 global $pagenow;
 if( 'wp-login.php' == $pagenow ) {
	wp_redirect(get_home_url().'/mon-compte');
	return ;
 }
}

add_shortcode('chatbot_button','function_chatbot_button');

function function_chatbot_button(){
	ob_clean();
	ob_start();
	?><div class="et_pb_with_border et_pb_module et_pb_cta_0 et_pb_promo  et_pb_text_align_center et_pb_bg_layout_dark" onclick="$crisp.push(['do', 'chat:open'])" style="padding:15px; cursor:pointer;">
		<div><span style="color: #333333; font-size:18px;" ><strong>Chatter avec un conseiller</strong></span></div>
		</div>
	<?php
	return ob_get_clean();
};


add_filter( 'woocommerce_per_product_shipping_ship_via', function ( $ship_via ) {
	$ship_via[] = 'flat_rate';
	return $ship_via;
} );


	
