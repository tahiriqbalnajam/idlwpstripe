<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://tahir.codes/
 * @since      1.0.0
 *
 * @package    Idlwpstripe
 * @subpackage Idlwpstripe/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Idlwpstripe
 * @subpackage Idlwpstripe/includes
 * @author     Tahir Iqbal <tahiriqbal09@gmail.com>
 */
class Idlwpstripe_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'idlwpstripe',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
