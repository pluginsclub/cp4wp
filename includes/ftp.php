<?php

// DB PAGE
function pluginsclub_cpanel_ftp_page() {

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
			<h1 class="pluginsclub-cpanel-hide">FTP Accounts</h1>
				<h2>FTP Accounts</h2>
        <p>
<?php

// Get FTP deamon info
$url1 = "https://$hostname:2083/cpsess1235467/execute/Ftp/get_ftp_daemon_info";
$response1 = wp_remote_get($url1, array(
    'headers' => array(
        'Authorization' => 'Basic ' . base64_encode(get_option('cpanel_username') . ':' . get_option('cpanel_password'))
    )
));
$response_data1 = json_decode(wp_remote_retrieve_body($response1), true);
$module_name = $response_data1['data']['name'];

// Get FTP port
$url2 = "https://$hostname:2083/cpsess1235467/execute/Ftp/get_port";
$response2 = wp_remote_get($url2, array(
    'headers' => array(
        'Authorization' => 'Basic ' . base64_encode(get_option('cpanel_username') . ':' . get_option('cpanel_password'))
    )
));
$response_data2 = json_decode(wp_remote_retrieve_body($response2), true);
$port = $response_data2['data']['port'];

echo "FTP Server: <b>" . ucwords(str_replace('-', ' ', $module_name)) . "</b> | Port: <b>" . $port . "</b>";


?>
</p>			
		<div class="pluginsclub-cpanel-sep"></div></br>
<?php
  echo '<div id="search-box-wrapper">
          <input type="text" id="DBAccountsInput" onkeyup="searchdb()" placeholder="Search FTP Accounts.." title="Start typing..">
        </div>';

?>  
<div id="create-db-form-container" style="display:none;">
    <table class="form-table" role="presentation">
    <h3>Create FTP Account</h3>
    <form id="create-db-form" method="POST">
       <tbody>
    <tr>
      <th scope="row">Username</th>
      <td>
        <input type="text" id="create_username" name="create_username" placeholder="username" title="Username">
      </td>
    </tr>
        <tr>
      <th scope="row">Domain</th>
      <td>
        <input type="text" id="create_domain" name="create_domain" placeholder="optional" title="Domain">
      </td>
    </tr>
        <tr>
      <th scope="row">Homedir</th>
      <td>
        <input type="text" id="create_homedir" name="create_homedir" placeholder="homedir" title="homedir">
      </td>
    </tr>
            <tr>
      <th scope="row">Password</th>
      <td>
        <input type="password" id="create_password" name="create_password" placeholder="strong password" title="Password">
      </td>
    </tr>
                <tr>
      <th scope="row">Quota</th>
      <td>
        <input type="number" id="create_quota" name="create_quota" placeholder="optional" min="0" title="Quota"> MB
      </td>
    </tr>
  </tbody>
  </table> 

<div class="pluginsclub-cpanel-sep pluginsclub-cpanel-sep-last"></div>
       <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Create"> 
       <a href="#" id="hide-form-link">Cancel</a></p>
    </form>
</div>
<a href="#" class="button" id="show-form-link">Create FTP Account</a>

<?php
// CREATE DATABASES

$messagecreate = ''; // Initialize a variable to hold the success or error message

if (isset($_POST['create_db'])) {
    $create_db = $_POST['create_db'];

    // Build the URL with the extracted values
    $url = "https://$hostname:2083/cpsess1235467/execute/Mysql/create_database?name=" . urlencode($username) . '_' . urlencode($create_db);
    $response = wp_remote_get($url, array(
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode(get_option('cpanel_username') . ':' . get_option('cpanel_password'))
        )
    ));
    $data = json_decode(wp_remote_retrieve_body($response), true);

    if ($data['status'] == 1) {
             $messagecreate = "<div class='notice notice-success is-dismissible'><p>Successfully created a new MySQL database <b>$create_db</b>.</p></div>";


    } else {
            $messagecreate = "<div class='notice notice-error is-dismissible'><p>Error creating new MySQL database <b>$create_db</b>: </br>" . implode(", ", $data['errors']) . "</p></div>";

    }
}
     
           
           
// CREATE DATABASE USERS


