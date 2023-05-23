<?php

// WEBMAIL AUTO LOGIN
if (isset($_POST['login_email']) && isset($_POST['login_domain'])) {
  $hostname = get_option('cpanel_host');
    $domain = $_POST['login_domain'];
    $ip_address = $_SERVER['REMOTE_ADDR'];
    list($email, $new_domain) = explode('@', $_POST['login_email']);
    $createSessionUrl = "https://$hostname:2083/cpsess1235467/execute/Session/create_webmail_session_for_mail_user?login=" . urlencode($email) . "&domain=" . urlencode($domain) . "&remote_address=" . $ip_address;

    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "Authorization: Basic " . base64_encode(get_option('cpanel_username') . ':' . get_option('cpanel_password')) . "\r\n",
        ],
    ]);

    $response = file_get_contents($createSessionUrl, false, $context);
    $data = json_decode($response, true);
    $session = $data['data']['session'];
    $token = $data['data']['token'];
    $postUrl = "https://$hostname:2096" . $token."/login";
    $postData = "session=" . urlencode($session);
    $url = 'https://'.$hostname.':2096'.$token.'/login?session='.urlencode($session);
    header('Location: '.$url);
}

// EMAILS SETTINGS PAGE
function pluginsclub_cpanel_emails_page() {

    $username = get_option('cpanel_username');
    $password = get_option('cpanel_password');
  $hostname = get_option('cpanel_host');
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
			<h1 class="pluginsclub-cpanel-hide">Email Accounts</h1>
				<h2>Email Accounts</h2>
        <p>This page lists all cPanel Email Accounts and allows you to manage them: create new accounts, change password, delete them, etc.</p>

			
		<div class="pluginsclub-cpanel-sep"></div></br>

<?php


// CREATE EMAIL ACCOUNTS
if (isset($_POST['create_email']) && isset($_POST['create_password'])) {
    $new_password = $_POST['create_password'];
    list($email, $new_domain) = explode('@', $_POST['create_email']);

    // Build the URL with the extracted values
    $url = "https://$hostname:2083/cpsess1235467/execute/Email/add_pop?email=" . urlencode($email) . "&password=" . urlencode($new_password) . "&domain=" . urlencode($new_domain);
    $response = wp_remote_get($url, array(
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode(get_option('cpanel_username') . ':' . get_option('cpanel_password'))
        )
    ));
    $data = json_decode(wp_remote_retrieve_body($response), true);

    if ($data['status'] == 1) {
             $messagecreate = "<div class='notice notice-success is-dismissible'><p>Successfully created a new email account <b>$create_email</b></p></div>";


    } else {
            $messagecreate = "<div class='notice notice-error is-dismissible'><p>Error creating new email account <b>$create_email</b> </br>" . implode(", ", $data['errors']) . "</p></div>";

    }
}
 echo $messagecreate; // Display the success or error message above the table, if any




// CHANGE QUOTA
if (isset($_POST['email']) && isset($_POST['new_quota'])) {
    $email = $_POST['email'];
    $new_quota = $_POST['new_quota'];
    list($email, $new_domain) = explode('@', $_POST['email']);
    $url = "https://$hostname:2083/cpsess1235467/execute/Email/edit_pop_quota?email=" . urlencode($email) . "&domain=" . urlencode($new_domain). "&quota=" . urlencode($new_quota);
    $response = wp_remote_get($url, array(
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode(get_option('cpanel_username') . ':' . get_option('cpanel_password'))
        )
    ));
    $data = json_decode(wp_remote_retrieve_body($response), true);

    if ($data['status'] == 1) {
             $messagequota = "<div class='notice notice-success is-dismissible'><p>Disk quota successfully changed for <b>$email@$new_domain</b></p></div>";


    } else {
            $messagequota = "<div class='notice notice-error is-dismissible'><p>Error changing quota for <b>$email@$new_domain</b> </br>" . implode(", ", $data['errors']) . "</p></div>";

    }
}
 echo $messagequota; // Display the success or error message above the table, if any



