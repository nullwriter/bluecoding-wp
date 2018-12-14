<?php
/**
 * Manage Wordpress Users
 *
 * @author    Christian Feo <christianfeob@yahoo.com>
 * @license   GPL-2.0+
 * @link      https://github.com/nullwriter/bluecoding-wp
 *
 * @wordpress-plugin
 * Plugin Name:       Manage Wordpress Users
 * Plugin URI:        https://github.com/nullwriter/bluecoding-wp
 * Description:       Lists your wordpress users with different set of actions available.
 * Version:           1.0.0
 * Author:            Christian Feo
 * Author URI:        https://github.com/nullwriter/bluecoding-wp
 * Text Domain:       manage-wordpress-users
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */


class Manage_WP_Users
{
	static $instance = false;

	private $plugin_name;

	/**
	 * Constructor calls to add hooks and filters
	 *
	 * Manage_WP_Users constructor.
	 */
	private function __construct()
	{
		$this->plugin_name = 'manage-wordpress-users';

		$this->loadStyles();
		$this->loadScripts();
		$this->loadActions();
	}

	private function loadActions()
	{
		add_action('admin_menu', array($this, 'registerMenuPage'));
	}

	private function loadScripts()
	{
		wp_enqueue_script(
			$this->plugin_name . '_main-js',
			plugin_dir_url(__FILE__) . 'lib/js/main.js',
			array( 'jquery', $this->plugin_name . '_datatables' ),
			'1.0.0',
			false
		);

		wp_enqueue_script(
			$this->plugin_name . '_datatables',
			plugin_dir_url(__FILE__) . 'lib/js/datatables.min.js',
			array( 'jquery' ),
			'1.0.0',
			false
		);

		wp_enqueue_script(
			$this->plugin_name . '_bs4',
			plugin_dir_url(__FILE__) . 'lib/js/bootstrap.min.js',
			array( 'jquery' ),
			'1.0.0',
			false
		);
	}

	private function loadStyles()
	{
		wp_enqueue_style(
			$this->plugin_name . '_main-style',
			plugin_dir_url(__FILE__) . 'lib/css/style.css',
			array($this->plugin_name . '_datatables'),
			'1.0.0',
			'all'
		);

		wp_enqueue_style(
			$this->plugin_name . '_datatables',
			plugin_dir_url(__FILE__) . 'lib/css/datatables.min.css',
			array(),
			'1.0.0',
			'all'
		);

		wp_enqueue_style(
			$this->plugin_name . '_bs4',
			plugin_dir_url(__FILE__) . 'lib/css/bootstrap.min.css',
			array(),
			'1.0.0',
			'all'
		);
	}

	public function registerMenuPage()
	{
		add_menu_page(
			__('Manage Users', $this->plugin_name),
			__('Manage Users', $this->plugin_name),
			'manage_options',
			$this->plugin_name,
			array($this, 'displayManageUsersPage'),
			plugin_dir_url(__FILE__) . 'lib/img/list-logo.png'
		);
	}

	/**
	 * Callback function for the manage users list page
	 */
	public function displayManageUsersPage()
	{
		require_once plugin_dir_path(__FILE__) . 'partials/manage-wp-users-list-page.php';
	}

	/**
	 * Create if not exists or return current instance. (Singleton)
	 *
	 * @return bool|Manage_WP_Users
	 */
	public static function getInstance()
	{
		if (!self::$instance)
		{
			self::$instance = new self;
		}

		return self::$instance;
	}

}

$Manage_WP_Users = Manage_WP_Users::getInstance();
