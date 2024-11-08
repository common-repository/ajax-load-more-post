<?php if ( ! defined( 'ABSPATH' ) ) exit;  ?> 

<?php if( $type == "widget" ) { ?>

	<tr>
		<td class="tp-label">
			<p> 
				<label for="<?php echo $this->get_field_id( $key ); ?>">
				<?php echo $fields[$key]["field_title"]; ?>:</label> 
			</p> 
		</td>  
		<td>  
			<div class="cls-row-item">							
				<div class="cls-row-item-field">
					<input <?php echo (( isset($fields[$key]["pm"]) && $fields[$key]["pm"] == 1 && $fields["st"]["flag"] != "valid"  )?"disabled='disabled'":""); ?> type="text" id="<?php echo $this->get_field_id( $key ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" class="<?php echo esc_attr($fields[$key]["class"]); ?> rplg_field rplg_field_text is_required_<?php echo esc_attr( $fields[$key]["is_required"] ); ?> <?php echo (( isset($fields[$key]["pm"]) && $fields[$key]["pm"] == 1 && $fields["st"]["flag"] != "valid"  )?"fld_disabled":""); ?>" value="<?php echo   $default_val ; ?>" />
					<?php
						if( isset($fields[$key]["pm"]) && $fields[$key]["pm"] == 1 && $fields["st"]["flag"] != "valid" ) {
							?>
								<p>
									<a target="_blank" href="<?php echo admin_url('edit.php?post_type=rplg_view&page=richpostslistandgrid_settings'); ?>">
									<strong><?php echo __( 'Add your license key', 'richpostslistandgrid' ); ?></strong></a> <strong><?php echo __( 'to enable this field', 'richpostslistandgrid' ); ?>.</strong>
									<input type="hidden" name="<?php echo $this->get_field_name( $key ); ?>" value="<?php echo $default_val; ?>" />
								</p>
							<?php
						}
					?> 
				</div>
			</div> 
		</td>
	</tr>
	
<?php } else if( $type == "shortcode" ) { ?>	

<tr>
	<td class="tp-label"  valign="top">	
		<p> <label for="id_rplg_<?php echo esc_attr($key); ?>"><?php echo $fields[$key]["field_title"]; ?>:</label> </p>
		
	</td>
	<td>
		<div class="cls-row-item">							
			<div class="cls-row-item-field">
				<input <?php echo (( isset($fields[$key]["pm"]) && $fields[$key]["pm"] == 1 && $fields["st"]["flag"] != "valid"  )?"disabled='disabled'":""); ?> type="text" id="id_rplg_<?php echo esc_attr( $key ); ?>" name="nm_<?php echo esc_attr( $key ); ?>" class="rplg_field rplg_field_text is_required_<?php echo esc_attr( $fields[$key]["is_required"] ); ?> <?php echo (( isset($fields[$key]["pm"]) && $fields[$key]["pm"] == 1 && $fields["st"]["flag"] != "valid"  )?"fld_disabled":""); ?>" value="<?php echo   $default_val ; ?>" />
				<?php
					if( isset($fields[$key]["pm"]) && $fields[$key]["pm"] == 1 && $fields["st"]["flag"] != "valid" ) {
						?>
							<p>
								<a target="_blank" href="<?php echo admin_url('edit.php?post_type=rplg_view&page=richpostslistandgrid_settings'); ?>">
								<strong><?php echo __( 'Add your license key', 'richpostslistandgrid' ); ?></strong></a> <strong><?php echo __( 'to enable this field', 'richpostslistandgrid' ); ?>.</strong>
								<input type="hidden" name="nm_<?php echo esc_attr($key); ?>" value="<?php echo $default_val; ?>" />
							</p>
						<?php
					}
				?> 	
			</div>
			<div>
				<?php 
					if(isset($fields[$key]["description"])) 
						echo $fields[$key]["description"];
				?>		
			</div>
		</div>
	</td>
</tr>

<?php } ?>