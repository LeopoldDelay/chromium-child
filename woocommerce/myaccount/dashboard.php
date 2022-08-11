<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */
if ( ! defined( 'ABSPATH' ) ) {
exit; // Exit if accessed directly.
}
$allowed_html = array(
'a' => array('href' => array(),)
);
do_action( 'woocommerce_account_navigation' );
do_action( 'woocommerce_before_account_navigation' );

$user_id = get_current_user_id();

$orders_count =  count(wc_get_orders( [
                'type'        => 'shop_order',
                'limit'       => - 1,
                'customer_id' => $user_id
	] ));

$quote_count =count(get_quotes_by_user_id($user_id)); 

?>

<nav class="woocommerce-MyAccount-navigation">
		<ul>
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
			<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" name = "<?php checkCurrentPage($endpoint)?>">
				<li id="<?php echo $endpoint; ?>"><?php echo esc_html( $label ); ?></li>
			</a>
		<?php endforeach; ?>
	</ul>
</nav>
	
<?php do_action( 'woocommerce_after_account_navigation' );?>


<div class="woocommerce-MyAccount-content">
<h1><?php printf( wp_kses( __( 'Bonjour %1$s et bienvenue dans votre espace personnel !', 'woocommerce' ), $allowed_html ),esc_html( $current_user->user_firstname ));?></h1>
<br>
<div class="account_insight">
	<a href="orders" class="account_insight_rectangle">
	 	<h2>Mes commandes</h2>
		<div class="account_insight_value">
			<?php echo $orders_count  ?>
		</div>
	</a>
	
	<a href="mes-devis" class="account_insight_rectangle">
	 	<h2>Mes devis</h2>
		<div class="account_insight_value">
			<?php echo $quote_count  ?>
		</div>
	</a>
	
    <a href="edit-account" class="account_insight_rectangle">
	 	<h2>Mes informations personnelles</h2>
	</a>
    <a href="payment-methods" class="account_insight_rectangle">
	 	<h2>Mes moyens de paiments</h2>
	</a>	
	
	</div>
	
</div>
	 
<?php