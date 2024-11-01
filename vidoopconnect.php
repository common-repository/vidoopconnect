<?php
/*
 Plugin Name: VidoopConnect
 Plugin URI: http://wordpress.org/extend/plugins/vidoopconnect/
 Description: Connect to Vidoop
 Author: Vidoop
 Author URI: http://vidoop.com/
 Version: trunk
 */

require_once dirname(__FILE__) . '/widget.php';

remove_action( 'login_form', 'openid_wp_login_form');
add_action( 'login_form', 'vidoop_connect_wp_login_form');
add_action( 'login_form', 'vidoop_connect_popup_script', 11);

add_action( 'admin_menu', 'vidoop_connect_admin_menu' );


/**
 * Get the base URL for VidoopConnect.
 *
 * @return string base URL
 * @uses apply_filters() Calls 'vidoop_connect_base_url' before returning URL
 */
function vidoop_connect_base_url() {
	$url = 'https://vidoopconnect.com/';
	$url = apply_filters('vidoop_connect_base_url', $url);
	return trailingslashit($url);
}


/**
 * Get the URL for the VidoopConnect login button.
 *
 * @return string URL for login button
 * @uses apply_filters() Calls 'vidoop_connect_login_button_url' before returning URL
 */
function vidoop_connect_login_button_url() {
	$url = plugins_url('vidoopconnect/vc-login.png');
	$url = apply_filters('vidoop_connect_login_button_url', $url);
	return $url;
}


/**
 * Print javascript for VidoopConnect popup script.
 */
function vidoop_connect_popup_script() {
	echo '<script type="text/javascript" src="' . vidoop_connect_base_url() . 'popup.js"></script>';
}


/**
 * Print form additions for WordPress login form.
 */
function vidoop_connect_wp_login_form() {
	echo '
	<input type="submit" class="vidoop_connect_button" name="openid_identifier" id="vc_start" value="' . vidoop_connect_base_url() . '" />';

	vidoop_connect_button_style();

	echo '
	<style type="text/css">
		#vc_start { display: block; margin: 0 auto 2em auto; }
	</style>
	<script type="text/javascript">
		jQuery(function() {
			jQuery("#vc_start").insertAfter("p.submit").css("margin", "4em auto 0 auto");
		});
	</script>
	';
}


/**
 * Print stylesheet for VidoopConnect login button.
 */
function vidoop_connect_button_style() {
	echo '
	<style type="text/css">
		.vidoop_connect_button {
			height: 40px; width: 205px;
			background: url("' . vidoop_connect_login_button_url() . '") center center no-repeat;
			text-indent: -9999px;
			border: 0;
			cursor: pointer;
		}
	</style>';
}


/**
 * Print VidoopConnect login form.  This form can be included in any template 
 * to add a VidoopConnect login button.
 */
function vidoop_connect_login_form( $redirect_to = null) {
	if (empty($redirect_to)) $redirect_to = admin_url();

	echo '
	<form action="' . site_url('wp-login.php', 'login_post') . '" method="POST">
		<input type="submit" class="vidoop_connect_button" name="wp-submit" id="vc_start" value="Sign in with VidoopConnect" />
		<input type="hidden" name="openid_identifier" value="' . vidoop_connect_base_url() . '" />
		<input type="hidden" name="redirect_to" value="' . attribute_escape($redirect_to) . '" />
	</form>';

	vidoop_connect_button_style();

	add_action( 'wp_footer', 'vidoop_connect_popup_script', 11);
}


/**
 * Add VidoopConnect options page to admin menu.
 */
function vidoop_connect_admin_menu() {
	// global options page
    $hookname = add_options_page(__('VidoopConnect options', 'vidoopconnect'), __('VidoopConnect', 'vidoopconnect'), 8, 'vidoopconnect', 'vidoop_connect_options_page' );
	add_contextual_help($hookname, vidoop_connect_help_text());
}


/**
 * Display the VidoopConnect options page.
 */
function vidoop_connect_options_page() {
	screen_icon('vidoopconnect');
?>
	<style type="text/css"> #icon-vidoopconnect { background-image: url("<?php echo plugins_url('vidoopconnect/icon.png'); ?>"); } </style>

<?php if (!function_exists('openid_trust_root')): ?>
	<div class="error"><p><strong>You must also install the <a href="http://wordpress.org/extend/plugins/openid/" target="_blank">WordPress OpenID</a> plugin in order to use VidoopConnect.</strong></p></div>
<?php endif; ?>

    <div class="wrap">
        <h2>VidoopConnect</h2>
        <form method="post" action="options.php">

		<p>In order to use VidoopConnect, you must first <a href="https://vidoopconnect.com/rp/" target="_blank">register your site</a>,
		using the following values for <strong>OpenID Realm</strong> and <strong>Return-to URL</strong>.</p>

        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row"><?php _e('OpenID Realm') ?></th>
                    <td><?php echo openid_trust_root(); ?></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Return-to URL') ?></th>
                    <td><?php echo openid_service_url('openid', 'consumer'); ?></td>
                </tr>
            </tbody>
        </table>

        </form>
    </div>
<?php
}


/**
 * Return help text to display on VidoopConnect admin page.
 *
 * @return string help text
 */
function vidoop_connect_help_text() {
	ob_start();
?>

	<p>Detailed instructions for enabling VidoopConnect on your site can be found 
	at <a href="https://vidoopconnect.com/developers/" target="_blank">https://vidoopconnect.com/developers/</a>.  
	However, the VidoopConnect WordPress plugin takes care of all of this for you.</p>

	<p>If you continue to have trouble, you can <a href="https://vidoopconnect.com/contact/" target="_blank">contact VidoopConnect</a>.</p>

<?php
	$text = ob_get_contents();
	ob_end_clean();

	return $text;
}

?>
