<?php
/*
Plugin Name: Test Ajax Requests
Plugin URI: 
Description: -
Version: 1.0
Author: okolovas
Author URI: http://okolovas.net/
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain: tajx
Domain Path: /languages
Network: true
*/

/*
Copyright 2015 - 2018 okolovas (email : hi@okolovas.net)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

class tajx {
	private static $PREFIX;
	private static $LOG_TABLE;
	private static $MESSAGES;

	/**
	* @var iwt_igliker The singleton instance.
	*/
	protected static $instance;

	private static function set_defaults() {
		global $wpdb;
		self::$PREFIX = $wpdb->prefix.'tajx_';
		self::$LOG_TABLE = self::$PREFIX.'log';
		self::$MESSAGES = array();
	}
	/**
	* Instantiates a new iwt_igliker object.
	*/
	private function __construct() {
		self::set_defaults();
		load_plugin_textdomain( 'tajx', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		add_action( 'admin_menu', array( $this, 'register_custom_menu_page' ) );

		if( !wp_script_is('tajx', 'enqueued') ) {
			wp_enqueue_script( 'tajx', plugin_dir_url( __FILE__ ) . 'js/tajx.js', array('jquery') );
		}
		add_action( 'wp_ajax_get_results', array( $this, 'get_results' ) );
	}

	/**
	* Avoid public cloning the object
	*/
	private function __clone() {
	}
   
	/**
	* Avoid public cloning the object
	*/
	private function __wakeup() {
	}

	/**
	* Installs plugin
	*/
	public function uninstall() {
		global $wpdb;

		$sql = "DROP TABLE `" . self::$LOG_TABLE . "`;";
		$wpdb->query($sql);
	}

	/**
	* Installs plugin
	*/
	public static function install() {
		global $wpdb;

		self::set_defaults();

		$sql = "CREATE TABLE IF NOT EXISTS `" . self::$LOG_TABLE . "` (
	  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
	  `media_id` bigint(20) NOT NULL,
	  `user_id` bigint(20) NOT NULL,
	  `user_token` varchar(200) NOT NULL,
	  `like` int(1) NOT NULL DEFAULT '0',
	  `error` varchar(1000) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
	  PRIMARY KEY `ID` (`ID`),
	  UNIQUE KEY `user_photo` (`media_id`,`user_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;";
		$wpdb->query($sql);
	}
	
	/**
	 * Adds admin menu
	*/
	public function register_custom_menu_page() {
		$page = add_options_page( __( 'Test Ajax Plugin', 'tajx' ), __( 'Test Ajax Plugin', 'tajx' ), 'manage_options', 'tajx', array($this, 'admin_render') );
	}

	/**
	 * Insures we always return the same object.
	 *
	 * @return tajx
	 */
	public static function get_instance() {

		if ( ! ( self::$instance instanceof tajx ) ) {
			self::$instance = new tajx();
		}

		return self::$instance;
	}

	/**
	* Adding a result message to array to output it later
	* 
	* @param $message string
	*/
	public function add_message( $message ) {
		self::$MESSAGES[] = $message;
	}
	
	/**
	* Output all saved result messages
	*/
	public function get_messages() {
		if ( !empty(self::$MESSAGES) ) {
			foreach( self::$MESSAGES as $m ) {
				$this->write_message( $m );
			}
		}
	}

	/**
	 * Renders admin page
	*/
	function admin_render() {
	?>
		<div class="wrap">
			<h2><?php _e('Admin test ajax', 'tajx'); ?></h2>
			<div class="output-message"></div>
			<form method="post" id="tajx-form" action="">
				<div>
					<input type="submit" class="button-primary" value="<?php _e('Get Results', 'tajx'); ?>" />
				</div>
			</form>
		</div>
	<?php
	}

	/**
	* Main function called by Ajax
	* Put your plugin code here
	*/
	function get_results() {
		self::add_message('Tajx get_results function called!');
		
		// put your code here
		
		$m = join("<br/>\n", self::$MESSAGES);
		
		die( $m );
	}
}

tajx::get_instance();
register_activation_hook( __FILE__, array( 'tajx', 'install' ) );
?>