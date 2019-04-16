<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://welaunch.io/plugins/woocommerce-packing-slips/
 * @since      1.0.0
 *
 * @package    WooCommerce_Packing_Slips
 * @subpackage WooCommerce_Packing_Slips/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WooCommerce_Packing_Slips
 * @subpackage WooCommerce_Packing_Slips/public
 * @author     Daniel Barenkamp <contact@db-dzine.de>
 */
class WooCommerce_Packing_Slips_Public extends WooCommerce_Packing_Slips {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	protected $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of this plugin.
	 */
	protected $version;

	/**
	 * options of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $options
	 */
	protected $options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $generator) 
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->generator = $generator;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() 
	{

		global $woocommerce_packing_slips_options;

		$this->options = $woocommerce_packing_slips_options;

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-packing-slips-public.css', array(), $this->version, 'all' );
		
	}
	
	/**
	 * Inits the print products
	 *
	 * @since    1.0.0
	 */
    public function init()
    {

		global $woocommerce_packing_slips_options;

		$this->options = $woocommerce_packing_slips_options;

		if (!$this->get_option('enable')) {
			return false;
		}

		$this->upload_dir = $this->get_uploads_dir( 'packing-slips' );
		if ( ! file_exists( $this->upload_dir ) ) {
			mkdir( $this->upload_dir, 0755, true );
		}

		$user_id = get_current_user_id();
		if(!$user_id) {
			return false;
		}

		if(isset($_POST['create_packing_slip']) && !empty($_POST['create_packing_slip'])) {
			$order = wc_get_order(intval($_POST['create_packing_slip']));
			$customer_id = $order->get_customer_id();

			if(!$this->is_user_role('administrator', $user_id) && !$this->is_user_role('shop_manager', $user_id) && ($customer_id !== $user_id) ) {
				return false;
			}

			$this->generator->setup_data($_POST['create_packing_slip']);
			$this->generator->create_pdf($this->upload_dir);
		}

		if(isset($_GET['create_packing_slip']) && !empty($_GET['create_packing_slip'])) {

			$order = wc_get_order(intval($_GET['create_packing_slip']));
			$customer_id = $order->get_customer_id();

			if(!$this->is_user_role('administrator', $user_id) && !$this->is_user_role('shop_manager', $user_id) && ($customer_id !== $user_id) ) {
				return false;
			}

			$this->generator->setup_data($_GET['create_packing_slip']);
			$this->generator->create_pdf($this->upload_dir, true);
		}
    }

	/**
	 * add_files_to_email_attachments.
	 *
	 * @version 1.2.0
	 * @since   1.0.0
	 */
	public function add_files_to_email_attachments( $attachments, $status, $order ) {

		if(!$this->get_option('generalAttachToMail')) {
			return false;
		}

		if ( ! $order instanceof WC_Order ) {
			return $attachments;
		}

		$allowed_statuses = $this->get_option('generalAttachToMailStatus');
		if(!$allowed_statuses) {
			$allowed_statuses = array( 
				'new_order', 
			);
		}

		if( isset( $status ) && in_array ( $status, $allowed_statuses ) ) {
			$order_id = $order->get_id();
			$packing_slips = $this->upload_dir . '/' . $order_id . '.pdf';
			$packing_slips_exists = file_exists($packing_slips);
			if(!$packing_slips_exists) {
				return $attachments;
			}
			$attachments[] = $packing_slips;
		}
		return $attachments;
	}

	/**
	 * add_files_to_order.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public function create_pdf_packing_slips_automatically( $order_id, $posted ) 
	{
		if(!$this->get_option('generalAutomatic')) {
			return false;
		}

		if ( ! file_exists( $this->upload_dir ) ) {
			mkdir( $this->upload_dir, 0755, true );
		}

		$this->generator->setup_data($order_id);
		if(!$this->generator->create_pdf($this->upload_dir)) {
			return false;
		}

		return true;
	}

	public function customer_download_pdf()
	{
		if ( isset( $_GET['download_packing_slip'] ) && isset( $_GET['_wpnonce'] ) && ( false !== wp_verify_nonce( $_GET['_wpnonce'], 'download_packing_slip_nonce' ) ) ) {

			$order_id = isset( $_GET['order'] ) ? $_GET['order'] : '';
			if(empty($order_id)) {
				return false;
			}

			$user_id = get_current_user_id();
			if(!$user_id) {
				return false;
			}

			$order = wc_get_order($order_id);
			$customer_id = $order->get_customer_id();
			
			if(!$this->is_user_role('administrator', $user_id) && !$this->is_user_role('shop_manager', $user_id) && ($customer_id !== $user_id) ) {
				return false;
			}

			$packing_slips = $this->upload_dir . '/' . $order_id . '.pdf';
			$packing_slips_exists = file_exists($packing_slips);
			if(!$packing_slips_exists) {
				return false;
			}
			
			$disposition = 'attachment';

			header( "Expires: 0" );
			header('Content-Type: application/pdf');
			header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
			header( "Cache-Control: private", false );
			header( 'Content-disposition: ' . $disposition . '; filename=' . $order_id . '.pdf' );
			header( "Content-Transfer-Encoding: binary" );
			header( "Content-Length: ". filesize( $packing_slips ) );
			readfile( $packing_slips );
			exit();
		}
	}

	protected function get_uploads_dir( $subdir = '' ) 
	{
		$upload_dir = wp_upload_dir();
		$upload_dir = $upload_dir['basedir'];
		if ( '' != $subdir ) {
			$upload_dir = $upload_dir . '/' . $subdir;
		}
		return $upload_dir;
	}

	protected function is_user_role( $user_role, $user_id = 0 ) 
	{
		$the_user = ( 0 == $user_id ) ? wp_get_current_user() : get_user_by( 'id', $user_id );
		if ( ! isset( $the_user->roles ) || empty( $the_user->roles ) ) {
			$the_user->roles = array( 'guest' );
		}
		return ( isset( $the_user->roles ) && is_array( $the_user->roles ) && in_array( $user_role, $the_user->roles ) );
	}
}