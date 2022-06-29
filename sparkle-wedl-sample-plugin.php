<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * Plugin Name: Sparkle WEDL Sample Plugin
 * Plugin URI: https://sparklewpthemes.com/
 * Description: Sample plugin to work with EDD/WOO plugin
 * Version: 1.0.0
 * Author: Sparkle WP Themes
 * Author URI: https://sparklewpthemes.com/
 * Text Domain: sparkle-wedl-sample-plugin
 * Domain Path: /languages/
 */

/**
 * Please set these constants properly
 */
defined( 'SWEDLS_SHOP_BASE_URL' )     	or define( 'SWEDLS_SHOP_BASE_URL', 'http://yourshopurl.com' ); // PLEASE SET YOUR SHOP BASE URL PROPERLY
defined( 'SWEDLS_CURRENT_SITE_URL' )  	or define( 'SWEDLS_CURRENT_SITE_URL', get_site_url() );
defined( 'SWEDLS_PRODUCT_ID' )     		or define( 'SWEDLS_PRODUCT_ID', 'PRODUCT_ID' ); //  PLEASE SET THE PRODUCT ID FROM DOWNLOADS PRODUCT ID PROPERLY
defined( 'SWEDLS_PRODUCT_PRICE_ID' )  	or define( 'SWEDLS_PRODUCT_PRICE_ID', '' ); // PLEASE SET THE PRODUCT PRICE ID FROM DOWNLOADS PRODUCT's PRICE ID PROPERLY ( set this value if you are using a variable product )

defined( 'SWEDLS_PRODUCT_NAME' )     	or define( 'SWEDLS_PRODUCT_NAME', 'Product Name' );
defined( 'SWEDLS_PLUGIN_VERSION' )    	or define( 'SWEDLS_PLUGIN_VERSION', '1.0.0' );
defined( 'SWEDLS_PLUGIN_NAME' )     	or define( 'SWEDLS_PLUGIN_NAME', 'Sparkle WEDL Sample Plugin' );
defined( 'SWEDLS_PLUGIN_SLUG' )     	or define( 'SWEDLS_PLUGIN_SLUG', 'sparkle-wedl-sample-plugin' );

// The name of the settings page for the license input to be displayed
defined( 'SWEDLS_PLUGIN_LICENSE_PAGE' ) or define( 'SWEDLS_PLUGIN_LICENSE_PAGE', 'swedl-sample-license' );

if( !class_exists( 'Sparkle_WEDL_Sample_Plugin_Updater' ) ) {
	// load our custom updater
	include_once( dirname( __FILE__ ) . '/sparkle-wedl-sample-plugin-updater.php' );
}else{
	echo "class not included";
	die();
}

function swedl_sample_plugin_updater(){
	$plugin_current_version = SWEDLS_PLUGIN_VERSION;
	$plugin_remote_path 	= SWEDLS_SHOP_BASE_URL."/wp-json/sparkleddl/v1/software_update/";
	$plugin_slug 			= plugin_basename( __FILE__ );
	$license_key 			= trim( get_option( 'swedl_sample_license_key' ) );

	new Sparkle_WEDL_Sample_Plugin_Updater ( $plugin_current_version, $plugin_remote_path, $plugin_slug, $license_key );
}
add_action( 'admin_init', 'swedl_sample_plugin_updater' );

function sparkle_swedl_sample_menu(){
	add_plugins_page( "Sparkle License", "Sparkle License", 'manage_options', SWEDLS_PLUGIN_LICENSE_PAGE, 'swedl_license_page' );
}
add_action( 'admin_menu', 'sparkle_swedl_sample_menu' );

