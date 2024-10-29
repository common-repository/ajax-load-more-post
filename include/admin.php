<?php 
/** 
 * Admin panel widget configuration
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
if ( ! class_exists( 'richpostslistandgridWidget_Admin' ) ) { 
	class richpostslistandgridWidget_Admin extends richpostslistandgridLib {    
 
	   /**
		* Update the widget settings.
		* 
		* @access  private
		* @since   1.0
		*
		* @param   array  $new_instance  Set of POST form values
		* @param   array  $old_instance  Set of old form values
		* @return  array Sanitize form data 
		*/ 
		function update( $new_instance, $old_instance ) { 
		 
			foreach( $new_instance as $_key => $_value ) {
				if(is_array($new_instance[$_key]))
					$new_instance[$_key] = sanitize_text_field( implode( ",", $new_instance[$_key] ) );  
				else
					$new_instance[$_key] = sanitize_text_field( $new_instance[$_key] );  
			}    
			
			return $new_instance;
		
		} 
 
	   /**
		* Displays the widget settings controls on the widget panel.  
		*
		* @access  private
		* @since   1.0
		*
		* @param   array  $instance  Set of form values
		* @return  void
		*/
		function form( $instance ) {  
		
		//	$instance = wp_parse_args( $instance, $this->_config );   
		 
			// Filter values
			foreach( $instance as $_key => $_value ) {
				$instance[$_key]  = htmlspecialchars( $instance[$_key], ENT_QUOTES ); 
			}  	 
			
			if(count($instance) <= 0){
				foreach( $this->_config as $kw => $kw_val ) {
					$instance[$kw] = $this->_config[$kw]["default"];
				}
			}
			
			require( $this->getrichpostslistandgridTemplate( 'admin/admin_widget_settings.php' ) );
		
		}

 
	   /**
		* Show the list panel
		*
		* @access  private
		* @since   1.0
		*
		* @param   array  $args  Set of configuration values
		* @param   array  $instance  Set of configuration values
		* @return  void	  Displays widget html
		*/
		function widget( $args, $instance ) { 
		
			// Filter values
			foreach( $instance as $_key => $_value ) {
				$instance[$_key]  = htmlspecialchars( $instance[$_key], ENT_QUOTES ); 
			}   
			
			// default option settings
			foreach($this->_config as $default_options => $default_option_value ){
			  if(!isset($instance[$default_options]) && isset($default_option_value["default"]))
				$instance[$default_options] = $default_option_value["default"];
			}
			 
			if(count($instance)>0) {
				$this->_config = shortcode_atts( $this->_config, $instance ); 
			}
			if(!isset($this->_config["category_id"]))	
				$this->_config["category_id"] = 0;
				
			$this->_config["vcode"] = $vcode =  "uid_".md5(time().md5(json_encode($this->_config)).$this->getUCode());
			
			$this->_config["all_selected_categories"] = array( "type" => "none", "in_js" => "yes");	  
			$this->_config["all_selected_categories"]["default"] = "";			
			$_all_selected_categories =  "";
			if( isset($this->_config["category_id"]) && trim($this->_config["category_id"]) != "" ) {
				$_all_selected_categories = $this->_config["category_id"];
			} else {
				$_category_res = $this->getCategories(); 
				$_opt_all_id = array();
				foreach( $_category_res as $_category ) {  
					$_opt_all_id[] = $_category->id; 
				}
				$_all_selected_categories = implode( ",", $_opt_all_id );
			}
			$this->_config["all_selected_categories"]	= $_all_selected_categories;
			
			/**
			 * Load template according to admin settings
			 */
			echo $args['before_widget']; 
			ob_start();			
			require( $this->getrichpostslistandgridTemplate( "fronted/front_template.php" ) );	
			echo ob_get_clean();
			echo $args['after_widget']; 
		}
		
	}

}

/** 
 * Admin panel license configuration
 */
