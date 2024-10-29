<?php if ( ! defined( 'ABSPATH' ) ) exit;   $vcode = $this->_config["vcode"];    ?>
<script type='text/javascript' language='javascript'><?php echo $this->rplg_js_obj( $this->_config ); ?></script> 
<?php 
 
$_categories = $this->_config["category_id"];
$category_type = $this->_config["category_type"];
$rplg_default_category_open = $this->_config["rplg_default_category_open"];
$all_selected_categories = $this->_config["all_selected_categories"];
$_is_rtl_enable = $this->_config["rplg_enable_rtl"];
$rplg_enable_post_count = $this->_config["rplg_enable_post_count"];
$rplg_hide_empty_category = $this->_config["rplg_hide_empty_category"];
$rplg_short_category_name_by = $this->_config["rplg_short_category_name_by"];
$rplg_hide_paging = $this->_config["rplg_hide_paging"]; 
$rplg_hide_post_image = $this->_config["rplg_hide_post_image"]; 
$rplg_hide_post_short_content = $this->_config["rplg_hide_post_short_content"]; 
$rplg_select_paging_type = $this->_config["rplg_select_paging_type"]; 
$rplg_hide_post_short_content_length = $this->_config["rplg_hide_post_short_content_length"]; 
$rplg_read_more_link = $this->_config["rplg_read_more_link"]; 
$rplg_order_category_ids = $this->_config["rplg_order_category_ids"]; 
$rplg_image_content_width = $this->_config["rplg_image_content_width"];	
$rplg_image_height = $this->_config["rplg_image_height"]; 
$rplg_shorting_posts_by = $this->_config["rplg_shorting_posts_by"]; 
$rplg_post_ordering_type = $this->_config["rplg_post_ordering_type"]; 
$_rplg_image_height_class = ""; 
 
if( $rplg_short_category_name_by != "id" ) 
	$rplg_order_category_ids = "";
	
$rplg_space_margin_between_posts = $this->_config["rplg_space_margin_between_posts"];
$rplg_posts_grid_alignment = $this->_config["rplg_posts_grid_alignment"];
$rplg_posts_loading_effect_on_pagination = $this->_config["rplg_posts_loading_effect_on_pagination"];
$rplg_mouse_hover_effect = $this->_config["rplg_mouse_hover_effect"];
$rplg_show_author_image_and_name = $this->_config["rplg_show_author_image_and_name"]; 
$template = $this->_config["template"];

$_u_agent = $_SERVER['HTTP_USER_AGENT'];
$_m_browser = '';  
if(strpos($_u_agent,'MSIE')>-1)
	$_m_browser = 'cls-ie-browser';
	
