<?php 

function get_construction_sites_by_user_id($user_id){
    global $wpdb;
	return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}construction_sites WHERE owner_id = {$user_id}", OBJECT );
}

function get_selected_construction_site_by_user_id($user_id){
    global $wpdb;
	return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}construction_sites WHERE owner_id = {$user_id} and selected = 1", OBJECT );
}

function get_selected_construction_site_by_id($user_id,$site_id){
    global $wpdb;
	return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}construction_sites WHERE ID = {$site_id} and owner_id = {$user_id}", OBJECT );
}


function delete_construction_site($user_id,$site_id){
    global $wpdb;
	return $wpdb->delete( $wpdb->prefix."construction_sites",array("ID"=> $site_id ,"owner_id"=>$user_id));
}

function change_selected_construction_site($user_id,$construction_site_id){
    try{
        global $wpdb;
        $wpdb->update( "{$wpdb->prefix}construction_sites", array("selected"=>0),array("owner_id"=>$user_id));
        $wpdb->update( "{$wpdb->prefix}construction_sites", array("selected"=>1),array("owner_id"=>$user_id,"ID"=>$construction_site_id));

        $changed_site =  get_selected_construction_site_by_id($user_id,$construction_site_id);
        if (isset($_COOKIE['wcprbl_location'])) {
            unset($_COOKIE['wcprbl_location']); 
        }
		if (isset($_COOKIE['wcprbl_first_time'])) {
            unset($_COOKIE['wcprbl_first_time']); 
        }
        setcookie(
            "wcprbl_location",
            $changed_site[0]->zipcode,
            time()+60*60*24*3
        );
		setcookie(
            "wcprbl_first_time",
            0,
            time()+60*60*24*3
        );
        //TODO : change the user shipping meta else the shipping code won't work !!!!!
        return array("status"=>200,"message"=>"Construction site has been updated to ".$changed_site[0]->zipcode);
		
    }catch(Exception $e){
        echo 'Exception received : ',  $e->getMessage(), "\n";
        return $e;
	}
}


function add_construction_site($owner_id,$construction_site_address,$selected = 0){
    try{
        global $wpdb;
		if(!$construction_site_address["country"]) $construction_site_address["country"] = 'France';
		if(!$construction_site_address["address_2"]) $construction_site_address["address_2"] = '';

		$data = array(
            "owner_id"=>$owner_id,
            "zipcode"=>$construction_site_address["zipcode"],
            "city"=>$construction_site_address["city"],
            "address_1"=>$construction_site_address["address_1"],
            "address_2"=>$construction_site_address["address_2"],
            "country"=>$construction_site_address["country"],
            "contact_phone"=>$construction_site_address["contact_phone"],
            "contact_first_name"=>$construction_site_address["contact_first_name"],
            "contact_last_name"=>$construction_site_address["contact_last_name"],
            "site_name"=>$construction_site_address["site_name"],
            "selected"=>$selected
        );
        $res = $wpdb->insert("{$wpdb->prefix}construction_sites",$data);
		echo var_dump($wpdb->insert_id);
        return array("status"=>200,"message"=>"Construction site has been added to {$wpdb->prefix}construction_sites : ".$res , "insert_id"=>$wpdb->insert_id);
    }catch(Exception $e){
		echo 'Exception received : ',  $e->getMessage(), "\n";
		return $e;
	}
}

function edit_construction_site($construction_site_id,$construction_site_address){
    try{
        global $wpdb;
        $wpdb->update(
            "{$wpdb->prefix}construction_sites",array(
            "zipcode"=>$construction_site_address["zipcode"],
            "city"=>$construction_site_address["city"],
            "address_1"=>$construction_site_address["address_1"],
            "address_2"=>$construction_site_address["address_2"],
            "country"=>$construction_site_address["country"],
            "contact_phone"=>$construction_site_address["contact_phone"],
            "contact_first_name"=>$construction_site_address["contact_first_name"],
            "contact_last_name"=>$construction_site_address["contact_last_name"],
            "site_name"=>$construction_site_address["site_name"]
        ),
            array("ID"=>$construction_site_id)
        );
        return array("status"=>200,"message"=>"Construction site has been added to db");
    }catch(Exception $e){
		echo 'Exception received : ',  $e->getMessage(), "\n";
		return $e;
	}
}


