<?php
add_shortcode('shortcode_livraison','create_shipping_panel');

function create_shipping_panel(){
	ob_clean();
	ob_start();
	
	


	$are_all_product_shippable = true;
	$is_cart_exclusively_on_quote = true;
	$items_to_quote = array();
	$cart_items = WC()->cart->get_cart();
	
	if(empty($cart_items)){
		echo "<b>Votre panier est vide, ajoutez un produit pour passer à la livraison</b>";
		return ob_get_clean();
	};
	
	$packages = WC()->cart->get_shipping_packages();
	$package = $packages[0]; //Not sure about this ??
	$shipping_class_names = WC()->shipping->get_shipping_method_class_names();
	$methode_instance = new $shipping_class_names['per_product']( $instance_id );
	if(is_user_logged_in()){
			
			$package['destination']['postcode']	= get_user_meta(get_current_user_id(),'shipping_postcode')[0];
	}
	
	foreach ($cart_items as $cart_item){ //TODO : error $cart_items doesnt have names & shipping cost
		
		$product_meta = get_post_meta( $cart_item['product_id']);
		$is_shipping_configured = $product_meta["_per_product_shipping"][0];
		
		$product = wc_get_product( $cart_item['product_id'] );
		$cart_item['product_shipping_price'] = $methode_instance->calculate_product_shipping_cost($cart_item,$package);
		$cart_item['name'] = wc_get_product($cart_item['product_id'])->get_name();
		
		if(!$is_shipping_configured || $cart_item['product_shipping_price']=== false){
			$are_all_product_shippable = false;
			$items_to_quote[] = $cart_item;
		}
		else{
			$is_cart_exclusively_on_quote = false;
			$items_to_ship[] = $cart_item;
		};
	};
		
				
		if(!$is_cart_exclusively_on_quote){
		?>	
		<div class="panel_wrapper">
			<div class="panel_header">
				<h3 style="color:#5a5555; font-size:18px">Mes produits livrables <span class='item_quantity'>(<?php echo sizeof($items_to_ship) ?>)</span></h3>
			</div>
			<table class="shipping panel items_to_ship" style="width:100%;">
				<?php foreach($items_to_ship as $item_to_ship){ ?>
				<tr class="woocommerce-cart-form__cart-item cart_item item_to_ship" >
					<td class="product-thumbnail">
						<a href="<?php echo get_permalink( $item_to_ship['product_id'] ); ?>">
							<img width="300" height="300" src="<?php echo get_the_post_thumbnail_url($item_to_ship['product_id']); ?>" 
								 class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="" loading="lazy">
						</a>
					</td>
					<td class="product-name">
						<a href = "<?php echo get_permalink( $item_to_ship['product_id'] );?>"> 
							<?php echo $item_to_ship['name']; ?>
						</a>
					</td>
					<td class="product-price">
						<span class="woocommerce-Price-amount amount">
							<bdi>
								<?php echo $item_to_ship['line_total']; ?>€
							</bdi>
						</span>
					</td>
					<td class="product-quantity">
						Quantité : <?php  echo $item_to_ship['quantity']; ?>
					</td>

					<td class="product-shipping-method">
						<svg class="svg-icon icon-quotation" viewBox="0 0 16 16">
							<path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5v-7zm1.294 7.456A1.999 1.999 0 0 1 4.732 11h5.536a2.01 2.01 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456zM12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12v4zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
						</svg>
						<div class="shipping_type shipping_standard"> <?php if($item_to_ship['product_shipping_price']==0){
                                echo 'Livraison gratuite';
                            } else {
                                echo 'Livraison standard';
                            } ?>
						</div>
					</td><?php
					if(! ($item_to_ship['product_shipping_price']==0)){
						?><td class="product-shipping-cost">
						Frais de livraison : <?php echo $item_to_ship['product_shipping_price']; ?>€
					</td>
					<?php } ?>
				</tr>
	  <?php } ?>
	 </table> 
 </div>

	<?php } 

	if(!$are_all_product_shippable){ ?>
	<div class="panel_wrapper">
		<div class="panel_header">
			<h3 style="color:#5a5555; font-size:18px">Mes produits qui necessitent un devis de livraison <span class='item_quantity'>(<?php echo sizeof($items_to_quote) ?>)</span></h3>
		</div>
		<table class="shipping panel items_to_quote" style="width:100%;">
			<?php foreach($items_to_quote as $item_to_quote){ ?>
			<tr class="woocommerce-cart-form__cart-item cart_item item_to_ship" >
				<td class="product-thumbnail">
					<a href="<?php echo get_permalink( $item_to_quote['product_id'] ); ?>">
						<img width="300" height="300" src="<?php echo get_the_post_thumbnail_url($item_to_quote['product_id']); ?>" 
							 class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="" loading="lazy">
					</a>
				</td>
				<td class="product-name">
					<a href = "<?php echo get_permalink( $item_to_quote['product_id'] );?>"> 
						<?php echo $item_to_quote['name']; ?>
					</a>
				</td>
				<td class="product-price">
					<span class="woocommerce-Price-amount amount">
						<bdi>
							<?php echo $item_to_quote['line_total']; ?>€
						</bdi>
					</span>
				</td>
				<td class="product-quantity">
					Quantité : <?php  echo $item_to_quote['quantity']; ?>
				</td>

				<td class="product-shipping-method">
					<svg class="svg-icon icon-quotation" viewBox="0 0 16 16">
						<path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/>
						<path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
					</svg>
					<div class="shipping_type shipping_quotation">Par devis uniquement</div>
				</td>
			</tr>
	  <?php } ?>
	 </table> 
	</div>
<?php } ?>



	
	<div class="shipping_row_button">
		<?php if($are_all_product_shippable){ ?>
			<div class="button_wrapper">
				<a class="button button_return" href="/panier">Retourner au panier</a>
				<a class="button button_confirm active" href="/commander">Valider la livraison</a>
			</div>
		<?php 
			return ob_get_clean();								 
			}
			else{
				if(!is_user_logged_in()){
					?>
					<div style="font-weight:600; margin-bottom:20px; padding: 5px;">
					Certains de vos produits necessitent de demander un devis pour être livrés. 
					Nous vous invitons à vous connecter pour continuer. 
					</div>
					<div class="button_wrapper">
						<a class="button button_return" href="/panier">Retourner au panier</a>
						<a class="button button_confirm active" href="/mon-compte">Se connecter</a>
					</div><?php
					return ob_get_clean();
				}
				$current_user = wp_get_current_user();
				$user_email = $current_user->user_email;
				?>
				<div class='quotation_info'>
					<div style="padding: 0px 5px;font-weight:600; margin-bottom:20px;">
					Certains de vos produits necessitent de demander un devis pour être livrés. 
					Remplissez les champs ci dessous, nous traiterons votre devis et votre commande. 
					</div>
					<label for="text" style="padding: 0.5%; margin-top:20px;">Information supplementaires pour le devis :</label>
					<div class="input other_info">
						<textarea  type="text" id="quotation_more_info" class="text-input" placeholder="Indiquer ici les informations supplémentaire pour le devis, au plus au mieux" required minlength="0" maxlength="10000" size="10"></textarea>
					</div>
					<label for="tel"style="padding: 0.5%;">Téléphone :</label>
					<div class="input phone">
						<input type="tel" id="quotation_phone" class="text-input" placeholder="0123456789" required minlength="5" maxlength="20" size="10"  >
					</div>
				</div>
				<div class="button_wrapper">
					<a class="button button_return" href="/panier">Retourner au panier</a>
					<?php
					if($is_cart_exclusively_on_quote){
					?><a id="button_confirm_quotation_only" class="button disabled button_confirm">Demander un devis pour mes produits</a> <?php	
					}else{
					?><a id="button_confirm_quotation_mixed" class="button disabled button_confirm">Demander un devis et passer à la commande</a> <?php
					} ?>
				</div>
				<?php 
				};
		?>
	</div>
	
	<div id='popup_confirm_quote' class='popup hidden'>
		<div class='popup_confirm_quote_overlay'></div>
		<div class='popup_confirm_quote_wrapper'>
			<h2>Choix du mode de commande</h2>
			<div class='popup_text'>
				Un devis de livraison doit être crée pour les articles suivant : 
				<ul class="quote_list">
				<?php 
				foreach ($items_to_quote as $item_to_quote){
					?><li class="quote_list_item">
					<span class="item_name"><?php echo $item_to_quote['name'];?></span>
					<span class="item_quantity"><?php echo "- Quantité : ".$item_to_quote['quantity'];?></span>
					<span class="item_quantity"><?php echo " - Total : ".$item_to_quote['line_total'];?>€</span>
					</li>
					<?php
				}
				?>
				
				</ul>
			</div>
			<div class='popup_text popup_text_2'>
				Un confirmation par mail sera envoyé à l'adresse <?php echo $user_email; ?> . Il vous est possible de commander directement les produits qui ne necessitent pas de devis de livraison, et de demander un devis livraison pour les autres. OU il vous est possible de demander un devis de livraison pour l'ensemble de votre panier, pour pouvoir commander tous vous artcles en même temps.
			</div>
			<div class='popup_button_wrapper'>
				<a id="button_quote_all_product"class="button button_confirm button_confirm_popup active">Demander un devis livraison pour tous mes articles</a>
				-OU-
 				<a id="button_quote_only_not_sendable" class="button button_confirm button_confirm_popup active">Commander directement <?php echo sizeof($cart_items) - sizeof($items_to_quote); ?> articles et demander un devis pour le reste</a>

			</div>
		</div>
	</div>
	


	<div id='popup_quote_sended' class='popup hidden'>
		<div class='popup_confirm_quote_overlay'></div>
		<div class='popup_confirm_quote_wrapper'>
			<h3>Merci de votre confiance !</h3>
			Un devis à été envoyé à nos services, il sera traité dans les plus brefs delais.
			
			<?php //TODO ajouter un beau design
		?> 
			<div id="popup_quote_sended_button" class='popup_button_wrapper'>
				<a class="button button_confirm  active" href="../">Retourner à l'acceuil</a>
				<a class="button button_confirm  active" href='/mon-compte/mes-devis'>Voir mes devis</a>
			</div>
		</div>
	</div>


	
	<script>
		$(document).ready(function () {
			$('#quotation_phone').on('input', function() {
						//Check if phone format is valid
						//Then activate the button
						var phone_input = $(this).val();
						var phone_input = phone_input.toString().replace(/\s/g, '');
						var button_quotation = document.getElementById('button_confirm_quotation_only');
						if(!button_quotation) button_quotation = document.getElementById('button_confirm_quotation_mixed');
						var postcode = <?php  
							$user_meta = get_user_meta(get_current_user_id());
							if($user_meta['shipping_postcode'][0])echo $user_meta['shipping_postcode'][0];
							else echo 0;
							?>;
						if(phone_input.length >= 10 && postcode){//Phone is valid
							button_quotation.classList.add('active');
							button_quotation.classList.remove('disabled');
						}
							else{
							button_quotation.classList.remove('active');
							button_quotation.classList.add('disabled');
							}
						});
			$(' #button_quote_only_not_sendable').on('click',function(){
				//Click on button on popup "demander un devis pour seulement..."
				var quotation_more_info = $('#quotation_more_info').val();
				var phone_input = $('#quotation_phone').val();
				var phone_input = phone_input.toString().replace(/\s/g, '');
				var data = {
					action: 'shipping_confirm',
					_ajax_nonce: '<?php echo wp_create_nonce( "secure_nonce_name" ); ?>',
					phone: phone_input,
					more_info : quotation_more_info,
					items_to_quote : <?php echo json_encode($items_to_quote); ?>
				}
				$.post( "https:\/\/koncrete.fr\/wp-admin\/admin-ajax.php", data, function(response){console.log( 'Got this from the server: ' + response )})
				.done(function() {
					document.getElementById('popup_quote_sended').classList.remove('hidden');
					document.getElementById("popup_confirm_quote").classList.add('hidden');
					document.getElementById('popup_quote_sended_button').classList.add('hidden');
					window.scrollTo(0, 0);
					window.location.replace("/commander");					

				})
				.fail(function(xhr, status, error) {
					alert("Une erreur est survenue, veuillez reesayer. Si l'erreur persiste, merci de nous contacter via le chat-bot");
					console.log(status,error);
				}); 
			});
			
			$(' #button_quote_all_product').on('click',function(){
				//Click on button on popup "demander un devis pour tous
				var quotation_more_info = $('#quotation_more_info').val();
				var phone_input = $('#quotation_phone').val();
				var phone_input = phone_input.toString().replace(/\s/g, '');
				var data = {
					action: 'shipping_confirm',
					_ajax_nonce: '<?php echo wp_create_nonce( "secure_nonce_name" ); ?>',
					phone: phone_input,
					more_info : quotation_more_info,
					items_to_quote : <?php echo json_encode(array_merge($items_to_quote,$items_to_ship)); ?>
				}
					$.post( "https:\/\/koncrete.fr\/wp-admin\/admin-ajax.php", data, function(response){console.log( 'Got this from the server: ' + response )})
						.done(function() {
							document.getElementById('popup_quote_sended').classList.remove('hidden');
							document.getElementById("popup_confirm_quote").classList.add('hidden');
							window.scrollTo(0, 0);
						})
							.fail(function(xhr, status, error) {
						alert("Une erreur est survenue, veuillez re-essayer. Si l'erreur persiste, merci de nous contacter via le chat-bot");
						console.log(status,error);
						}); 
			});
			$(' #button_confirm_quotation_mixed').on('click',function(){
				var button_confirm_quotation = document.getElementById('button_confirm_quotation_mixed')
				if(button_confirm_quotation.classList.contains('active')){
					 var popup = document.getElementById("popup_confirm_quote");
					popup.classList.remove("hidden");
					window.scrollTo(0, 0);

				}
			});
			
			$(' #button_confirm_quotation_only').on('click',function(){
				var button_confirm_quotation = document.getElementById('button_confirm_quotation_only')
				if(!button_confirm_quotation.classList.contains('active')) return;
				var quotation_more_info = $('#quotation_more_info').val();
				var phone_input = $('#quotation_phone').val();
				var phone_input = phone_input.toString().replace(/\s/g, '');
				var data = {
					action: 'shipping_confirm',
					_ajax_nonce: '<?php echo wp_create_nonce( "secure_nonce_name" ); ?>',
					phone: phone_input,
					more_info : quotation_more_info,
					items_to_quote : <?php echo json_encode($items_to_quote); ?>
				}
					$.post( "https:\/\/koncrete.fr\/wp-admin\/admin-ajax.php", data, function(response){console.log( 'Got this from the server: ' + response )})
						.done(function() {
							document.getElementById('popup_quote_sended').classList.remove('hidden');
							window.scrollTo(0, 0);

						})
					  .fail(function(xhr, status, error) {
						alert("Une erreur est survenue, veuillez re-essayer. Si l'erreur persiste, merci de nous contacter via le chat-bot");
						console.log(status,error);
					});
			});

		});

	</script>
	





<?php
	return ob_get_clean();
};


