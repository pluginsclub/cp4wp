<?php

// Block direct access
if (!defined('ABSPATH')) { exit; }






// Display the SERVER RESOURCES widget
function pluginsclub_cpanel_server_widget_display() {
  $username = get_option('cpanel_username');
  $password = get_option('cpanel_password');
  $hostname = get_option('cpanel_host');
  if (file_exists('/usr/local/cpanel/base/backend/env.cgi')) {
       
// Make an API call to get the user quota
$url = "https://$hostname:2083/cpsess1235467/execute/ServerInformation/get_information";
$auth = base64_encode("$username:$password");
$headers = array(
    "Authorization: Basic $auth",
    "Content-Type: application/json"
);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
curl_close($ch);


    // Decode the response JSON data
    $data = json_decode($response, true)['data'];

    // Display the device data as a table
    echo '<table class="wp-list-table widefat striped" id="server">';
    echo '<tr><th>Device</th><th>Current Usage</th></tr>';
    foreach ($data as $item) {
        if ($item['type'] === 'device') {
            // Get the value and convert it to a percentage
            $value = str_replace('%', '', $item['value']);
            $value = intval($value);
            
            // Set the indicator color based on the value
            if ($value < 50) {
                $color = 'green';
            } else if ($value < 85) {
                $color = 'orange';
            } else {
                $color = 'red';
            }
            
            // Display the item data in the table row
            echo '<tr>';
            echo '<td><span style="display: inline-block; width: 16px; height: 16px; border-radius: 50%; vertical-align: middle; background-color: ' . $color . ';"></span> ' . $item['name'] . '</td>';
            echo '<td>' . $item['value'] . '</td>';
            echo '</tr>';
        }
    }
    echo '</table>';
?>
<style>
#server tbody tr:hover {
  background-color: #f0f6fc;
}
#server tr:hover td {
  /*color: white;*/
}
</style>
<?php
    // Display an error message if the API call failed
    $status = json_decode($response, true)['status'];
    if ($status !== 1) {
        $settings_page_link = admin_url('admin.php?page=cpanel');
        echo "<p>⚠️ Error: Failed to retrieve quota information from: <small>$url</small></p>";
        echo '<p>Please validate and insert correct cPanel login credentials on the <a href="' . $settings_page_link . '">settings page</a>.</p>';
    }
  }
}





// Display the CPANEL OVERVIEW widget
function pluginsclub_cpanel_widget_display() {
  $username = get_option('cpanel_username');
  $password = get_option('cpanel_password');
  $hostname = get_option('cpanel_host');
  if (file_exists('/usr/local/cpanel/base/backend/env.cgi')) {
    // Make an API call to get the user quota
$url = "https://$hostname:2083/cpsess1235467/execute/ResourceUsage/get_usages";
$response = wp_remote_get($url, array(
    'headers' => array(
        'Authorization' => 'Basic ' . base64_encode(get_option('cpanel_username') . ':' . get_option('cpanel_password'))
    )
));

$data = json_decode(wp_remote_retrieve_body($response), true);

$response_data = json_decode(wp_remote_retrieve_body($response), true);

if ($response_data['status'] == 1) {
// link to the Settings page
$resources_page_link = admin_url('admin.php?page=cpanel_resources');
$settings_page_link = admin_url('admin.php?page=cpanel');    
    
?>
<script src="https://www.gstatic.com/charts/loader.js"></script>
<div  style="display: flex; justify-content: space-around; align-items: center;">
<?php

if (isset($response_data['data'])) {
    $specific_data = array(
        'CPU Usage' => 'CPU',
        'Physical Memory Usage' => 'RAM',
        'IOPS' => 'I/O'
    );

    foreach ($response_data['data'] as $item) {
        if (array_key_exists($item['description'], $specific_data)) {
            // Calculate percentage of usage relative to maximum
            $percentage = ($item['maximum'] !== null) ? round(($item['usage'] / $item['maximum']) * 100, 2) : 0;

            // Create a Google Chart for each specific data
            $description = $specific_data[$item['description']];
            echo '<a href="' . $resources_page_link . '"><div id="' . $description . '"></div></a>';
            echo "<script>
                    google.charts.load('current', {'packages':['gauge']});
                    google.charts.setOnLoadCallback(drawChart);
                    function drawChart() {
                        var data = google.visualization.arrayToDataTable([
                            ['Label', 'Value'],
                            ['" . $description . "', " . $percentage . "]
                        ]);
                        var options = {
                            width: 130, height: 130,
                            redFrom: 90, redTo: 100,
                            yellowFrom:65, yellowTo: 90,
                            minorTicks: 5,
                            max: 100
                        };
                        var chart = new google.visualization.Gauge(document.getElementById('" . $description . "'));
                        chart.draw(data, options);
                        
                        // Add percentage label to the chart

                    }
                </script>";
        }
    }
 
     $small_data = array(
        'Email Accounts' => 'Emails',
        'MySQL® Databases' => 'Databases'
    );
    
echo '</div>';
echo '</br>';
?>
<style>
#big, #big.a {
  display: inline-block;
  width: 49%;
  vertical-align: top;
  text-align: center!important;
}

#big p {
    margin: 0px;
}
</style>
<?php

$emails_page_link = admin_url('admin.php?page=cpanel_emails');
$db_page_link = admin_url('admin.php?page=cpanel_mysql');

// Initialize the counter variable
$counter = 0;

foreach ($response_data['data'] as $item) {
    if (array_key_exists($item['description'], $small_data)) {
        
        if ($item['maximum'] === null) {
            $item['maximum'] = '∞';
        }
        
        // Create a Google Chart for each specific data
        $description = $small_data[$item['description']];
        
        // Check the counter value to decide which link to add
        if ($counter == 0) {
            $link = $emails_page_link;
        } else {
            $link = $db_page_link;
        }
        
        // Output the div with the link
        echo '<div id="big">';
        echo '<a href="' . $link . '" style="text-decoration: none!important;">';
        echo "<h2>" . $item['usage'] . ' / ' .$item['maximum'] . "</h2>";
        echo '<p>' . $description . '</p>';
        echo '</a>';
        echo '</div>';
        
        // Increment the counter
        $counter++;
    }
}

}
?>

<?php
} else {
    // link to the Settings page
$settings_page_link = admin_url('admin.php?page=cpanel');
    echo "<p>⚠️ Error: Failed to retrieve quota information from: <small>$url</small></p>";
   // echo '<p>Please validate and insert correct cPanel login credentials on the <a href="' . $settings_page_link . '">settings page</a>.</p>';
}






       
// Make an API call to get the user quota
$url = "https://$hostname:2083/cpsess1235467/execute/Quota/get_local_quota_info";
$auth = base64_encode("$username:$password");
$headers = array(
    "Authorization: Basic $auth",
    "Content-Type: application/json"
);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
curl_close($ch);