if ( ! class_exists( 'richpostslistandgridLicenseConfig_Admin' ) ) {
	
	class richpostslistandgridLicenseConfig_Admin extends richpostslistandgridLib {
	
		/**
		 * PHP5 constructor method.
		 *
		 * Register config menu and manage received data.
		 * 
		 * @access    public
		 * @since     1.0
		 *
		 * @return    void
		 */
		public function __construct() {
				
			add_action( 'admin_init', array( $this,'richpostslistandgrid_manage_license' ) ); 
			add_action( 'admin_menu', array( $this,'rplg_add_plugin_admin_menu' )  );  	
			$this->init_settings();	
			add_action( 'admin_notices', array( $this, 'ik_review_plugin' ) );
			add_action( 'admin_init', array( $this, 'ik_review_plugin_ignore' ) );
		}		
		
		/**
		 * Activate or deactivate plugin using license key.
		 * 
		 * @access    public
		 * @since     1.0
		 *
		 * @return    void
		 */
		public function richpostslistandgrid_manage_license() {

			if( isset( $_POST['btnAct'] ) && trim($_POST['btnAct']) != "" &&  isset( $_GET["page"] ) && trim( $_GET["page"] ) == "richpostslistandgrid_settings" ){
			
				if( ! check_admin_referer( 'richpostslistandgrid_nonce', 'richpostslistandgrid_nonce' ) ) 	
					return; 
				
				$act_key = trim( $_POST['richpostslistandgrid_license_key'] ); 
				
				if( $act_key == "" ) {
					
					wp_redirect(site_url()."/wp-admin/edit.php?post_type=rplg_view&page=richpostslistandgrid_settings&st=11");
					die();
					
				}
				
				$api_params = array( 
					'action'=> 'activate_license', 
					'license' 	=> $act_key, 
					'item_name' => 'wp_richpostslistandgrid', 
					'url'       => home_url()
				);
				
				$response = wp_remote_get( add_query_arg( $api_params, $this->_config["richpostslistandgrid_license_url"]["license_url"] ), array( 'timeout' => 15, 'sslverify' => false ) );
				
				if ( is_wp_error( $response ) )
					wp_redirect( site_url()."/wp-admin/edit.php?post_type=rplg_view&page=richpostslistandgrid_settings&st=10" );
					 
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );
				 
				update_option( 'richpostslistandgrid_license_status', $license_data->license_status );
				update_option( 'richpostslistandgrid_license_key', $license_data->license_key );
				update_option( 'richpostslistandgrid_license_reff', $license_data->license_reff );				
				 
				wp_redirect( site_url()."/wp-admin/edit.php?post_type=rplg_view&page=richpostslistandgrid_settings&st=".$license_data->st );
				die();				 
				
			}
			 
			
			if( isset( $_POST['btnDeact'] ) && trim($_POST['btnDeact']) != ""  &&  isset( $_GET["page"] ) && trim( $_GET["page"] ) == "richpostslistandgrid_settings" ) {
				
				if( ! check_admin_referer( 'richpostslistandgrid_nonce', 'richpostslistandgrid_nonce' ) ) 	
					return; 
				
				$license = trim( get_option( 'richpostslistandgrid_license_key' ) );
				  
				$api_params = array( 
					'action'=> 'deactivate_license', 
					'license' 	=> $license, 
					'item_name' => 'wp_richpostslistandgrid', 
					'url'       => home_url()
				);
				
				$response = wp_remote_get( add_query_arg( $api_params, $this->_config["richpostslistandgrid_license_url"]["license_url"] ), array( 'timeout' => 15, 'sslverify' => false ) );
				if ( is_wp_error( $response ) )
					wp_redirect(site_url()."/wp-admin/edit.php?post_type=rplg_view&page=richpostslistandgrid_settings&st=10");
				
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );
				
				delete_option( 'richpostslistandgrid_license_status' );
				delete_option( 'richpostslistandgrid_license_key' );
				delete_option( 'richpostslistandgrid_license_reff' );
				 
				wp_redirect(site_url()."/wp-admin/edit.php?post_type=rplg_view&page=richpostslistandgrid_settings&st=".$license_data->st);
				die();	
			}
			
		}


		/**
		 * View fields form to activate or deactivate plugin using license key and display the activation status of plugin.
		 * 
		 * @access    public
		 * @since     1.0
		 *
		 * @return    void
		 */		
		public function rplg_display_plugin_settings_admin_page() {
			
			$license 	= get_option( 'richpostslistandgrid_license_key' );
			$status 	= get_option( 'richpostslistandgrid_license_status' );
			
			$_message = "";
			if( isset( $_REQUEST["st"] ) && trim( $_REQUEST["st"] ) != "" ) { 
			
			
			
				$_st = trim($_REQUEST["st"]);
				if( $_st == '1' ) {
					$_message = __( 'License key has been activated.', 'richpostslistandgrid');
						
				} else if( $_st == '2' ) {
					$_message = __( 'Already plugin activated. Please deactivate from all of your sites before activating it.', 'richpostslistandgrid');	
						
				} else if( $_st == '3' ) {
					$_message = __( 'Your site url is not registered from ikhodal.com or invalid license key, Please add your site url and get license key from ikhodal.com account, if you have already purchased plugin for wordpress.', 'richpostslistandgrid');	
						
				} else if( $_st == '4' ) {
					$_message = __( 'License key has been deactivated.', 'richpostslistandgrid');
						
				} else if( $_st == '5' ) {
					$_message = __( "Invalid license key. Please get valid licence key from ikhodal.com account, If you have already purchased plugin for wordpress.", 'richpostslistandgrid');
						
				} else if( $_st == '10' ) {
					$_message = __( 'Please try again after some time.', 'richpostslistandgrid');
						
				} else if( $_st == '11' ) {
					$_message = __( 'Please enter valid license key.', 'richpostslistandgrid');
						
				}  
				
			}
			
			?> 
				<div  class="wrap">
					 
					<h2 class="hndle ui-sortable-handle"><span><?php echo esc_html( get_admin_page_title() ); ?></span></h2> 
					
					<p><?php _e( "Activate/Deactivate 'Rich Posts List & Grid View' plugin using provided license key.", 'richpostslistandgrid' ); ?></p>
					
					<?php
						if( $_message != "" ) {
							?>
								<div class=" notice <?php echo (($_st==1)?"updated":"error"); ?> ">
									<p><?php echo $_message; ?></p>
								</div>
							<?php
						}
					?>	
					 
					<form method="post" action="<?php echo site_url(); ?>/wp-admin/edit.php?post_type=rplg_view&page=richpostslistandgrid_settings"> 
						
						<table style="width:100%" id="wp_richpostslistandgrid_fields" class="richpostslistandgrid-admin postbox">
							<tbody>
								<?php if(!( $status !== false && $status == 'valid' )){ ?>
								<tr valign="top">	
									<td  class="tp-label" align="right" valign="top">
										<p><?php _e( 'License Key', 'richpostslistandgrid' ); ?></p>
									</td>
									<td>
										<p><input id="richpostslistandgrid_license_key" name="richpostslistandgrid_license_key" type="text" class="regular-text" value="<?php  echo $license; ?>" />
										<br /> <i><?php _e('Please enter valid license key.', 'richpostslistandgrid'); ?></i></p>
									</td>
								</tr> 
								<?php } ?>
								<tr valign="top">	
									<td  class="tp-label" align="right" valign="top">
										<p><?php _e('Current License Status', 'richpostslistandgrid'); ?></p>
									</td>
									<td>
										<p><strong><?php echo ( !( $status !== false && $status == 'valid' )?__( 'Deactivated', 'richpostslistandgrid' ) : __( 'Activated', 'richpostslistandgrid' ) ); ?></strong></p> 
									</td>
								</tr> 
								<tr valign="top">	
									<td><p>&nbsp;</p></td>
									<td>
										<p><?php wp_nonce_field( 'richpostslistandgrid_nonce', 'richpostslistandgrid_nonce' ); ?> 
										<?php if(!( $status !== false && $status == 'valid' )){ ?>
										<input type="submit" name="btnAct" id="btnAct" class="button button-primary" value="<?php _e( 'Activate', 'richpostslistandgrid' ); ?>" />&nbsp;
										<?php } else { ?>
										<input type="submit" name="btnDeact" id="btnDeact" class="button button-primary" value="<?php _e( 'Deactivate' , 'richpostslistandgrid' ); ?>" />
										<?php } ?></p>
									</td>
								</tr>
							</tbody>
						</table>	
						
					</form>
				</div> 
			
			<?php 
		}
		
		/**
		 * Register menu on left sidebar in admin panel.
		 * 
		 * @access    public
		 * @since     1.0
		 *
		 * @return    void
		 */	
		public function rplg_add_plugin_admin_menu() {
		
		
			add_submenu_page('edit.php?post_type=rplg_view', __( 'License Key', 'richpostslistandgrid'), __( 'License Key', 'richpostslistandgrid'), 'manage_options', 'richpostslistandgrid_settings', array( $this, 'rplg_display_plugin_settings_admin_page' )); 			
		 
		 
		 
		}		

	   /**
        * Write your reivew message.
        * 
        * @access    public
        * @since     1.0
        *
        * @return    void
        */			
		function ik_review_plugin() {
			global $current_user;
			$user_id = $current_user->ID;
			if (!get_user_meta($user_id, 'ik_rplg_review')) {
				echo '<div class="notice notice-info is-dismissible"><p>'. __('Thank you for installing <b>'.rplg_plugin_name.'</b>. <br />We really appreciate if you <a  href="'.rplg_plugin_url.'">write your reivew</a> to help us improve our plugin.  ') .' <a class="button" href="'.rplg_plugin_url.'">Write Review :)</a> <a class="button" href="?ik_rplg_review">Dismiss</a><br /> For more instant developer support <a href="'.rplg_plugin_support.'/supports">Create Support Ticket!</a> </p></div>';
			} 
		}
			
	   /**
		* Dismiss the plugin review.
		* 
		* @access    public
		* @since     1.0
		*
		* @return    void
		*/	
		function ik_review_plugin_ignore() {
			global $current_user;
			$user_id = $current_user->ID;
			if (isset($_GET['ik_rplg_review'])) { 
				add_user_meta($user_id, 'ik_rplg_review', 'true', true); 
			} 
		} 
		
	}
}

new richpostslistandgridLicenseConfig_Admin();