function swedl_license_page(){
	$license_key = trim( get_option( 'swedl_sample_license_key' ) );
	$status 	 = get_option( 'swedl_sample_license_status' );

	if( isset($_GET['sl_deactivation'] ) && isset( $_GET['message'] ) && $_GET['sl_deactivation'] === 'false' ){ ?>
		<div class="swedl-error-message notice notice-error is-dismissible">
				<p><?php echo esc_attr( $_GET['message'] ); ?></p>
		</div>
		<?php
	}

	if( isset($_GET['sl_deactivation'] ) && isset( $_GET['message'] ) && $_GET['sl_deactivation'] === 'true' ){ ?>
		<div class="swedl-success-message notice notice-success is-dismissible">
				<p><?php echo esc_attr( $_GET['message'] ); ?></p>
		</div>
		<?php
	}

	if( isset($_GET['sl_activation'] ) && isset( $_GET['message'] ) && $_GET['sl_activation'] === 'false' ){ ?>
		<div class="swedl-error-message notice notice-error is-dismissible">
				<p><?php echo esc_attr( $_GET['message'] ); ?></p>
		</div>
		<?php
	}

	if( isset($_GET['sl_activation'] ) && isset( $_GET['message'] ) && $_GET['sl_activation'] === 'true' ){ ?>
		<div class="swedl-success-message notice notice-success is-dismissible">
				<p><?php echo esc_attr( $_GET['message'] ); ?></p>
		</div>
		<?php
	}
	?>
	<div class="swedl-license-verify-wrap">
		<h2><?php esc_html_e( 'License key validator', 'sparkle-wedl-sample-plugin' ); ?></h2>
		<form action="options.php" method="post" >
			<?php settings_fields( 'swedl_license_bundle' ); ?>
			<table class='form-table' role='presentation'>
				<tbody>
					<tr>
						<th scope='row'><label for='swedl-sample-license-key'><?php esc_html_e( 'License Key', 'sparkle-wedl-sample-plugin' ); ?></label></th>
						<td><input type="text" id='swedl-sample-license-key' class='regular-text' name='swedl_sample_license_key' value="<?php echo esc_attr( $license_key ); ?>" /></td>
					</tr>
					<?php if( !empty($status) && $status->action === 'activate' && $status->status === 'success'){ ?>
					<tr>
						<?php wp_nonce_field( 'swedl_sample_license_nonce_action', 'swedl_sample_license_nonce_field' ); ?>
						<th scope='row'><label for='swedl-sample-validate-license-key'><?php esc_html_e( 'Validate License', 'sparkle-wedl-sample-plugin' ); ?></label></th>
						<td><?php if( isset( $status->status ) && $status->status === 'success' ){ echo "<span style='color:green;' class='wedl-status'>Active</span>"; } ?><input type="submit" id='swedl-sample-validate-license-key' class='button button-secondary' name="swedl_deactivate_license" value="<?php esc_html_e( 'Deactivate License', 'sparkle-wedl-sample-plugin' ); ?>" /> <input type="submit" id='swedl-sample-delete-license-key' class='button button-secondary' name="swedl_delete_license" value="<?php esc_html_e( 'Delete License', 'sparkle-wedl-sample-plugin' ); ?>" /></td>
					</tr>
					<?php }else{ ?>
					<tr>
						<?php wp_nonce_field( 'swedl_sample_license_nonce_action', 'swedl_sample_license_nonce_field' ); ?>
						<th scope='row'><label for='swedl-sample-validate-license-key'><?php esc_html_e( 'Validate License', 'sparkle-wedl-sample-plugin' ); ?></label></th>
						<td><input type="submit" id='swedl-sample-validate-license-key' class='button button-secondary' name="swedl_validate_license" value="<?php esc_html_e( 'Activate License', 'sparkle-wedl-sample-plugin' ); ?>" /></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<?php submit_button(); ?>	
		</form>
	</div>
	<?php	
}

function swedl_sample_register_option() {
	// creates plugin settings in the options table
	register_setting( 'swedl_license_bundle', 'swedl_sample_license_key', 'swedl_sanitize_license' );
}
add_action( 'admin_init', 'swedl_sample_register_option' );

function swedl_sanitize_license( $new ) {
	$old = get_option( 'swedl_sample_license_key' );
	if( $old && $old != $new ) {
		delete_option( 'swedl_sample_license_status' );
	}
	return $new;
}

function getBrowser($agent = null){
    $u_agent = ($agent!=null)? $agent : $_SERVER['HTTP_USER_AGENT']; 
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }

    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
    { 
        $bname = 'Internet Explorer'; 
        $ub = "MSIE"; 
    } 
    elseif(preg_match('/Firefox/i',$u_agent)) 
    { 
        $bname = 'Mozilla Firefox'; 
        $ub = "Firefox"; 
    } 
    elseif(preg_match('/Opera/i',$u_agent)) 
    { 
        $bname = 'Opera'; 
        $ub = "Opera"; 
    } 
    elseif(preg_match('/Netscape/i',$u_agent)) 
    { 
        $bname = 'Netscape'; 
        $ub = "Netscape"; 
    }
    elseif(preg_match('/Edg/i',$u_agent)){
	    $bname = 'Edge';
	    $ub = "Edg";
	}elseif(preg_match('/Trident/i',$u_agent)){
	    $bname = 'Internet Explorer';
	    $ub = "MSIE";
	} 
    elseif(preg_match('/Chrome/i',$u_agent)) 
    { 
        $bname = 'Google Chrome'; 
        $ub = "Chrome"; 
    } 
    elseif(preg_match('/Safari/i',$u_agent)) 
    { 
        $bname = 'Apple Safari'; 
        $ub = "Safari"; 
    } 

    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }

    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }
        else {
            $version= $matches['version'][1];
        }
    }
    else {
        $version= $matches['version'][0];
    }

    // check if we have a number
    if ($version==null || $version=="") {$version="?";}

    return array(
        // 'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        // 'pattern'    => $pattern
    );
}

