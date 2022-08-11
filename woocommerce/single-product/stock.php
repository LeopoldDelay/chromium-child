<?php
 /**
 * Single Product stock
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/stock.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $product; 
$numleft  = $product->get_stock_quantity(); 
if($numleft==0) {
    // out of stock
    ?>
    <p class='out-of-stock'>Cet article n'est plus disponible.</p>
    <?php
}
else {?>
    <p class='in-stock'><svg aria-hidden="true" focusable="false" height="1em" role="presentation" width="1em" viewBox="0 0 24 24" class="svg_091e878c"><g id="iconsCheckCircleOutlineBaseline"><path d="M16.59 7.58L10 14.17l-3.59-3.58L5 12l5 5 8-8zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"></path></g></svg>En stock</p>
    <?php
}
?>
