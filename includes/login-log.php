<?php
// LOGS PAGE
function pluginsclub_cpanel_loginlog_page() {
  $username = get_option('cpanel_username');
  $password = get_option('cpanel_password');
  $hostname = get_option('cpanel_host');

  $abuseipdb_key = get_option('abuseipdb_key');
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
			<h1 class="pluginsclub-cpanel-hide">cPanel Login History</h1>
				<h2>Login History</h2>
        <p>View up to last 20 successful cPanel logins and (optionally) receive email alerts when IP with bad reputation logs in.</p>
        		<div class="pluginsclub-cpanel-sep"></div></br>
<?php

  // Build the URL with the extracted values
  $url = "https://$hostname:2083/cpsess1235467/execute/Fileman/get_file_content?dir=%2Fhome%2F" . $username . "&file=.lastlogin";
  $response = wp_remote_get($url, array(
    'headers' => array(
        'Authorization' => 'Basic ' . base64_encode($username . ':' . $password)
    )
  ));
  $response_data = json_decode(wp_remote_retrieve_body($response), true);
  if (isset($response_data['data']['content'])) {
    $lines = explode("\n", $response_data['data']['content']);
    ?>

    <table class="wp-list-table widefat fixed" id="loginlog">
      <thead>
        <tr>
          <th colspan="">IP Address</th>
          <th>Last Login</th>
            <th colspan="">AbuseIPDB Reports</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($lines as $line) {
          $fields = explode(' ', $line);
          $ip_address = $fields[0];
          $last_login = date('d.m.Y H:i:s', strtotime($fields[2] . ' ' . $fields[3]));
          $abuseipdb_reports = '';
          if (!empty($abuseipdb_key)) {
            $abuseipdb_url = 'https://api.abuseipdb.com/api/v2/check?ipAddress=' . $ip_address . '&maxAgeInDays=90';
            $abuseipdb_response = wp_remote_get($abuseipdb_url, array(
              'headers' => array(
                  'Key' => $abuseipdb_key,
                  'Accept' => 'application/json'
              )
            ));
            $abuseipdb_data = json_decode(wp_remote_retrieve_body($abuseipdb_response), true);
            if (isset($abuseipdb_data['data']) && isset($abuseipdb_data['data']['totalReports'])) {
              $abuseipdb_reports = $abuseipdb_data['data']['totalReports'];

if ($abuseipdb_reports <= 10) {
  $class = 'green';
} elseif ($abuseipdb_reports <= 20) {
  $class = 'orange';
} else {
  $class = 'red';
  $email = get_option('cpanel_email');
}


              
if (isset($_POST['notify'])) {
    
  if ($abuseipdb_reports > 20) {  
    $email = get_option('admin_email'); // use admin email by default
    $subject = "🚨 cPanel login from IP address with bad reputation ($hostname)";

    $headers = array('Content-Type: text/html; charset=UTF-8');
    $headers[] = 'From: CPWP Plugin <noreply@plugins.club>';

    $body = "<html><head><style>
              body {font-family: Arial, sans-serif; font-size: 14px; line-height: 1.4;}
              p {margin-top: 0;}
              small {text-align: center;}
              a {color: #337ab7; text-decoration: none;}
            </style></head><body>";
    $body .= "<h3>There was a cPanel login from an IP address with a bad reputation.</h3>";
    $body .= "<p>IP address: <b>$ip_address</b></p>";
    $body .= "<p>Last login: <b><a href='" . get_site_url() . "/wp-admin/admin.php?page=cpanel_loginlog' target='_blank'>$last_login</a></b></p>";
    $body .= "<p>AbuseIPDB reports: <b><a href='https://www.abuseipdb.com/check/$ip_address' target='_blank'>$abuseipdb_reports</a></b></p>";
    $body .= "</br><hr></br><small><a href='" . get_site_url() . "/wp-admin/admin.php?page=cpanel_loginlog' target='_blank'>Click here to disable these email notifications.</a></small>";
    $body .= "</body></html>";
    
    wp_mail($email, $subject, $body, $headers);
    echo "<div class='notice notice-success'><p>Email notification sent to $email.</p></div>";
  }
}



            }
  else {



        }
      }
      ?>
      <tr class="<?php echo $class; ?>">
            <td><?php echo $ip_address; ?> <button class="button button-secondary" onclick="copyToClipboard('<?php echo $ip_address; ?>', this)">Copy</button></td>

            <td><?php echo $last_login; ?></td>

<?php 
 if (!empty($abuseipdb_key)) { ?>
              <td><a href="https://www.abuseipdb.com/check/<?php echo $ip_address; ?>" target="_blank"><?php echo $abuseipdb_reports; ?></a></td>
            <?php }
            else {
            ?>   
                 <td><a href="https://www.abuseipdb.com/check/<?php echo $ip_address; ?>" target="_blank">Check Abuse Reports</a></td>
            <?php
                
            }
            
            ?>
          </tr>
        <?php } ?>
      </tbody>
    </table>
<?php
  $abuseipdb_key = get_option('abuseipdb_key');

  if (isset($_POST['save_key'])) {
    $abuseipdb_key = $_POST['abuseipdb_key'];
    update_option('abuseipdb_key', $abuseipdb_key);
  }
?>

<div id="create-db-form-container">
    <table class="form-table" role="presentation">
    <h3>AbuseIPDB API Key</h3>
    <p>Optionally you can generate a free AbuseIPDB API key here: <a href="https://www.abuseipdb.com/account/api" target="_blank">https://www.abuseipdb.com/account/api</a> so that the plugin can check Abuse Reports for each IP address that logs into your cPanel account and notify you via email if any IP with bad reputation logs in (cPanel account is most likely compromised).</p>
<style>
@media (min-width: 750px) {
#abuseipdb_key {
    width:82ch;
}
}
</style>
    <form method="POST">
       <tbody>
    <tr>
      <th scope="row">AbuseIPDB API Key:</th>
      <td>
        <input type="text" id="abuseipdb_key" name="abuseipdb_key" value="<?php echo esc_attr($abuseipdb_key); ?>">
        <input type="hidden" name="email" value="<?php echo get_option('admin_email'); ?>">
      </td>
    </tr>
  </tbody>
  </table> 

<div class="pluginsclub-cpanel-sep pluginsclub-cpanel-sep-last"></div>
       <p class="submit"><input type="submit" name="save_key" id="save_key" class="button button-primary" value="Save API Key">
        <!--input type="submit" name="notify" id="notify" class="button button-secondary" value="Email Alert!"></p-->
    </form>
</div>

<script>
function copyToClipboard(text, button) {
  const el = document.createElement('textarea');
  el.value = text;
  document.body.appendChild(el);
  el.select();
  document.execCommand('copy');
  document.body.removeChild(el);
  button.innerHTML = 'Copied!';
}
</script>
<style>
tr.green {
  /*background-color: #d0ffd0;*/
}

tr.orange {
  background-color: #ffd0b0;
}

tr.red {
  background-color: #ffb0b0;
}

</style>
    
  <?php
  } else {
    echo "No login data found.";
  }
}
?>
