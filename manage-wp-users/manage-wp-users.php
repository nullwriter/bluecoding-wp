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
	 * Constructor calls to add hooks and filters.
	 *
	 * Manage_WP_Users constructor.
	 */
	private function __construct()
	{
		$this->plugin_name = 'manage-wordpress-users';

		$this->loadActions();
		$this->loadFilters();
	}

	private function loadActions()
	{
		add_action('admin_enqueue_scripts', array($this, 'loadScripts'));
		add_action('admin_enqueue_scripts', array($this, 'loadStyles'));

		add_action('admin_menu', array($this, 'registerMenuPage'));
		add_action('wp_ajax_changeMemberStatus', array($this, 'changeMemberStatus'));
		add_action('wp_ajax_updateMemberDisplayName', array($this, 'updateMemberDisplayName'));
		add_action('wp_ajax_changeMemberStatusInBulk', array($this, 'changeMemberStatusInBulk'));
	}

	private function loadFilters()
	{
		add_filter('authenticate', array($this, 'checkUserStatus'), 30, 3);
	}

	/***************************************************************************************************************/
	/********************************************** PUBLIC FUNCTIONS ***********************************************/
	/***************************************************************************************************************/

	public function loadScripts()
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

		wp_enqueue_script(
			$this->plugin_name . '_datatables-select',
			plugin_dir_url(__FILE__) . 'lib/js/dataTables.select.min.js',
			array( 'jquery', $this->plugin_name . '_datatables', $this->plugin_name . '_main-js' ),
			'1.0.0',
			false
		);

		wp_enqueue_script(
			$this->plugin_name . '_alertify',
			plugin_dir_url(__FILE__) . 'lib/js/alertify.min.js',
			array( 'jquery', $this->plugin_name . '_main-js' ),
			'1.0.0',
			false
		);

		// global variables
		wp_localize_script($this->plugin_name . '_main-js', 'admin_url', array(
			'ajax_url' => admin_url('admin-ajax.php')
		));
	}

	public function loadStyles()
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

		wp_enqueue_style(
			$this->plugin_name . '_alertify',
			plugin_dir_url(__FILE__) . 'lib/css/alertify.min.css',
			array(),
			'1.0.0',
			'all'
		);
	}

	/**
	 * Checks user's member_status at authentication. If inactive, returns a WP_Error.
	 *
	 * @param $user
	 * @param string $username
	 * @param string $password
	 * @return WP_Error
	 */
	public function checkUserStatus($user, $username = '', $password = '')
	{
		$user_id = $user->data->ID;
		$member_status = get_user_meta($user_id, "member_status", true);

		// Absense of an inactive status corresponds to an active account
		if (empty($member_status) || $member_status == 'active') {
			return $user;
		} else {
			return new WP_Error('disabled_account', 'This account is currently disabled for access.');
		}
	}

	/**
	 * Registers the user list page and admin menu item
	 */
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
	 * Ajax function that changes the member_status of a user by switching the current status to the opposite.
	 */
	public function changeMemberStatus()
	{
		$user_id = $_POST['user_id'];
		$currentStatus = get_user_meta($user_id, 'member_status', true);
		$newStatus = 'active';
		$result = false;

		if (empty($currentStatus) || $currentStatus == 'active') {
			$newStatus = 'inactive';
		}

		if (update_user_meta($user_id, 'member_status', $newStatus)) {
			$result = true;
		}

		echo json_encode(array('result' => $result, 'status' => $newStatus));
		die();
	}

	public function log($log) {
		if (true === WP_DEBUG) {
			if (is_array($log) || is_object($log)) {
				error_log(print_r($log, true));
			} else {
				error_log($log);
			}
		}
	}

	/**
	 * Ajax function to change member status in bulk
	 */
	public function changeMemberStatusInBulk()
	{
		$selectedIds = $_POST['selected_ids'];
		$newStatus = $_POST['status'];

		foreach($selectedIds as $user_id){
			update_user_meta($user_id, 'member_status', $newStatus);
		}

		echo json_encode(array('result' => true, 'count' => count($selectedIds), 'status' => $newStatus));
		die();
	}

	/**
	 * Ajax function to update members display name
	 */
	public function updateMemberDisplayName()
	{
		$user_id = $_POST['user_id'];
		$newName = $_POST['name'];
		$result = false;

		if (wp_update_user(array( 'ID' => $user_id, 'display_name' => $newName ))) {
			$result = true;
		}

		echo json_encode(array('result' => $result, 'name' => $newName));
		die();
	}

	/**
	 * Create if not exists or return current instance. (Singleton)
	 *
	 * @return bool|Manage_WP_Users
	 */
	public static function getInstance()
	{
		if (!self::$instance) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}

$Manage_WP_Users = Manage_WP_Users::getInstance();
