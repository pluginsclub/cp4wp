<?php

// Block direct access
if (!defined('ABSPATH')) { exit; }


// Code for displaying cPanel Backups page
function pluginsclub_cpanel_backups_page() {

// Get cPanel login data
  $username = get_option('cpanel_username');
  $password = get_option('cpanel_password');
  $hostname = get_option('cpanel_host');
  
?>
<div id="pluginsclub-cpanel">
					<div id="pluginsclub-cpanel-header">
			<div id="pluginsclub-cpanel-header-title">
				<div id="pluginsclub-cpanel-header-title-image">
<h1><a href="<?php echo admin_url( 'admin.php?page=cpanel' ); ?>" class="logo">CP4WP</a></h1></div>
				<div id="pluginsclub-cpanel-header-title-image-sep">
				</div>

				<div id="pluginsclub-cpanel-header-title-nav">
<div class="pluginsclub-cpanel-header-nav-item"><a href="<?php echo admin_url( 'admin.php?page=cpanel' ); ?>" class="tab">cPanel Settings</a></div>
<div class="pluginsclub-cpanel-header-nav-item"><a href="<?php echo admin_url( 'admin.php?page=cpanel_emails' ); ?>" class="tab">Email Accounts</a></div>
<div class="pluginsclub-cpanel-header-nav-item"><a href="<?php echo admin_url( 'admin.php?page=cpanel_domains' ); ?>" class="tab">Domains</a></div>
<div class="pluginsclub-cpanel-header-nav-item"><a href="<?php echo admin_url( 'admin.php?page=cpanel_ftp' ); ?>" class="tab">FTP</a></div>
<div class="pluginsclub-cpanel-header-nav-item"><a href="<?php echo admin_url( 'admin.php?page=cpanel_mysql' ); ?>" class="tab">MySQL</a></div>
<div class="pluginsclub-cpanel-header-nav-item"><a href="<?php echo admin_url( 'admin.php?page=cpanel_postgresql' ); ?>" class="tab">PostgreSQL</a></div>
<div class="pluginsclub-cpanel-header-nav-item active"><a href="<?php echo admin_url( 'admin.php?page=cpanel_backups' ); ?>" class="tab">Backups</a></div>
<div class="pluginsclub-cpanel-header-nav-item"><a href="<?php echo admin_url( 'admin.php?page=cpanel_resources' ); ?>" class="tab">Resources</a></div>
<div class="pluginsclub-cpanel-header-nav-item"><a href="<?php echo admin_url( 'admin.php?page=cpanel_bandwidth' ); ?>" class="tab">Bandwidth</a></div>
<div class="pluginsclub-cpanel-header-nav-item"><a href="<?php echo admin_url( 'admin.php?page=cpanel_loginlog' ); ?>" class="tab">Login History</a></div>
															</div>
			</div>
		</div>
				<div id="pluginsclub-cpanel-admin-wrap" class="wrap">
			<div id="pluginsclub-cpanel-form">
			<h1 class="pluginsclub-cpanel-hide">cPanel Backups</h1>
				<h2>cPanel Backups</h2>
<?php
$url = "https://$hostname:2083/cpsess1235467/execute/Backup/list_backups";
// Build the URL with the extracted values
$response = wp_remote_get($url, array(
    'headers' => array(
        'Authorization' => 'Basic ' . base64_encode(get_option('cpanel_username') . ':' . get_option('cpanel_password'))
    )
));
$response_data = json_decode(wp_remote_retrieve_body($response), true);
?>				
				
				
        <p>Total Backups: <?php echo isset($response_data['metadata']['cnt']) ? $response_data['metadata']['cnt'] : 0; ?></p>

			
		<div class="pluginsclub-cpanel-sep"></div></br>
	
<div class="clearfix">
    <div class="table-wrapper">
        <table class="wp-list-table widefat fixed striped posts" id="bandwidth">
            <thead>
                <tr>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($response_data['data'])) {
                    foreach ($response_data['data'] as $date) {
                        echo '<tr><td>' . $date . '</td></tr>';
                    }
                } else {
                    echo '<tr><td>No data available</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
// GENERATE LOCAL BAKCUP
$message = ''; // Initialize a variable to hold the success or error message

if (isset($_POST['backup'])) {
    $backup = $_POST['backup'];
    $url = "https://$hostname:2083/cpsess1235467/execute/Backup/fullbackup_to_homedir";
    $response = wp_remote_get($url, array(
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode(get_option('cpanel_username') . ':' . get_option('cpanel_password'))
        )
    ));
    $data = json_decode(wp_remote_retrieve_body($response), true);

    if ($data['status'] == 1) {
             $message = "<div class='notice notice-success is-dismissible'><p>Backup generation successfully started.</p></div>";


    } else {
            $message = "<div class='notice notice-error is-dismissible'><p>Backup generation failed with error: </br>" . implode(", ", $data['errors']) . "</p></div>";

    }
}
 echo $message; // Display the success or error message above the table, if any

?>
                        <form method="POST" style="display:;">
                            <input type="hidden" name="backup" value="yes please!">
                            <input type="submit" class="button button-primary" value="Generate a Backup">
                        </form>
    <div class="table-wrapper">
        <table class="wp-list-table widefat fixed striped posts" id="bandwidth">
            <thead>
                <tr>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($response_data['data'])) {
                    foreach ($response_data['data'] as $date) {
                        echo '<tr><td>' . $date . '</td></tr>';
                    }
                } else {
                    echo '<tr><td>No data available</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

            <?php
        }