add_action( 'wp_ajax_create_construction_site', 'ajax_create_construction_site' );

function ajax_create_construction_site(){
	$user_id = get_current_user_id();
	check_ajax_referer( 'secure_nonce_name', 'security' );
	$construction_site_address = array(
        "zipcode"=>$_POST["zipcode"],
        "city"=>$_POST["city"],
        "address_1"=>$_POST["address_1"],
        "address_2"=>$_POST["address_2"],
        "country"=>$_POST["country"],
        "contact_phone"=>$_POST["contact_phone"],
        "contact_first_name"=>$_POST["contact_first_name"],
        "contact_last_name"=>$_POST["contact_last_name"],
        "site_name"=>$_POST["site_name"]

    );
	echo var_dump($construction_site_address);
    if($_POST["selected"]){
		echo "selected";
        $res = add_construction_site($user_id,$construction_site_address,1);
		echo var_dump($res);
		echo change_selected_construction_site($user_id,$res["insert_id"]);
    }
    else{
		echo "not selected";
        echo var_dump(add_construction_site($user_id,$construction_site_address));
    }
}

add_action( 'wp_ajax_edit_construction_site', 'ajax_edit_construction_site' );

function ajax_edit_construction_site(){
	check_ajax_referer( 'secure_nonce_name', 'security' );
	$construction_site_address = array(
        "zipcode"=>$_POST["zipcode"],
        "city"=>$_POST["city"],
        "address_1"=>$_POST["address_1"],
        "address_2"=>$_POST["address_2"],
        "country"=>$_POST["country"],
        "contact_phone"=>$_POST["contact_phone"],
        "contact_first_name"=>$_POST["contact_first_name"],
        "contact_last_name"=>$_POST["contact_last_name"],
        "site_name"=>$_POST["site_name"]
    );
    if($_POST["construction_site_id"]){
        echo edit_construction_site($_POST["construction_site_id"],$construction_site_address);
    }
    else{
        echo "Erreur : Pas de chantier selectionné.";
    }
}

add_action( 'wp_ajax_change_selected_construction_site', 'ajax_change_selected_construction_site' );

function ajax_change_selected_construction_site(){
	if(!check_ajax_referer( 'secure_nonce_name', 'security' )){
		return "invalid nonce";
	}
    $user_id = get_current_user_id();
    if($_POST["construction_site_id"]){
       echo var_dump(change_selected_construction_site($user_id,$_POST["construction_site_id"]));
    }
    else{
        echo "Erreur : Pas de chantier selectionné.";
    }
}

add_action( 'wp_ajax_delete_construction_site', 'ajax_delete_construction_site' );

function ajax_delete_construction_site(){
	if(!check_ajax_referer( 'secure_nonce_name', 'security' )){
		return "invalid nonce";
	}
    $user_id = get_current_user_id();
    if($_POST["construction_site_id"]){
        echo var_dump(delete_construction_site($user_id,$_POST["construction_site_id"]));
    }
    else{
        echo "Erreur : Pas de chantier selectionné.";
    }
}

add_shortcode('construction_sites_selector','display_construction_sites_selector');

