<?php
/*
Plugin Name: WP Antivirus Site Protection (by SiteGuarding.com)
Plugin URI: http://www.siteguarding.com/en/website-extensions
Description: Adds more security for your WordPress website. Server-side scanning. Performs deep website scans of all the files. Virus and Malware detection.
Version: 2.0.1
Author: SiteGuarding.com (SafetyBis Ltd.)
Author URI: http://www.siteguarding.com
License: GPLv2
TextDomain: plgavp
*/
define( 'WPAVP_SVN', false);

define( 'SITEGUARDING_SERVER', 'https://www.siteguarding.com/ext/antivirus/index.php');


error_reporting(E_ERROR);

if( !is_admin() ) {
	if ( isset($_GET['task']) && $_GET['task'] == 'cron' )
	{
		error_reporting(E_ERROR);
		
		$access_key = trim($_GET['access_key']);
	
		$params = plgwpavp_GetExtraParams();
	
		if ($params['access_key'] == $access_key)
		{
								if ( !isset($params['last_scan_date']) || $params['last_scan_date'] < date("Y-m-d"))
				{
	
										$domain = get_site_url();
					$access_key = $params['access_key'];
	
					SGAntiVirus::GetAntivirusModule($domain, $access_key);
	
					$data = array('last_scan_date' => date("Y-m-d"));
					plgwpavp_SetExtraParams($data);
				}	
				
				include_once(dirname(__FILE__).'/sgantivirus.class.php');
				
				if (!class_exists('SGAntiVirus_module'))
				{
										exit;
				}
				
								$_POST['scan_path'] = ABSPATH;
				$_POST['access_key'] = $access_key;
				$_POST['do_evristic'] = $params['do_evristic'];
				$_POST['domain'] = get_site_url();
				$_POST['email'] = get_option( 'admin_email' );
				
				
								SGAntiVirus_module::scan(false, false);
		}
		
		exit;
	}
}