?> 
<div id="richpostslistandgrid" style="width:<?php echo esc_attr($this->_config["tp_widget_width"]); ?>"  class="<?php echo ((trim($_is_rtl_enable)=="yes")?"rplg-rtl-enabled":""); ?>   cls-<?php echo $rplg_posts_grid_alignment; ?> <?php echo $template; ?> ">
	<?php if($this->_config["hide_widget_title"]=="no"){ ?>
		<div class="ik-pst-tab-title-head" style="background-color:<?php echo esc_attr( $this->_config["header_background_color"] ); ?>;color:<?php echo esc_attr( $this->_config["header_text_color"] ); ?>"  >
			<?php echo esc_html( $this->_config["widget_title"] ); ?>   
		</div>
	<?php } ?> 
	<span class='wp-load-icon'>
		<img width="18px" height="18px" src="<?php echo rplg_media.'images/loader.gif'; ?>" />
	</span>
	<div  id="<?php echo esc_attr($vcode); ?>"  class="wea_content <?php echo $_m_browser; ?>  lt-tab <?php echo esc_attr($rplg_select_paging_type); ?>">
		
		<?php
			$_image_width_item = 0;
			if(   intval($rplg_image_content_width) > 0 ) {
				$_image_width_item = intval($rplg_image_content_width); 
			}	 
		?>
		<input type="hidden" class="imgwidth" value = "<?php echo $_image_width_item; ?>" />
		 
		<div class="clr"></div>
		<div class="item-posts <?php echo $rplg_mouse_hover_effect; ?>">
			<input type="hidden" class="ikh_templates" value="<?php echo $rplg_posts_grid_alignment; ?>" />
			<input type="hidden" class="ikh_posts_loads_from" value="<?php echo $rplg_posts_loading_effect_on_pagination; ?>" />
			<input type="hidden" class="ikh_border_difference" value="0" />
			<input type="hidden" class="ikh_margin_bottom" value="<?php echo $rplg_space_margin_between_posts; ?>" />
			<input type="hidden" class="ikh_margin_left" value="<?php echo $rplg_space_margin_between_posts; ?>" />
			<input type="hidden" class="ikh_image_height" value="<?php echo $rplg_image_height; ?>" />
			<input type="hidden" class="ikh_item_area_width" value="<?php echo $_image_width_item; ?>" /> 
			<div class="item-posts-wrap">
			<?php   
					 $post_search_text = ""; 
					 $category_id = $rplg_default_category_open;
					 $_limit_start = 0;
					 $_limit_end = $this->_config["number_of_post_display"];
					 $is_default_category_with_hidden = 0; 
				 
					// Category and search text field start ==== 
					 $_category_res = array();
					 $_total_post_count = 0;
					 $_category_res_n = array(); 
					 
					 if( trim($category_type) != "0" ) {
					 
							if( trim($all_selected_categories)=="0" || trim($all_selected_categories) == "" )
								$_category_res = $this->getCategories("",$rplg_order_category_ids);
							else 
								$_category_res = $this->getCategories($all_selected_categories,$rplg_order_category_ids); 

							
							if( count( $_category_res ) > 0 ) {  
						
								foreach( $_category_res as $_category ) { 
									$_total_post_count = $_total_post_count + $_category->count;
								} 
								
							} 
					 }
					 ?>  
						<div class="ik-post-category" > 
							<?php if( sanitize_text_field( $this->_config["hide_searchbox"] ) == 'no' ) { ?>
							<div class="ik-search-title" >
								 <input type="text" name="txtSearch" placeholder="<?php echo __( 'Search', 'richpostslistandgrid' ); ?>" value="<?php echo esc_html( htmlspecialchars( stripslashes( $post_search_text ) ) ); ?>" class="ik-post-search-text"  /> 
							</div>
							<?php }  
							if( count($_category_res) > 0 ) { 	?>    
								<div class="ik-search-category " style="<?php echo (( sanitize_text_field( $this->_config["hide_searchbox"] ) == 'yes' )?"display:none":""); ?>">
									<select name="selSearchCat" class='ik-drp-post-category' id="ik-drp-post-category" >
											
											<?php
												$_opt_arr = array();
												$_opt_all_id = array();
												foreach( $_category_res as $_category ) {  
												
													$_category_name = $_category->category;
													$_opt_all_id[] = $_category_id = $_category->id; 
													$_post_count = "";
													
													if( trim( $rplg_enable_post_count ) == "yes" ||  trim( $rplg_hide_empty_category ) == "yes" ) {
													
														$_post_count = " (".$_category->count.")";
														
														if( trim( $rplg_hide_empty_category ) == "yes"  && intval( $_category->count ) <= 0 )
															continue;
														
													} 
													
													$_opt_arr[] = '<option value="'.$_category_id.'">'.$_category_name.$_post_count.'</option>';
												
												}
											?>
											<option value="<?php echo implode(",",$_opt_all_id); ?>"><?php echo __( 'All', 'richpostslistandgrid' ); ?></option>
											<?php echo implode("",$_opt_arr); ?>
									</select> 
								</div> 	
							<?php } ?>	
							<div style="<?php echo (( sanitize_text_field( $this->_config["hide_searchbox"] ) == 'yes' )?"display:none":""); ?>" class="ik-search-button" onclick='rplg_fillPosts( "<?php echo esc_js( $this->_config["vcode"] ); ?>", "<?php echo esc_js($all_selected_categories); ?>", request_obj_<?php echo esc_js( $this->_config["vcode"] ); ?>, 2)'> <img width="18px" alt="Search" height="18px" src="<?php echo rplg_media.'images/searchicon.png'; ?>" /> </div>
							
							<?php if( count($_category_res) <= 0 ) {
								  echo "<input type='hidden' value='0' id='ik-drp-post-category' class='ik-drp-post-category' />"; 
							} ?>
							<div class="clrb"></div>
						</div> 
						<?php 
						
					
					// Category and search text field end ==== 
					$__current_term_count = $this->getSqlResult( $all_selected_categories, $post_search_text, 0, 0, 1, $is_default_category_with_hidden, 1 );
					$__current_term_count = $__current_term_count[0]->total_val;
					$_total_posts =  $__current_term_count; 
					 
					$post_list = $this->getSqlResult( $all_selected_categories, $post_search_text, 0, $_limit_end ); 
					if( count($post_list) > 0 ) {
						foreach ( $post_list as $_post ) { 
					
						$image  = $this->getPostImage( $_post->post_image, $rplg_image_content_width, $this->_config["rplg_image_height"] ); 
						$_author_name = esc_html($_post->display_name);
						$_author_image = get_avatar($_post->post_author,25);
						?> 
						<div style="<?php echo "width:".esc_attr($rplg_image_content_width)."px"; ?>" class='ikh-post-item-box pid-<?php echo esc_attr( $_post->post_id ); ?>'> 
							<div class="ikh-post-item ikh-simple"> 
							<?php 
								ob_start();
								if( $rplg_hide_post_image == "no" ) { ?> 	
									<div  class='ikh-image'  > 
										<a href="<?php echo get_permalink( $_post->post_id ); ?>"> 
											<?php echo $image; ?>
										</a>     
									</div>  
								<?php } 
								$_ob_image = ob_get_clean();  

								ob_start();
								?>   
								<div class='ikh-content'> 
								   <div class="ikh-content-data">
									
										<div class='ik-post-name'>												
											<?php if( sanitize_text_field( $this->_config["hide_post_title"] ) =='no'){ ?>  
												<a href="<?php echo get_permalink( $_post->post_id ); ?>" style="color:<?php echo esc_attr( $this->_config["title_text_color"] ); ?>" >
													<?php echo esc_html( $_post->post_title ); ?>
												</a>
											<?php } ?>	 
											
											<?php if( sanitize_text_field( $this->_config["rplg_hide_posted_date"] ) =='no'){ ?> 
													<div class='ik-post-date'>
														<i><?php echo date(get_option("date_format"),strtotime($_post->post_date)); ?></i>
													</div>
											<?php } ?>	
										
											 <?php if( $rplg_hide_post_short_content == "no" ) { ?>
												<div class='ik-post-sub-content'>
													<?php																		
													 if( strlen( strip_tags( $_post->post_content ) ) > intval( $rplg_hide_post_short_content_length ) ) 	
														echo substr( strip_tags( $_post->post_content ), 0, $rplg_hide_post_short_content_length )."..";  
													 else
														echo trim( strip_tags( $_post->post_content ) );																			
													?> 
												</div>
											<?php } ?> 
										</div>
										
										<?php if( sanitize_text_field( $this->_config["rplg_hide_comment_count"] ) =='no'){ ?> 
											<div class='ik-post-comment'>
												<?php 
													$_total_comments = (get_comment_count($_post->post_id)); 			
													if($_total_comments["total_comments"] > 0) {
														echo $_total_comments["total_comments"]; 
														?> <?php echo (($_total_comments["total_comments"]>1)?__( 'Comments', 'richpostslistandgrid' ):__( 'Comment', 'richpostslistandgrid' )); 
													}
												?>
											</div>
										<?php } ?>	 
											
										<?php if( sanitize_text_field( $this->_config["rplg_show_author_image_and_name"] ) =='yes') { ?> 
											<div class='ik-post-author'>
												<?php echo (($_author_image!==FALSE)?$_author_image:"<img src='".rplg_media."images/user-icon.png' width='25' height='25' />"); ?> <?php echo __( 'By', 'richpostslistandgrid' ); ?> <?php echo $_author_name; ?>
											</div>
										<?php } ?>	 		
										
										<?php if( $rplg_read_more_link == "no" ) { ?>
											<div class="rplg-read-more-link">
												<a class="lnk-post-content" href="<?php echo get_permalink( $_post->post_id ); ?>" >
													<?php echo __( 'Read More', 'richpostslistandgrid' ); ?>
												</a>
											</div>
										<?php } ?>  
										</div> 
								</div>	
							 <?php
							$_ob_content = ob_get_clean(); 
						
							if($rplg_mouse_hover_effect=='ikh-image-style-40'|| $rplg_mouse_hover_effect=='ikh-image-style-41' ){
								echo $_ob_content;
								echo $_ob_image;
							} else {
								echo $_ob_image;
								echo $_ob_content;														
							}	
							 ?>
							<div class="clr1"></div>
							</div> 
						</div> 
						<?php 
					} 
					 
					/******PAGING*******/
					if( $rplg_hide_paging == "no" &&  $rplg_select_paging_type == "load_more_option" && $_total_posts > sanitize_text_field( $this->_config["number_of_post_display"] ) ) { 

								?>
								<div class="clr"></div>
								<div class='ik-post-load-more'  align="center" onclick='rplg_loadMorePosts( "<?php echo esc_js( $all_selected_categories ); ?>", "<?php echo esc_js( $_limit_start+$_limit_end ); ?>", "<?php echo esc_js( $this->_config["vcode"] ); ?>", "<?php echo esc_js( $_total_posts ); ?>", request_obj_<?php echo esc_js( $this->_config["vcode"] ); ?> )'>
									<?php echo __('Load More', 'richpostslistandgrid' ); ?>
								</div>
								<?php   
							 
					} else if( $rplg_hide_paging == "no" &&  $rplg_select_paging_type == "next_and_previous_links" ) { 
						
							?><div class="clr"></div>
							<div class="rplg-simple-paging"><?php
							echo $this->displayPagination(  0, $_total_posts, $all_selected_categories, $_limit_start, $_limit_end, $this->_config["vcode"], 2 );
							?></div><div class="clr"></div><?php
						
					} else if( $rplg_hide_paging == "no" &&  $rplg_select_paging_type == "simple_numeric_pagination" ) { 
						
							?><div class="clr"></div>
							<div class="rplg-simple-paging"><?php
							echo $this->displayPagination(  0, $_total_posts, $all_selected_categories, $_limit_start, $_limit_end, $this->_config["vcode"], 1 );
							?></div><div class="clr"></div><?php
						
					} else {
							?><div class="clr"></div><?php
					}
					/******PAGING END*********/
				} else {
					?><div class="ik-post-no-items"><?php echo __( 'No posts found.', 'richpostslistandgrid' ); ?></div><?php 										
				}
				
				?><script type='text/javascript' language='javascript'><?php echo $this->rplg_js_obj( $this->_config ); ?></script><?php
				
			  
			?> 
			</div>
		</div>
		<div class="clr"></div>
	</div>
</div>