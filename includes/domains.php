<?php

// EMAILS PAGE
function pluginsclub_cpanel_domains_page() {
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
		
		
    <style>

#domains tbody tr:hover, #zone tbody tr:hover {
  background-color: #f0f6fc;
}
    @media screen and (min-width: 600px)  {
        .mobile-break { display: none; }
    }


</style>
		
				<div id="pluginsclub-cpanel-admin-wrap" class="wrap">
			<div id="pluginsclub-cpanel-form">
			<h1 class="pluginsclub-cpanel-hide">cPanel Domain Names</h1>
				<h2 class="h2">Domain Names</h2>
<?php

// LIST ALL DOMAINS FROM CPANEL
$domains = array();

    $url = "https://$hostname:2083/cpsess1235467/execute/DomainInfo/list_domains";
    $response = wp_remote_get($url, array(
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode(get_option('cpanel_username') . ':' . get_option('cpanel_password'))
        )
    ));
    $data = json_decode(wp_remote_retrieve_body($response), true);

    $domains['main_domain'] = array($data['data']['main_domain']);
    $domains['addon_domains'] = $data['data']['addon_domains'];
    $domains['sub_domains'] = $data['data']['sub_domains'];
    $domains['parked_domains'] = $data['data']['parked_domains'];

    // Define the background colors for each type
$bg_colors = [
    'parked_domains' => '#FFC107',
    'addon_domains' => '#2196F3',
    'sub_domains' => '#4CAF50',
    'main_domain' => '#9C27B0'
];
    
    
    if (isset($_GET['filter'])) {
        $filter = $_GET['filter'];
        switch ($filter) {
            case 'parked':
                $domains = array('parked_domains' => $data['data']['parked_domains']);
                $bg = "black";
                break;
            case 'addons':
                $domains = array('addon_domains' => $data['data']['addon_domains']);
                break;
            case 'subdomains':
                $domains = array('sub_domains' => $data['data']['sub_domains']);
                break;
            case 'main_domain':
                $domains = array('main_domain' => $data['data']['main_domain']);
                break;
            default:
                $domains = $data['data'];
                break;
        }
    }

    ?>
<p class="main">
            <a href="<?php echo admin_url('admin.php?page=cpanel_domains'); ?>" <?php echo !isset($_GET['filter']) ? 'class="current"' : ''; ?>>All</a> |
            <a href="<?php echo admin_url('admin.php?page=cpanel_domains&filter=addons'); ?>" <?php echo isset($_GET['filter']) && $_GET['filter'] == 'addons' ? 'class="current"' : ''; ?>>Addons</a> |
            <a href="<?php echo admin_url('admin.php?page=cpanel_domains&filter=subdomains'); ?>" <?php echo isset($_GET['filter']) && $_GET['filter'] == 'subdomains' ? 'class="current"' : ''; ?>>Subdomains</a> |
            <a href="<?php echo admin_url('admin.php?page=cpanel_domains&filter=parked'); ?>" <?php echo isset($_GET['filter']) && $_GET['filter'] == 'parked' ? 'class="current"' : ''; ?>>Parked Domains</a>
    </p>
<p class="main"></p>
            

<?php

// Get the Zone file for a domain
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['domain'])) {
            $domain = $_GET['domain'];
            $parseUrl = "https://$hostname:2083/cpsess1235467/execute/DNS/parse_zone?zone=" . urlencode($domain);

            $parseResponse = wp_remote_get($parseUrl, array(
                'headers' => array(
                    'Authorization' => 'Basic ' . base64_encode(get_option('cpanel_username') . ':' . get_option('cpanel_password'))
                )
            ));

            if (!is_wp_error($parseResponse)) {
                $parseData = json_decode(wp_remote_retrieve_body($parseResponse), true);
                $zoneData = $parseData['data'];

                if ($zoneData && !empty($zoneData)) {
echo '<h1 class="pluginsclub-cpanel-hide">DNS Zone for ' . $domain . '</h1>';
echo '<h2>DNS Zone File for ' . $domain . '</h2>';

echo '<p><a href="admin.php?page=cpanel_domains">← Back to Domains list</a></p><table class="wp-list-table widefat fixed striped posts" id="zone">';
echo '<thead><tr><th>Name</th><th class="mobile-hidden">TTL</th><th class="mobile-hidden">Type</th><th>Data</th></tr></thead>';
        echo '<tbody>';
foreach ($zoneData as $record) {
    if ($record['type'] === 'record') { // Filter records of type "record"
        echo '<tr>';
        echo '<td class="">' . base64_decode($record['dname_b64']);
        echo '<span class="desktop-hidden"></br>Type: <b>' . $record['record_type'] . '</b> TTL: ' . $record['ttl'] . '</span>';
        echo '</td>';
        echo '<td class="mobile-hidden">' . $record['ttl'] . '</td>';
        echo '<td class="mobile-hidden">' . $record['record_type'] . '</td>';

        if (is_array($record['data_b64'])) {
            echo '<td class="">';
            foreach ($record['data_b64'] as $data) {
                echo base64_decode($data) . '<br>';
            }
            echo '</td>';
        } else {
            echo '<td class="">' . base64_decode($record['data_b64']) . '</td>';
        }

        echo '</tr>';
    }
}
        echo '</tbody>';

echo '</table>';


?>
<style>
/* CSS for hiding the TTL and Type columns on mobile devices */
@media (max-width: 768px) {
  .mobile-hidden {
    display: none;
  }
}
@media (min-width: 768px) {
  span.desktop-hidden {
    display: none;
  }
}

table#domains, p.main, h1.pluginsclub-cpanel-hide, h2.h2, .pluginsclub-cpanel-sep {
    display:none!important;
}
#zone th:first-child,
#zone td:first-child {
    width: 30%;
}

#zone th:nth-child(2),
#zone td:nth-child(2),
#zone th:nth-child(3),
#zone td:nth-child(3) {
    width: 10%;
}

