<?php

add_shortcode('product_display_delivery_method','display_product_delivery_method');

function display_product_delivery_method(){
	ob_start();
	
    global $product;
	$product_id = $product->get_id();
    $product_meta = get_post_meta($product_id);
	$is_shipping_configured = $product_meta["_per_product_shipping"][0];

	$query = "SELECT rule_postcode,rule_cost,rule_item_cost FROM mal788woocommerce_per_product_shipping_rules WHERE product_id = $product_id" ;
    if($is_shipping_configured){
        global $wpdb;
        $result = $wpdb->get_results ($query);
        $product_shipping_rule_postcode = $result[0]->rule_postcode;
        $product_shipping_rule_cost = $result[0]->rule_cost;
        $product_shipping_rule_item_cost = $result[0]->rule_item_cost;
        
		if($product_shipping_rule_item_cost < 0.001 and $$product_shipping_rule_cost<0.001 and $product_shipping_rule_postcode =='*'){
			//Free shipping
			 ?> 
				<div class="product_shipping">
				<span class='shipping_method_display'>
					<svg class="svg-icon icon-shipping" viewBox="0 0 16 16">
        				<path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5v-7zm1.294 7.456A1.999 1.999 0 0 1 4.732 11h5.536a2.01 2.01 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456zM12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12v4zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
         			</svg>
					<div class="shipping_type">Livré sur votre chantier</div></span> 
				<span class="shipping_cost">Gratuit</span>
				</div>
		<?php
		}
		else{
			 ?> <span class='shipping_method_display'><svg class="svg-icon icon-shipping" viewBox="0 0 16 16">
        <path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5v-7zm1.294 7.456A1.999 1.999 0 0 1 4.732 11h5.536a2.01 2.01 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456zM12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12v4zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
         </svg><div class="shipping_type">Livré sur votre chantier</div></span> <?php
		}
    }
    else{
        ?> <span class='shipping_method_display'><svg class="svg-icon icon-shipping" viewBox="0 0 16 16">
        <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/>
        <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
    </svg><div class="shipping_type">Disponible sur devis</div></span>
        <?php
    }
    return ob_get_clean();
}