function display_construction_sites_selector(){
	ob_start();

    if(!is_user_logged_in()){
        return ob_get_clean();
    }
	$user_id = get_current_user_id();
    $construction_sites = get_construction_sites_by_user_id($user_id);
    $selected_construction_site = get_selected_construction_site_by_user_id($user_id);
 

    if(!$selected_construction_site[0]){
        ?>
        <div class="construction_sites construction_sites_selector_button" id="construction_sites_create_button_header">
            <a class="construction_sites_header" href='https://koncrete.fr/mon-compte/mes-chantiers?creer'>Créer mon chantier</a>
        </div>
        <?php
        return ob_get_clean();
    }
    ?>
    <div class="construction_sites construction_sites_selector_button" id="construction_sites_selector_button_header">
       <a class="construction_sites_header" href="#"> Mon chantier : <?php echo $selected_construction_site[0]->site_name; ?> </a>
        <div class="construction_sites_popup">
            <div class="construction_sites_popup_header">
                <span class="construction_sites_popup_title">Mes chantiers (<?php echo count($construction_sites); ?>)</span>
                <a class="construction_sites_popup_add" href="https://koncrete.fr/mon-compte/mes-chantiers?creer">+</a>
            </div>
            <ul class="construction_sites_list">
                <?php
                foreach($construction_sites as $construction_site){
                    ?> <li class="construction_sites_item <?php if($construction_site->selected==1) echo "item_selected"; ?>" id="construction_site_<?php echo $construction_site->ID;?>" site_id="<?php echo $construction_site->ID;?>" zipcode="<?php echo $construction_site->zipcode; ?>" <?php if( $construction_site->selected) echo "selected"; ?> >                            
							<div class="site_info">
                              <div class="site_name"><?php echo $construction_site->site_name; ?> </div>
                              <div class="site_address_wrapper"><?php echo $construction_site->zipcode." - ". $construction_site->city; ?></div>
                            </div>
							<?php if($construction_site->selected==1){ //TO DELETE ????? ?>
                           <?php /* <div class="site_select_button">
                                <a id="button_select_site_<?php echo $construction_site->ID; ?>" class="button construction_site_select_button">Sélectionné</a>
                            </div>*/ ?>
						<?php } ?>
                        </li>
                <?php
                }  
                ?>
				
            </ul>
        </div>
    </div>
    <script>
	$(document).ready(function () {
		$('.construction_sites_item').on('click', function() {
            if($(this).attr("selected")){
                return;
            }
            var zipcode = $(this).attr("zipcode");
            var data = {
				action: 'change_selected_construction_site',
				_ajax_nonce: '<?php echo wp_create_nonce( "secure_nonce_name" ); ?>',
				construction_site_id: $(this).attr("site_id")
			};
			var element = $(this);
            $.post( "https:\/\/koncrete.fr\/wp-admin\/admin-ajax.php", data, function(response){console.log( 'Got this from the server: ' + response )})
			.done(function() {
                var url = (window.location.href.indexOf('?') > -1) ? window.location.href + '&' : window.location.href + '?';
				url += 'wcprbl_location=' + zipcode;
		        window.location.href = url;				
			})
			.fail(function(xhr, status, error) {
						alert("Une erreur est survenue, veuillez re-essayer. Si l'erreur persiste, merci de nous contacter via le chat-bot");
						console.log(status,error);
			});
        })
    })
    </script>
    <?php
    return ob_get_clean();
}


add_action( 'woocommerce_account_mes-chantiers_endpoint', 'display_account_construction_sites');


