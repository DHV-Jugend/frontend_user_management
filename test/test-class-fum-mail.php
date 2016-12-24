<?php

/**
 * @author Christoph Bessei
 * @version
 */
class Fum_MailTest extends WP_UnitTestCase {

	private $plugin;

	function setUp() {

		parent::setUp();


	} // end setup

	function testPluginInitialization() {
		$path                 = trailingslashit( dirname( dirname( __FILE__ ) ) );
		$returned_plugin_path = Frontend_User_Management::get_plugin_path();
		$this->assertTrue( $path == $returned_plugin_path );
	} // end testPluginInitialization

	function testGetRecentPost() {
		$this->assertTrue( false !== wp_get_recent_posts() );
	}
}
 