if (isset($_POST['create_user']) && isset($_POST['create_password'])) {
    $create_user = $_POST['create_user'];
    $create_password = $_POST['create_password'];
    // Build the URL with the extracted values

    $url = "https://$hostname:2083/cpsess1235467/execute/Mysql/create_user?name=" . urlencode($username) . '_' . urlencode($create_user) . '&password=' . urlencode($create_password);
    $response = wp_remote_get($url, array(
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode(get_option('cpanel_username') . ':' . get_option('cpanel_password'))
        )
    ));
    $data = json_decode(wp_remote_retrieve_body($response), true);

    if ($data['status'] == 1) {
             $messagecreateuser = "<div class='notice notice-success is-dismissible'><p>Successfully created a new MySQL user <b>$create_user</b>.</p></div>";


    } else {
            $messagecreateuser = "<div class='notice notice-error is-dismissible'><p>Error creating new MySQL user <b>$create_user</b>: </br>" . implode(", ", $data['errors']) . "</p></div>";

    }
}
 echo $messagecreateuser; // Display the success or error message above the table, if any    
                
                
// CHANGE DATABASE USERS PASSWORD


if (isset($_POST['change_user']) && isset($_POST['change_password'])) {
    $change_user = $_POST['change_user'];
    $change_password = $_POST['change_password'];
    // Build the URL with the extracted values
    $url = "https://$hostname:2083/cpsess1235467/execute/Mysql/set_password?user=" . urlencode($change_user) . '&password=' . urlencode($change_password);
    $response = wp_remote_get($url, array(
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode(get_option('cpanel_username') . ':' . get_option('cpanel_password'))
        )
    ));
    $data = json_decode(wp_remote_retrieve_body($response), true);

    if ($data['status'] == 1) {
             $messagechangeuser = "<div class='notice notice-success is-dismissible'><p>Successfully changed password for user <b>$change_user</b>.</p></div>";


    } else {
            $messagechangeuser = "<div class='notice notice-error is-dismissible'><p>Error changing password for user <b>$change_user</b>: </br>" . implode(", ", $data['errors']) . "</p></div>";

    }
}
 echo $messagechangeuser; // Display the success or error message above the table, if any    
                

// ASSIGN USERS TO DATABASES

if (isset($_POST['assign_user']) && isset($_POST['assign_db'])) {
    $assign_user = $_POST['assign_user'];
    $assign_db = $_POST['assign_db'];
    // Build the URL with the extracted values
    $url = "https://$hostname:2083/cpsess1235467/execute/Mysql/set_privileges_on_database?user=" . urlencode($assign_user) . urlencode($create_user) . '&database=' . urlencode($assign_db);
    $response = wp_remote_get($url, array(
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode(get_option('cpanel_username') . ':' . get_option('cpanel_password'))
        )
    ));
    $data = json_decode(wp_remote_retrieve_body($response), true);

    if ($data['status'] == 1) {
             $messagecreatepriv = "<div class='notice notice-success is-dismissible'><p>Successfully added MySQL user <b>$assign_user</b> to the database <b>$assign_db</b> and grant ALL privileges.</p></div>";


    } else {
            $messagecreatepriv = "<div class='notice notice-error is-dismissible'><p>Error assigning MySQL user <b>$assign_user</b> to the database <b>$assign_db</b>: </br>" . implode(", ", $data['errors']) . "</p></div>";

    }
}
 echo $messagecreatepriv; // Display the success or error message above the table, if any    
 
 
 
 
// REMOVE USERS FROM DATABASE


if (isset($_POST['remove_user']) && isset($_POST['remove_db'])) {
    $remove_user = $_POST['remove_user'];
    $remove_db = $_POST['remove_db'];
    // Build the URL with the extracted values
    $url = "https://$hostname:2083/cpsess1235467/execute/Mysql/revoke_access_to_database?user=" . urlencode($assign_user) . urlencode($remove_user) . '&database=' . urlencode($remove_db);
    $response = wp_remote_get($url, array(
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode(get_option('cpanel_username') . ':' . get_option('cpanel_password'))
        )
    ));
    $data = json_decode(wp_remote_retrieve_body($response), true);

    if ($data['status'] == 1) {
             $messageremove = "<div class='notice notice-success is-dismissible'><p>Successfully removed MySQL user <b>$assign_user</b> from the database <b>$assign_db</b> and revoke ALL privileges.</p></div>";


    } else {
            $messageremove = "<div class='notice notice-error is-dismissible'><p>Error removing MySQL user <b>$assign_user</b> from the database <b>$assign_db</b>: </br>" . implode(", ", $data['errors']) . "</p></div>";

    }
}
 echo $messageremove; // Display the success or error message above the table, if any    


