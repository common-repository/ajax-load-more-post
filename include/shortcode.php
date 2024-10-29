<?php 
/** 
 * Register custom post type to manage shortcode
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
if ( ! class_exists( 'richpostslistandgridShortcode_Admin' ) ) {
	class richpostslistandgridShortcode_Admin extends richpostslistandgridLib {
	
		public $_shortcode_config = array();
		 
		/**
		 * constructor method.
		 *
		 * Register post type for category and posts view shortcode
		 * 
		 * @access    public
		 * @since     1.0
		 *
		 * @return    void
		 */
		public function __construct() {
			
			parent::__construct();
			
	       /**
		    * Register hooks to manage custom post type for category and posts view
		    */
			add_action( 'init', array( &$this, 'rplg_registerPostType' ) );  
			//add_action( 'admin_menu', array( &$this, 'rplg_addadminmenu' ) );  
			add_action( 'add_meta_boxes', array( &$this, 'add_richpostslistandgrid_metaboxes' ) );
			add_action( 'save_post', array(&$this, 'wp_save_richpostslistandgrid_meta' ), 1, 2 ); 
			add_action( 'admin_enqueue_scripts', array( $this, 'rplg_admin_enqueue' ) ); 
			
		   /* Register hooks for displaying shortcode column. */ 
			if( isset( $_REQUEST["post_type"] ) && !empty( $_REQUEST["post_type"] ) && trim($_REQUEST["post_type"]) == "rplg_view" ) {
				add_action( "manage_posts_custom_column", array( $this, 'richpostslistandgridShortcodeColumns' ), 10, 2 );
				add_filter( 'manage_posts_columns', array( $this, 'rplg_shortcodeNewColumn' ) );
			}
			
			add_action( 'wp_ajax_rplg_getCategoriesOnTypes',array( &$this, 'rplg_getCategoriesOnTypes' ) ); 
			add_action( 'wp_ajax_nopriv_rplg_getCategoriesOnTypes', array( &$this, 'rplg_getCategoriesOnTypes' ) );
			add_action( 'wp_ajax_rplg_getCategoriesRadioOnTypes',array( &$this, 'rplg_getCategoriesRadioOnTypes' ) ); 
			add_action( 'wp_ajax_nopriv_rplg_getCategoriesRadioOnTypes', array( &$this, 'rplg_getCategoriesRadioOnTypes' ) ); 
			add_filter( 'wp_editor_settings', array( $this, 'rplg_postbodysettings' ), 10, 2 );
		}  
		
		/**
		* Set the post body type
		*
		* @access  private
		* @since   1.0
		*
		* @return  void
		*/  
		public function rplg_postbodysettings( $settings, $editor_id ) { 
		
			global $post; 
			
			if( $post->post_type == "rplg_posttabs" ) {
			
				$settings = array(
						'wpautop'             => false,
						'media_buttons'       => false,
						'default_editor'      => '',
						'drag_drop_upload'    => false,
						'textarea_name'       => $editor_id,
						'textarea_rows'       => 20,
						'accordionindex'            => '',
						'accordionfocus_elements'   => ':prev,:next',
						'editor_css'          => '',
						'editor_class'        => '',
						'teeny'               => false,
						'dfw'                 => false,
						'_content_editor_dfw' => false,
						'tinymce'             => true,
						'quicktags'           => true
					);
			
			}
			
			return $settings;
			
		}
		
		/**
		* Admin menu configuration 
		*
		* @access  private
		* @since   1.0
		*
		* @return  void
		*/  
		public function rplg_addadminmenu() { 
		
		
			add_submenu_page('edit.php?post_type=rplg_view', __( 'All Posts', 'richpostslistandgrid' ), __( 'All Posts', 'richpostslistandgrid' ),  'manage_options', 'edit.php?post_type=rplg_posttabs');
			
			add_submenu_page('edit.php?post_type=rplg_view', __( 'New Post', 'richpostslistandgrid' ), __( 'New Post', 'richpostslistandgrid' ),  'manage_options', 'post-new.php?post_type=rplg_posttabs'); 
			
			add_submenu_page('edit.php?post_type=rplg_view', __( 'Custom Categories', 'richpostslistandgrid' ), __( 'Custom Categories', 'richpostslistandgrid' ),  'manage_options', 'edit-tags.php?taxonomy=rplg_categories&post_type=rplg_view'); 
			
		}
		
 	   /**
		* Register and load JS/CSS for admin widget configuration 
		*
		* @access  private
		* @since   1.0
		*
		* @return  bool|void It returns false if not valid page or display HTML for JS/CSS
		*/  
		public function rplg_admin_enqueue() {

			if ( ! $this->validate_page() )
				return FALSE;
			
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_style( 'admin-richpostslistandgrid.css', rplg_media."css/admin-richpostslistandgrid.css" );
			wp_enqueue_script( 'admin-richpostslistandgrid.js', rplg_media."js/admin-richpostslistandgrid.js" ); 
			
		}		
		 
	   /**
		* Add meta boxes to display shortcode
		*
		* @access  private
		* @since   1.0
		*
		* @return  void
		*/ 
		public function add_richpostslistandgrid_metaboxes() {
			
			/**
			 * Add custom fields for shortcode settings
		     */
			add_meta_box( 'wp_richpostslistandgrid_fields', __( 'Post List and Grid View', 'richpostslistandgrid' ),
				array( &$this, 'wp_richpostslistandgrid_fields' ), 'rplg_view', 'normal', 'high' );
			
			/**
			 * Display shortcode of category and posts tab
		     */
			add_meta_box( 'wp_richpostslistandgrid_shortcode', __( 'Shortcode', 'richpostslistandgrid' ),
				array( &$this, 'shortcode_meta_box' ), 'rplg_view', 'side' );	
		
		}  
		
	   /**
		* Validate widget or shortcode post type page
		*
		* @access  private
		* @since   1.0
		*
		* @return  bool It returns true if page is post.php or widget otherwise returns false
		*/ 
		private function validate_page() {

			if ( ( isset( $_GET['post_type'] )  && $_GET['post_type'] == 'rplg_view' ) || strpos($_SERVER["REQUEST_URI"],"widgets.php") > 0  || strpos($_SERVER["REQUEST_URI"],"post.php" ) > 0 || strpos($_SERVER["REQUEST_URI"], "richpostslistandgrid_settings" ) > 0  )
				return TRUE;
		
		} 			
 
	   /**
		* Display richpostslistandgrid block configuration fields
		*
		* @access  private
		* @since   1.0
		*
		* @return  void Returns HTML for configuration fields 
		*/  
		public function wp_richpostslistandgrid_fields() {
			
			global $post; 
			 
			foreach( $this->_config as $kw => $kw_val ) {
				$this->_shortcode_config[$kw] = get_post_meta( $post->ID, $kw, true ); 
			}
			 
			foreach ( $this->_shortcode_config as $sc_key => $sc_val ) {
				if( trim( $sc_val ) == "" )
					unset( $this->_shortcode_config[ $sc_key ] );
				else {
					if(!is_array($sc_val) && trim($sc_val) != "" ) 
						$this->_shortcode_config[ $sc_key ] = htmlspecialchars( $sc_val, ENT_QUOTES );
					else 
						$this->_shortcode_config[ $sc_key ] = $sc_val;
				}	
			}
			if(count($this->_shortcode_config) <= 0){
				foreach( $this->_config as $kw => $kw_val ) {
					$this->_shortcode_config[$kw] = $this->_config[$kw]["default"];
				}
			}
			foreach( $this->_config as $kw => $kw_val ) {
				if( isset($this->_shortcode_config[$kw]) && !is_array($this->_shortcode_config[$kw]) && trim($this->_shortcode_config[$kw]) == "" ) {
					$this->_shortcode_config[$kw] = $this->_config[$kw]["default"];
				} 
			}
			
			$this->_shortcode_config["vcode"] = get_post_meta( $post->ID, 'vcode', true );    
			 
			require( $this->getrichpostslistandgridTemplate( "admin/admin_shortcode_post_type.php" ) );
			
		}
		
	   /**
		* Display shortcode in edit mode
		*
		* @access  private
		* @since   1.0
		*
		* @param   object  $post Set of configuration data.
		* @return  void	   Displays HTML of shortcode
		*/
		public function shortcode_meta_box( $post ) {

			$richpostslistandgrid_id = $post->ID;

			if ( get_post_status( $richpostslistandgrid_id ) !== 'publish' ) {

				echo '<p>'.__( 'Please make the publish status to get the shortcode', 'richpostslistandgrid' ).'</p>';

				return;

			}

			$richpostslistandgrid_title = get_the_title( $richpostslistandgrid_id );

			$shortcode = sprintf( "[%s id='%s']", 'richpostslistandgrid', $richpostslistandgrid_id );
			
			echo "<p class='tpp-code'>".$shortcode."</p>";
		}
				  
	   /**
		* Save category and posts view shortcode fields
		*
		* @access  private
		* @since   1.0 
		*
		* @param   int    	$post_id post id
		* @param   object   $post    post data object
		* @return  void
		*/ 
		function wp_save_richpostslistandgrid_meta( $post_id, $post ) {
			
		/*	if( !isset($_POST['richpostslistandgrid_nonce']) ) {
				return $post->ID;
			} 
			if( !wp_verify_nonce( $_POST['richpostslistandgrid_nonce'], plugin_basename(__FILE__) ) ) {
				return $post->ID;
			}
			*/
			
		   /**
			* Check current user permission to edit post
			*/
			if(!current_user_can( 'edit_post', $post->ID ))
				return $post->ID;
				
			 /**
			* sanitize text fields 
			*/
			$rplg_meta = array(); 
			
			foreach( $this->_config as $kw => $kw_val ) { 
				$_save_value =  $_POST["nm_".$kw];
				if($kw_val["type"]=="boolean"){
					$_save_value = $_POST["nm_".$kw][0];
				}
				if( $kw_val["type"]=="checkbox" && count($_POST["nm_".$kw]) > 0 ) {
					$_save_value = implode( ",", $_POST["nm_".$kw] );
				}
				$rplg_meta[$kw] =  sanitize_text_field( $_save_value );
			}     
			 
			foreach ( $rplg_meta as $key => $value ) {
			
			   if( $post->post_type == 'revision' ) return;
				$value = implode( ',', (array)$value );
				
				if( trim($value) == "Array" || is_array($value) )
					$value = "";
					
			   /**
				* Add or update posted data 
				*/
				if( get_post_meta( $post->ID, $key, FALSE ) ) { 
					update_post_meta( $post->ID, $key, $value );
				} else { 
					add_post_meta( $post->ID, $key, $value );
				} 
			
			}		
			
		  
		}
		
			 
	   /**
		* Register post type category and posts shortcode view
		*
		* @access  private
		* @since   1.0
		*
		* @return  void
		*/  
		function rplg_registerPostType() { 
			
		   /**
			* Post type and menu labels 
			*/
			$labels = array(
				'name' => __('Rich Posts List & Grid View Shortcode', 'richpostslistandgrid' ),
				'singular_name' => __( 'Rich Posts List & Grid View Shortcode', 'richpostslistandgrid' ),
				'add_new' => __( 'Add New Shortcode', 'richpostslistandgrid' ),
				'add_new_item' => __( 'Add New Shortcode', 'richpostslistandgrid' ),
				'edit_item' => __( 'Edit', 'richpostslistandgrid'  ),
				'new_item' => __( 'New', 'richpostslistandgrid'  ),
				'all_items' => __( 'All', 'richpostslistandgrid'  ),
				'view_item' => __( 'View', 'richpostslistandgrid'  ),
				'search_items' => __( 'Search', 'richpostslistandgrid'  ),
				'not_found' =>  __( 'No item found', 'richpostslistandgrid'  ),
				'not_found_in_trash' => __( 'No item found in Trash', 'richpostslistandgrid'  ),
				'parent_item_colon' => '',
				'menu_name' => __( 'APLM', 'richpostslistandgrid'  ) 
			);
			
		   /**
			* Custom posts posttype registration options
			*/
			$args = array(
				'labels' => $labels,
				'public' => false,
				'publicly_queryable' => false,
				'show_ui' => true,
				'show_in_menu' => true,
				'query_var' => false,
				'rewrite' => false,
				'capability_type' => 'post',
				'menu_icon' => 'dashicons-list-view',
				'has_archive' => false,
				'hierarchical' => false,
				'menu_position' => null,
				'supports' => array( 'title' )
			);
			 
		   /**
			* Register new post type
			*/ 
			register_post_type( 'rplg_view', $args );
			
			/**
			*  menu labels 
			*/
			/*$labels = array(
				'name' => __('Posts', 'richpostslistandgrid' ),
				'singular_name' => __( 'Posts', 'richpostslistandgrid' ),
				'add_new' => __( 'New Post', 'richpostslistandgrid' ),
				'add_new_item' => __( 'New Post', 'richpostslistandgrid' ),
				'edit_item' => __( 'Edit', 'richpostslistandgrid'  ),
				'new_item' => __( 'New', 'richpostslistandgrid'  ),
				'all_items' => __( 'All', 'richpostslistandgrid'  ),
				'view_item' => __( 'View', 'richpostslistandgrid'  ),
				'search_items' => __( 'Search', 'richpostslistandgrid'  ),
				'not_found' =>  __( 'No item found', 'richpostslistandgrid'  ),
				'not_found_in_trash' => __( 'No item found in Trash', 'richpostslistandgrid'  ),
				'parent_item_colon' => '',
				'menu_name' => __( 'Posts', 'richpostslistandgrid'  ) 
			);*/
			
		   /**
			*  post type registration options
			*/
			/*$args = array(
				'labels' => $labels,
				'public' => false,
				'publicly_queryable' => false,
				'show_ui' => true,
				'show_in_menu' => false,
				'query_var' => false,
				'rewrite' => false,
				'capability_type' => 'post',
				'menu_icon' => 'dashicons-list-view',
				'has_archive' => false,
				'hierarchical' => false,
				'menu_position' => null,
				'supports' => array(  'title','editor','thumbnail' )
			);*/ 
			
		   /**
			* Register post type
			*/ 
			//register_post_type( 'rplg_posttabs', $args ); 	
				
		   
		   /**
			* Register category for custom post type
			*/
			
			/*$labels = array(
					'name' => _x( 'Custom Posts Categories', 'taxonomy general name' ),
					'singular_name' => _x( 'Custom Post Category', 'taxonomy singular name' ),
					'search_items' => __( 'Search Categories' ),
					'all_items' => __( 'All Categories' ),
					'parent_item' => array( null ),
					'parent_item_colon' => array( null ),
					'edit_item' => __( 'Edit Category' ),
					'view_item' => __( 'View Category' ),
					'update_item' => __( 'Update Category' ),
					'add_new_item' => __( 'Add New Category' ),
					'new_item_name' => __( 'New Category Name' ), 
					'not_found' => __( 'No categories found.' ),
					'no_terms' => __( 'No categories' ),
					'items_list_navigation' => __( 'Categories list navigation' ),
					'items_list' => __( 'Categories list' ),
			);

			register_taxonomy('rplg_categories',array('rplg_posttabs'),array(
				'hierarchical'=>true,
				'labels' => $labels,
				'show_ui'=>true,
				'show_admin_column'=>true,
				'query_var'=>true,
				'rewrite'=>array('slug' => 'rplg_categories'),
			));	*/

		}
		
	   /**
		* Display shortcode column in category and posts list
		*
		* @access  private
		* @since   1.0
		*
		* @param   string  $column  Column name
		* @param   int     $post_id Post ID
		* @return  void	   Display shortcode in column	
		*/
		public function richpostslistandgridShortcodeColumns( $column, $post_id ) { 
		
			if( $column == "shortcode" ) {
				 echo sprintf( "[%s id='%s']", 'richpostslistandgrid', $post_id ); 
			}  
		
		}
		
	   /**
		* Register shortcode column
		*
		* @access  private
		* @since   1.0
		*
		* @param   array  $columns  Column list 
		* @return  array  Returns column list
		*/
		public function rplg_shortcodeNewColumn( $columns ) {
			
			$_edit_column_list = array();	
			$_i = 0;
			
			foreach( $columns as $__key => $__value) {
					
					if($_i==2){
						$_edit_column_list['shortcode'] = __( 'Shortcode', 'richpostslistandgrid' );
					}
					$_edit_column_list[$__key] = $__value;
					
					$_i++;
			}
			
			return $_edit_column_list;
		
		}
		
	} 

}

new richpostslistandgridShortcode_Admin();
 
?>