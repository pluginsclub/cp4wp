<?php
/**
 * Plugin Name:       CP4WP - Manage cPanel from WordPress
 * Plugin URI:        https://plugins.club/cp4wp/
 * Description:       Connect your cPanel account to your WordPress website and manage Emails, Databases, Domains, FTP Accounts, and much more.
 * Version:           1.0
 * Author:            plugins.club
 * Author URI:        https://plugins.club
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires at least: 5.0
 * Tested up to: 	  6.2
*/

// Block direct access
if (!defined('ABSPATH')) { exit; }

// Include the main Settings page where user adds cPanel logins
require_once plugin_dir_path( __FILE__ ) . 'includes/settings.php';
// Include Admin Dashboard Widget
require_once plugin_dir_path( __FILE__ ) . 'includes/widget.php';
// Include Resources Usage Page
require_once( plugin_dir_path( __FILE__ ) . 'includes/resources.php' );
// Include Databases Page
require_once( plugin_dir_path( __FILE__ ) . 'includes/mysql.php' );
// Include Domains Page
require_once( plugin_dir_path( __FILE__ ) . 'includes/domains.php' );
// Include PostgreSQL Page
require_once( plugin_dir_path( __FILE__ ) . 'includes/postgresql.php' );
// Include Emails Page
require_once( plugin_dir_path( __FILE__ ) . 'includes/emails.php' );
// Include Bandwidth Page
require_once( plugin_dir_path( __FILE__ ) . 'includes/login-log.php' );
// Include FTP Page
require_once( plugin_dir_path( __FILE__ ) . 'includes/ftp.php' );
// Include Bandwidth Page
require_once( plugin_dir_path( __FILE__ ) . 'includes/bandwidth.php' );
// Include plugin helper
include 'includes/PluginHelperClass.php';

/**
 * Enqueue a script in the WordPress admin on admin.php.
 *
 * @param int $hook Hook suffix for the current admin pagees.
 */
function pluginsclub_cpanel_menu_page() {
    $screen = get_current_screen();
    if ( $screen->id === 'toplevel_page_cpanel' || $screen->id === 'cpanel_page_cpanel_emails' || $screen->id === 'cpanel_page_cpanel_postgresql' || $screen->id === 'cpanel_page_cpanel_ftp' || $screen->id === 'cpanel_page_cpanel_domains' || $screen->id === 'cpanel_page_cpanel_bandwidth' || $screen->id === 'cpanel_page_cpanel_resources' || $screen->id === 'cpanel_page_cpanel_mysql' || $screen->id === 'cpanel_page_cpanel_loginlog'/* || $screen->id === 'cpanel_page_cpanel_backups'*/) {
        //wp_enqueue_script( 'pluginsclub_cpanel', plugin_dir_url( __FILE__ ) . 'includes/js/settings-page.js', array(), '1.0.0', true );
        wp_enqueue_style( 'pluginsclub_cpanel', plugin_dir_url( __FILE__ ) . 'includes/css/settings-page.css', array(), '1.2.2' );

    }
    if ( $screen->id === 'cpanel_page_cpanel_emails'){
            wp_enqueue_style( 'pluginsclub_cpanel_emails_page', plugin_dir_url( __FILE__ ) . 'includes/css/emails-page.css', array(), '1.2.1' );
            wp_enqueue_script( 'pluginsclub_cpanel_emails_page', plugin_dir_url( __FILE__ ) . 'includes/js/emails-page.js', array(), '1.9.0', true );

    }
    if ( $screen->id === 'cpanel_page_cpanel_mysql'){
            wp_enqueue_style( 'pluginsclub_cpanel_mysql_page', plugin_dir_url( __FILE__ ) . 'includes/css/mysql-page.css', array(), '1.9.0' );
            wp_enqueue_script( 'pluginsclub_cpanel_mysql_page', plugin_dir_url( __FILE__ ) . 'includes/js/mysql-page.js', array(), '1.9.0', true );
    }
    if ( $screen->id === 'cpanel_page_cpanel_postgresql'){
            wp_enqueue_style( 'pluginsclub_cpanel_postgresql_page', plugin_dir_url( __FILE__ ) . 'includes/css/postgresql-page.css', array(), '2.0.0' );
            wp_enqueue_script( 'pluginsclub_cpanel_postgresql_page', plugin_dir_url( __FILE__ ) . 'includes/js/postgresql-page.js', array(), '1.9.0', true );
    }
    if ( $screen->id === 'cpanel_page_cpanel_ftp'){
            wp_enqueue_style( 'pluginsclub_cpanel_ftp_page', plugin_dir_url( __FILE__ ) . 'includes/css/ftp-page.css', array(), '1.0.0' );
            wp_enqueue_script( 'pluginsclub_cpanel_ftp_page', plugin_dir_url( __FILE__ ) . 'includes/js/ftp-page.js', array(), '1.0.0', true );
    }
}
add_action( 'admin_enqueue_scripts', 'pluginsclub_cpanel_menu_page' );

