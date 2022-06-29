<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class Sparkle_WEDL_Sample_Plugin_Updater{
	/**
	 * The plugin current version
	 * @var string
	 */
	private $current_version;

	/**
	 * The plugin remote update path
	 * @var string
	 */
	private $update_path;

	/**
	 * Plugin Slug (plugin_directory/plugin_file.php)
	 * @var string
	 */
	private $plugin_slug;

	/**
	 * Plugin name (plugin_file)
	 * @var string
	 */
	private $slug;

	private $product_id; 

	/**
	 * License Key 
	 * @var string
	 */
	private $license_key;

	private $health_check_timeout = 5;

	public function __construct ( $current_version, $update_path, $plugin_slug, $license_key = '' ) {
		$this->current_version 	= $current_version;
		$this->update_path 		= $update_path;
		$this->license_key 		= $license_key;
		$this->product_id 		= SWEDLS_PRODUCT_ID;	
		$this->plugin_slug 		= $plugin_slug;

		list ($t1, $t2) = explode( '/', $plugin_slug );
		$this->slug 	= str_replace( '.php', '', $t2 );	

		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );

		// Define the alternative response for information checking
		add_filter( 'plugins_api', array( $this, 'check_info' ), 10, 3 );
	}

	function check_update( $transient ){
		if ( empty($transient->checked ) ) {
            return $transient;
        }

		//check the transient and if set get the value from transient else fetch new data
		if ( false === ( get_transient( $this->slug.'_plugin_cron_transient' ) ) ) {
			// Get the remote version
			$remote_version =  $this->get_remote( 'version' );
			set_transient( $this->slug.'_plugin_cron_transient', $remote_version, 12 * HOUR_IN_SECONDS );
		}else{
			$remote_version = get_transient( $this->slug.'_plugin_cron_transient' );
		}

		// If a newer version is available, add the update
		if ( isset( $remote_version->new_version ) && version_compare( $this->current_version, $remote_version->new_version, '<' ) ) {
			//we just need version and package url
			$obj 				= new stdClass();
			$obj->slug 			= $this->slug;
			$obj->new_version 	= $remote_version->new_version;
			$obj->plugin 		= $this->plugin_slug;
			$obj->package 		= $remote_version->package;

			$transient->response[$this->plugin_slug] = $obj;
		}
		return $transient;
	}

	/**
	 * Add our self-hosted description to the filter
	 *
	 * @param boolean $false
	 * @param array $action
	 * @param object $arg
	 * @return bool|object
	 */
	public function check_info( $obj, $action, $args ){
		// do nothing if this is not about getting plugin information
		if( 'plugin_information' !== $action ) {
			return false;
		}

		// do nothing if it is not our plugin
		if( $this->slug !== $args->slug ) {
			return false;
		}

		if ( ($action=='query_plugins' || $action=='plugin_information' ) && 
		    isset($args->slug) && $args->slug === $this->slug ) {
			$json_obj = $this->get_remote('info');

			//format the returned object to required wordpress display format
			$output_obj 				= new stdClass();
			$output_obj->name 			= $json_obj->name; //SWEDLS_PLUGIN_NAME;
			$output_obj->slug 			= $this->slug; //$json_obj->slug
			$output_obj->version 		= $json_obj->version;
			$output_obj->download_url 	= $json_obj->download_url;
			$output_obj->requires 		= $json_obj->requires;  
			$output_obj->tested 		= $json_obj->tested;  
			$output_obj->requires_php 	= $json_obj->requires_php;
			$output_obj->sections 		= array(  
											'description' 	=> $json_obj->sections->description,  
											'installation' 	=> $json_obj->sections->installation,
											'changelog' 	=> $json_obj->sections->changelog  
										);
			$output_obj->banners 		= array(
											"low" 	=> $json_obj->banners->low,
											"high" 	=> $json_obj->banners->high
										);
			$output_obj->download_link 	= $json_obj->download_link;
			return $output_obj;
			exit;
		}
		return $obj;
	}

	/**
	 * Return the remote version
	 * 
	 * @return string $remote_version
	 */
	public function get_remote( $action ){
		$params = array(
			// for license validation we need valid product id and license key
			'body' => array(
						'action'        => $action,
						'product_id' 	=> $this->product_id,
						'license_key'   => $this->license_key,
						'site_url' 		=> site_url() 	
					),
		);

		// Make the POST request
		$request = wp_remote_post( $this->update_path, $params );

		// Check if response is valid
		if ( !is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
			return json_decode($request['body']); 
		}
		return false;
	}
}