if( is_admin() ) {
    
	add_action( 'admin_init', 'plgavp_admin_init' );
	function plgavp_admin_init()
	{
		wp_register_style( 'plgavp_LoadStyle', plugins_url('css/antivirus.css', __FILE__) );	
	}


	add_action('admin_menu', 'register_plgavp_settings_page');

	function register_plgavp_settings_page() 
	{
		add_menu_page('plgavp_Antivirus', 'Antivirus', 'activate_plugins', 'plgavp_Antivirus', 'plgavp_settings_page_callback', plugins_url('images/', __FILE__).'antivirus-logo.png'); 
	}

	function plgavp_settings_page_callback() 
	{
		wp_enqueue_style( 'plgavp_LoadStyle' );
		
		?>
			<h2 class="avp_header icon_radar">WP Antivirus Site Protection</h2>
			
		<?php
		
		
						if (isset($_POST['action']) && $_POST['action'] == 'ConfirmRegistration' && check_admin_referer( 'name_254f4bd3ea8d' ))
		{
			$errors = SGAntiVirus::checkServerSettings(true);
			$access_key = md5(time.get_site_url());
			$email = trim($_POST['email']);
			$result = SGAntiVirus::sendRegistration(get_site_url(), $email, $access_key, $errors);
			if ($result === true)
			{
				$data = array('registered' => 1, 'email' => $email, 'access_key' => $access_key);
				plgwpavp_SetExtraParams($data);
			}
			else {
								SGAntiVirus::ShowMessage($result);
				return;
			}
		}
		
				if (isset($_POST['action']) && $_POST['action'] == 'StartScan' && check_admin_referer( 'name_254f4bd3ea8d' ))
		{
			$data = array('allow_scan' => intval($_POST['allow_scan']), 'do_evristic' => intval($_POST['do_evristic']));
			plgwpavp_SetExtraParams($data);
			
			$params = plgwpavp_GetExtraParams();
			
			if (!file_exists(dirname(__FILE__).'/sgantivirus.class.php'))
			{
								SGAntiVirus::ShowMessage('File '.dirname(__FILE__).'/sgantivirus.class.php is not exist.');
				return;
			}
			
			include_once(dirname(__FILE__).'/sgantivirus.class.php');
			
			if (!class_exists('SGAntiVirus_module'))
			{
								SGAntiVirus::ShowMessage('Main antivirus scanner module is not loaded. Please try again.');
				return;
			}
			
			
			$session_id = md5(time().'-'.rand(1,10000));
			ob_start();
			session_start();
			ob_end_clean();
			$_SESSION['scan']['session_id'] = $session_id;
			SGAntiVirus::ScanProgress($session_id, ABSPATH, $params);
			return;
		}
		
		


				$params = plgwpavp_GetExtraParams();
		
						if (!isset($params['registered']) || intval($params['registered']) == 0) { SGAntiVirus::page_ConfirmRegistration(); return; }
		
				$license_info = SGAntiVirus::GetLicenseInfo(get_site_url(), $params['access_key']);
		if ($license_info === false) { SGAntiVirus::page_ConfirmRegistration(); return; }
		
				if (!SGAntiVirus::checkServerSettings()) return;

		
		
		foreach ($license_info as $k => $v)
		{
			$params[$k] = $v;	
		}
		
		
		SGAntiVirus::page_PreScan($params);

	}
	
	
	
	
	add_action('admin_menu', 'register_plgavp_settings_subpage');

	function register_plgavp_settings_subpage() {
		add_submenu_page( 'plgavp_Antivirus', 'Settings', 'Settings', 'manage_options', 'plgavp_Antivirus_settings_page', 'plgavp_antivirus_settings_page_callback' ); 
	}
	
	
	function plgavp_antivirus_settings_page_callback()
	{
		wp_enqueue_style( 'plgavp_LoadStyle' );

		$img_path = plugins_url('images/', __FILE__);
		
		if (isset($_POST['action']) && $_POST['action'] == 'update' && check_admin_referer( 'name_AFAD78D85E01' ))
		{
			$data = array('access_key' => trim($_POST['access_key']));
			if (trim($_POST['access_key']) != '') 
			{
				$data['registered'] = 1;
				$data['email'] = get_option( 'admin_email' );
			}
			plgwpavp_SetExtraParams($data);
			
			SGAntiVirus::ShowMessage('Settings saved.');
		}
		
		$params = plgwpavp_GetExtraParams();
		
		?>
		<h2 class="avp_header icon_settings">WP Antivirus Settings</h2>
		
<form method="post" id="plgwpagp_settings_page" action="admin.php?page=plgavp_Antivirus_settings_page" onsubmit="return SG_CheckForm(this);">


			<table id="settings_page">


			<tr class="line_4">
			<th scope="row"><?php _e( 'Access Key', 'plgwpap' )?></th>
			<td>
	            <input type="text" name="access_key" id="access_key" value="<?php echo $params['access_key']; ?>" class="regular-text">
	            <br />
	            <span class="description">This key is necessary to access to <a target="_blank" href="http://www.siteguarding.com">SiteGuarding API</a> features. Every website has uniq access key. Don't change it fo you don't know what is it.</span>
			</td>
			</tr>

			
			</table>

<?php
wp_nonce_field( 'name_AFAD78D85E01' );
?>			
<p class="submit">
  <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
</p>

<input type="hidden" name="page" value="plgavp_Antivirus_settings_page"/>
<input type="hidden" name="action" value="update"/>
</form>


		<h3>Cron Settings</h3>
		
		<p>
		If you want to enable daily scan of your website. Add this line in your hosting panel in cron settings.<br /><br />
		<b>Unix time settings:</b> 0 0 * * *<br />
		<b>Command:</b> wget -O /dev/null "<?php echo get_site_url(); ?>/index.php?task=cron&access_key=<?php echo $params['access_key']; ?>"
		</p>

		<?php
	}
	
	
	add_action('admin_menu', 'register_plgavp_extentions_subpage');

	function register_plgavp_extentions_subpage() {
		add_submenu_page( 'plgavp_Antivirus', 'Extentions', 'Extentions', 'manage_options', 'plgavp_Antivirus_extentions_page', 'plgavp_antivirus_extentions_page_callback' ); 
	}
	
	
	function plgavp_antivirus_extentions_page_callback()
	{
		wp_enqueue_style( 'plgavp_LoadStyle' );
		
		?>
		
		<h2 class="avp_header icon_settings">Security Extentions</h2>
		
		<div class="grid-box width25 grid-h" style="width: 250px;">
		  <div class="module mod-box widget_black_studio_tinymce">
		    <div class="deepest">
		      <h3 class="module-title">WordPress Admin Protection</h3>
		      <div class="textwidget">
		        <table class="table-val" style="height: 180px;">
		          <tbody>
		            <tr>
		              <td class="table-vat">
		                <ul style="list-style-type: circle;">
		                  <li>
		                    Prevents password brute force attack with strong 'secret key'
		                  </li>
		                  <li>
                    		White & Black IP list access
		                  </li>
		                  <li>
		                    Notifications by email about all not authorized actions
		                  </li>
		                  <li>
		                    Protection for login page with captcha code
		                  </li>
		                </ul>
		              </td>
		            </tr>
		            <tr>
		              <td class="table-vab">
		                <a class="button button-primary extbttn" href="https://www.siteguarding.com/en/wordpress-admin-protection">
		                  Learn More
		                </a>
		              </td>
		            </tr>
		          </tbody>
		        </table>
		        <p>
		          <img class="imgpos_ext" alt="WordPress Admin Protection" src="<?php echo plugins_url('images/wpAdminProtection-logo.png', __FILE__); ?>">
		        </p>
		      </div>
		    </div>
		  </div>
		</div>
		
		
		<div class="grid-box width25 grid-h" style="width: 250px;">
		  <div class="module mod-box widget_black_studio_tinymce">
		    <div class="deepest">
		      <h3 class="module-title">Graphic Captcha Protection</h3>
		      <div class="textwidget">
		        <table class="table-val" style="height: 180px;">
		          <tbody>
		            <tr>
		              <td class="table-vat">
		                <ul style="list-style-type: circle;">
		                  <li>
		                    Strong captcha protection
		                  </li>
		                  <li>
                    		Easy for human, complicated for robots
		                  </li>
		                  <li>
		                    Prevents password brute force attack on login page
		                  </li>
		                  <li>
		                    Blocks spam software
		                  </li>
		                  <li>
		                    Different levels of the security
		                  </li>
		                </ul>
		              </td>
		            </tr>
		            <tr>
		              <td class="table-vab">
		                <a class="button button-primary extbttn" href="https://www.siteguarding.com/en/wordpress-graphic-captcha-protection">
		                  Learn More
		                </a>
		              </td>
		            </tr>
		          </tbody>
		        </table>
		        <p>
		          <img class="imgpos_ext" alt="WordPress Graphic Captcha Protection" src="<?php echo plugins_url('images/wpGraphicCaptchaProtection-logo.png', __FILE__); ?>">
		        </p>
		      </div>
		    </div>
		  </div>
		</div>
		
		
		<div class="grid-box width25 grid-h" style="width: 250px;">
		  <div class="module mod-box widget_black_studio_tinymce">
		    <div class="deepest">
		      <h3 class="module-title">Admin Graphic Protection</h3>
		      <div class="textwidget">
		        <table class="table-val" style="height: 180px;">
		          <tbody>
		            <tr>
		              <td class="table-vat">
		                <ul style="list-style-type: circle;">
		                  <li>
		                    Good solution if you access to your website from public places or infected computers
		                  </li>
		                  <li>
		                    Prevent password brute force attack with strong "graphic password"
		                  </li>
		                  <li>
		                    Notifications by email about all not authorized actions
		                  </li>
		                </ul>
		              </td>
		            </tr>
		            <tr>
		              <td class="table-vab">
		                <a class="button button-primary extbttn" href="https://www.siteguarding.com/en/wordpress-admin-graphic-password">
		                  Learn More
		                </a>
		              </td>
		            </tr>
		          </tbody>
		        </table>
		        <p>
		          <img class="imgpos_ext" alt="WordPress Admin Graphic Protection" src="<?php echo plugins_url('images/wpAdminGraphicPassword-logo.png', __FILE__); ?>">
		        </p>
		      </div>
		    </div>
		  </div>
		</div>
		
		
		<div class="grid-box width25 grid-h" style="width: 250px;">
		  <div class="module mod-box widget_black_studio_tinymce">
		    <div class="deepest" >
		      <h3 class="module-title">User Access Notification</h3>
		      <div class="textwidget">
		        <table class="table-val" style="height: 180px;">
		          <tbody>
		            <tr>
		              <td class="table-vat">
		                <ul style="list-style-type: circle;">
		                  <li>
		                    Catchs successful and failed login actions
		                  </li>
		                  <li>
		                    Sends notifications to the user and to the administrator by email
		                  </li>
		                  <li>
		                    Shows Date/Time of access action, Browser, IP address, Location (City, Country)
		                  </li>
		                </ul>
		              </td>
		            </tr>
		            <tr>
		              <td class="table-vab">
		                <a class="button button-primary extbttn" href="https://www.siteguarding.com/en/wordpress-user-access-notification">
		                  Learn More
		                </a>
		              </td>
		            </tr>
		          </tbody>
		        </table>
		        <p>
		          <img class="imgpos_ext" alt="WordPress User Access Notification" src="<?php echo plugins_url('images/wpUserAccessNotification-logo.jpeg', __FILE__); ?>">
		        </p>
		      </div>
		    </div>
		  </div>
		</div>
		
		
		

				
		<?php
	}
	

 
 
    
	function plgwpavp_activation()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'plgwpavp_config';
		if( $wpdb->get_var( 'SHOW TABLES LIKE "' . $table_name .'"' ) != $table_name ) {
			$sql = 'CREATE TABLE IF NOT EXISTS '. $table_name . ' (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `var_name` char(255) CHARACTER SET utf8 NOT NULL,
                `var_value` char(255) CHARACTER SET utf8 NOT NULL,
                PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
            

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );             
            if (!WPAGP_SVN) wpagp_NotityDeveloper();
		}
	}
	register_activation_hook( __FILE__, 'plgwpavp_activation' );
    
    
	function plgwpavp_uninstall()
	{
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'plgwpavp_config';
		$wpdb->query( 'DROP TABLE ' . $table_name );
		
	}
	register_uninstall_hook( __FILE__, 'plgwpavp_uninstall' );
	

}










