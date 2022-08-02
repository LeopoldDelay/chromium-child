<?php
add_shortcode( 'boutton_demande_de_devis', 'display_cart_quotation_button' );
    
function display_cart_quotation_button() {
	//This function handle the button in cart page.
	ob_clean();
	ob_start();
	global $woocommerce;
    if (floatval( preg_replace( '#[^\d.]#', '', $woocommerce->cart->get_cart_total() ) ) > 150000 && is_user_logged_in()){
?>
	<div id="button_demande_de_devis" class="button active">		
			<a href="/demande-de-devis/">
			<h2 class="et_pb_module_header">Demander un devis gratuit</h2>
		</a>
	</div>
<?php
		}elseif (is_user_logged_in())
		{
			?>
<div id="button_demande_de_devis" class="button disabled">
		<a href="/demande-de-devis/">
			<h2 class="et_pb_module_header">Demander un devis gratuit</h2>
		</a>
	</div>
<?php }; 
?>
<script> //This handle the cart total amount change, disable the button if amount < 1500e
		   jQuery( document.body ).on( 'updated_cart_totals', function(){
				var cart_total = document.getElementsByClassName('order-total')[0].getElementsByClassName('woocommerce-Price-amount amount')[0].getElementsByTagName("bdi")[0].firstChild.textContent;
				cart_total = parseFloat(cart_total);
				var element = document.getElementById('button_demande_de_devis')
				if(cart_total>1499){
					element.classList.add('active')
					element.classList.remove('disabled');
				}
				else{
					element.classList.remove('active')
					element.classList.add('disabled');				
				}
				return 0;
			});
		
	</script>
<?php   return ob_get_clean();
}


function create_quotation($items_to_quote,$user_data ,$quotation_other_info = null,$type='quote'){
		//This function create a quotation, send an email to the admin and store the quotation into the DB.
	    if(!is_user_logged_in()){
			return 'User is not logged in';
		};
		
		
	
		$current_user_id = $user_data["id"];
		$check_address_res = is_user_address_valid($current_user_id);

		if(!($check_address_res["is_valid"])){
			return $check_address_res["message"];
		}
		
		$user_cart = array();
		$user_email = $user_data["email"];
	
		if( is_null($items_to_quote)){
			//Quote all cart (compatiblity)
			$session_handler = new WC_Session_Handler();
			$session = $session_handler->get_session($current_user_id);
			$cart_items = maybe_unserialize($session['cart']);

			foreach ($cart_items as $cart_item){
				$user_cart_item = array();
				$item_name = wc_get_product($cart_item['product_id'])->get_name();
				$cart_items[$cart_item['key']]['name']= $item_name;
				$user_cart_item['name'] = $item_name;
				$user_cart_item['quantity']= $cart_item['quantity'];
				$user_cart_item['line_subtotal']= $cart_item['line_subtotal'];
				$user_cart_item['line_total']= $cart_item['line_total'];
				$user_cart_item['variation_id']= $cart_item['variation_id'];
				$user_cart[] = $user_cart_item;
			}
		}
		else{
			foreach ($items_to_quote as $cart_item){
				$user_cart_item = array();
				$user_cart_item['name'] = $cart_item['name'];
				$user_cart_item['quantity']= $cart_item['quantity'];
				$user_cart_item['line_subtotal']= $cart_item['line_subtotal'];
				$user_cart_item['line_total']= $cart_item['line_total'];
				$user_cart_item['variation_id']= $cart_item['variation_id'];
				$user_cart[] = $user_cart_item;
			}
		}
		
		$user_data["type"] = $type;
		$user_data["more_info"] = $quotation_other_info;
	
		$quote_data = add_quotation_to_db($user_cart,$user_data);
		$email_body = create_quote_email($user_data,$user_cart,$quote_data);
		$result_email = send_quotation_email($user_data,$email_body);
		
	    return var_dump($result_email);
};

function send_quotation_email($user_data,$email_body){ 
		try{
			$to = $user_data["email"];
			$sujet = 'Demande de devis';
			$headers[] = 'leopold@koncrete.fr'; 
			$res = wp_mail( $to, $sujet,$email_body,$headers ); 
			return $res;
		}catch (Exception $e) {
    		echo 'Exception received : ',  $e->getMessage(), "\n";
			return $e;
		}
};

add_filter('wp_mail_content_type', function( $content_type ) {
            return 'text/html';
});

add_filter('wp_mail_from', function(  $from_email ) {
            return 'leopold@koncrete.fr';
});


add_filter('wp_mail_from_name', function(  $from_name ) {
            return 'Leopold de Koncrete';
});


function add_quotation_to_db($user_cart,$user_data){
	try{
	  global $wpdb;
	   $wpdb->insert("{$wpdb->prefix}quotes",array(
			"user_id"=>$user_data["id"],
			"quote_cart"=>json_encode($user_cart),
			"phone"=>$user_data['phone'],
			"more_info"=> $user_data['more_info'],
		    "type" => $user_data['type'],
		    "user_shipping_meta" => json_encode($user_data["shipping_address"])
		));
			
		return $wpdb->get_row("select * from {$wpdb->prefix}quotes where ID = {$wpdb->insert_id}", ARRAY_A);
		
	}catch(Exception $e){
		echo 'Exception received : ',  $e->getMessage(), "\n";
		return $e;
	}
};

function get_quotes_by_user_id($user_id){
	global $wpdb;
	return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}quotes WHERE user_id = {$user_id}", OBJECT );
};

add_shortcode('quotation_info','create_quotation_info_panel');

