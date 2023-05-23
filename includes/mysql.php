<?php

// DB PAGE
function pluginsclub_cpanel_mysql_page() {

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
			<h1 class="pluginsclub-cpanel-hide">MySQL Databases</h1>
				<h2>MySQL Databases</h2>
        <p>
<?php

// DISPLAY HOSTNAME AND DB VERSION
    $url = "https://$hostname:2083/cpsess1235467/execute/Mysql/get_server_information";
    $response = wp_remote_get($url, array(
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode(get_option('cpanel_username') . ':' . get_option('cpanel_password'))
        )
    ));
    $data = json_decode(wp_remote_retrieve_body($response), true);        
                
$response_data = json_decode(wp_remote_retrieve_body($response), true);
$host = $response_data['data']['host'];
$version = $response_data['data']['version'];

echo "Host: <b>" . $host . "</b> | Version: <b>" . ucfirst(str_replace('_', ' ', $version)) . "</b>";

?>
</p>			
		<div class="pluginsclub-cpanel-sep"></div></br>
<?php
  echo '<div id="search-box-wrapper">
          <input type="text" id="DBAccountsInput" onkeyup="searchdb()" placeholder="Search Databases.." title="Type in database name">
        </div>';

?>  
<div id="create-db-form-container" style="display:none;">
    <table class="form-table" role="presentation">
    <h3>Create a MySQL Database</h3>
    <form id="create-db-form" method="POST">
       <tbody>
    <tr>
      <th scope="row">Database Name</th>
      <td>
        <b><?php echo $username . '_'; ?></b><input type="text" id="create_db" name="create_db" placeholder="db name" title="Database Name">
      </td>
    </tr>
  </tbody>
  </table> 

<div class="pluginsclub-cpanel-sep pluginsclub-cpanel-sep-last"></div>
       <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Create"> 
       <a href="#" id="hide-form-link">Cancel</a></p>
    </form>
</div>
<a href="#" class="button" id="show-form-link">New Database</a>


<div id="create-user-form-container" style="display:none;">
    <h3>Create a MySQL User</h3>
    <table class="form-table" role="presentation">

<form id="create-user-form" method="POST">
  <tbody>
    <tr>
      <th scope="row">Username</th>
      <td>
        <b><?php echo $username . '_'; ?></b><input type="text" id="create_user" name="create_user" placeholder="username" title="Username">
      </td>
    </tr>
    <tr>
      <th scope="row">Password</th>
      <td>
        <div style="position:relative">
          <input type="text" name="create_password" placeholder="Set Password">
        </div>
      </td>
    </tr>
  </tbody>
  </table>
<div class="pluginsclub-cpanel-sep pluginsclub-cpanel-sep-last"></div>
       <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Create"> 
       <a href="#" id="hide-user-form-link">Cancel</a></p>
</form>
    
</div>
<a href="#" class="button" id="show-user-form-link">New User</a>


<div id="change-user-form-container" style="display:none;">
<h3>Change Password for a MySQL User</h3>
<table class="form-table" role="presentation">
<?php
// Set up API request parameters
        $api_url = "https://$hostname:2083/cpsess1235467/execute/Mysql/list_users";
        $args = array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode( "$username:$password" ),
                'Content-Type' => 'application/json'
            )
        );
        // Make API request and decode JSON response
        $response = wp_remote_get( $api_url, $args );
// Decode the JSON response
$response_data = json_decode($response['body'], true);

// Extract all users from the response
$users = array_column($response_data['data'], 'user');

// Create the dropdown list
$options = '';
foreach ($users as $user) {
    $options .= '<option value="' . $user . '">' . $user . '</option>';
}
?>
<form id="change-user-form" method="POST">
  <tbody>
    <tr>
      <th scope="row">Username</th>
      <td>
        <?php
echo '<select name="change_user" id="change_user">';
echo $options;
echo '</select>';
?>
      </td>
    </tr>
    <tr>
      <th scope="row">New Password</th>
      <td>
        <div style="position:relative">
          <input type="text" name="change_password" placeholder="New Password">
        </div>
      </td>
    </tr>
  </tbody>
  </table>
<div class="pluginsclub-cpanel-sep pluginsclub-cpanel-sep-last"></div>
       <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Change"> 
       <a href="#" id="hide-change-form-link">Cancel</a></p>
</form>

</div>
<a href="#" class="button" id="show-change-form-link">Reset Password</a>
<?php
// Set up API request parameters
        $api_url = "https://$hostname:2083/cpsess1235467/execute/Mysql/list_users";
        $args = array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode( "$username:$password" ),
                'Content-Type' => 'application/json'
            )
        );
        // Make API request and decode JSON response
        $response = wp_remote_get( $api_url, $args );
