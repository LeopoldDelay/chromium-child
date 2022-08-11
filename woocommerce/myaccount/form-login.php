<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
do_action( 'woocommerce_before_customer_login_form' ); ?>

<h1>
	Bienvenue dans l'espace client Koncrete !
</h1>

<div class="u-columns col2-set" id="customer_login">
	<div class="u-column" style="min-width:350px!important;">
		<div class="col-1">

			<h2><?php esc_html_e( 'Déjà inscrit ?', 'woocommerce' ); ?></h2>

			<form class="woocommerce-form woocommerce-form-login login" method="post">

				<?php do_action( 'woocommerce_login_form_start' ); ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="username"><?php esc_html_e( 'Adresse mail', 'woocommerce' ); ?></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
				</p>
				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="password"><?php esc_html_e( 'Mot de passe', 'woocommerce' ); ?></label>
					<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" />
				</p>

				<?php do_action( 'woocommerce_login_form' ); ?>

				<p class="woocommerce-LostPassword lost_password">
					<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Mot de passe oublié ?', 'woocommerce' ); ?></a>
				</p>
				<p class="form-row">
					<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
						<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Se souvenir de moi', 'woocommerce' ); ?></span>
					</label>
				</p>
				<div id="button_connect">
					<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
					<button type="submit" class="woocommerce-button button button-account woocommerce-form-login__submit" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Se connecter', 'woocommerce' ); ?></button>
				</div>
			

				<?php do_action( 'woocommerce_login_form_end' ); ?>

			</form>
		</div>
	</div>

	<div class="u-column rows2-set"style="min-width:350px!important;">

		<div class="col-2 row-1">
			<h2><?php esc_html_e( 'Pas encore inscrit ?', 'woocommerce' ); ?></h2>
			<p>
				<?php esc_html_e( 'Il est temps d\'y remédier !', 'woocommerce' ); ?>
				<br/>
				<br/>
				<?php esc_html_e( 'Avoir un compte vous permet de suivre vos commandes et d\'avoir des conseils professionnels à n\'importe quel moment.', 'woocommerce' ); ?>
				<br/>
			</p>
			<form action="https://koncrete.fr/creation-compte/">
				<div id="button_connect">
    			<button type="submit" class="woocommerce-button button button-account" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Créer un compte', 'woocommerce' ); ?></button>
				</div>
			</form>
		</div>
		<?php /*
		<div class="col-2 row-2">
			<h2><?php esc_html_e( 'Se connecter en un seul clic', 'woocommerce' ); ?></h2>
			<div id="Google">
				<a href="/wp-login.php?gaautologin=true&redirect_to=http://koncrete.fr/mon-compte">
					<img src="https://koncrete.fr/wp-content/uploads/2022/01/Google__G__Logo-1.svg"/>
				</a>
			</div>
		</div>*/
		?>
		
	</div>

</div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