function create_quotation_info_panel(){
	ob_clean();
	ob_start();
	?>
	<label for="text" style="padding:0.5%;">Information supplementaires :</label>
	<div class="input other_info">
		<textarea  type="text" id="quotation_more_info" class="text-input" placeholder="Indiquer ici les informations supplémentaire pour le devis, au plus au mieux" required minlength="0" maxlength="10000" size="10"></textarea>
	</div>
	<label for="tel"style="padding:0.5%;">Téléphone :</label>
	<div class="input phone">
		<input type="tel" id="quotation_phone" class="text-input" placeholder="0123456789" required minlength="5" maxlength="20" size="10"  >
	</div>
	<?php
	return ob_get_clean();
};

add_shortcode('quotation_confirm_button','function_quotation_confirm_button');

function function_quotation_confirm_button(){
	?>
	<a id="button_confirm_quotation" class="button disabled">Demander un devis</a>
	<script>
	$(document).ready(function () {
		$('#quotation_phone').on('input', function() {
		//Check if phone format is valid
		//Then activate the button
		var phone_input = $(this).val();
		var phone_input = phone_input.toString().replace(/\s/g, '');
		var button_quotation = document.getElementById('button_confirm_quotation')

		if(phone_input.length >= 10){//Phone is valid
			button_quotation.classList.add('active');
			button_quotation.classList.remove('disabled');
		}
			else{
			button_quotation.classList.remove('active');
			button_quotation.classList.add('disabled');
			}
		});
		
		$("#button_confirm_quotation").on('click',function(){
			var button_demande_de_devis = document.getElementById("button_confirm_quotation")
			if(!button_demande_de_devis.classList.contains("active")){
				return;
			}
			var phone_input = $("#quotation_phone").val();
			var quotation_more_info = $('#quotation_more_info').val();
			var data = {
				action: 'create_quote',
				_ajax_nonce: '<?php echo wp_create_nonce( "secure_nonce_name" ); ?>',
				phone: phone_input,
				more_info : quotation_more_info
			};
			console.log("Data to send is :",data);
			$.post( "https:\/\/koncrete.fr\/wp-admin\/admin-ajax.php", data, function(response){console.log( 'Got this from the server: ' + response )})
			.done(function() {
				window.location.replace("/demande_de_devis_en_cours");
			})
			.fail(function(xhr, status, error) {
						alert("Une erreur est survenue, veuillez re-essayer. Si l'erreur persiste, merci de nous contacter via le chat-bot");
						console.log(status,error);
			});
		})
	});
	</script>
	<?php
	return ob_get_clean();
};

add_action( 'wp_ajax_create_quote', 'ajax_create_quote' );

function ajax_create_quote(){
	check_ajax_referer( 'secure_nonce_name', 'security' );
	$user_meta = get_user_meta(get_current_user_id());
	$user_data =array(
		"id" => get_current_user_id(),
		"phone" => $_POST['phone'],
		"email" => get_userdata( get_current_user_id() )->user_email,
		"shipping_address" => array(
			"first_name" => $user_meta['shipping_first_name'][0],
			"last_name" => $user_meta['shipping_last_name'][0],
			"postcode" => $user_meta['shipping_postcode'][0],
			"city" => $user_meta['shipping_city'][0],
			"address_1" => $user_meta['shipping_address_1'][0],
			"country" => $user_meta['shipping_country'][0]
			)
		);
	$more_info = $_POST['more_info'] ;
  	echo create_quotation(null,$user_data,$more_info,'quote');
	WC()->cart->empty_cart();
};

function is_user_address_valid($user_id){
	//For now, just checking the postcode
	$postcode = get_user_meta($user_id,'shipping_postcode')[0];
	if(!$postcode){
		return array(
			"is_valid" => false,
			"message" => "Pas de code postal"
			);
	}
	if(! (strlen($postcode) == 5) ){
		return array(
			"is_valid" => false,
			"message" => "Code postal non valide"
			);
	}
	return array(
			"is_valid" => true,
			"message" => "Code postal  valide"
			);
};


function create_quote_email($user_data,$items_to_quote,$quote_data){
	//Handle bars are : quote_created_date , user_firstname , quote_type , quote_id , item_to_quote (html formated)
	$template = file_get_contents('template_quote_email_user.html',FILE_USE_INCLUDE_PATH);
	$items_to_display = "";
	
	foreach($items_to_quote as $item_to_quote){
		$items_to_display = $items_to_display.'<li class="item_to_quote"><span class="item_name"><b>'.$item_to_quote['name'].'</b></span> - Quantité : <span class="item_quantity">'.$item_to_quote['quantity'].'</span> - <span class="item_price">'.$item_to_quote['line_total'].'€</span></li>';                         
	}

	if($quote_data['type'] == 'shipping') $quote_data['type'] = 'de livraison';
	if($quote_data['type'] == 'quote') $quote_data['type'] = 'de prix';
	
	$date = DateTime::createFromFormat('Y-m-d H:i:s', $quote_data['created_at']);
	$quote_data['created_at'] = $date->format('d/m/Y');

	$keys = array(
		"quote_created_at" => $quote_data['created_at'],
		"user_firstname" => $user_data['shipping_data']['first_name'],
		"quote_type" => $quote_data['type'],
		"quote_id" => $quote_data['ID'],
		"items_to_quote" => $items_to_display,
		"quote_more_info" => $quote_data['more_info']
	);

	foreach ($keys as $key => $value){
		$template = str_replace("%".$key."%", $value, $template);
	};
	
	return $template;
};