// CHANGE PASSWORD
if (isset($_POST['email']) && isset($_POST['new_password'])) {
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];
    list($email, $new_domain) = explode('@', $_POST['email']);
    $url = "https://$hostname:2083/cpsess1235467/execute/Email/passwd_pop?email=" . urlencode($email) . "&domain=" . urlencode($new_domain). "&password=" . urlencode($new_password);
    $response = wp_remote_get($url, array(
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode(get_option('cpanel_username') . ':' . get_option('cpanel_password'))
        )
    ));
    $data = json_decode(wp_remote_retrieve_body($response), true);

    if ($data['status'] == 1) {
             $message = "<div class='notice notice-success is-dismissible'><p>Password successfully changed for <b>$email@$new_domain</b></p></div>";


    } else {
            $message = "<div class='notice notice-error is-dismissible'><p>Error changing password for <b>$email@$new_domain</b> </br>" . implode(", ", $data['errors']) . "</p></div>";

    }
}
 echo $message; // Display the success or error message above the table, if any

// LIST ALL EMAIL ACCOUNTS FROM CPANEL
// Call the cPanel API to retrieve a list of email accounts
$url = "https://$hostname:2083/cpsess1235467/execute/Email/list_pops_with_disk";
$response = wp_remote_get($url, array(
    'headers' => array(
        'Authorization' => 'Basic ' . base64_encode(get_option('cpanel_username') . ':' . get_option('cpanel_password'))
    )
));
$data = json_decode(wp_remote_retrieve_body($response), true);

?>
<div>
<div>


<?php
if (is_array($data['data']) && count($data['data']) > 10) {
  echo '<div id="search-box-wrapper">
          <input type="text" id="EmailAccountsInput" onkeyup="searchEmailAccounts()" placeholder="Search Email Accounts.." title="Type in an email account">
        </div>';
}
?>

<div id="create-email-form-container" style="display:none;">
<table class="form-table" role="presentation">


<form id="create-email-form" method="POST">
  <tbody>
    <tr>
      <th scope="row">Email Address</th>
      <td>
        <input type="text" name="create_email" placeholder="stefan@example.com" title="Email address in user@domain.tld format">
        <input type="hidden" name="new_domain" value="<?php echo $new_domain; ?>">
      </td>
    </tr>
    <tr>
      <th scope="row">Password</th>
      <td>
        <div style="position:relative">
          <input type="password" name="create_password" id="password" placeholder="Set Password">
          <span id="password-toggle" class="dashicons dashicons-visibility"></span>
        </div>
      </td>
    </tr>
  </tbody>
  </table>

<div class="pluginsclub-cpanel-sep pluginsclub-cpanel-sep-last"></div>
       <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Create"> 
       <a href="#" id="hide-form-link">Cancel</a></p>
</form>
    
</div>
<a href="#" class="button" id="show-form-link">Create Email</a>

