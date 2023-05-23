<?php

// SETTINGS PAGE
function pluginsclub_cpanel_page() {
?>



<div id="pluginsclub-cpanel">
					<div id="pluginsclub-cpanel-header">
			<div id="pluginsclub-cpanel-header-title">
				<div id="pluginsclub-cpanel-header-title-image">
<!--h1><a href="<?php //echo admin_url( 'admin.php?page=cpanel' ); ?>" class="logo">CP4WP</a></h1></div-->
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
			<h1 class="pluginsclub-cpanel-hide">cPanel Login Information</h1>
			<form id="pluginsclub-cpanel-form" action="options.php" method="POST">
				<h2>cPanel Login Information</h2>
		<p>
			Add your cPanel username and password to enable the dahboard widgets and Email Accounts management.		</p>
			
			
		<div class="pluginsclub-cpanel-sep"></div>
		
<table class="form-table" role="presentation">

<form method="post" action="options.php">
                <?php
            settings_fields( 'cpanel_settings' );
            do_settings_sections( 'cpanel_settings' );
            ?>
   <tbody>
      <tr>
         <th scope="row">cPanel Username</th>
         <td>		<input name="cpanel_username" type="text" class="regular-text" value="<?php echo esc_attr( get_option( 'cpanel_username' ) ); ?>" placeholder="cpanel_user">
         </td>
      </tr>
      <tr>
         <th scope="row">cPanel Password</th>
         <td>		<input name="cpanel_password" type="password" class="regular-text" value="<?php echo esc_attr( get_option( 'cpanel_password' ) ); ?>" placeholder="cpanel_pass">
         </td>
      </tr>
              <tr>
            <th scope="row">cPanel Hostname</th>
            <td>
                <?php
                $cpanel_hostname_value = '';
                if (get_option('cpanel_host')) {
                    // If cpanel_hostname option exists, use it as the value
                    $cpanel_hostname_value = esc_attr(get_option('cpanel_host'));
                } else {
                    // If cpanel_hostname option does not exist, use $hostname as the value
                    $cpanel_hostname_value = gethostname();
                }
                ?>
                <input name="cpanel_host" type="text" class="regular-text" value="<?php echo $cpanel_hostname_value; ?>" placeholder="cpanel_host">
            </td>
        </tr>
   </tbody>
</table>
<div class="pluginsclub-cpanel-sep pluginsclub-cpanel-sep-last"></div>
<?php submit_button(); ?>
</form>
				
				
<?php
// Check if the plugin settings form is submitted
if (isset($_POST['plugin_settings_submit'])) {
    // Save the settings
    update_option('cp4wp_enable_overview_widgets', isset($_POST['enable_overview_widgets']));
    update_option('cp4wp_enable_server_widgets', isset($_POST['enable_server_widgets']));
    update_option('cp4wp_enable_resources', isset($_POST['enable_resources']));
    update_option('cp4wp_enable_mysql', isset($_POST['enable_mysql']));
    update_option('cp4wp_enable_domains', isset($_POST['enable_domains']));
    update_option('cp4wp_enable_emails', isset($_POST['enable_emails']));
    update_option('cp4wp_enable_postgresql', isset($_POST['enable_postgresql']));
    update_option('cp4wp_enable_bandwidth', isset($_POST['enable_bandwidth']));
    update_option('cp4wp_enable_ftp', isset($_POST['enable_ftp']));
    update_option('cp4wp_enable_login_log', isset($_POST['enable_login_log']));
    // Add other options for each page
    
    // Redirect to the settings page after saving
    wp_redirect(admin_url('admin.php?page=cpanel'));
    exit;
}

    // Retrieve the options for each page, set enabled by default
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

// Retrieve other options for each page

// Display the settings page
?>


					<!--div id="pluginsclub-cpanel-product-education-admin_settings_bottom" class="pluginsclub-cpanel-product-education" data-product-education-id="admin_settings_bottom" data-nonce="424ce1f816">

			<div class="pluginsclub-cpanel-product-education-content">


				<h3>Manage cPanel Email Accounts and Then Some!</h3>

						<p>
			<a target="_blank" href="https://plugins.club/cpanel-for-wordpress/">CP4WP Premium</a> allows you to modify PostgreSQL & MySQL Databases, Monitor Resource & Bandwidth usage, review cPanel login logs, get email alerts, plus much more!		</p>

		<p>
			Includes premium support, regular updates and guaranteed compatibility with all future WordPress and cPanel versions.
		</p>

		<div class="pluginsclub-cpanel-product-education-images-row">
		    
		    							<div class="pluginsclub-cpanel-product-education-images-row-image">
						<a href="<?php echo plugin_dir_url( __FILE__ ) . 'images/emails.gif'; ?>" data-lity="" data-lity-desc="Manage Email Addresses">
		<img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/emails.gif'; ?>" alt="">
	</a>
					<span>Manage Email Addresses</span>
				</div>
							<div class="pluginsclub-cpanel-product-education-images-row-image">
							    						<a href="<?php echo plugin_dir_url( __FILE__ ) . 'images/login_log.png'; ?>" data-lity="" data-lity-desc="Manage Email Addresses">
		<img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/login_log.png'; ?>" alt="">
</a>
					<span>Login Logs and Alerts</span>
				</div>
							<div class="pluginsclub-cpanel-product-education-images-row-image">
							    
							    				<a href="<?php echo plugin_dir_url( __FILE__ ) . 'images/databases.gif'; ?>" data-lity="" data-lity-desc="Manage Email Addresses">
		<img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/databases.gif'; ?>" alt="">
</a>
					<span>Manage MySQL Databases</span>
				</div>
					</div>

		
									<a class="pluginsclub-cpanel-product-education-btn button button-primary" target="_self" href="https://plugins.club/cpanel-for-wordpress/">
						Buy Now ($19)					</a>
								</div>
		</div-->
				</div>


<?php

// Show page & widgets settings only if user already added cpanel login information
$cpanel_username = get_option('cpanel_username');
$cpanel_password = get_option('cpanel_password');

if (!empty($cpanel_username) && !empty($cpanel_password)) {
    ?>
				<div id="pluginsclub-cpanel-admin-wrap" class="wrap">
			<h1 class="pluginsclub-cpanel-hide">Enable Features</h1>
			<form id="pluginsclub-cpanel-form" method="POST">
				<h2>Enable cPanel Features</h2>
		<p>
			Here you can disable the cPanel features that you dont need.		</p>
			
			
		<div class="pluginsclub-cpanel-sep"></div>
		

<table class="form-table" role="presentation">

   <tbody>
<tr>
                <th scope="row">Dashboard Widgets:</th>
                <td style="display: grid;">
                    <label for="enable_overview_widgets" style="text-align: left!important;">
                        <input type="checkbox" name="enable_overview_widgets" id="enable_overview_widgets" <?php checked($enable_overview_widgets); ?>>
                        Enable Overview Widget
                    </label>
                    <label for="enable_server_widgets" style="text-align: left!important;">
                        <input type="checkbox" name="enable_server_widgets" id="enable_server_widgets" <?php checked($enable_server_widgets); ?>>
                        Enable Server Resources Widget
                    </label>
                </td>
            </tr>
<tr>
                <th scope="row">cPanel Pages:</th>
                <td style="display: grid;">
                    <label for="enable_emails" style="text-align: left!important;">
                        <input type="checkbox" name="enable_emails" id="enable_emails" <?php checked($enable_emails); ?>>
                        Enable Emails Page
                    </label>
                    <label for="enable_domains" style="text-align: left!important;">
                        <input type="checkbox" name="enable_domains" id="enable_domains" <?php checked($enable_domains); ?>>
                        Enable Domains Page
                    </label>
                    <label for="enable_ftp" style="text-align: left!important;">
                        <input type="checkbox" name="enable_ftp" id="enable_ftp" <?php checked($enable_ftp); ?>>
                        Enable FTP Page
                    </label>
                    <label for="enable_mysql" style="text-align: left!important;">
                        <input type="checkbox" name="enable_mysql" id="enable_mysql" <?php checked($enable_mysql); ?>>
                        Enable MySQL Page
                    </label>
                    <label for="enable_postgresql" style="text-align: left!important;">
                        <input type="checkbox" name="enable_postgresql" id="enable_postgresql" <?php checked($enable_postgresql); ?>>
                        Enable PostgreSQL Page
                    </label>
                    <label for="enable_resources" style="text-align: left!important;">
                        <input type="checkbox" name="enable_resources" id="enable_resources" <?php checked($enable_resources); ?>>
                        Enable Resources Page
                    </label>
                    <label for="enable_bandwidth" style="text-align: left!important;">
                        <input type="checkbox" name="enable_bandwidth" id="enable_bandwidth" <?php checked($enable_bandwidth); ?>>
                        Enable Bandwidth Page
                    </label>
                    <label for="enable_login_log" style="text-align: left!important;">
                        <input type="checkbox" name="enable_login_log" id="enable_login_log" <?php checked($enable_login_log); ?>>
                        Enable Login History Page
                    </label>
                </td>
            </tr>       







   </tbody>
</table>
<div class="pluginsclub-cpanel-sep pluginsclub-cpanel-sep-last"></div>
        <?php submit_button('Save Settings', 'button button-primary', 'plugin_settings_submit'); ?>
</form>    



				
				

				</div>    
    
<?php
}

?>
    
    
    
    
    

</div>



<?php
}

