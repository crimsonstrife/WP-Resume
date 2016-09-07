<?php 
/**
 * Template for exclusive taxonomies metabox (section and organizations)
 * With extensions by VictorBargains to allow non-exclusivity using a multiselect box in the case of Skills (added in v2.5.8a)
 * @package WP_Resume
 */
?><?php if( $type == 'wp_resume_skill' ){ ?>
<i>Use command or control-click to select multiple skills.</i>
<?php $select_terms = wp_dropdown_categories( array( 'show_option_none' => 'None', 'echo' => 0, 'taxonomy' => $type, 'hide_empty' => false, 'hierarchical' => is_taxonomy_hierarchical( $type ), 'name' => $type, 'value_field' => 'slug' ) );
$select_terms = str_replace( "' id=", "[]' style='max-height: 400px; overflow-y: scroll; height: 100%' multiple='multiple' id=", $select_terms );
if( is_array($current) ){
	foreach ($current as $key => $post_term){
		$select_terms = str_replace(' value="' . $post_term->slug . '"', ' value="' . $post_term->slug . '" selected="selected"', $select_terms);
	}
} else {
	$select_terms = str_replace(' value="' . $current->slug . '"', ' value="' . $current->slug . '" selected="selected"', $select_terms);
}
echo $select_terms; ?>

<?php } else { ?>
	<?php foreach ($terms as $term) { ?>
	<input type="radio" name="<?php echo $type; ?>" value="<?php echo $term->term_id; ?>" id="<?php echo $term->slug; ?>"<?php	if ( isset( $current[0]->term_id ) )
			checked( $term->term_id, $current[0]->term_id );
?>>
	<label for="<?php echo $term->slug; ?>"><?php echo $term->name; ?></label><br />
	<?php } ?>
<input type="radio" name="<?php echo $type; ?>" value="" id="none" <?php checked( empty($current[0]->term_id) ); ?> />
<label for="none"><?php _e('None', 'wp-resume'); ?></label><br />
<?php } ?>
<a href="#" id="add_<?php echo $type ?>_toggle">+ <?php _e( $taxonomy->labels->add_new_item, 'wp-resume' ); ?></a>
<div id="add_<?php echo $type ?>_div" style="display:none">
	<label for="new_<?php echo $type ?>"><?php _e( $taxonomy->labels->singular_name, 'wp-resume' ); ?>:</label>
	<input type="text" name="new_<?php echo $type ?>" id="new_<?php echo $type ?>" /><br />
	<?php if ($type == 'wp_resume_organization') { ?>
		<label for="new_<?php echo $type ?>_location" style="padding-right:24px;"><?php _e('Location', 'wp-resume'); ?>:</label>
			<input type="text" name="new_<?php echo $type ?>_location" id="new_<?php echo $type ?>_location" /><br />
	<?php } else  if ($type == 'wp_resume_skill') { ?>
		<label for="new_<?php echo $type ?>_level" style="padding-right:24px;"><?php _e('Skill Level', 'wp-resume'); ?>:</label>
			<input type="text" name="new_<?php echo $type ?>_level" id="new_<?php echo $type ?>_level" /><br />
        <label for="new_<?php echo $type ?>_parent" style="padding-right:24px;"><?php _e('Skill Group:', 'wp-resume'); ?></label>
            <?php wp_dropdown_categories( array( 'show_option_none' => 'None', 'echo' => 1, 'taxonomy' => $type, 'hide_empty' => false, 'hierarchical' => is_taxonomy_hierarchical( $type ), 'name' => 'new_' . $type . '_parent', 'value_field' => 'term_id' ) ); ?>
	<?php } ?>
	<input type="button" value="Add New" id="add_<?php echo $type ?>_button" />
	<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" id="<?php echo $type ?>-ajax-loading" style="display:none;" alt="" />
</div>
<?php wp_nonce_field( 'add_'.$type, '_ajax_nonce-add-'.$type ); ?>
<?php wp_nonce_field( 'wp_resume_taxonomy', 'wp_resume_nonce'); ?>