<?php
/**
 * Lost password form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-lost-password.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.2
 */

defined( 'ABSPATH' ) || exit;
if ( is_user_logged_in() == "1" ) {?>
    <p><?php esc_html_e( 'Vous êtes déjà connecté, vous ne pouvez pas accéder à cette page.', 'woocommerce' ); ?></p>
      <?php;
   };
if (is_user_logged_in() == ""){


do_action( 'woocommerce_before_lost_password_form' );
?>

<div class="u-column col-1" id="lost-password">
<form method="post" class="woocommerce-ResetPassword lost_reset_password">

      <h2><?php esc_html_e( 'Mot de passe perdu ?', 'woocommerce' ); ?></h2>

	<p><?php echo esc_html_e( 'Veuillez entrer votre adresse mail, un nouveau mot de passe vous sera envoyé par mail.', 'woocommerce' ) ?></p><?php // @codingStandardsIgnoreLine ?>

	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<input class="woocommerce-Input woocommerce-Input--text input-text" type="text" name="user_login" id="user_login" autocomplete="username" placeholder="Adresse mail"/>
	</p>

	<div class="clear"></div>

	<?php do_action( 'woocommerce_lostpassword_form' ); ?>

	<p class="woocommerce-form-row form-row">
		<input type="hidden" name="wc_reset_password" value="true" />
		<button type="submit" class="woocommerce-Button button" value="<?php esc_attr_e( 'Reset password', 'woocommerce' ); ?>"><?php esc_html_e( 'Envoyer', 'woocommerce' ); ?></button>
	</p>

	<?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>
</form>
</div>
<?php
do_action( 'woocommerce_after_lost_password_form' );
}