function swedl_sample_activate_license(){
	if( isset( $_POST['swedl_validate_license'] ) ){
		if( ! check_admin_referer( 'swedl_sample_license_nonce_action', 'swedl_sample_license_nonce_field' ) ) return;
		$license_key = get_option( 'swedl_sample_license_key' );
		
		
		$args = array(
					'timeout'     => 120,
				    'httpversion' => '1.1',
					'action'      => 'activate',
					'pid'         => SWEDLS_PRODUCT_ID,
					'license_key' => $license_key,
					'product_name'=> urlencode(SWEDLS_PRODUCT_NAME)
				);
		
		$wp_version = get_bloginfo( 'version' );
		$phpversion = phpversion();
		$browserdetails = json_encode( $this->getBrowser() );
		$current_activated_date = date('Y-m-d H:i:s', time());
			
		$url = SWEDLS_SHOP_BASE_URL."/wp-json/sparkleddl/v1/license_key/?action=activate&pid=".SWEDLS_PRODUCT_ID."&license_key=$license_key&product_name=".urlencode(SWEDLS_PRODUCT_NAME).'&site_url='.SWEDLS_CURRENT_SITE_URL."&wp_version=$wp_version&php_version=$phpversion&browser=$browserdetails&activated_date=$current_activated_date";

		$request = wp_remote_get( $url, $args );


		if( is_wp_error( $request ) ) {
			return false;
		}

		$body = wp_remote_retrieve_body( $request );

		$response = json_decode( $body );
		
		if( $response->status === 'fail' ){
			if( $response->error === 'notfoundlicense' ){
				$message = esc_html__( 'Your license key not found in our database.', 'sparkle-wedl-sample-plugin' );
			}

			if( $response->error === 'licensedisabled' ){
				$message = esc_html__( 'Your license key has been disabled. Please contact license provider.', 'sparkle-wedl-sample-plugin' );
			}
			
			if( $response->error === 'activationlimitreached' ){
				$message = esc_html__( 'Your maximum limit for using this license key has been reached.', 'sparkle-wedl-sample-plugin' );
			}

			if( $response->error === 'licensekeyexpired' ){
				$message = esc_html__( 'Your license key has been expired.', 'sparkle-wedl-sample-plugin' );
			}
			
		}

		if( $response->status === 'success' ){
			if( $response->poststatus === 'alreadyactive' ){
				$remaining_activation = $response->activation_limit - $response->use_count;
				$message = esc_html__( "Your license key is already active. You have used the license in {$response->use_count} sites. Now remaining activation count is {$remaining_activation}", 'sparkle-wedl-sample-plugin' );
			}

			if( $response->poststatus === 'activated' ){
				$remaining_activation = $response->activation_limit - $response->use_count;
				$message = esc_html__( "Your license key has been activated successfully. You have used the license in {$response->use_count} sites. Now remaining activation count is {$remaining_activation}", "sparkle-wedl-sample-plugin" );
			}

			if( $response->poststatus === 'reactivated' ){
				$remaining_activation = $response->activation_limit - $response->use_count;
				$message = esc_html__( "Your license key has been reactivated successfully. You have used the license in {$response->use_count} sites. Now remaining activation count is {$remaining_activation}", "sparkle-wedl-sample-plugin" );
			}				
		}

		if( $response->status === 'success' ){
			$activation = 'true';

		}else if( $response->status === 'fail' ){
			$activation = 'false';
		}

		update_option( 'swedl_sample_license_status', $response );
		$base_url = admin_url( 'plugins.php?page=' . SWEDLS_PLUGIN_LICENSE_PAGE );
		$redirect = add_query_arg( array( 'sl_activation' => $activation, 'message' => urlencode( $message ) ), $base_url );
		wp_redirect( $redirect );
		exit();
	}	
}
add_action( 'admin_init', 'swedl_sample_activate_license');