// API CALL FOR THE TABLE
$api_url = "https://$hostname:2083/cpsess1235467/execute/Ftp/list_ftp_with_disk";
$args = array(
    'headers' => array(
        'Authorization' => 'Basic ' . base64_encode( "$username:$password" ),
        'Content-Type' => 'application/json'
    )
);

// Make API request and decode JSON response
$response = wp_remote_get( $api_url, $args );
$body = wp_remote_retrieve_body( $response );
$data = json_decode( $body, true );


// Display the list of users as a table
if ( $data && isset( $data['data'] ) && count( $data['data'] ) > 0 ) {
    ?>

    <div>
        <?php echo isset($messagecreate) ? $messagecreate : ''; ?>
        <?php echo isset($messagedelete) ? $messagedelete : ''; ?>
    </div>

    <table class="wp-list-table widefat striped" id="ftp">
        <thead>
            <tr>
                <th>Log in</th>
                <th>Usage / Quota</th>
                <th>Path</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach ( $data['data'] as $user ) {
                    ?>
                    <tr>
                        <?php 
        $used = ($user['humandiskused'] == 'None') ? '0' : $user['humandiskused'];
        $quota = ($user['humandiskquota'] == 'None') ? '∞' : $user['humandiskquota'];
        ?>
                        <td><?php echo $user['user']; ?> <span style="padding:5px 10px; background-color: #1a245b; color:white; border-radius: 25px;"><?php echo ucfirst(str_replace('_', ' ', $user['type'])); ?></span>
                        
                        <?php if ($user['type'] == 'sub') {  
                            ?>
    <button class="button-secondary delete-email" style="display: inline-block!important; vertical-align: middle;" data-user="<?php echo $user['user']; ?>">🗑️ Delete</button>

                        <div class="delete-email-popup" style="display: none;">
    <form method="post" action="">
        <p>Are you sure you want to delete the FTP account "<?php echo $user['user']; ?>"?</p>
        <input type="hidden" name="user" value="<?php echo $user['user']; ?>">
        <button type="submit" name="confirm-delete" class="button-primary">Confirm</button>
        <button type="button" name="cancel-delete" class="button-secondary">Cancel</button>
        </br>
    </form>
 </div>   
                            
                        <form method="POST" style="display:none;">
                            <input type="hidden" name="user" value="<?php echo $user['user']; ?>">
                            <input type="text" name="new_path" id="path" placeholder="New Path" value=<?php echo $user['dir']?>>
                            <input type="submit" class="button button-primary" value="Delete">
                        </form>
                        
                            <?php } ?>
                        
                        </td>
                        <td><?php echo $used; ?> / <?php echo $quota; ?> (<?php echo $user['diskusedpercent']; ?>%)
                        <?php if ($user['type'] == 'sub') {
                            ?> <button class="button-secondary" style="vertical-align: middle;">Change Quota</button>
                            <form method="POST" style="display:none;">
                            <input type="hidden" name="user" value="<?php echo $user['user']; ?>">
                            <input type="text" name="new_quota" placeholder="New Quota in MB">
                            <input type="submit" class="button button-primary" value="Change">
                        </form>
                        <?php
                        }
                            ?>
                        </br>
                           <?php
$diskusedpercent = $user['diskusedpercent'];
// Determine the color of the progress bar based on the diskusedpercent from response
if ($diskusedpercent < 70) {
  $progress_color = 'green';
} elseif ($diskusedpercent < 90) {
  $progress_color = 'orange';
} else {
  $progress_color = 'red';
}

// Display the progress bar
echo '<div style="width: 100%; background-color: #ddd; height: 10px; margin-top: 3px; ">
      <div style="width: '.$diskusedpercent.'%; max-width: 100%; background-color: '.$progress_color.'; height: 10px;"></div>
      </div>';
    ?>   
                        
                        </td>
                        <td><?php echo $user['dir']; if ($user['type'] == 'sub') {  
                            ?>
                            <button class="button-secondary" style="vertical-align: middle;">Change</button>
                        <form method="POST" style="display:none;">
                            <input type="hidden" name="user" value="<?php echo $user['user']; ?>">
                            <input type="text" name="new_path" id="path" placeholder="New Path" value=<?php echo $user['dir']?>>
                            <input type="submit" class="button button-primary" value="Change">
                        </form>
                        
                            <?php } ?>
                        </td>
                    </tr>
                    <?php
                }
            ?>
        </tbody>
    </table>
    <?php
} else {
    echo '<p>No FTP users found.</p>';
}
        ?>
    </div>
    <?php

