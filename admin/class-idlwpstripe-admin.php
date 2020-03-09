<?php

class Idlwpstripe_Admin {


	private $plugin_name;
	private $version;
	private $option_name = 'idlwpstripe';
	private $currencies;

	public function __construct( $plugin_name, $version ) {

		include plugin_dir_path(dirname(__FILE__)).'includes/currencies.php';
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->currencies = $currencies;
		

	}


	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name.'_bulma', plugin_dir_url( __FILE__ ) . 'css/bulma.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'_custom', plugin_dir_url( __FILE__ ) . 'css/idlwpstripe-admin.css', array(), $this->version, 'all' );

	}

	public function enqueue_scripts() {

	}

	public function idl_stripe_options() {

		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'IDL Stripe Configuration', $this->plugin_name ),
			__( 'IDL stripe', $this->plugin_name ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'load_option_page' )
		);
		
	}

	public function load_option_page() {
		include_once 'partials/admin_options.php';
	}


	public function register_setting() {

		add_settings_section(
			$this->option_name . '_general',
			__( 'Stripe Configuration', $this->plugin_name ),
			array( $this, $this->option_name . '_configuration_sec' ),
			$this->plugin_name
		);

		add_settings_field(
			$this->option_name . '_product_price',
			__( 'Product Price', $this->plugin_name ),
			array( $this, $this->option_name . '_product_price' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_product_price' )
		);

		add_settings_field(
			$this->option_name . '_currency',
			__( 'Product Price', $this->plugin_name ),
			array( $this, $this->option_name . '_currency' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_currency' )
		);

		add_settings_field(
			$this->option_name . '_strip_publishable_key',
			__( 'Stripe Publish Key', $this->plugin_name ),
			array( $this, $this->option_name . '_strip_publishable_key' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_strip_publishable_key' )
		);

		add_settings_field(
			$this->option_name . '_strip_api_key',
			__( 'Stripe API Key', $this->plugin_name ),
			array( $this, $this->option_name . '_strip_api_key' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_strip_api_key' )
		);

		add_settings_section(
			$this->option_name . '_payemnt',
			__( 'Payment', $this->plugin_name ),
			array( $this, $this->option_name . '_payment' ),
			$this->plugin_name
		);

		add_settings_field(
			$this->option_name . '_payment_mode',
			__( 'Fixed amount?', $this->plugin_name ),
			array( $this, $this->option_name . '_payment_mode' ),
			$this->plugin_name,
			$this->option_name . '_payemnt',
			array( 'label_for' => $this->option_name . '_payment_mode' )
		);

		add_settings_section(
			$this->option_name . '_fixed_amount',
			__( 'Fixed amount', $this->plugin_name ),
			array( $this, $this->option_name . '_fixed_amount' ),
			$this->plugin_name
		);

		register_setting( $this->plugin_name, $this->option_name . '_strip_publishable_key');
		register_setting( $this->plugin_name, $this->option_name . '_strip_api_key' );
		register_setting( $this->plugin_name, $this->option_name . '_currency' );
		register_setting( $this->plugin_name, $this->option_name . '_product_price', 'intval' );
		register_setting( $this->plugin_name, $this->option_name . '_payment_mode' );
		register_setting( $this->plugin_name, $this->option_name . '_fixed_amount' );
	}

	/**
	 * Render the text for the general section
	 *
	 * @since  1.0.0
	 */
	public function idlwpstripe_configuration_sec() {
		echo '<p>' . __( 'Please change the settings accordingly.', $this->plugin_name ) . '</p>';
	} 

	public function idlwpstripe_product_price() {
		$product_price = get_option( $this->option_name . '_product_price' );
		echo '<input type="text" name="' . $this->option_name . '_product_price' . '" id="' . $this->option_name . '_product_price' . '" value="' . $product_price . '"> ';
	}

	public function idlwpstripe_currency() {
		$currency = get_option( $this->option_name . '_currency' );
		echo '<select name="' . $this->option_name . '_currency" name="' . $this->option_name . '_currency">';
		foreach($this->currencies  as $key => $val) {
			$selected = ($currency == $key) ? 'selected' : '';
			echo '<option value="'.$key.'" '.$selected.'>'.$val.'</option>';
		}
		
		echo '</select>';
	}

	public function idlwpstripe_strip_publishable_key() {
		$strip_publishable_key = get_option( $this->option_name . '_strip_publishable_key' );
		echo '<input type="text" name="' . $this->option_name . '_strip_publishable_key' . '" id="' . $this->option_name . '_strip_publishable_key' . '" value="' . $strip_publishable_key . '"> ';
	}

	public function idlwpstripe_strip_api_key() {
		$strip_api_key = get_option( $this->option_name . '_strip_api_key' );
		echo '<input type="text" name="' . $this->option_name . '_strip_api_key' . '" id="' . $this->option_name . '_strip_api_key' . '" value="' . $strip_api_key . '"> ';
	}

	public function idlwpstripe_payment() {
		echo '<p>' . __( 'Set that you want fixed amount or user can put an amount.', $this->plugin_name ) . '</p>';
	}

	public function idlwpstripe_payment_mode() {
		$payment_mode = get_option( $this->option_name . '_payment_mode' );
		if($payment_mode == "fixed"){
			$fixed = "checked";
			$userdefine = "";
		}
		else {
			$fixed = "";
			$userdefine = "checked";
		}

		echo 'Yes: <input type="radio" '.$fixed.' name="' . $this->option_name . '_payment_mode' . '" id="' . $this->option_name . '_payment_mode_fixed' . '" value="fixed">';
		echo 'No: <input type="radio" '.$userdefine.' name="' . $this->option_name . '_payment_mode' . '" id="' . $this->option_name . '_payment_mode_fixed' . '" value="user_define">';
	}

	public function idlwpstripe_fixed_amount() {
		$fixed_amount = get_option( $this->option_name . '_fixed_amount' );
		echo '<input type="text" name="' . $this->option_name . '_fixed_amount' . '" id="' . $this->option_name . '_fixed_amount' . '" value="' . $fixed_amount . '"> ';
	}

}