</div>
    <table class="wp-list-table widefat striped posts" id="mejlovi">
        <thead>
            <tr>
                <th>Email</th>
                <th class="desktop">Webmail</th>
                <th class="desktop">Size</th>
                <th class="desktop">Login / Incomming</th>
                <th class="desktop" id="password">Modify Account</th>
                <th class="mobile" id="password">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (is_array($data['data'])) {
                foreach ($data['data'] as $account) {
                    ?>
                    <tr>
                        <td><?php echo $account['email']; ?> <span class="mobile">
                            </br>
                        <?php echo $account['humandiskused']; ?> / <?php echo $account['humandiskquota']; ?> (<?php echo $account['diskusedpercent']; ?>%)
                        <?php
                        $email_percent = $account['diskusedpercent'];
// Determine the color of the progress bar based on the diskusedpercent from response
if ($email_percent < 70) {
  $progress_color = 'green';
} elseif ($email_percent < 90) {
  $progress_color = 'orange';
} else {
  $progress_color = 'red';
}

// Display the progress bar
echo '<div style="width: 100%; background-color: #ddd; height: 10px; margin-top: 3px; ">
      <div style="width: '.$email_percent.'%; max-width: 100%; background-color: '.$progress_color.'; height: 10px;"></div>
      </div>';
    ?>                    
                        </span></td>
                        <td class="desktop">                            
                        <form method="POST">
                            <input type="hidden" name="login_email" value="<?php echo $account['email']; ?>">
                            <input type="hidden" name="login_domain" value="<?php echo $account['domain']; ?>">
                            <input type="submit" class="button button-primary" value="LOGIN">
                        </form></td>
                        <td class="desktop"><?php echo $account['humandiskused']; ?> / <?php echo $account['humandiskquota']; ?> (<?php echo $account['diskusedpercent']; ?>%)
                        <?php
                        $email_percent = $account['diskusedpercent'];
// Determine the color of the progress bar based on the diskusedpercent from response
if ($email_percent < 70) {
  $progress_color = 'green';
} elseif ($email_percent < 90) {
  $progress_color = 'orange';
} else {
  $progress_color = 'red';
}

// Display the progress bar
echo '<div style="width: 100%; background-color: #ddd; height: 10px; margin-top: 3px; border-radius: 10px;">
      <div style="width: '.$email_percent.'%; max-width: 100%; background-color: '.$progress_color.'; height: 10px; border-radius: 10px;"></div>
      </div>';
    ?>                    
                        </td>
                        <td class="desktop"><?php echo $account['suspended_login'] == 0 ? '✔️' : '❌'; ?> <?php echo $account['suspended_incoming'] == 0 ? '✔️' : '❌'; ?></td>
                        <td class="grid">

 <span class="mobile">
    <form method="POST">
                            <input type="hidden" name="login_email" value="<?php echo $account['email']; ?>">
                            <input type="hidden" name="login_domain" value="<?php echo $account['domain']; ?>">
                            <!--input type="submit" class="button button-primary" value="LOGIN"-->
                            <button class="button-primary" style="display: inline-block!important; width: 100%;" type="submit">Webmail</button>

                            </form></span>
                        <button class="button-secondary">Reset Password</button>
                        <form method="POST" style="display:none;">
                            <input type="hidden" name="email" value="<?php echo $account['email']; ?>">
                            <input type="text" name="new_password" id="password" placeholder="New Password">
                            <input type="submit" class="button button-primary" value="Change">
                        </form>
                        <button class="button-secondary">Change Quota</button>
                        <form method="POST" style="display:none;">
                            <input type="hidden" name="email" value="<?php echo $account['email']; ?>">
                            <input type="text" name="new_quota" placeholder="New Quota in MB">
                            <input type="submit" class="button button-primary" value="Change">
                        </form>
                           <button class="button-secondary delete-email" style="display: inline-block!important;" data-email="<?php echo $account['email']; ?>">🗑️ Delete</button>

                        <div class="delete-email-popup" style="display: none;">
    <form method="post" action="">
        <p>Are you sure you want to delete the email account "<?php echo $account['email']; ?>"?</p>
        <input type="hidden" name="email" value="<?php echo $account['email']; ?>">
        <button type="submit" name="confirm-delete" class="button-primary">Confirm</button>
        <button type="button" name="cancel-delete" class="button-secondary">Cancel</button>
        </br>
    </form>
 </div>   
                    </td>
                </tr>
                <?php
            }
        }
        ?>
    </tbody>
</table>
<?php
// DELETE EMAIL ACCOUNT
if (isset($_POST['confirm-delete'])) {
    $email = $_POST['email'];
    $url = "https://$hostname:2083/cpsess1235467/execute/Email/delete_pop?email=" . urlencode($email);
    $response = wp_remote_get($url, array(
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode(get_option('cpanel_username') . ':' . get_option('cpanel_password'))
        )
    ));
    if (!is_wp_error($response)) {
        echo '<div class="notice notice-success"><p>Email account "' . esc_html($email) . '" deleted successfully.</p></div>';
    } else {
        echo '<div class="notice notice-error"><p>Error deleting email account: ' . esc_html($response->get_error_message()) . '</p></div>';
    }
}
?>

</div>

<?php
}