#zone th:last-child,
#zone td:last-child {
    width: 50%;
}
</style>
<?php
                } else {
                    echo 'No zone data available for ' . $domain;
                }
            } else {
                echo 'Error occurred while parsing zone for ' . $domain;
            }
        }
    
}
?>
<style>
    @media (max-width: 768px) {
        .desktop {
            display: none;
        }

        .mobile {
            display: inline;
        }
    }

    @media (min-width: 769px) {
        .desktop {
            display: table-cell;
        }

        .mobile {
            display: none;
        }
    }
    
        @media (min-width: 768px) {  /* Adjust the breakpoint as needed */
        .desktop-column-1 {
            width: 30%;
        }

        .desktop-column-2 {
            width: 60%;
        }

        .desktop-column-3 {
            width: 10%;
        }
    }
</style>
<table class="wp-list-table widefat striped posts" id="domains">
    <thead>
        <tr>
            <th>Domain</th>
            <th colspan="">DNS Lookup</th>
            <th class="desktop">Zone</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($domains as $type => $type_domains): ?>
            <?php foreach ($type_domains as $domain): ?>
<tr>
    <td class="desktop-column-1"><span style="line-height: 3em;"><?php echo $domain; ?></span><br class="mobile-break">
        <?php echo $bg ?> <span style="padding:5px 10px; background-color: <?php echo $bg_colors[$type] ?>; color:white; white-space: nowrap; border-radius: 25px;"><?php echo ucfirst(str_replace('_', ' ', $type)); ?></span>
    <span class="mobile">   
        <?php if ($type !== 'sub_domains'): ?>
            <form method="get" action="admin.php">
                <input type="hidden" name="page" value="cpanel_domains">
                <input type="hidden" name="domain" value="<?php echo $domain; ?>">
                <br class="mobile-break">
                <button type="submit" class="button button-primary">View DNS Zone</button>
            </form>
        <?php endif; ?>  
         </span>
    </td>
    <td class="desktop-column-2" id="<?php echo $domain; ?>_lookup">Loading data...</td>
    <td class="desktop desktop-column-3">
        <?php if ($type !== 'sub_domains'): ?>
            <form method="get" action="admin.php">
                <input type="hidden" name="page" value="cpanel_domains">
                <input type="hidden" name="domain" value="<?php echo $domain; ?>">
                <button type="submit" class="button button-primary">View DNS Zone</button>
            </form>
        <?php endif; ?>
    </td>
</tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
                <?php foreach ($domains as $type => $type_domains): ?>
            <?php foreach ($type_domains as $domain): ?>
                <?php
                $lookup_url = "https://$hostname:2083/cpsess1235467/execute/DNS/lookup?domain=" . urlencode($domain);
                $lookup_response = wp_remote_get($lookup_url, array(
                    'headers' => array(
                        'Authorization' => 'Basic ' . base64_encode(get_option('cpanel_username') . ':' . get_option('cpanel_password'))
                    )
                ));
                $lookup_data = json_decode(wp_remote_retrieve_body($lookup_response), true);
                ?>
                <script>
                    var lookupData = <?php echo isset($lookup_data['data']) ? json_encode($lookup_data['data']) : 'null'; ?>;
                    var lookupElement = document.getElementById("<?php echo $domain; ?>_lookup");
                    if (lookupElement) {
                        if (lookupData) {
                            lookupElement.innerHTML = lookupData.join("<br>");
                        } else {
                            lookupElement.innerHTML = 'No DNS lookup data available';
                        }
                    }
                </script>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </tbody>
</table>





    </div>

<div>
    
    
    
<div>

</div>

</div>

<?php
}