function swedl_sample_deactivate_license(){
	if( isset( $_POST['swedl_deactivate_license'] ) ){

		if( ! check_admin_referer( 'swedl_sample_license_nonce_action', 'swedl_sample_license_nonce_field' )) return;
		$license_key = trim( get_option ('swedl_sample_license_key') );
		$args = array(
					'timeout'     => 120,
				    'httpversion' => '1.1',
				);
		$url = SWEDLS_SHOP_BASE_URL."/wp-json/sparkleddl/v1/license_key/?action=deactivate&pid=".SWEDLS_PRODUCT_ID."&license_key=$license_key&product_name=".urlencode(SWEDLS_PRODUCT_NAME).'&site_url='.SWEDLS_CURRENT_SITE_URL;
		
		$request = wp_remote_get( $url, $args );
		if( is_wp_error( $request ) ) {
			return false;
		}

		$body 		= wp_remote_retrieve_body( $request );
		$response 	= json_decode( $body );
		
		if( $response->status === 'fail' ){
			if( $response->error === 'notfoundlicense' ){
				$message = esc_html__( 'Your license key not found in our database.', 'sparkle-wedl-sample-plugin' );
			}

			if( $response->error === 'licensedisabled' ){
				$message = esc_html__( 'Your license key has been disabled. Please contact license provider.', 'sparkle-wedl-sample-plugin' );
			}
			
			if( $response->error === 'activationlimitreached' ){
				$message = esc_html__( 'Your maximum limit for using this license key has been reached.', 'sparkle-wedl-sample-plugin' );
			}

			if( $response->error === 'licensekeyexpired' ){
				$message = esc_html__( 'Your license key has been expired.', 'sparkle-wedl-sample-plugin' );
			}
		}

		if( $response->status === 'success' ){
			if( $response->poststatus === 'deactivated' ){
				$message = esc_html__( 'Your license key has been successfully deactivated.', 'sparkle-wedl-sample-plugin' );
			}	

			if( $response->poststatus === 'alreadydeactivated' ){
				$message = esc_html__( 'Your license key has already been deactivated.', 'sparkle-wedl-sample-plugin' );
			}		
		}

		if( $response->status === 'success' ){
			$activation = 'true';

		}else if( $response->status === 'fail' ){
			$activation = 'false';
		}

		update_option( 'swedl_sample_license_status', $response );
		$base_url = admin_url( 'plugins.php?page=' . SWEDLS_PLUGIN_LICENSE_PAGE );
		$redirect = add_query_arg( array( 'sl_deactivation' => $activation, 'message' => urlencode( $message ) ), $base_url );
		wp_redirect( $redirect );
		exit();	
	}
}
add_action( 'admin_init', 'swedl_sample_deactivate_license' );

function swedl_sample_delete_license(){	
	if( isset( $_POST["swedl_delete_license"] ) ){
		
		if( ! check_admin_referer( "swedl_sample_license_nonce_action" , "swedl_sample_license_nonce_field" ) ) return;
		
		$license_key = trim( get_option ( 'swedl_sample_license_key' ) );
		$args 		 = array(
							'timeout'     => 120,
						    'httpversion' => '1.1',
						);
		$url 		 = SWEDLS_SHOP_BASE_URL."/wp-json/sparkleddl/v1/license_key/?action=delete&pid=".SWEDLS_PRODUCT_ID."&license_key=$license_key&product_name=".urlencode(SWEDLS_PRODUCT_NAME).'&site_url='.SWEDLS_CURRENT_SITE_URL;
		$request 	 = wp_remote_get( $url, $args );
        
		if( is_wp_error( $request ) ) {
			return false;
		}

		$body 		= wp_remote_retrieve_body( $request );
		$response   = json_decode( $body );
		
		if( $response->status === 'fail' ){
			$activation = 'false';
			if( $response->error === 'cannotdelete' ){
				$message = esc_html__( 'Your license key for this site not found in our database.', 'sparkle-wedl-sample-plugin' );
			}
		}

		if( $response->status === 'success' ){
			$activation = 'true';
			if( $response->poststatus === 'deleted' ){
				$message = esc_html__( 'Your license key for this site has been deleted successfully.', 'sparkle-wedl-sample-plugin' );
			}	
		}


		update_option( 'swedl_sample_license_status', $response );
		$base_url = admin_url( 'plugins.php?page=' . SWEDLS_PLUGIN_LICENSE_PAGE );
		$redirect = add_query_arg( array( 'sl_deactivation' => $activation, 'message' => urlencode( $message ) ), $base_url );
		wp_redirect( $redirect );
		exit();	
	}
}
add_action( 'admin_init', 'swedl_sample_delete_license');
