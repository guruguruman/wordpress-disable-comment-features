<?php

/**
 * Plugin Name:       Disable Comment
 * Plugin URI:        https://github.com/guruguruman/wordpress-disable-comment-features
 * Description:       Fully disable all built-in comment related features in WordPress with only activate plugin.
 * Version:           0.1.0
 * Author:            Tomoyuki Kato
 * Author URI:        https://github.com/guruguruman
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       disable-comments-features
 */
use DisableCommentFeatures\Activator;

// When this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The core plugin class.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-activator.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks, then kicking off the plugin from this point in the file does not affect the page life cycle.
 */
$activator = new Activator();
$activator->run();
