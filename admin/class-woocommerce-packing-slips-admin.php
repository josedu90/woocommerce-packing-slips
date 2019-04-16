<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://welaunch.io/plugins/woocommerce-packing-slips/
 * @since      1.0.0
 *
 * @package    WooCommerce_Packing_Slips
 * @subpackage WooCommerce_Packing_Slips/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WooCommerce_Packing_Slips
 * @subpackage WooCommerce_Packing_Slips/admin
 * @author     Daniel Barenkamp <contact@db-dzine.de>
 */
class WooCommerce_Packing_Slips_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) 
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	public function load_redux()
	{
        if(!is_admin() || !current_user_can('administrator')){
            return false;
        }

	    // Load the theme/plugin options
	    if ( file_exists( plugin_dir_path( dirname( __FILE__ ) ) . 'admin/options-init.php' ) ) {
	        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/options-init.php';
	    }
	}

    /**
     * Init
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @return  boolean
     */
    public function init()
    {
        global $woocommerce_packing_slips_options;

        if(!is_admin() || !current_user_can('administrator')){
            $woocommerce_packing_slips_options = get_option('woocommerce_packing_slips_options');
        }

        $this->upload_dir = $this->get_uploads_dir( 'packing-slips' );
        $this->options = $woocommerce_packing_slips_options;
    }

   /**
     * Enqueue Admin Styles
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @return  boolean
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name.'-admin', plugin_dir_url(__FILE__).'css/woocommerce-packing-slips-admin.css', array(), $this->version, 'all');
    }

    /**
     * Enqueue Admin Scripts
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @return  boolean
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script($this->plugin_name.'-admin', plugin_dir_url(__FILE__).'js/woocommerce-packing-slips-admin.js', array('jquery'), $this->version, true);
    }

	public function add_custom_order_status_actions_button( $actions, $order ) {

		$order_id = $order->get_id();

		$packing_slips = $this->upload_dir . '/' . $order_id . '.pdf';
		$packing_slips_exists = file_exists($packing_slips);
		if(!$packing_slips_exists) {
			return $actions;
		}

		$query_params = array( 
			'download_packing_slip' => 'true', 
			'_wpnonce' => wp_create_nonce( 'download_packing_slip_nonce' ),
			'order' => $order_id,
		);

        // Set the action button
        $actions['packing_slips'] = array(
            'url'       => add_query_arg( $query_params ),
            'name'      => __( 'Download Packing Slip', 'woocommerce-packing-slips' ),
            'action'    => "download-packing_slips",
        );

	    return $actions;
	}

	protected function get_uploads_dir( $subdir = '' ) {
		$upload_dir = wp_upload_dir();
		$upload_dir = $upload_dir['basedir'];
		if ( '' != $subdir ) {
			$upload_dir = $upload_dir . '/' . $subdir;
		}
		return $upload_dir;
	}

    public function add_pdf_packing_slips_meta_box()
    {
		$screen   = 'shop_order';
		$context  = 'side';
		$priority = 'high';
		add_meta_box(
			'wc_packing_slips_pdfs_upload_metabox',
			__( 'Packing Slip', 'woocommerce-packing-slips' ),
			array( $this, 'create_pdf_packing_slips_meta_box' ),
			$screen,
			$context,
			$priority
		);
    }

	/**
	 * create_pdf_packing_slips_meta_box.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public function create_pdf_packing_slips_meta_box() 
	{
		$html = '';
		$order_id = get_the_ID();

		$packing_slips = $this->upload_dir . '/' . $order_id . '.pdf';
		$packing_slips_exists = file_exists($packing_slips);

		if($packing_slips_exists) {

			$query_params = array( 
				'download_packing_slip' => 'true', 
				'_wpnonce' => wp_create_nonce( 'download_packing_slip_nonce' ),
				'order' => $order_id,
			);
			$html .= '<a target="_blank" href="' . add_query_arg( $query_params ) . '">' . __('Download Packing Slip', 'woocommerce-packing-slips') . '</a>';
			$html .= '<hr><button type="submit" class="button button-primary" name="create_packing_slip" value="' . $order_id . '">' . __('Update Packing Slip', 'woocommerce-packing-slips') . '</button>';

		} else {
			$html .= '<p><em>' . __( 'No files uploaded.', 'woocommerce-packing-slips' ) . '</em></p>';
		
			$html .= '<hr><button type="submit" class="button button-primary" name="create_packing_slip" value="' . $order_id . '">' . __('Create Packing Slip', 'woocommerce-packing-slips') . '</button>';
		}

		echo $html;
	}

	public function add_preview_frame()
	{
		$shop_order_ids = get_posts(array(
		    'fields'          => 'ids',
		    'posts_per_page'  => 20,
		    'post_type' => 'shop_order',
		    'post_status' => 'any'
		));
		?>
		<div id="packing-slip-preview-frame-container" class="packing-slip-preview-frame-container">
			<div class="packing-slip-preview-frame-header">
				<label for="order_id"><?php _e('Select Order ID', 'woocommerce-packing-slips') ?></label>
				<select name="order_id" id="packing-slip-preview-order-id">
					<?php foreach ($shop_order_ids as $key => $shop_order_id) {
						if($key == 0) {
							echo '<option value="' . $shop_order_id . '" selected>' . $shop_order_id . '</option>';
							continue;
						}
						echo '<option value="' . $shop_order_id . '">' . $shop_order_id . '</option>';
					} ?>
				</select>
			</div>
			<div id="packing-slip-preview-spinner" class="packing-slip-preview-spinner">
				<i class="el el-refresh el-spin"></i>
			</div>
			<iframe id="packing-slip-preview-frame" src="" width="100%" height="100%" class="packing-slip-preview-frame">

			</iframe>
		</div>
		<div id="packing-slip-preview-frame-overlay" class="packing-slip-preview-frame-overlay"></div>
		<?php
	}

   	public function add_bulk_action_download_packing_slips($bulk_actions)
	{
        $bulk_actions['download_packing_slips'] = __( 'Download Packing Slips', 'woocommerce-packing-slips');
        return $bulk_actions;
    }

    public function handle_bulk_action_download_packing_slips($redirect_to, $action, $order_ids)
    {        
        if ( $action !== 'download_packing_slips') {
            return $redirect_to;
        }
		
		$files = array();
		foreach ($order_ids as $order_id) {
			$packing_slips = $this->upload_dir . '/' . $order_id . '.pdf';
			$packing_slips_exists = file_exists($packing_slips);

			if($packing_slips_exists) {
				$files[] = $packing_slips;
			}
		}

		if(empty($files)) {
			wp_die(__('No Packing Slips found', 'woocommerce-packing-slips'));
		}

		$zipname = 'packing-slips-' . time() . '.zip';
		$zip = new ZipArchive;
		$zip->open($zipname, ZipArchive::CREATE);
		foreach ($files as $file) {
			$zip->addFile($file,basename($file));
		}
		$zip->close();

		header('Content-Type: application/zip');
		header('Content-disposition: attachment; filename='.$zipname);
		header('Content-Length: ' . filesize($zipname));
		readfile($zipname);

        exit();
    }
}