// Display the user quota
$quota = json_decode($response, true);
if ($quota['status'] == 1) {
$byte_limit = $quota['data']['byte_limit'];
$bytes_used = $quota['data']['bytes_used'];
$size_units = array('B', 'KB', 'MB', 'GB', 'TB');
$bytes_used_formatted = @round($bytes_used/pow(1024, ($i=floor(log($bytes_used, 1024)))), 2).' '.$size_units[$i];




if ($byte_limit === null) {
  $bytes_limit_formatted = '∞';
} else {
  $bytes_limit_formatted = @round($byte_limit/pow(1024, ($i=floor(log($byte_limit, 1024)))), 2).' '.$size_units[$i];
  
  // Calculate the percentage of quota used
  $quota_percent = round(($bytes_used / $byte_limit) * 100);
}

echo "<p>Disk Usage: <b>$bytes_used_formatted</b> / <b>$bytes_limit_formatted </b></p>";


if ($byte_limit === null) {
  $bytes_limit_formatted = '∞';
} else {
  $bytes_limit_formatted = @round($byte_limit/pow(1024, ($i=floor(log($byte_limit, 1024)))), 2).' '.$size_units[$i];
  
  // Calculate the percentage of quota used
  $quota_percent = round(($bytes_used / $byte_limit) * 100);

  // Determine the color of the progress bar based on the percentage of quota used
  if ($quota_percent < 70) {
    $progress_color = 'green';
  } elseif ($quota_percent < 85) {
    $progress_color = 'orange';
  } else {
    $progress_color = 'red';
  }

  // Display the progress bar
  echo '<div style="width: 100%; background-color: #ddd; height: 10px; margin-top: 10px;">
        <div style="width: '.$quota_percent.'%; background-color: '.$progress_color.'; height: 10px;"></div>
        </div>';
}


$inode_limit = $quota['data']['inode_limit'];
$inodes_used = $quota['data']['inodes_used'];

if ($inode_limit !== null) {
  echo "<p>Inodes: <b>$inodes_used</b> / <b>$inode_limit</b></p>";
  
  // Calculate the percentage of inodes quota used
  $inode_percent = round(($inodes_used / $inode_limit) * 100);

  // Determine the color of the progress bar based on the percentage of quota used
  if ($inode_percent < 70) {
    $progress_color = 'green';
  } elseif ($inode_percent < 85) {
    $progress_color = 'orange';
  } else {
    $progress_color = 'red';
  }

  // Display the progress bar
  echo '<div style="width: 100%; background-color: #ddd; height: 10px; margin-top: 10px;">
        <div style="width: '.$inode_percent.'%; background-color: '.$progress_color.'; height: 10px;"></div>
        </div>';
} else {
  echo "<p>Inodes: <b>$inodes_used</b> / <b>∞</b></p>";
}

} else {
    // link to the Settings page
$settings_page_link = admin_url('admin.php?page=cpanel');
    echo "<p>⚠️ Error: Failed to retrieve quota information from: <small>$url</small></p>";
    echo '<p>Please validate and insert correct cPanel login credentials on the <a href="' . $settings_page_link . '">settings page</a>.</p>';
}

  } else {
    echo '<p>Error: This website is not hosted on cPanel.</p>';
  }
}

// Save the login information when the form is submitted
add_action('pluginsclub_admin_post_save_cpanel_login_info', 'pluginsclub_cpanel_widget_save_login_info');
