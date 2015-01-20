<?php
/*
Plugin Name: Easy Digital Downloads - Hide Download
Plugin URI: http://sumobi.com/shop/edd-hide-download/
Description: Allows a download to be hidden as well as preventing direct access to the download
Version: 1.2.6
Author: Andrew Munro, Sumobi
Author URI: http://sumobi.com/
License: GPL-2.0+
License URI: http://www.opensource.org/licenses/gpl-license.php
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDD_Hide_Download' ) ) {

	final class EDD_Hide_Download {

		/**
		 * Holds the instance
		 *
		 * Ensures that only one instance of EDD Hide Download exists in memory at any one
		 * time and it also prevents needing to define globals all over the place.
		 *
		 * TL;DR This is a static property property that holds the singleton instance.
		 *
		 * @var object
		 * @static
		 * @since 1.2
		 */
		private static $instance;

		/**
		 * Keep the hidden downloads in options
		 *
		 * @since  1.2
		 * @var    array
		 */
		private $hidden_downloads;

		/**
		 * Main Instance
		 *
		 * Ensures that only one instance exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.2
		 *
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof EDD_Hide_Download ) ) {
				self::$instance = new EDD_Hide_Download;
				self::$instance->setup_globals();
				self::$instance->hooks();
			}

			return self::$instance;
		}

		/**
		 * Constructor Function
		 *
		 * @since 1.2
		 * @access private
		 * @see EDD_Hide_Download::init()
		 * @see EDD_Hide_Download::activation()
		 */
		private function __construct() {
			self::$instance = $this;

			add_action( 'init', array( $this, 'init' ) );
		}

		/**
		 * Reset the instance of the class
		 *
		 * @since 1.2
		 * @access public
		 * @static
		 */
		public static function reset() {
			self::$instance = null;
		}

		/**
		 * Globals
		 *
		 * @since 1.2
		 * @return void
		 */
		private function setup_globals() {
			$this->version 		= '1.2.6';
			$this->title 		= 'EDD Hide Download';

			// paths
			$this->file         = __FILE__;
			$this->basename     = apply_filters( 'edd_hd_plugin_basenname', plugin_basename( $this->file ) );
			$this->plugin_dir   = apply_filters( 'edd_hd_plugin_dir_path',  plugin_dir_path( $this->file ) );
			$this->plugin_url   = apply_filters( 'edd_hd_plugin_dir_url',   plugin_dir_url ( $this->file ) );
		}

		/**
		 * Function fired on init
		 *
		 * This function is called on WordPress 'init'. It's triggered from the
		 * constructor function.
		 *
		 * @since 1.2
		 * @access public
		 *
		 * @uses EDD_Hide_Download::load_textdomain()
		 *
		 * @return void
		 */
		public function init() {
			do_action( 'edd_hd_before_init' );

			$this->load_textdomain();

			do_action( 'edd_hd_after_init' );
		}

		/**
		 * Setup the default hooks and actions
		 *
		 * @since 1.2
		 *
		 * @return void
		 */
		private function hooks() {
			
			add_filter( 'plugin_row_meta', array( $this, 'plugin_meta' ), null, 2 );

			add_action( 'edd_meta_box_settings_fields', array( $this, 'add_metabox' ), 100 );
			add_action( 'edd_metabox_fields_save', array( $this, 'save_metabox' ) );
			add_action( 'pre_get_posts',  array( $this, 'pre_get_posts' ), 9999 );
			add_filter( 'edd_downloads_query', array( $this, 'shortcode_query' ) );

			// find all hidden products on metabox render
			add_action( 'edd_meta_box_fields', array( $this, 'query_hidden_downloads' ), 90 );

			// redirect if product is set to be hidden
			add_action( 'template_redirect', array( $this, 'redirect_hidden' ) );

			// load the hidden downloads
			$this->hidden_downloads = get_option( 'edd_hd_ids', array() );

			// insert actions
			do_action( 'edd_wl_setup_actions' );
		}


		/**
		 * Loads the plugin language files
		 *
		 * @access public
		 * @since 1.2
		 * @return void
		 */
		public function load_textdomain() {
			// Set filter for plugin's languages directory
			$lang_dir = dirname( plugin_basename( $this->file ) ) . '/languages/';
			$lang_dir = apply_filters( 'edd_hd_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale        = apply_filters( 'plugin_locale',  get_locale(), 'edd-hd' );
			$mofile        = sprintf( '%1$s-%2$s.mo', 'edd-hd', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/edd-hd/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				load_textdomain( 'edd-hd', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				load_textdomain( 'edd-hd', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'edd-hd', false, $lang_dir );
			}
		}

		/**
		 * Add Metabox
		 *
		 * @since 1.0
		*/
		function add_metabox( $post_id ) {
			$checked = (boolean) get_post_meta( $post_id, '_edd_hide_download', true );
			$is_redirect = (boolean) get_post_meta( $post_id, '_edd_hide_redirect_download', true );
		?>
			<p><strong><?php apply_filters( 'edd_hide_download_header', printf( __( 'Hide %s', 'edd-hd' ), edd_get_label_singular() ) ); ?></strong></p>
			<p>
				<label for="edd_hide_download">
					<input type="checkbox" name="_edd_hide_download" id="edd_hide_download" value="1" <?php checked( true, $checked ); ?> />
					<?php apply_filters( 'edd_hide_download_label', printf( __( 'Hide this %s', 'edd-hd' ), strtolower( edd_get_label_singular() ) ) ); ?>
				
				</label>
			</p>
			<p>
				<label for="edd_hide_redirect_download">
					<input type="checkbox" name="_edd_hide_redirect_download" id="edd_hide_redirect_download" value="1" <?php checked( true, $is_redirect ); ?> />
						<?php apply_filters( 'edd_hide_download_disable_access_label', printf( __( 'Disable direct access to this %s', 'edd-hd' ), strtolower( edd_get_label_singular() ) ) ); ?>
				</label>
			</p>

		<?php
		}

		/**
		 * Add to save function
		 *
		 * @since 1.0
		*/
		function save_metabox( $fields ) {
			$fields[] = '_edd_hide_download';
			$fields[] = '_edd_hide_redirect_download';

			return $fields;
		}

		/**
		 * Store the hidden products ids in the options table
		 *  @since 1.1
		 */
		function query_hidden_downloads() {
			$args = array(
				'post_type' => 'download',
				'meta_key' => '_edd_hide_download',
				'posts_per_page' => -1
			);

			$downloads = get_posts( $args );

			$hidden_downloads = array();

			if ( $downloads ) {
				foreach ( $downloads as $download ) {
					$hidden_downloads[] = $download->ID;
				}
			}
			
			update_option( 'edd_hd_ids', $hidden_downloads );
		}

		/**
		 * Get array hidden downloads
		 *
		 * @since 1.0
		*/
		function get_hidden_downloads() {			
			return $this->hidden_downloads;
		}

		/**
		 * Hook into shortcode query and modify
		 *
		 * @since 1.0
		*/
		function shortcode_query( $query ) {
			$excluded_ids = isset( $query['post__not_in'] ) ? $query['post__not_in'] : array();
			$query['post__not_in'] = array_merge( $excluded_ids, $this->get_hidden_downloads() );

			return $query;
		}

		/**
		 * Alter the main loop to hide download using pre_get_posts
		 * We're not using ! is_main_query because no matter what the query is on the page we want to hide them
		 * @since 1.0
		 */
		function pre_get_posts( $query ) {

			if ( ! isset( $query ) ) {
				return;
			}

			if ( $query->is_single || ( function_exists( 'is_bbpress' ) && is_bbpress() ) || is_admin() ) {
				return;
			}

			// if a download is hidden, prevent it from being hidden on the FES vendor dashboard page
			if ( is_page( EDD_FES()->helper->get_option( 'fes-vendor-dashboard-page', false ) ) ) {
				return;
			}

			// hide downloads from all queries except singular pages, which will 404 without the conditional
			// is_singular('download') doesn't work inside pre_get_posts
			
			if ( ! $query->is_single ) {
				$excluded_ids = isset( $query->query_vars['post__not_in'] ) ? $query->query_vars['post__not_in'] : array();
				// make sure we're merging with existing post__not_in so we do not override it
				$query->set( 'post__not_in', array_merge( $excluded_ids, $this->get_hidden_downloads() ) );
			}

		}

		/**
		 * Redirect if product needs to be hidden
		 *  @since 1.1
		 */
		function redirect_hidden() {
			global $post;

			if ( ! is_singular( 'download' ) )
				return;

			$is_redirect_active = (boolean) get_post_meta( $post->ID, '_edd_hide_redirect_download', true );

			if ( $is_redirect_active ) {
				$redirect_url = apply_filters( 'edd_hide_download_redirect', site_url() );

				if ( isset( $_REQUEST['HTTP_REFERER'] ) ) {
					$referer = esc_url( $_REQUEST['HTTP_REFERER '] );

					if ( strpos( $referer, $redirect_url ) !== false ) {
						$redirect_url = $referer;
					}
				}

				wp_redirect( $redirect_url, 301 ); exit;
			}
			
		}

		/**
		 * Modify plugin metalinks
		 *
		 * @access      public
		 * @since       1.2
		 * @param       array $links The current links array
		 * @param       string $file A specific plugin table entry
		 * @return      array $links The modified links array
		 */
		public function plugin_meta( $links, $file ) {
		    if ( $file == plugin_basename( __FILE__ ) ) {
		        $plugins_link = array(
		            '<a title="View more plugins for Easy Digital Downloads by Sumobi" href="https://easydigitaldownloads.com/blog/author/andrewmunro/?ref=166" target="_blank">' . __( 'Author\'s EDD plugins', 'edd-hd' ) . '</a>'
		        );

		        $links = array_merge( $links, $plugins_link );
		    }

		    return $links;
		}

	}

	/**
	 * Loads a single instance
	 *
	 * This follows the PHP singleton design pattern.
	 *
	 * Use this function like you would a global variable, except without needing
	 * to declare the global.
	 *
	 * @example <?php $edd_hide_download = edd_hide_download(); ?>
	 *
	 * @since 1.0
	 *
	 * @see EDD_Hide_Download::get_instance()
	 *
	 * @return object Returns an instance of the main class
	 */
	function edd_hide_download() {

	    if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {

	        if ( ! class_exists( 'EDD_Extension_Activation' ) ) {
	            require_once 'includes/class-activation.php';
	        }

	        $activation = new EDD_Extension_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
	        $activation = $activation->run();
	     
	    } else {
	        return EDD_Hide_Download::get_instance();
	    }
	}
	add_action( 'plugins_loaded', 'edd_hide_download', apply_filters( 'edd_hd_action_priority', 10 ) );

}