<?php

// Block direct access
if (!defined('ABSPATH')) { exit; }

// Code for displaying cPanel Bandwidth page
function pluginsclub_cpanel_bandwidth_page() {

// Load CSS
wp_enqueue_style( 'pluginsclub_bandwidth', plugin_dir_url( __FILE__ ) . 'css/bandwidth-page.css', array(), '1.0.0' );

// Get cPanel login data
  $username = get_option('cpanel_username');
  $password = get_option('cpanel_password');
  $hostname = get_option('cpanel_host');
  
// API Link
// https://api.docs.cpanel.net/openapi/cpanel/operation/query/
$url = "https://$hostname:2083/cpsess1235467/execute/Bandwidth/query?grouping=domain|protocol|year";

?>
<div id="pluginsclub-cpanel">
					<div id="pluginsclub-cpanel-header">
			<div id="pluginsclub-cpanel-header-title">
				<div id="pluginsclub-cpanel-header-title-image">
<h1><a href="http://plugins.club/" target="_blank" class="logo"><img src="<?php echo plugins_url('images/pluginsclub_logo_black.png', __FILE__) ?>" style="height:27px"></a></h1></div>

				<div id="pluginsclub-cpanel-header-title-image-sep">
				</div>

<div id="pluginsclub-cpanel-header-title-nav">
    <?php
    $pages = array(
        array(
            'slug' => 'cpanel',
            'label' => 'cPanel Settings',
            'option' => 'cp4wp_enable_settings',
        ),
        array(
            'slug' => 'cpanel_emails',
            'label' => 'Email Accounts',
            'option' => 'cp4wp_enable_emails',
        ),
        array(
            'slug' => 'cpanel_domains',
            'label' => 'Domains',
            'option' => 'cp4wp_enable_domains',
        ),
        array(
            'slug' => 'cpanel_ftp',
            'label' => 'FTP',
            'option' => 'cp4wp_enable_ftp',
        ),
        array(
            'slug' => 'cpanel_mysql',
            'label' => 'MySQL',
            'option' => 'cp4wp_enable_mysql',
        ),
        array(
            'slug' => 'cpanel_postgresql',
            'label' => 'PostgreSQL',
            'option' => 'cp4wp_enable_postgresql',
        ),
        array(
            'slug' => 'cpanel_resources',
            'label' => 'Resources',
            'option' => 'cp4wp_enable_resources',
        ),
        array(
            'slug' => 'cpanel_bandwidth',
            'label' => 'Bandwidth',
            'option' => 'cp4wp_enable_bandwidth',
        ),
        array(
            'slug' => 'cpanel_loginlog',
            'label' => 'Login History',
            'option' => 'cp4wp_enable_loginlog',
        ),
    );

    foreach ($pages as $page) {
        $option_value = get_option($page['option'], true);
        $active_class = (isset($_GET['page']) && $_GET['page'] === $page['slug']) ? ' active' : '';
        if ($option_value) {
            echo '<div class="pluginsclub-cpanel-header-nav-item' . $active_class . '"><a href="' . admin_url('admin.php?page=' . $page['slug']) . '" class="tab">' . $page['label'] . '</a></div>';
        }
    }
    ?>
</div>
			</div>
		</div>
				<div id="pluginsclub-cpanel-admin-wrap" class="wrap">
			<div id="pluginsclub-cpanel-form">
			<h1 class="pluginsclub-cpanel-hide">cPanel Bandwidth</h1>
				<h2>cPanel Bandwidth</h2>
        <p>Bandwidth usage per domain name</p>

			
		<div class="pluginsclub-cpanel-sep"></div></br>

<?php
                
// Build the URL with the extracted values
$response = wp_remote_get($url, array(
    'headers' => array(
        'Authorization' => 'Basic ' . base64_encode(get_option('cpanel_username') . ':' . get_option('cpanel_password'))
    )
));
$response_data = json_decode(wp_remote_retrieve_body($response), true);


// Round data to human-readable format
function format_bytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
}


?>

<div class="clearfix">
    <?php
	
	// Display the tables
    if (isset($response_data['data'])) {
        foreach ($response_data['data'] as $domain => $protocols) {
            ?>
            <div class="table-wrapper">
                <h3>Bandwidth usage for <?php echo $domain ?>:</h3>
                <table class="wp-list-table widefat fixed striped posts" id="bandwidth">
                    <thead>
                        <tr>
                            <th>Protocol</th>
                            <th>Year</th>
                            <th>Bandwidth Usage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($protocols as $protocol => $years) { ?>
                            <?php foreach ($years as $year => $bandwidth) { ?>
                                <tr>
                                    <td><?php echo $protocol ?></td>
                                    <td><?php echo date('Y', $year) ?></td>
                                    <td><?php echo format_bytes($bandwidth) ?></td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php
        }
    } else {
        echo "No data found.";
    }
    ?>
</div>


    <?php
}