// CHANGE PATH
$messagepath = ''; // Initialize a variable to hold the success or error message

if (isset($_POST['user']) && isset($_POST['new_path'])) {
    $user = $_POST['user'];
    $new_path = $_POST['new_path'];
    list($email, $new_domain) = explode('@', $_POST['email']);
    $url = "https://$hostname:2083/cpsess1235467/execute/Ftp/set_homedir?user=" . urlencode($user) . "&homedir=" . urlencode($new_path);
    $response = wp_remote_get($url, array(
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode(get_option('cpanel_username') . ':' . get_option('cpanel_password'))
        )
    ));
    $data = json_decode(wp_remote_retrieve_body($response), true);

    if ($data['status'] == 1) {
             $messagepath = "<div class='notice notice-success is-dismissible'><p>path successfully changed for <b>$user</b></p></div>";


    } else {
            $messagepath = "<div class='notice notice-error is-dismissible'><p>Error changing path for <b>$user</b> </br>" . implode(", ", $data['errors']) . "</p></div>";

    }
}
 echo $messagepath; // Display the success or error message above the table, if any    


// DELETE FTP ACCOUNT
if (isset($_POST['confirm-delete'])) {
    $user = $_POST['user'];
    $url = "https://$hostname:2083/cpsess1235467/execute/Ftp/delete_ftp?user=" . urlencode($user);
    $response = wp_remote_get($url, array(
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode(get_option('cpanel_username') . ':' . get_option('cpanel_password'))
        )
    ));
    if (!is_wp_error($response)) {
        echo '<div class="notice notice-success"><p>FTP account "' . esc_html($user) . '" deleted successfully.</p></div>';
    } else {
        echo '<div class="notice notice-error"><p>Error deleting FTP account: ' . esc_html($response->get_error_message()) . '</p></div>';
    }
}


// CHANGE QUOTA
$messagequota = ''; // Initialize a variable to hold the success or error message
if (isset($_POST['user']) && isset($_POST['new_quota'])) {
    $user = $_POST['user'];
    $new_quota = $_POST['new_quota'];
    $url = "https://$hostname:2083/cpsess1235467/execute/Ftp/set_quota?user=" . urlencode($user) . "&quota=" . urlencode($new_quota);
    $response = wp_remote_get($url, array(
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode(get_option('cpanel_username') . ':' . get_option('cpanel_password'))
        )
    ));
    $data = json_decode(wp_remote_retrieve_body($response), true);

    if ($data['status'] == 1) {
             $messagequota = "<div class='notice notice-success is-dismissible'><p>Disk quota successfully changed for <b>$user</b></p></div>";


    } else {
            $messagequota = "<div class='notice notice-error is-dismissible'><p>Error changing quota for <b>$user</b> </br>" . implode(", ", $data['errors']) . "</p></div>";

    }
}
 echo $messagequota; // Display the success or error message above the table, if any



// CREATE FTP ACCOUNTS

$messagecreate = ''; // Initialize a variable to hold the success or error message

if (isset($_POST['create_username']) && isset($_POST['create_password'])) {
    $create_username = $_POST['create_username'];
    $create_domain = $_POST['create_domain'];
    $create_homedir = $_POST['create_homedir'];
    $create_password = $_POST['create_password'];
    $create_quota = $_POST['create_quota'];

    // Build the URL with the extracted values
    $url = "https://$hostname:2083/cpsess1235467/execute/Ftp/add_ftp?user=" . urlencode($create_username) . "&pass=" . urlencode($create_password) . "&domain=" . urlencode($create_domain) . "&homedir=" . urlencode($create_homedir) . "&quota=" . urlencode($create_quota);
    $response = wp_remote_get($url, array(
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode(get_option('cpanel_username') . ':' . get_option('cpanel_password'))
        )
    ));
    $data = json_decode(wp_remote_retrieve_body($response), true);

    if ($data['status'] == 1) {
             $messagecreate = "<div class='notice notice-success is-dismissible'><p>Successfully created a new FTP account <b>$create_username</b></p></div>";


    } else {
            $messagecreate = "<div class='notice notice-error is-dismissible'><p>Error creating new FTP account <b>$create_username</b> </br>" . implode(", ", $data['errors']) . "</p></div>";

    }
}
 echo $messagecreate; // Display the success or error message above the table, if any
    
    
}