// Register the settings page and subpages
function pluginsclub_cpanel_register_settings() {
    // Add the main settings page
    add_menu_page('cPanel', 'cPanel', 'manage_options', 'cpanel', 'pluginsclub_cpanel_page', plugins_url('cp4wp/includes/icons/cpanel-icon.png'), 9999);

    // Retrieve the options for each page
    $enable_overview_widgets = get_option('cp4wp_enable_overview_widgets', true);
    $enable_server_widgets = get_option('cp4wp_enable_server_widgets', true);

    $enable_resources = get_option('cp4wp_enable_resources', true);
    $enable_emails = get_option('cp4wp_enable_emails', true);
    $enable_mysql = get_option('cp4wp_enable_mysql', true);
    $enable_domains = get_option('cp4wp_enable_domains', true);
    $enable_ftp = get_option('cp4wp_enable_ftp', true);
    $enable_postgresql = get_option('cp4wp_enable_postgresql', true);
    $enable_bandwidth = get_option('cp4wp_enable_bandwidth', true);
    $enable_login_log = get_option('cp4wp_enable_login_log', true);

    if ($enable_overview_widgets) {
    // Add the widget to the admin dashboard
    add_action('wp_dashboard_setup', 'pluginsclub_cpanel_widget_add_dashboard_widget');
    function pluginsclub_cpanel_widget_add_dashboard_widget() {
        wp_add_dashboard_widget('pluginsclub_cpanel_widget', 'cPanel Overview', 'pluginsclub_cpanel_widget_display');
}
    }
    
    if ($enable_server_widgets) {
    // Add the widget to the admin dashboard
    add_action('wp_dashboard_setup', 'pluginsclub_cpanel_widget_add_dashboard_widget2');
    function pluginsclub_cpanel_widget_add_dashboard_widget2() {
        wp_add_dashboard_widget('pluginsclub_cpanel_server_widget', 'cPanel Server', 'pluginsclub_cpanel_server_widget_display');
}
    }

    // Add subpages based on the settings
    if ($enable_emails) {
    add_submenu_page( 'cpanel', 'cPanel Email Accounts', '<span class="dashicons dashicons-email"></span> Email Accounts', 'manage_options', 'cpanel_emails', 'pluginsclub_cpanel_emails_page' );
    }
    
    if ($enable_domains) {
        add_submenu_page('cpanel', 'cPanel Domain Names', '<span class="dashicons dashicons-admin-site"></span> Domain Names', 'manage_options', 'cpanel_domains', 'pluginsclub_cpanel_domains_page');
    }
    
    if ($enable_ftp) {
    add_submenu_page( 'cpanel', 'cPanel FTP Accounts', '<span class="dashicons dashicons-open-folder"></span> FTP Accounts', 'manage_options', 'cpanel_ftp', 'pluginsclub_cpanel_ftp_page' );
    }

    if ($enable_mysql) {
        add_submenu_page('cpanel', 'cPanel Databases', '<span class="dashicons dashicons-database"></span> MySQL', 'manage_options', 'cpanel_mysql', 'pluginsclub_cpanel_mysql_page');
    }

    if ($enable_postgresql) {
    add_submenu_page( 'cpanel', 'PostgreSQL Databases', '<span class="dashicons dashicons-database-add"></span> PostgreSQL', 'manage_options', 'cpanel_postgresql', 'pluginsclub_cpanel_postgresql_page' );
    }

    if ($enable_resources) {
        add_submenu_page('cpanel', 'cPanel Resource Usage', '<span class="dashicons dashicons-chart-pie"></span> Resource Usage', 'manage_options', 'cpanel_resources', 'pluginsclub_cpanel_resources_page');
    }    
    
    if ($enable_bandwidth) {
    add_submenu_page( 'cpanel', 'Bandwidth', '<span class="dashicons dashicons-chart-line"></span> Bandwidth', 'manage_options', 'cpanel_bandwidth', 'pluginsclub_cpanel_bandwidth_page' );
    }
    
    if ($enable_login_log) {
    add_submenu_page( 'cpanel', 'cPanel Login Log', '<span class="dashicons dashicons-code-standards"></span> Login History', 'manage_options', 'cpanel_loginlog', 'pluginsclub_cpanel_loginlog_page' );
    }
   
   

    // Add other subpages based on the options

    // Register the plugin settings
    register_setting('cpanel_settings', 'cpanel_username');
    register_setting('cpanel_settings', 'cpanel_password');
    register_setting('cpanel_settings', 'cpanel_host');
}

add_action('admin_menu', 'pluginsclub_cpanel_register_settings');