function display_account_construction_sites(){

    $user_id = get_current_user_id();
    $construction_sites = get_construction_sites_by_user_id($user_id);
    $selected_construction_site = get_selected_construction_site_by_user_id($user_id);
	?>
    <div class="account_header construction_sites_header">
    <h1 style="padding-bottom:0px;">Mes chantiers</h1>
    <a class="button_add_construction_sites" id='button_toggle_creation_site'>+</a>
    </div>
	  <ul id='construction_sites_list' class="construction_sites_list">
    <?php
    if(!$selected_construction_site[0]){
        ?> 
        <div class="woocommerce-message construction_site_message">
            Vous ne nous avez pas encore dit où vous livrer :) . Pour vous fournir une selection pertinente de produits, merci de bien vouloir créer votre premier chantier.
        </div>
        <?php
    }
	else{
    foreach($construction_sites as $construction_site){
        ?> <li class="construction_sites_item <?php if($construction_site->selected==1) echo "item_selected"; ?>" id="construction_site_<?php echo $construction_site->ID;?>" site_id="<?php echo $construction_site->ID;?>" zipcode="<?php echo $construction_site->zipcode; ?>" <?php if( $construction_site->selected) echo "selected"; ?> >                            
                <div class="site_info">
                  <span class="site_name"><?php echo $construction_site->site_name; ?> </span>
                  <div class="site_details">
                     <span class="site_address_wrapper"><?php echo $construction_site->address_1." ". $construction_site->address_2.", ". $construction_site->zipcode." ". $construction_site->city; ?></span>
                     <span class="site_contact_details"><?php echo $construction_site->contact_first_name." ". $construction_site->contact_last_name." - ". $construction_site->contact_phone; ?></span>
                  </div>
                </div>
				<a class="site_action_button" id="button_more_info" >
                        <svg class="more_action_icon" site_id="<?php echo $construction_site->ID; ?>" x="0px" y="0px" viewBox="0 0 490 490" style="enable-background:new 0 0 490 490;" xml:space="preserve">
                            <g>
                                <g>
                                    <path d="M245,185.5c-32.8,0-59.5,26.7-59.5,59.5s26.7,59.5,59.5,59.5s59.5-26.7,59.5-59.5S277.8,185.5,245,185.5z M280,245
                                        c0,19.3-15.7,35-35,35s-35-15.7-35-35s15.7-35,35-35S280,225.7,280,245z"/>
                                    <path d="M185.5,430.5c0,32.8,26.7,59.5,59.5,59.5s59.5-26.7,59.5-59.5S277.8,371,245,371S185.5,397.7,185.5,430.5z M280,430.5
                                        c0,19.3-15.7,35-35,35s-35-15.7-35-35s15.7-35,35-35S280,411.2,280,430.5z"/>
                                    <path d="M185.5,59.5c0,32.8,26.7,59.5,59.5,59.5s59.5-26.7,59.5-59.5S277.8,0,245,0S185.5,26.7,185.5,59.5z M280,59.5
                                        c0,19.3-15.7,35-35,35s-35-15.7-35-35s15.7-35,35-35S280,40.2,280,59.5z"/>
                                </g>
                            </g>
                        </svg>

                    </a>
					<div class="popup_construction_site_action" id="popup_site_<?php echo $construction_site->ID; ?>" site_id=<?php echo $construction_site->ID; ?>>
						<?php if(!$construction_site->selected){ ?><div class="popup_action_option" id="action_edit_site_<?php echo $construction_site->ID; ?>" onclick="change_selected_construction_site(<?php echo $construction_site->ID;?>,<?php echo $construction_site->zipcode;?>)">Choisir</div> <?php } ?>
                        <?php if(!$construction_site->selected){ ?><div class="popup_action_option action_delete" id="action_delete_site_<?php echo $construction_site->ID; ?>" onclick="delete_site(<?php echo $construction_site->ID;?>)">Supprimer</div> <?php } ?>
                    </div>
            </li>
    <?php }
	}
    ?>
    </ul>
	<div id='create_site_form' class="wrapper_form_create_new_construction_site hidden">
		<form class="create_new_construction_site" id="create_construction_site_form">
			<h3>Créer un chantier</h3>
			<div class='text-notice'>Indiquez nous les informations de votre chantier</div>
			<div class='construction_site_form_address'>
				<div class='form-row'>
				<div class='form-wrapper  form-wrapper-full'>
					<label for="site_name">Nom du chantier<span class="required">*</span></label>
					<input class='form-input' type="text" id="site_name" name="site_name" required="required" placeholder="Mon chantier" maxlength="50">
				</div>
				</div>
				<div class='form-row'>
					<div class='form-wrapper form-wrapper-half form-wrapper-half-1'>
					<label for="city">Ville<span class="required">*</span></label>
					<input class='form-input' type="text" id="city" name="city" required="required" placeholder="St-Pol-sur-Ternoise" maxlength="100">
					</div>
					<div class='form-wrapper form-wrapper-half form-wrapper-half-2'>
					<label for="zipcode">Code postal<span class="required">*</span></label>
					<input class='form-input' type="text" id="zipcode" name="zipcode" required="required" placeholder="62130" patern="{0-9}[5]">
					</div>
				</div>
				<div class='form-row'>
					<div class='form-wrapper form-wrapper-full'>
					<label for="address_1">Adresse du chantier<span class="required">*</span></label>
					<input class='form-input' type="text" id="address_1" name="address_1" required="required" placeholder="15 avenue des Champs-Elysées" maxlength="200">
					</div>
				</div>
				<div class='form-row'>
					<div class='form-wrapper form-wrapper-full'>
					<label for="address_2">Complément d'adresse</label>
					<input class='form-input' type="text" id="address_2" name="address_2" maxlength="200">
					</div>
				</div>
			</div>
			<div class='text-notice'>Quelqu'un à contacter sur votre chantier ?</div>
			<div class='construction_site_form_contact'>
				<div class='form-row'>
					<div class='form-wrapper form-wrapper-half form-wrapper-half-1'>
						<label for="contact_first_name">Prénom du contact</label>
						<input class='form-input' type="text" id="contact_first_name" name="contact_first_name" placeholder="Julien" maxlength="50">
					</div>
					<div class='form-wrapper form-wrapper-half form-wrapper-half-2'>
						<label for="contact_last_name">Nom du contact</label>
						<input class='form-input' type="text" id="contact_last_name" name="contact_last_name"  placeholder="Dupont" maxlength="50">
					</div>
				</div>
				<div class='form-row'>
					<div class='form-wrapper  form-wrapper-full'>
						<label for="contact_first_name">Téléphone du contact</label>
						<input type='tel' class='form-input' type="text" id="contact_phone" name="contact_phone" placeholder="06 01 23 45 67" patern="[0-9]{10}">
					</div>
				</div>
			</div>
			<div class='form-row-button'>
				<input class='button-form-submit' type="submit" id="submit" name="submit" value="Créer">
			</div>
    </form>
	</div>
	
	<script>
	$(document).ready(function () {
		$(document).click(function() {
			var container_popup = $(".popup_construction_site_action");
			if (!container_popup.is(event.target) && !container_popup.has(event.target).length) {
				container_popup.removeClass('visible');
			}
			if($(".more_action_icon").is(event.target)){
				var site_id = event.target.getAttribute('site_id');
				$('#popup_site_'+site_id).addClass('visible');
			}
		});
		$("#create_construction_site_form").submit(function(e){
            var data = {
                    action: 'create_construction_site',
                    _ajax_nonce: '<?php echo wp_create_nonce( "secure_nonce_name" ); ?>',
                    site_name: $("#site_name").val(),
                    city: $("#city").val(),
                    zipcode: $("#zipcode").val(),
                    address_1: $("#address_1").val(), 
                    address_2: $("#address_2").val(), 
                    contact_first_name: $("#contact_first_name").val(),
                    contact_last_name: $("#contact_last_name").val(),
                    contact_phone: $("#contact_phone").val(),
                    selected:1
            }
            var zipcode = $("#zipcode").val();
            $.ajax({ 
                data: data,
                type: 'post',
                url: "https:\/\/koncrete.fr\/wp-admin\/admin-ajax.php",
                success: function(data) {
                    var url = (window.location.href.indexOf('?') > -1) ? window.location.href + '&' : window.location.href + '?';
                    url += 'wcprbl_location=' + zipcode;
                    window.location.href = url;	
                }
            });
        });
		$('#button_toggle_creation_site').on('click',function(e){
			if(e.target.classList.contains('button_close')){
				//The create construction site panel is displayed
				$('#create_site_form').addClass('hidden');
				$('#construction_sites_list').removeClass('hidden');
				e.target.classList.remove('button_close');
				
			}
			else{
				e.target.classList.add('button_close');
				$('#create_site_form').removeClass('hidden');
				$('#construction_sites_list').addClass('hidden');
			}
		})
	});
		
	function delete_site(site_id){
		 var data = {
				action: 'delete_construction_site',
				_ajax_nonce: '<?php echo wp_create_nonce( "secure_nonce_name" ); ?>',
				construction_site_id: site_id
			};
			console.log(data);
            $.post( "https:\/\/koncrete.fr\/wp-admin\/admin-ajax.php", data, function(response){console.log( 'Got this from the server: ' + response )})
			.done(function() {
				location.reload();
             
			})
			.fail(function(xhr, status, error) {
						alert("Une erreur est survenue, veuillez re-essayer. Si l'erreur persiste, merci de nous contacter via le chat-bot");
						console.log(status,error);
			});
	}
		
	function change_selected_construction_site(site_id,site_zipcode){
		var data = {
				action: 'change_selected_construction_site',
				_ajax_nonce: '<?php echo wp_create_nonce( "secure_nonce_name" ); ?>',
				construction_site_id: site_id
			};
			console.log(data);
            $.post( "https:\/\/koncrete.fr\/wp-admin\/admin-ajax.php", data, function(response){console.log( 'Got this from the server: ' + response )})
			.done(function() {
				/*location.reload();	*/
                var url = (window.location.href.indexOf('?') > -1) ? window.location.href + '&' : window.location.href + '?';
				url += 'wcprbl_location=' + zipcode;
		        window.location.href = url;		
			})
			.fail(function(xhr, status, error) {
						alert("Une erreur est survenue, veuillez re-essayer. Si l'erreur persiste, merci de nous contacter via le chat-bot");
						console.log(status,error);
			});
	}
	
	</script>
    <?php
} 

