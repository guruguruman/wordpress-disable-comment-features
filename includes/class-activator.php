<?php

namespace DisableCommentFeatures;

/**
 * Activate disable comment features.
 */
class Activator {
	/**
	 * Run to execute all of the hooks with WordPress.
	 */
	public function run() {
		$this->remove_posttype_supports();
		$this->disable_admin();
		$this->disable_enduser();
	}

	/**
	 * Remove supports for all of post types.
	 */
	private function remove_posttype_supports() {
		add_action( 'init', function() {
				$post_types = get_post_types();
				foreach ( $post_types as $post_type ) {
					if ( post_type_supports( $post_type, 'comments' ) ) {
						remove_post_type_support( $post_type, 'comments' );
					}
				}
			}, 1, 9999 // Low prioritized to work with final settings of init.
		);
	}

	/**
	 * Disable comment related features from admin panel.
	 */
	private function disable_admin() {
		// Remove recent commeent metabox from dashboard.
		add_action( 'wp_dashboard_setup', function() {
				remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
			}
		);
		add_filter( 'pre_option_default_pingback_flag', '__return_zero' );

		// Remove comment links from the admin bar.
		add_action( 'wp_before_admin_bar_render', function() {
				global $wp_admin_bar;

				$wp_admin_bar->remove_menu( 'comments' );
			}
		);

		// Remove default built-in 'Recent Comments' widget
		add_action( 'widgets_init', function() {
				unregister_widget( 'WP_Widget_Recent_Comments' );
				add_filter( 'show_recent_comments_widget_style', '__return_false' );
			}
		);

		// Remove comment + discussion editing admin panels.
		add_action( 'admin_menu', function () {
				global $pagenow;

				if ( 'comment.php' == $pagenow || 'edit-comments.php' == $pagenow ) {
					wp_redirect( admin_url() );
				}
				remove_menu_page( 'edit-comments.php' );

				if ( 'options-discussion.php' == $pagenow ) {
					wp_redirect( admin_url() );
				}
				remove_submenu_page( 'options-general.php', 'options-discussion.php' );
			}, 9999 // do this as late as possible since 'admin_menu' action has lots of admin panel building process.
		);
	}

	/**
	 * Remove all of comment related features from html toward end user.
	 */
	private function disable_enduser() {
		// Remove comment RSS feeds link from head.
		remove_action( 'wp_head', 'feed_links_extra' );

		// Disable comment feeds links on post.
		add_filter( 'feed_links_show_comments_feed', '__return_false', 1, 10 );
		add_filter( 'post_comments_feed_link_html', '__return_empty_string' );
		add_filter( 'post_comments_feed_link', '__return_empty_string' );
		add_filter( 'feed_link', function ( $output ) {
				return ( false === strpos( $output, 'comments' ) ) ? $output : '';
			}
		);

		// Disable comment feeds contents.
		$dsiable_feed_comments = function() {
			wp_die();
		};
		add_action( 'do_feed_rss2_comments', $dsiable_feed_comments, 1 );
		add_action( 'do_feed_atom_comments', $dsiable_feed_comments, 1 );

		// Always comment open status to be false.
		add_filter( 'comments_open', '__return_false', 20, 2 );
		add_filter( 'pings_open', '__return_false', 20, 2 );

		// Always comment count for each pages to be 0 if requested.
		add_filter( 'comments_array', '__return_empty_array' );

		// Disable comment reply.
		add_action( 'admin_enqueue_scripts', function() {
			wp_deregister_script( 'comment-reply' );
		} );
		add_filter( 'comment_reply_link', '__return_false' );
		add_filter( 'comments_rewrite_rules', '__return_empty_array' );
	}

}