// The function that handles the AJAX request
add_action( 'wp_ajax_shipping_confirm', 'function_shipping_confirm' );
function function_shipping_confirm() {
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
	$items_to_quote = $_POST['items_to_quote'] ;
  	echo create_quotation($items_to_quote,$user_data,$more_info,'shipping');
	
	//Flush cart
	/*foreach($items_to_quote as $item_to_quote){
		$product_id = $item_to_quote['product_id'];
   		$product_cart_id = WC()->cart->generate_cart_id( $product_id );
   		$cart_item_key = WC()->cart->find_product_in_cart( $product_cart_id );
   		if ( $cart_item_key ) WC()->cart->remove_cart_item( $cart_item_key );
	};*/
  	die();
}

add_shortcode('edit_address_panel','create_address_panel');

function create_address_panel(){
	ob_clean();
	ob_start();
	if(!is_user_logged_in()){
		return ob_get_clean();
	}
	
	// get the user meta
	$user_meta = get_user_meta(get_current_user_id());

	// get the form fields
	$countries = new WC_Countries();
	$shipping_fields = $countries->get_address_fields( '', 'shipping_' );
	?>

	<!-- shipping form -->
	<?php
	$load_address = 'shipping';
	$page_title   = __( 'Shipping Address', 'woocommerce' );
	?>
	<div class='address_wrapper'>
		<div class='address_wrapper_header'>
			<div class="panel_header"
;				 ><h3 style="color:#5a5555; font-size:18px">Mon adresse de livraison</h3>
			</div>
		
			<button id="edit_address_button" type="button">
				<span class="icon">
					<svg aria-hidden="true" focusable="false" height="1em" role="presentation" width="1em" viewBox="0 0 24 24" class="svg_4cc5dac2">
						<path d="M3 17.46v3.04c0 .28.22.5.5.5h3.04c.13 0 .26-.05.35-.15L17.81 9.94l-3.75-3.75L3.15 17.1c-.1.1-.15.22-.15.36zM20.71 7.04a.996.996 0 000-1.41l-2.34-2.34a.996.996 0 00-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill="currentColor"></path>
					</svg>
				</span>
				Modifier
			</button>
		</div>
		<address class='address_thumbnail'>
				<span class='address address_name'><?php echo $user_meta['shipping_first_name'][0]." ".$user_meta['shipping_last_name'][0]." ".$user_meta["shipping_company"][0]; ?></span>
				<span class='address address_address'><?php echo $user_meta['shipping_address_1'][0]." ".$user_meta['shipping_address_2'][0]; ?></span>
				<span class='address address_city'><?php echo $user_meta['shipping_postcode'][0]." ".$user_meta['shipping_city'][0]." ,".$user_meta["shipping_country"][0]; ?></span>
				<span class='address address_phone'><?php echo $user_meta['shipping_phone'][0]; ?></span>
		</address>
	</div>
	<?php //TODO : ajouter liste de mes chantiers?>
	<div id='address_popop_wrapper' class='hidden'>
		<div class='popup_overlay address_popup'></div>
		<div class='address_edit_popup address_popup'> 
			<form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" class="edit-account" method="post">

				<h3><?php echo apply_filters( 'woocommerce_my_account_edit_address_title', $page_title ); ?></h3>

				<?php do_action( "woocommerce_before_edit_address_form_{$load_address}" ); ?>
				<?php /*echo var_dump($shipping_fields);*/ ?>
				<?php foreach ( $shipping_fields as $key => $field ) : ?>
					<?php 	
							if($field['required']) woocommerce_form_field( $key, $field, $user_meta[$key][0] ); ?>
				<?php endforeach; ?>
				<?php /* do_action( "woocommerce_after_edit_address_form_{$load_address}" ); */?>

				<p>
					<input type="submit" class="button" name="save_address" value="<?php esc_attr_e( 'Sauvegarder', 'woocommerce' ); ?>" />
					<?php wp_nonce_field( 'woocommerce-edit_address' ); ?>
					<input type="hidden" name="action" value="edit_shipping_address" />
				</p>
			</form>
		</div>
	</div>
	<script>
	$(document).ready(function () {
			$('#edit_address_button').on('click', function() {
				document.getElementById('address_popop_wrapper').classList.remove('hidden');
			})
			$('.popup_overlay').on('click',function(){
				document.getElementById('address_popop_wrapper').classList.add('hidden');
			})
	});

	</script>
	<?php
	return ob_get_clean();
}


function edit_shipping_address() {
	$user_id = get_current_user_id();
	update_user_meta($user_id, 'shipping_first_name',$_POST['shipping_first_name']);
	update_user_meta($user_id, 'shipping_last_name',$_POST['shipping_last_name']);
	update_user_meta($user_id, 'shipping_country',$_POST['shipping_country']);
	update_user_meta($user_id, 'shipping_address_1',$_POST['shipping_address_1']);
	update_user_meta($user_id, 'shipping_postcode',$_POST['shipping_postcode']);
	update_user_meta($user_id, 'shipping_city',$_POST['shipping_city']);
	wp_redirect( 'https://koncrete.fr/livraison' );
	exit;

}
add_action( 'admin_post_nopriv_edit_shipping_address', 'edit_shipping_address' );
add_action( 'admin_post_edit_shipping_address', 'edit_shipping_address' );




