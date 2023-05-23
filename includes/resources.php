<?php


// RESOURCES USAGE PAGE
function pluginsclub_cpanel_resources_page() {
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
			<h1 class="pluginsclub-cpanel-hide">cPanel Resource Usags</h1>
				<h2>Resource Usage</h2>
        <p>cPanel Resource Usage statistics</p>
			
		<div class="pluginsclub-cpanel-sep"></div></br>

<?php


$url = "https://$hostname:2083/cpsess1235467/execute/ResourceUsage/get_usages";
$response = wp_remote_get($url, array(
    'headers' => array(
        'Authorization' => 'Basic ' . base64_encode(get_option('cpanel_username') . ':' . get_option('cpanel_password'))
    )
));
$data = json_decode(wp_remote_retrieve_body($response), true);


// Array to store the rounded usage values
$roundedUsages = array();

// Function to round numbers to human-readable sizes
function formatSize($size)
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $unitIndex = 0;

    while ($size >= 1024 && $unitIndex < count($units) - 1) {
        $size /= 1024;
        $unitIndex++;
    }

    return round($size, 2) . ' ' . $units[$unitIndex];
}

// Round the usage values for selected resources
foreach ($data['data'] as $item) {
    $usage = $item['usage'];
    $maximum = $item['maximum'];
    
    // Round specific usage types to human-readable sizes
    if (in_array($item['id'], ['disk_usage', 'cachedmysqldiskusage', 'cachedpostgresdiskusage', 'lvememphy', 'bandwidth'])) {
        $usage = formatSize($usage);
        $maximum = formatSize($maximum);
    }

    $roundedUsages[$item['id']] = $usage;
    $roundedUsage[$item['id']] = $maximum;
}

// Use $roundedUsages and $roundedUsage array to display the rounded usage values in the table
?>
<table class="wp-list-table widefat fixed striped posts">
    <thead>
        <tr>
            <th colspan="2">Hosting Plan Limits</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data['data'] as $item) {
            if ($item['maximum'] === null) {
                $usage_display = $roundedUsages[$item['id']] . ' / ' . '∞';
            } else {
                $usage_display = $roundedUsages[$item['id']] . ' / ' . $roundedUsage[$item['id']];

                // Calculate the percentage value based on usage and maximum values
                $percent = ($item['usage'] / $item['maximum']) * 100;

                // Set the progress color based on the percentage value
                if ($percent < 70) {
                    $progress_color = 'green';
                } elseif ($percent < 90) {
                    $progress_color = 'orange';
                } else {
                    $progress_color = 'red';
                }
            }
        ?>
            <tr>
                <td><?php echo $item['description']; ?></td>
                <td>
                    <?php echo $usage_display; ?>
                    <?php if ($item['maximum'] !== null) { ?>
                        <div style="width: 100%; background-color: #ddd; height: 10px; margin-top: 3px;">
                            <div style="width: <?php echo $percent; ?>%; max-width: 100%; background-color: <?php echo $progress_color; ?>; height: 10px;"></div>
                        </div>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>


 </div>

 <?php
}

