<?php if ( ! defined( 'ABSPATH' ) ) exit; 
	 $params = $_REQUEST;  
	 $all_selected_categories = $params["all_selected_categories"]; 
	 $category_id_default =( ( isset( $params["category_id_default"] ) && trim( $params["category_id_default"] ) != ""  ) ? esc_html( $params["category_id_default"] ) : esc_html( $all_selected_categories ));
	 $category_id_all =( ( isset( $params["category_id"] ) && trim( $params["category_id"] ) != ""  ) ? esc_html( $params["category_id"] ) : "" );	 
	 $rplg_order_category_ids =( ( isset( $params["rplg_order_category_ids"] ) && trim( $params["rplg_order_category_ids"] ) != ""  ) ? ( $params["rplg_order_category_ids"] ) : "" );	 	 
	 $rplg_enable_post_count =( isset( $params["rplg_enable_post_count"] ) ? esc_html( $params["rplg_enable_post_count"] ) : "" ); 
	 $post_search_text =( isset( $params["post_search_text"] ) ? esc_html( $params["post_search_text"] ) : "" ); 
	 $_limit_start =( isset( $params["limit_start"] ) ? intval( $params["limit_start"] ) : 0 );
	 $_limit_end = intval( $params["number_of_post_display"] );
	 $is_default_category_with_hidden = 0; 
	 $static_width = ( ( isset( $params["rplg_image_content_width"] ) && intval( $params["rplg_image_content_width"] ) > 0  ) ? intval($params["rplg_image_content_width"]) : 180 );
	 $final_width = $params["rplg_image_content_width"]; 
	 $rplg_image_height = $params["rplg_image_height"];   
	 $category_type = $params["category_type"];  
	 
	 $rplg_mouse_hover_effect = $params["rplg_mouse_hover_effect"]; 
	 
	     
	if( $this->rplg_getTotalPosts( $all_selected_categories, $post_search_text, 0, $is_default_category_with_hidden ) > 0 ) {
	
		$_category_res = array();
		if( trim($category_type) != "0" ) {
			if( trim($all_selected_categories)=="0" || trim($all_selected_categories) == "" )
				$_category_res = $this->getCategories("",$rplg_order_category_ids);
			else 
				$_category_res = $this->getCategories($all_selected_categories,$rplg_order_category_ids);
		}	
		
		if(  !( sanitize_text_field( $params["hide_searchbox"] ) == 'yes' ) ) { 
			?> 
			<div class="ik-post-category"> 
				<?php if( sanitize_text_field( $params["hide_searchbox"] ) == 'no' ) { ?>
					<div class="ik-search-title" >
					  <input type="text" name="txtSearch" placeholder="<?php echo __( 'Search', 'richpostslistandgrid' ); ?>" value="<?php echo esc_html( htmlspecialchars( stripslashes( $post_search_text ) ) ); ?>" class="ik-post-search-text"  /> 
					</div>	
				<?php }  if( count($_category_res) > 0 ) { 	?>    
						<div class="ik-search-category " >
							  <select name="selSearchCat" class='ik-drp-post-category' id="ik-drp-post-category" >
									<option <?php echo ((count(explode(",", $all_selected_categories )) > 1 && $all_selected_categories == $category_id_default )?"selected='true'":"");?> value="<?php echo $all_selected_categories; ?>"><?php echo __( 'All', 'richpostslistandgrid' ); ?></option>
									<?php
										foreach( $_category_res as $_category ) {  
										
											$_category_name = $_category->category;
											$_category_id = $_category->id; 
											$_post_count = "";
											
											if( trim( $rplg_enable_post_count ) == "yes"  ) {
											
												$_post_count = " (".$_category->count.")";
												
												if( trim( $rplg_hide_empty_category ) == "yes"  && intval( $_category->count ) <= 0 )
													continue;
												
											}  
											if((count(explode(",",$category_id_default)) == 1) && $category_id_default==$_category_id)
												echo '<option selected="true" value="'.$_category_id.'">'.$_category_name.$_post_count.'</option>';
											else
												echo '<option value="'.$_category_id.'">'.$_category_name.$_post_count.'</option>';									
										
										}
									?>
							</select> 
					   </div>
				<?php } ?>	 
					<div class="ik-search-button" onclick='rplg_fillPosts( "<?php echo esc_js( $params["vcode"] ); ?>", "<?php echo esc_js( $category_id_default ); ?>", request_obj_<?php echo esc_js( $params["vcode"] ); ?>, 2)'> <img width="18px" alt="Search" height="18px" src="<?php echo rplg_media.'images/searchicon.png'; ?>" /></div>
					<div class="clrb"></div>
			</div>
		 <?php
		}
	} //else { echo "<input type='hidden' value='".$category_id."' class='ik-drp-post-category' />"; }
	 
	  $_total_posts = $this->rplg_getTotalPosts( $category_id_default, $post_search_text, 1, $is_default_category_with_hidden );
	if( $_total_posts <= 0 ) {
		?><div class="ik-post-no-items"><?php echo __( 'No posts found.', 'richpostslistandgrid' ); ?></div><?php
		die();
	} 
	$post_list = $this->getPostList( $category_id_default, $post_search_text, $_limit_end );	 
	 
	foreach ( $post_list as $_post ) { 
		$image  = $this->getPostImage( $_post->post_image, $final_width, $params["rplg_image_height"] ); 
		$_author_name = esc_html($_post->display_name);
	    $_author_image = get_avatar($_post->post_author,25);
		?>
		<div style="width:<?php echo esc_attr($final_width); ?>px; " class='ikh-post-item-box pid-<?php echo esc_attr( $_post->post_id ); ?>'> 
			<div class="ikh-post-item ikh-simple"> 
			<?php
				ob_start();
				if( $params["rplg_hide_post_image"] == "no" ) { ?>
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
							<?php if( sanitize_text_field( $params["hide_post_title"] ) =='no'){ ?> 
								<a href="<?php echo get_permalink( $_post->post_id ); ?>" style="color:<?php echo esc_attr( $params["title_text_color"] ); ?>" >
									<?php echo esc_html( $_post->post_title ); ?>
								</a>
							<?php } ?>	 
							
							<?php if( sanitize_text_field( $params["rplg_hide_posted_date"] ) =='no'){ ?> 
								<div class='ik-post-date'>
									<i><?php echo date(get_option("date_format"),strtotime($_post->post_date)); ?></i>
								</div>
							<?php } ?>	
						
							<?php  
								if( $params["rplg_hide_post_short_content"] == "no" ) { ?>
								<div class='ik-post-sub-content'>
									<?php
									if( strlen( strip_tags( $_post->post_content ) ) > intval( $params["rplg_hide_post_short_content_length"] ) ) 	
										echo substr( strip_tags( $_post->post_content ), 0, $params["rplg_hide_post_short_content_length"] ).".."; 
									else
										echo trim( strip_tags( $_post->post_content ) );
									?> 
								</div>
							<?php } ?>										
						</div>
						
						<?php if( sanitize_text_field( $params["rplg_hide_comment_count"] ) =='no'){ ?> 
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
						
						<?php if( sanitize_text_field( $params["rplg_show_author_image_and_name"] ) =='yes') { ?> 
							<div class='ik-post-author'>
								<?php echo (($_author_image!==FALSE)?$_author_image:"<img src='".rplg_media."images/user-icon.png' width='25' height='25' />"); ?> <?php echo __( 'By', 'richpostslistandgrid' ); ?> <?php echo $_author_name; ?>
							</div>
						<?php } ?>	 	
						
						<?php if( $params["rplg_read_more_link"] == "no" ) { ?>
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
	
	 
	
	if( $params["rplg_hide_paging"] == "no" && $params["rplg_select_paging_type"] == "load_more_option"   && $_total_posts > sanitize_text_field( $params["number_of_post_display"] ) ) {
	
		?>	
		<div class="clr"></div>
		<div style="display:none" class='ik-post-load-more'  align="center" onclick = 'rplg_loadMorePosts( "<?php echo esc_js( $category_id_default ); ?>", "<?php echo esc_js( $_limit_start+$_limit_end ); ?>", "<?php echo esc_js( $params["vcode"] ); ?>", "<?php echo esc_js( $_total_posts ); ?>", request_obj_<?php echo esc_js( $params["vcode"] ); ?> )'>
			<?php echo __('Load More', 'richpostslistandgrid' ); ?>
		</div>
		<?php 
		
	} else if( $params["rplg_hide_paging"] == "no" && $params["rplg_select_paging_type"] == "next_and_previous_links" ) {
	
		?><div class="clr"></div>
		<div style="display:none" class="rplg-simple-paging"><?php
			echo $this->displayPagination(  0, $_total_posts, $category_id_default, $_limit_start, $_limit_end, $params["vcode"], 2 );
		?></div><div class="clr"></div><?php
	
	} else if( $params["rplg_hide_paging"] == "no" && $params["rplg_select_paging_type"] == "simple_numeric_pagination" ) {
	
		?><div class="clr"></div>
		<div style="display:none" class="rplg-simple-paging"><?php
			echo $this->displayPagination(  0, $_total_posts, $category_id_default, $_limit_start, $_limit_end, $params["vcode"], 1 );
		?></div><div class="clr"></div><?php	
	
	} else {
		?> <div class="clr"></div> <?php
	} 
	?><script type='text/javascript' language='javascript'><?php echo $this->rplg_js_obj( $params ); ?></script> 
	