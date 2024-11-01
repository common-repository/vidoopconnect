<?php

add_action('plugins_loaded', 'widget_vidoop_connect_init');

/**
 * Initialize Vidoop Connect widget.  This includes all of the logic for managing and displaying the widget.
 */
function widget_vidoop_connect_init() {

	if (!function_exists('register_sidebar_widget')) {
		return;
	}

	/**
	 * Display user profile widget.
	 *
	 * @param array $args widget configuration.
	 */
	function widget_vidoop_connect($args) {
		extract($args);
		echo $before_widget;

		if (is_user_logged_in()) {
			$user = wp_get_current_user();
			echo '
				<p>Logged in as: <a href="' . admin_url() . '">' . $user->display_name . '</a></p>
				<p style="margin: 0; font-size: 90%;">(<a href="' . wp_logout_url() . '">Logout</a>)</p>';
		} else {
			vidoop_connect_login_form( $_SERVER['REQUEST_URI'] );
		}

		echo '<style type="text/css"> .widget_vidoop_connect { margin: 1em 0; }</style>';
		echo $after_widget;
	}


	/**
	 * Manage user profile widget.
	 */
	function widget_vidoop_connect_control() {
	}

	register_sidebar_widget('VidoopConnect', 'widget_vidoop_connect');
	register_widget_control('VidoopConnect', 'widget_vidoop_connect_control');
}

?>
