<?php

class Idlwpstripe {

	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct() {
		if ( defined( 'IDLWPSTRIPE_VERSION' ) ) {
			$this->version = IDLWPSTRIPE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'idlwpstripe';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-idlwpstripe-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-idlwpstripe-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-idlwpstripe-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-idlwpstripe-public.php';

		$this->loader = new Idlwpstripe_Loader();

	}

	private function set_locale() {

		$plugin_i18n = new Idlwpstripe_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	private function define_admin_hooks() {

		$plugin_admin = new Idlwpstripe_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'idl_stripe_options' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_setting' );

	}

	private function define_public_hooks() {

		$plugin_public = new Idlwpstripe_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_shortcode( 'idlstripe', $plugin_public, 'loadstripeform', $priority = 10);
		$this->loader->add_action( 'wp_ajax_create_strip_payment', $plugin_public , 'create_strip_payment' );
		$this->loader->add_action( 'wp_ajax_nopriv_create_strip_payment', $plugin_public , 'create_strip_payment' );
		$this->loader->add_action( 'wp_ajax_confirmpayment', $plugin_public , 'confirmpayment' );
		$this->loader->add_action( 'wp_ajax_nopriv_confirmpayment', $plugin_public , 'confirmpayment' );

	}

	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}

}
