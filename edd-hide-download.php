<?php
/*
Plugin Name: Easy Digital Downloads - Hide Download
Plugin URI: http://sumobi.com/shop/edd-hide-download/
Description: Allows a download to be hidden as well as preventing direct access to the download
Version: 1.1.1
Author: Andrew Munro, Sumobi
Author URI: http://sumobi.com/
License: GPL-2.0+
License URI: http://www.opensource.org/licenses/gpl-license.php
*/


if ( !class_exists( 'EDD_Hide_Download' ) ) {

	class EDD_Hide_Download {

		/**
		 * Keep the hidden downloads in options
		 *
		 * @since  1.0.0
		 * @var    array
		 */
		private $hidden_downloads;

		function __construct() {
			add_action( 'init', array( $this, 'textdomain' ) );
			add_action( 'edd_meta_box_fields', array( $this, 'add_metabox' ), 100 );
			add_action( 'edd_metabox_fields_save', array( $this, 'save_metabox' ) );
			add_action( 'pre_get_posts',  array( $this, 'pre_get_posts' ) );
			add_filter( 'edd_downloads_query', array( $this, 'shortcode_query' ) );

			// find all hidden products on metabox render
			add_action( 'edd_meta_box_fields', array( $this, 'query_hidden_downloads' ), 90 );

			// redirect if product is set to be hidden
			add_action( 'template_redirect', array( $this, 'redirect_hidden' ) );

			// load the hidden downloads
			$this->hidden_downloads = get_option( 'edd_hd_ids', array() );

		}

		/**
		 * Internationalization
		 *
		 * @since 1.0
		 */
		function textdomain() {
			load_plugin_textdomain( 'edd-hd', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
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
			);

			$downloads = get_posts( $args );

			$hidden_downloads = array();

			foreach ( $downloads as $download ) {
				$hidden_downloads[] = $download->ID;
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
			$query['post__not_in'] = $this->get_hidden_downloads();

			return $query;
		}


		/**
		 * Alter the main loop to hide download using pre_get_posts
		 * We're not using ! is_main_query because no matter what the query is on the page we want to hide them
		 * @since 1.0
		 */
		function pre_get_posts( $query ) {

			// bail if in the admin or we're not working with the main WP query
			if ( is_admin() )
				return;

			// hide downloads from all queries except singular pages, which will 404 without the conditional
			// is_singular('download') doesn't work inside pre_get_posts
			if ( ! is_singular() )
				$query->set( 'post__not_in', $this->get_hidden_downloads() );

		}


		/**
		 * Redirect if product needs to be hidden
		 *  @since 1.1
		 */
		function redirect_hidden() {
			global $post;

			 if ( ! in_array( $post->ID, $this->hidden_downloads ) )
			 	return;	

			 $is_redirect_active = (boolean) get_post_meta( $post->ID, '_edd_hide_redirect_download', true );

			 if ( $is_redirect_active ) {

				$redirect_url = site_url();

				if ( isset( $_REQUEST['HTTP_REFERER'] ) ) {
					$referer = esc_url( $_REQUEST['HTTP_REFERER '] );

					if ( strpos( $referer, $redirect_url ) !== false )
						$redirect_url = $referer;
				}

				wp_redirect( $redirect_url, 301 ); exit;
				
			}
			
		}

	}

}
$EDD_Hide_Download = new EDD_Hide_Download();