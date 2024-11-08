<?php  
/**
 * Register shortcode and render post data as per shortcode configuration. 
 */ 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
if ( ! class_exists( 'richpostslistandgridWidget' ) ) { 
	class richpostslistandgridWidget extends richpostslistandgridLib {
	 
	   /**
		* constructor method.
		*
		* Run the following methods when this class is loaded
		*
		* @access  public
		* @since   1.0
		*
		* @return  void
		*/ 
		public function __construct() {
		
			add_action( 'init', array( &$this, 'init' ) ); 
			parent::__construct();
			
		}  
		
	   /**
		* Load required methods on wordpress init action 
		*
		* @access  public
		* @since   1.0
		*
		* @return  void
		*/ 
		public function init() {
		
			add_action( 'wp_ajax_rplg_getTotalPosts',array( &$this, 'rplg_getTotalPosts' ) );
			add_action( 'wp_ajax_rplg_getPosts',array( &$this, 'rplg_getPosts' ) ); 
			add_action( 'wp_ajax_rplg_getMorePosts',array( &$this, 'rplg_getMorePosts' ) );
			
			add_action( 'wp_ajax_nopriv_rplg_getTotalPosts', array( &$this, 'rplg_getTotalPosts' ) );
			add_action( 'wp_ajax_nopriv_rplg_getPosts', array( &$this, 'rplg_getPosts' ) ); 
			add_action( 'wp_ajax_nopriv_rplg_getMorePosts', array( &$this, 'rplg_getMorePosts' ) ); 
			
			add_shortcode( 'richpostslistandgrid', array( &$this, 'richpostslistandgrid' ) ); 
			
		} 
		
	   /**
		* Get the total numbers of posts
		*
		* @access  public
		* @since   1.0
		* 
		* @param   int    $category_id  		Category ID 
		* @param   string $post_search_text  Post name or any search keyword to filter posts
		* @param   int    $c_flg  				Whether to fetch whether posts by category id or prevent for searching
		* @param   int    $is_default_category_with_hidden  To check settings of default category If it's value is '1'. Default value is '0'
		* @return  int	  Total number of posts  	
		*/  
		public function rplg_getTotalPosts( $category_id, $post_search_text, $c_flg, $is_default_category_with_hidden ) { 
		
			global $wpdb;   
			
		   /**
			* Check security token from ajax request
			*/
			check_ajax_referer(  $this->_config["rplg_security_key"]["security_key"], 'security' );

		   /**
			* Fetch posts as per search filter
			*/	
			$_res_total = $this->getSqlResult( $category_id, $post_search_text, 0, 0, $c_flg, $is_default_category_with_hidden, 1 );
			
			return $_res_total[0]->total_val;
			 
		}	

		 
	   /**
		* Render category and posts view shortcode
		*
		* @access  public
		* @since   1.0
		*
		* @param   array   $params  Shortcode configuration options from admin settings
		* @return  string  Render category and posts HTML
		*/
		public function richpostslistandgrid( $params = array() ) { 	
			
			if(isset($params["id"]) && trim($params["id"]) != "" && intval($params["id"]) > 0) {
				$richpostslistandgrid_id = $params["id"]; 
				$rplg_shortcode = get_post_meta( $richpostslistandgrid_id ); 
				
				foreach ( $rplg_shortcode as $sc_key => $sc_val ) {			
					$rplg_shortcode[$sc_key] = $sc_val[0];			
				} 
				
				if(!isset($rplg_shortcode["number_of_post_display"]))	
					$rplg_shortcode["number_of_post_display"] = 0;
				if(!isset($rplg_shortcode["category_id"]))	
					$rplg_shortcode["category_id"] = 0;
					
				$this->_config = shortcode_atts( $this->_config, $rplg_shortcode ); 
				$this->_config["vcode"] =  "uid_".md5(md5(json_encode($this->_config)).$this->getUCode());	
				
			} else {
				$this->init_settings(); 
				
				if(!(is_array($params) && count($params > 0 ))){
					$params = array();
				}
				
				// default option settings
				foreach($this->_config as $default_options => $default_option_value ){
				  if(!isset($params[$default_options]) && isset($default_option_value["default"]))
					$params[$default_options] = $default_option_value["default"];
				}
				
				if(count($params)>0) {
					$this->_config = shortcode_atts( $this->_config, $params ); 
				}
				if(!isset($this->_config["category_id"]))	
					$this->_config["category_id"] = 0;
					
				$this->_config["vcode"] =  "uid_".md5(md5(json_encode($this->_config)).$this->getUCode());
			}
			
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
			ob_start();
			
			require( $this->getrichpostslistandgridTemplate( "fronted/front_template.php" ) );
			
			return ob_get_clean();
		
		}   
		
	   /**
		* Load more post via ajax request
		*
		* @access  public
		* @since   1.0
		* 
		* @return  void Displays searched posts HTML to load more pagination
		*/	
		public function rplg_getMorePosts() {
		
			global $wpdb, $wp_query; 
			
		   /**
			* Check security token from ajax request
			*/
			check_ajax_referer($this->_config["rplg_security_key"]["security_key"], 'security' );
			
			$_total = ( isset( $_REQUEST["total"] )?esc_attr( $_REQUEST["total"] ):0 );
			$category_id = ( isset( $_REQUEST["category_id"] )?esc_attr( $_REQUEST["category_id"] ):0 );
			$post_search_text = ( isset( $_REQUEST["post_search_text"] )?esc_attr( $_REQUEST["post_search_text"] ):"" );  
			$_limit_start = ( isset( $_REQUEST["limit_start"])?esc_attr( $_REQUEST["limit_start"] ):0 );
			$_limit_end = ( isset( $_REQUEST["number_of_post_display"])?esc_attr( $_REQUEST["number_of_post_display"] ):rplg_number_of_post_display ); 
			$all_selected_categories = $_REQUEST["all_selected_categories"]; 
		    $category_id_default =( ( isset( $_REQUEST["category_id_default"] ) && trim( $_REQUEST["category_id_default"] ) != ""  ) ? esc_html( $_REQUEST["category_id_default"] ) : esc_html( $all_selected_categories ));	
	
		   /**
			* Fetch posts as per search filter
			*/	
			$_result_items = $this->getSqlResult( $category_id_default, $post_search_text, $_limit_start, $_limit_end );
		  
			require( $this->getrichpostslistandgridTemplate( 'fronted/ajax_load_more_posts.php' ) );	
			
			wp_die();
		}    
		
	   /**
		* Load more posts via ajax request
		*
		* @access  public
		* @since   1.0
		* 
		* @return  object Displays searched posts HTML
		*/
		public function rplg_getPosts() {
		
		   global $wpdb; 
			
		   /**
			* Check security token from ajax request
			*/	
		   check_ajax_referer( $this->_config["rplg_security_key"]["security_key"], 'security' );	   
		   
		   require( $this->getrichpostslistandgridTemplate( 'fronted/ajax_load_posts.php' ) );	
		   
  		   wp_die();
		
		}
		 
	   /**
		* Get post list with specified limit and filtered by category and search text
		*
		* @access  public
		* @since   1.0 
		*
		* @param   int     $category_id 		 Selected category ID 
		* @param   string  $post_search_text  Post name or any search keyword to filter posts
		* @param   int     $_limit_end			 Limit to fetch post ending to given position
		* @return  object  Set of searched post data
		*/
		public function getPostList( $category_id, $post_search_text, $_limit_end ) {
			
		   /**
			* Check security token from ajax request
			*/	
			check_ajax_referer( $this->_config["rplg_security_key"]["security_key"], 'security' );		
			
		   /**
			* Fetch data from database
			*/
			return $this->getSqlResult( $category_id, $post_search_text, 0, $_limit_end );
			 
		} 
		
	}
	
}
new richpostslistandgridWidget();