function plgwpavp_GetExtraParams()
{
    global $wpdb, $current_user;;
    
    $table_name = $wpdb->prefix . 'plgwpavp_config';
    
    $rows = $wpdb->get_results( 
    	"
    	SELECT *
    	FROM ".$table_name."
    	"
    );
    
    $a = array();
    if (count($rows))
    {
        foreach ( $rows as $row ) 
        {
        	$a[trim($row->var_name)] = trim($row->var_value);
        }
    }

    return $a;
}


function plgwpavp_SetExtraParams($data = array())
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'plgwpavp_config';

    if (count($data) == 0) return;   
    
    foreach ($data as $k => $v)
    {
        $tmp = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . $table_name . ' WHERE var_name = %s LIMIT 1;', $k ) );
        
        if ($tmp == 0)
        {
                        $wpdb->insert( $table_name, array( 'var_name' => $k, 'var_value' => $v ) ); 
        }
        else {
                        $data = array('var_value'=>$v);
            $where = array('var_name' => $k);
            $wpdb->update( $table_name, $data, $where );
        }
    } 
}







class SGAntiVirus {

	function page_ConfirmRegistration()
	{
		?>
		<script>
		function form_ConfirmRegistration(form)
		{
			if ( jQuery('#registered').is(':checked') ) return true;
			else {
				alert('Confirmation is not checked.');	
				return false;
			}
		}
		</script>
		<form method="post" action="admin.php?page=plgavp_Antivirus" onsubmit="return form_ConfirmRegistration(this);">
		
			<h3>Registration</h3>
			
			<p>If you are using this plugin the first time you need to register your website on <a href="http://www.siteguarding.com">www.SiteGuarding.com</a>.<br>Click the button "Agree & Confirm Registration" to complete registration process.</p>
			
			<p>Already registered? Go to <a href="admin.php?page=plgavp_Antivirus_settings_page">Antivirus Settings</a> page and enter your Access Key.</p>
		
			<table id="settings_page">

			<tr class="line_4">
			<th scope="row">Domain</th>
			<td>
	            <input disabled type="text" name="domain" id="domain" value="<?php echo get_site_url(); ?>" class="regular-text">
			</td>
			</tr>
			    
			<tr class="line_4">
			<th scope="row">Email</th>
			<td>
	            <input type="text" name="email" id="email" value="<?php echo get_option( 'admin_email' ); ?>" class="regular-text">
			</td>
			</tr>
			
			<tr class="line_4">
			<th scope="row">Confirmation</th>
			<td>
	            <input name="registered" type="checkbox" id="registered" value="1"> I confirm to register my website on <a href="http://www.siteguarding.com">www.SiteGuarding.com</a>
			</td>
			</tr>
			
			</table>
			
		<?php
		wp_nonce_field( 'name_254f4bd3ea8d' );
		?>			
		<p class="submit">
		  <input type="submit" name="submit" id="submit" class="button button-primary" value="Confirm Registration">
		</p>
		
		<input type="hidden" name="page" value="plgavp_Antivirus"/>
		<input type="hidden" name="action" value="ConfirmRegistration"/>
		</form>
		
		<?php self::HelpBlock(); ?>
			
		<?php
	}	
	
	
	function page_PreScan($params)
	{
		?>
		<script>
		function form_ConfirmRegistration(form)
		{
			if ( jQuery('#allow_scan').is(':checked') ) return true;
			else {
				alert('Confirmation is not checked.');	
				return false;
			}
		}
		</script>
		<form method="post" action="admin.php?page=plgavp_Antivirus" onsubmit="return form_ConfirmRegistration(this);">

<?php
if (intval($params['scans']) == 0) {
?>
	<p class="avp_attention msg_box msg_error msg_icon">Your version of antivirus has limits. Get PRO version. <a href="https://www.siteguarding.com/en/buy-service/antivirus-site-protection?domain=<?php echo urlencode( get_site_url() ); ?>&email=<?php echo urlencode(get_option( 'admin_email' )); ?>" target="_blank">Learn more</a>.</p>
<?php
}
?>


<div class="divTable">
<div class="divRow">
<div class="divCell">

<p>
Free Scans: <?php echo $params['scans']; ?><br />
Valid till: <?php echo $params['exp_date']."&nbsp;&nbsp;"; 
if ($params['exp_date'] < date("Y-m-d")) echo '<span class="msg_box msg_error">Expired</span>';
if ($params['exp_date'] < date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")-7, date("Y")))) echo '<span class="msg_box msg_warning">Will Expired Soon</span>';
?>
</p>

<p class="avp_getpro"><a href="https://www.siteguarding.com/en/buy-service/antivirus-site-protection?domain=<?php echo urlencode( get_site_url() ); ?>&email=<?php echo urlencode(get_option( 'admin_email' )); ?>" target="_blank">Get PRO version of WP Antivirus Site Protection</a></p>

<div class="mod-box"><div>		
<p>To start the scan process click "Start Scanner" button.</p>
<p>Scanner will automatically collect and analyze the files of your website. The scanning process can take up to 10 mins (it depends of speed of your server and amount of the files to analyze).</p>
<p>After full analyze you will get the report. The copy of the report we will send by email for your records.</p>

			<table id="settings_page">

			<tr class="line_4">
			<th scope="row">Heuristic Logic</th>
			<td>
				<?php if (!isset($params['do_evristic'])) $params['do_evristic'] = 1; ?>
	            <input name="do_evristic" type="checkbox" id="do_evristic" value="1" <?php if (intval($params['do_evristic']) == 1) echo 'checked="checked"'; ?>> Enable advanced heuristic logic. 
			</td>
			</tr>
			
			<tr class="line_4">
			<th scope="row">Confirmation</th>
			<td>
	            <input name="allow_scan" type="checkbox" id="allow_scan" value="1" <?php if (intval($params['allow_scan']) == 1) echo 'checked="checked"'; ?>> I allow to scan and analyze the files of my website with <a target="_blank" href="http://www.siteguarding.com">SiteGuarding.com</a> service.
			</td>
			</tr>
			
			</table>
			
		<?php
		wp_nonce_field( 'name_254f4bd3ea8d' );
		?>			
		<p class="submit">
		  <input type="submit" name="submit" id="submit" class="button button-primary" value="Start Scanner">
		</p>
		
		<input type="hidden" name="page" value="plgavp_Antivirus"/>
		<input type="hidden" name="action" value="StartScan"/>
		</form>

<img class="imgpos" alt="WP Antivirus Site Protection" src="<?php echo plugins_url('images/', __FILE__).'mid_box.png'; ?>" width="110" height="70">
			
</div></div>
		
		<?php self::HelpBlock(); ?>

</div>
<div class="divCell divCellReka">
	<div class="RekaBlock">
		<a href="https://www.siteguarding.com/en/website-extensions">
		<img class="effect7" src="<?php echo plugins_url('images/rek1.png', __FILE__); ?>" />
		</a>
	</div>
	
	<div class="RekaBlock">
		<a href="http://www.getbestwebdesign.com/">
		<img class="effect7" src="<?php echo plugins_url('images/rek2.png', __FILE__); ?>" />
		</a>
	</div>
	
	<div class="RekaBlock">
		<a href="https://www.siteguarding.com/en/prices">
		<img class="effect7" src="<?php echo plugins_url('images/rek3.png', __FILE__); ?>" />
		</a>
	</div>
	
	<div class="RekaBlock">
		<a href="https://www.siteguarding.com/en/sitecheck">
		<img class="effect7" src="<?php echo plugins_url('images/rek4.png', __FILE__); ?>" />
		</a>
	</div>
	
	<div class="RekaBlock">
		<a href="https://www.siteguarding.com/en/buy-service/antivirus-site-protection">
		<img class="effect7" src="<?php echo plugins_url('images/rek5.png', __FILE__); ?>" />
		</a>
	</div>
	
	<div class="RekaBlock">
		Remove these ads?<br />
		<a href="#">Upgrade to PRO version</a>
	</div>
	
</div>
</div>
</div>	
		
		<?php
	}
	
	
	
	function ScanProgress($session_id = '', $wp_path = '/', $params = array())
	{
		?>
		
        <script>
            jQuery(document).ready(function(){
            	
            	var refreshIntervalId;
            	
         		<?php
               	$ajax_url = plugins_url('/ajax.php', __FILE__);
               	?>
               	var link = "<?php echo $ajax_url; ?>";

				jQuery.post(link, {
					    action: "StartScan_AJAX",
					    scan_path: "<?php echo $wp_path; ?>",
						session_id: "<?php echo $session_id; ?>",
						access_key: "<?php echo $params['access_key']; ?>",
						do_evristic: "<?php echo $params['do_evristic']; ?>",
						domain: "<?php echo get_site_url(); ?>",
						email: "<?php echo get_option( 'admin_email' ); ?>"
					},
					function(data){
						jQuery("#progress_bar_process").css('width', '100%');
						jQuery("#progress_bar").hide();
						
						clearInterval(refreshIntervalId);
						
					    //if (data == 'OK') jQuery("#adminForm").submit();
                        //else alert(data);
                        jQuery("#report_area").html(data);
                        jQuery("#back_bttn").show();
                        jQuery("#help_block").show();
                        jQuery("#rek_block").hide();
					}
				);
				
				
				function GetProgress()
				{
					//alert('send');
	         		<?php
	               	$ajax_url = plugins_url('/ajax.php', __FILE__);
	               	?>
	               	var link = "<?php echo $ajax_url; ?>";
	
					jQuery.post(link, {
						    action: "GetScanProgress_AJAX",
							session_id: "<?php echo $session_id; ?>"
						},
						function(data){
						    var tmp_data = data.split('|');
						    jQuery("#progress_bar_txt").html(tmp_data[0]+'% - '+tmp_data[1]);
						    jQuery("#progress_bar_process").css('width', parseInt(tmp_data[0])+'%');
						}
					);	
				}
				
				refreshIntervalId =  setInterval(GetProgress, 2000);
				
            });
        </script>
        
        <div id="progress_bar"><div id="progress_bar_process"></div><div id="progress_bar_txt">Scanning process started...</div></div>
        
        <div id="report_area"></div>
        
        <div id="help_block" style="display: none;">
		
		<a href="http://www.siteguarding.com" target="_blank">SiteGuarding.com</a> - Website Security. Professional security services against hacker activity.
		
		</div>
        
        <a id="back_bttn" style="display: none;" class="button button-primary" href="admin.php?page=plgavp_Antivirus">Back</a>
        
        <div id="rek_block">
			<a href="https://www.siteguarding.com" target="_blank">
				<img class="effect7" src="<?php echo plugins_url('images/rek_scan.jpg', __FILE__); ?>">
			</a>
		</div>


		
		<?php
	}
	
	


	function GetLicenseInfo($domain, $access_key)
	{
		$link = SITEGUARDING_SERVER.'?action=licenseinfo&type=json&data=';
		
	    $data = array(
			'domain' => $domain,
			'access_key' => $access_key
		);
	    $link .= base64_encode(json_encode($data));
	    $msg = file_get_contents($link);
	    
	    $msg = trim($msg);
	    if ($msg == '') return false;
	    
	    return (array)json_decode($msg);
	}

	
	function sendRegistration($domain, $email, $access_key = '', $errors = '')
	{
			    $link = SITEGUARDING_SERVER.'?action=register&type=json&data=';
	    
	    $data = array(
			'domain' => $domain,
			'email' => $email,
			'access_key' => $access_key,
			'errors' => $errors
		);
	    $link .= base64_encode(json_encode($data));
	    $msg = trim(file_get_contents($link));
	    
	    if ($msg == '') return true;
	    else return $msg;
	}

	
	function checkServerSettings($return_error_names = false)
	{
		$error_name = array();
		$error = 0;
		
				if (!is_writable(dirname(__FILE__).'/tmp/'))
		{
			chmod ( dirname(__FILE__).'/tmp/' , 0777 ); 
			if (!is_writable(dirname(__FILE__).'/tmp/'))
			{
				$error = 1;
				$error_name[] = 'tmp is not writable';
				self::ShowMessage('Folder '.dirname(__FILE__).'/tmp/'.' is not writable.');
				
				?>
				Please change folder <?php echo dirname(__FILE__).'/tmp/'; ?>permission to 777 to make it writable.
				<?php
			}
		}
		
		
				if ( !function_exists('exec') ) 
		{
		    if (!class_exists('ZipArchive'))
		    {
				$error = 1;
				$error_name[] = 'exec & ZipArchive';
				self::ShowMessage('ZipArchive class is not installed on your server.');
				
				?>
				Please ask your hoster support to install or enable PHP ZipArchive class for your server. More information about ZipArchive class please read here <a href="http://bd1.php.net/manual/en/class.ziparchive.php" target="_blank">http://bd1.php.net/manual/en/class.ziparchive.php</a>
				<?php
			}
		}
		
		
		if ($return_error_names) return json_encode($error_name);
		if ($error == 1) return false;
		else return true;
	}
	
	function ShowMessage($txt)
	{
		echo '<div id="setting-error-settings_updated" class="updated settings-error"><p><strong>'.$txt.'</strong></p></div>';
	}
	
	
	function HelpBlock()
	{
		?>
		<h3 class="avp_header icon_contacts">Support</h3>
		
		<p>
		For more information and details about Antivirus Site Protection please <a target="_blank" href="https://www.siteguarding.com/en/antivirus-site-protection">click here</a>.<br />
		For any questions and support please contact <a href="https://www.siteguarding.com/en/contacts" rel="nofollow" target="_blank" title="SiteGuarding.com">SiteGuarding.com contact form</a>.<br />
		<a href="https://www.siteguarding.com/" target="_blank">SiteGuarding.com</a> - Website Security. Professional security services against hacker activity.<br />
		</p>
		<?php
	}
	
}


?>