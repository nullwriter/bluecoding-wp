<?php
/**
 * Manage Wordpress Users
 *
 * @author    Christian Feo <christianfeob@yahoo.com>
 * @license   GPL-2.0+
 * @link      https://github.com/GaryJones/move-floating-social-bar-in-genesis
 *
 * @wordpress-plugin
 * Plugin Name:       Manage Wordpress Users
 * Plugin URI:        https://github.com/GaryJones/move-floating-social-bar-in-genesis
 * Description:       Lists your wordpress users with different set of actions available.
 * Version:           1.0.0
 * Author:            Christian Feo
 * Author URI:        https://github.com/GaryJones/move-floating-social-bar-in-genesis
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

		add_menu_page(
			__('Manage WP Users', $this->plugin_name),
			__('Manage WP Users', $this->plugin_name),
			'manage_options',
			$this->plugin_name,
			array($this, 'displayManageUsersPage'),
			plugin_dir_url(dirname(__FILE__)) . 'admin/img/api-logo.png'
		);

		$this->loadStyles();
		$this->loadScripts();
	}

	private function loadScripts()
	{
		wp_enqueue_script(
			$this->plugin_name . '_main-js',
			plugin_dir_url(__FILE__) . 'lib/js/main.js',
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
			array(),
			'1.0.0',
			'all'
		);
	}

	/**
	 * Callback function for the manage users list page
	 */
	public function displayManageUsersPage()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'partials/manage-wp-users-list-page.php';
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