// Decode the JSON response
$response_data = json_decode($response['body'], true);

// Extract all users from the response
$users = array_column($response_data['data'], 'user');

// Create the dropdown list
$options = '';
foreach ($users as $user) {
    $options .= '<option value="' . $user . '">' . $user . '</option>';
}
/////// LIST DATABASES
// Set up API request parameters
$api_url2 = "https://$hostname:2083/cpsess1235467/execute/Mysql/list_databases";
$args2 = array(
    'headers' => array(
        'Authorization' => 'Basic ' . base64_encode( "$username:$password" ),
        'Content-Type' => 'application/json'
    )
);
// Make API request and decode JSON response
$response2 = wp_remote_get( $api_url2, $args2 );
$data = json_decode( wp_remote_retrieve_body( $response2 ), true );

// Get list of databases
$databases = array_column( $data['data'], 'database' );

?>
<div id="create-assign-form-container" style="display:none;">
<h3>Assign a User to MySQL Database</h3>
<table class="form-table" role="presentation">


<form id="assign-db-user-form" method="POST">
  <tbody>
    <tr>
      <th scope="row">Username</th>
      <td>
        <?php
echo '<select name="assign_user" id="assign_user">';
echo $options;
echo '</select>'; ?>
      </td>
    </tr>
    <tr>
      <th scope="row">Database</th>
      <td>
        <div style="position:relative">
          <?php
// Display dropdown field for databases
echo '<select name="assign_db" id="assign_db">';
foreach ( $databases as $database ) {
    echo '<option value="' . $database . '">' . $database . '</option>';
}
echo '</select>';
?>
        </div>
      </td>
    </tr>
  </tbody>
  </table>
  <div class="pluginsclub-cpanel-sep pluginsclub-cpanel-sep-last"></div>
      <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Assign"> <a href="#" id="hide-assign-form-link">Cancel</a></p>
</form>

</div>
<a href="#" class="button" id="show-assign-form-link">Grant Privileges</a>





<div id="create-remove-form-container" style="display:none;">
    <h3>Remove a user from MySQL Database</h3>
    <table class="form-table" role="presentation">
    <form id="remove-db-user-form" method="POST">
  <tbody>
    <tr>
      <th scope="row">Username</th>
      <td>
        <?php
// Output the form with the dropdown list
echo '<select name="remove_user" id="remove_user">';
echo $options;
echo '</select>';
?>
      </td>
    </tr>
    <tr>
      <th scope="row">Database</th>
      <td>
        <div style="position:relative">
          <?php
// Display dropdown field for databases
echo '<select name="remove_db" id="remove_db">';
foreach ( $databases as $database ) {
    echo '<option value="' . $database . '">' . $database . '</option>';
}
echo '</select>';
?>
        </div>
      </td>
    </tr>
  </tbody>
  </table>   

<div class="pluginsclub-cpanel-sep pluginsclub-cpanel-sep-last"></div>
             <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Remove"> <a href="#" id="hide-remove-form-link">Cancel</a></p>
</form>
</div>

<a href="#" class="button" id="show-remove-form-link">Revoke Privileges</a>
<?php $phpmyadmin_link = "https://" . $hostname . ":2083/login/?user=" . $username . "&pass=" . $password . "&goto_uri=/3rdparty/phpMyAdmin/index.php" ?>
<a href="<?php echo $phpmyadmin_link ?>" target="_blank" class="button" id="phpmyadmin"><img src="<?php echo plugin_dir_url( __FILE__ ) . 'icons/phpmyadmin_logo.png'; ?>" style="vertical-align: middle;" alt=""></a>

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
$api_url = "https://$hostname:2083/cpsess1235467/execute/Mysql/list_databases";
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


// Display the list of databases as a table
if ( $data && isset( $data['data'] ) && count( $data['data'] ) > 0 ) {
    ?>

    <div>
        <?php echo isset($messagecreate) ? $messagecreate : ''; ?>
        <?php echo isset($messagedelete) ? $messagedelete : ''; ?>
    </div>

    <table class="wp-list-table widefat striped" id="baze">
        <thead>
            <tr>
                <th>Database</th>
                <th>Users</th>
                <th>Size</th>
            </tr>
        </thead>
        <tbody>
            <?php
        
        
        foreach ( $data['data'] as $db ) {
    ?>
    <tr>
        <td><?php echo $db['database']; ?></td>
        <td><?php echo implode( ', ', $db['users'] ); ?></td>
        <td><?php echo size_format( $db['disk_usage'] ); ?></td>
    </tr>
    <?php

            }
            ?>
        </tbody>
    </table>
            <?php
        } else {
            echo '<p>No MySQL databases found.</p>';
        }
        
        ?>
    </div>
    <?php
}