add_shortcode('construction_site_popup','display_construction_site_popup');

function display_construction_site_popup(){
    ob_start();
    if (isset($_COOKIE['wcprbl_first_time_edited'])) {
        // Do not display the popup
       return ob_get_clean();
    }
    ?> 
    
    <div id="popup_cs_overlay" class="popup_construction_site_overlay"></div>
    <div id="popup_cs_wrapper" class="popup popup_construction_site_wrapper">
        <div id="popup_close" class="popup_close_wrapper">
            Fermer
        </div>
        <div class="popup_section section_1">
            <img src="https://koncrete.fr/wp-content/uploads/2022/01/cropped-Flavicon-2-1-1.png" width="64" height="64" class="popup_header_logo">
            <div class="popup_header_welcome">Bienvenue chez <b>Koncrete</b> !
            <div class="popup_header_subtext">La 1ère marketplace de matériaux de construction en France</div>
            </div>
        </div>
        <div class="popup_section section_2">
            <div class="popup_section_header">Ou vous faire livrer ?</div>
            <div class="popup_section_body">
                <div class="popup_column col_1">
                    <form id="popup_zipcode_form" class="zipcode_input_wrapper">
                        <label class="input_zipcode_label">Mon code postal</label>
                        <input id="zipcode_input" maxlength="5" placeholder="Ex : 91310, 59175..." class="zipcode_input popup_input" required="required" type="text" pattern="^(0?[0-9]|2[ABab1-9]|[13-8][0-9]|9[0-5]|97[1-6])[0-9]{0,3}$" data-message-required="Champs requis" data-message-pattern="Code postal invalide">
                    </form>
                    <button id="zipcode_submit_button" class="button popup_button construction_site_button">Valider</button>
                </div> 
                <div class="popup_column col_2">
                    <div class="section_splitter">
                        OU
                    </div>
                </div>
                <div class="popup_column col_3">
                    <div class="section_header">
                        <a href="https://koncrete.fr/mon-compte/mes-chantier/creer">Connectez vous</a>
                    </div>
                </div>
            </div>
        </div> 
        <div class="popup_section section_3">
            <div class="section_header">Pourquoi indiquer mon chantier ?</div>
            <div class="section_body">
                <img src="https://koncrete.fr/wp-content/uploads/2022/08/contour_france-2.png" height="100" width="100">
                <div class="popup_section_text">
                    <ul class="popup_bullet_point_list">
                        <li class="popup_bullet_point">Les matériaux les plus proches de chez moi</li>
                        <li class="popup_bullet_point">Des stocks mis à jour en direct</li>
                        <li class="popup_bullet_point">Les prix les plus bas autous de chez moi</li>
                        <li class="popup_bullet_point">Des matériaux livrés sur mon chantier</li>
                    </ul>
                </div>
            </div>
        </div> 
    </div>
    <script> 
    $(document).ready(function () {
        $('#popup_cs_overlay').on('click',function(){
            $('#popup_cs_overlay').addClass('hidden');
            $('#popup_cs_wrapper').addClass('hidden');
            wcprblSetCookie('wcprbl_first_time', 1, 30);
        });
        $('#popup_close').on('click',function(){
            $('#popup_cs_overlay').addClass('hidden');
            $('#popup_cs_wrapper').addClass('hidden');
            wcprblSetCookie('wcprbl_first_time', 1, 30);
        });
        $('#popup_zipcode_form').on('submit',function(e){
            e.preventDefault();
            var zipcode = $("#zipcode_input").val();
            wcprblSetCookie('wcprbl_first_time', 1, 30);
            var url = (window.location.href.indexOf('?') > -1) ? window.location.href + '&' : window.location.href + '?';
            url += 'wcprbl_location=' + zipcode;
            window.location.href = url;
        })
    });
    </script>

    <?php
    return ob_get_clean();
}