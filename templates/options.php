<?php 
/**
 * Template for main WP Resume options page
 * @package WP_Resume
 */
?><div class="wp_resume_admin wrap">
	<h2><?php _e('Resume Options', 'wp-resume'); ?></h2>
	<form method="post" action='options.php' id="wp_resume_form">
<?php	settings_errors(); ?>
<?php	settings_fields( 'wp_resume_options' );  ?>	
    <table class="form-table">
    	<tr valign="top">
    		<th scope="row"><?php _e('Usage', 'wp-resume'); ?></label></th>
    		<td>
    			<strong><?php _e('To use WP Resume...', 'wp-resume'); ?></strong>
    			<ol>
    				<li><?php _e('Add content to your resume through the menus on the left', 'wp-resume'); ?></li>
    				<li><?php _e('If you wish, add your name, contact information, summary, and order your resume below', 'wp-resume'); ?></li>
    				<li><?php _e('Create a new page as you would normally', 'wp-resume'); ?>
    				<li><?php _e('Add the text <code>[wp_resume]</code> to the page\'s body', 'wp-resume'); ?></li>
    				<li><?php _e('Your resume will now display on that page', 'wp-resume'); ?>.</li>
    			</ol>
    			<?php if ( current_user_can( 'edit_others_posts' ) ) { ?>
    			<br />
    			<strong><?php _e('Want to have multiple resumes on your site?', 'wp-resume'); ?></strong> <a href="#" id="toggleMultiple"><?php _e('Yes!', 'wp-resume'); ?></a><br />
    			<div id="multiple">
    			<?php _e('WP Resume associates each resume with a user. To create a second resume...', 'wp-resume'); ?>
    			<ol>
    				<li style="font-size: 11px;"><?php _e('Simply <a href="user-new.php">add a new user</a> (or select an existing user in step two)', 'wp-resume'); ?>.</li>
    				<li style="font-size: 11px;"><a href="post-new.php?post_type=wp_resume_position"><?php _e('Add positions</a> as you would normally, being sure to select that user as the position\'s author. You may need to display the author box by enabling it in the "Screen Options" toggle in the top-right corner of the position page', 'wp-resume'); ?>.</li>
    				<li style="font-size: 11px;"><?php _e('Select the author from the drop down below and fill in the name, contact info, and summary fields (optional)', 'wp-resume'); ?>.</li>
    				<li style="font-size: 11px;"><a href="post-new.php?post_type=page"><?php _e('Create a new page</a> and add the <code>[wp_resume]</code> shortcode, similar to above, but set the page author to the resume\'s author (the author from step two). Again, you may need to enable the author box', 'wp-resume'); ?>.</li>
    			</ol>
    			 <em><?php _e('Note', 'wp_resume'); ?>:</em> <?php _e('To embed multiple resumes on the same page, you can alternatively use the syntax <code>[wp_resume user="user_nicename"]</code> where <code>user_nicename</code> is the username of the resume\'s author', 'wp-resume'); ?>.
    			 <?php } ?>
    			 </div>
    		</td>
    	</tr>
    	<?php 
    		if ( sizeof($authors) > 1 && current_user_can( 'edit_others_posts' ) ) {
    		?>
    	<tr valign="top">
 			<th scope="row"><label for="user"><?php _e('User', 'wp-resume'); ?></label></th>
    		<td>
    			<?php wp_dropdown_users( array( 'selected' => $current_author ) ); ?>
    			<input type="hidden" name="old_user" value="<?php echo $current_author; ?>" />
    		</td>
    	</tr>
    	<?php } ?>
    	<tr valign="top">
    		<th scope="row"><label for="wp_resume_options[name]"><?php _e('Name', 'wp-resume') ;?></label></th>
    		<td>
    			<input name="wp_resume_options[name]" type="text" id="wp_resume_options[name]" value="<?php if ( isset( $user_options['name'] ) ) echo $user_options['name']; ?>" class="regular-text" /><BR />
    			<span class="description"><?php _e('Your name -- displays on the top of your resume', 'wp-resume'); ?>.</span>
    		</td>
    	</tr>
    	<tr valign="top">
    		<th scope="row"><?php _e('Contact Information', 'wp-resume'); ?></th>
    		<td>
    			<ul class="contact_info_blank" style="display:none;">
    				<?php $this->parent->template->contact_info_row( array( 'field_id' => '', 'value' => '' ) ); ?>
    			</ul>
    			<ul id="contact_info">
    				<?php if ( isset($user_options['contact_info'] ) && is_array( $user_options['contact_info'] ) ) 
    					array_walk_recursive($user_options['contact_info'], array( &$this->parent->admin, 'contact_info_row' ) ); ?>
    			</ul>
    			<a href="#" id="add_contact_field">+ <?php _e('Add Field', 'wp-resume'); ?></a><br />
    			<span class="description"><?php _e('(optional) Add any contact info you would like included in your resume', 'wp-resume'); ?>.</span>
    		</td>
    	</tr>
    	<tr valign="top">
    		<th scope="row"><label for="wp_resume_options[summary]"><?php _e('Summary', 'wp-resume'); ?></label></th>
    		<td id="poststuff">
    		<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea">
    			<?php $this->parent->admin->summary_editor( ( ( isset($user_options['summary'] ) ) ? $user_options['summary'] : '' ) ); ?>	
    		</div>
    		<span class="description"><?php _e('(optional) Plain-text summary of your resume, professional goals, etc. Will appear on your resume below your contact information before the body', 'wp-resume'); ?>.</span>	
    		</td>
    	</tr>
    	<tr valign="top">
    		<th scope="row"><?php _e('Resume Order', 'wp-resume'); ?></th>
    		<td>
    		<?php $this->parent->admin->order_dragdrop( (int) $current_author ); ?>
    		<span class="description"><?php _e('New positions are automatically displayed in reverse chronological order, but you can fine tune that order by rearranging the elements in the list above', 'wp-resume'); ?>.</span>
    		</td>
    	</tr>
    	<?php if ( current_user_can( 'manage_options' ) ) { ?>
    	<tr valign="top">
    		<th scope="row">
    			<?php _e( 'Skills', 'wp-resume' ); ?>
    		</th>
    		<td>
    			<span class="description"><?php _e('These options specify how Skills are displayed in the default resume.php template. Skills added through the Skills admin panel can be associated with positions from within the position post editor. Skills are currently an alpha feature and have not yet been tested with multiple users', 'wp-resume'); ?>.</span>
    		</td>
    	</tr>

    	<tr valign="top">
    		<th scope="row">&nbsp; <?php _e('Position Skills', 'wp-resume'); ?></th>
    		<td>
    			<span class="input-group">
                    <label for="wp_resume_options[position-skills-label]">Label text: <input type="text" name="wp_resume_options[position-skills-label]" value="<?php echo strlen($options['position-skills-label']) ? $options['position-skills-label'] : '' ?>"></label><br />
                    <input type="radio" name="wp_resume_options[skills]" id="skills_above" value="above" <?php checked($options['skills'], 'above'); ?>/> <label for="skills_above"><?php _e('Above Position content', 'wp-resume'); ?></label><br />
                    <input type="radio" name="wp_resume_options[skills]" id="skills_below" value="below" <?php checked($options['skills'], 'below'); ?>/> <label for="skills_below"><?php _e('Below Position content', 'wp-resume'); ?></label><br />
    			    <input type="radio" name="wp_resume_options[skills]" id="skills_no" value="0" <?php checked($options['skills'], 0); ?> <?php checked($options['skills'], null); ?>/> <label for="skills_no"><?php _e('Not shown', 'wp-resume'); ?></label><br />
                </span>
                <span class="input-group">
                    <input type="radio" name="wp_resume_options[position-skill-groups]" id="position-skill-groups_label" value="label" <?php checked($options['position-skill-groups'], 'label'); ?>/> <label for="position-skill-groups_label"><?php _e('Show Group Name Labels', 'wp-resume'); ?></label><br />
                    <input type="radio" name="wp_resume_options[position-skill-groups]" id="position-skill-groups_title" value="title" <?php checked($options['position-skill-groups'], 'title'); ?>/> <label for="position-skill-groups_title"><?php _e('Show Group Names as Skill mouseover Titles', 'wp-resume'); ?></label><br />
                    <input type="radio" name="wp_resume_options[position-skill-groups]" id="position-skill-groups_both" value="both" <?php checked($options['position-skill-groups'], 'both'); ?>/> <label for="position-skill-groups_both"><?php _e('Show Group Name Labels and Titles', 'wp-resume'); ?></label><br />
                    <input type="radio" name="wp_resume_options[position-skill-groups]" id="position-skill-groups_neither" value="neither" <?php checked($options['position-skill-groups'], 'neither'); ?>/> <label for="position-skill-groups_neither"><?php _e('Group Skills but hide labels', 'wp-resume'); ?></label><br />
    			    <input type="radio" name="wp_resume_options[position-skill-groups]" id="position-skill-groups_no" value="0" <?php checked($options['position-skill-groups'], 0); ?> <?php checked($options['position-skill-groups'], null); ?>/> <label for="position-skill-groups_no"><?php _e('Do not group Skills.', 'wp-resume'); ?></label><br />
                </span><br />
    			<p class="description"><?php _e('Shows any skills attached to a Position, before or after the content, similar to categories or tags on a post. If URL rewriting is enabled, skills will be links to an archive page showing positions associated with that skill', 'wp-resume'); ?>.</p>
    		</td>
    	</tr>
        <tr valign="top">
    		<th scope="row">&nbsp; <?php _e('Skills Section', 'wp-resume'); ?></th>
    		<td>
                <span class="input-group">
                	<label for="wp_resume_options[skills-section-label]">Label text: <input type="text" name="wp_resume_options[skills-section-label]" value="<?php echo strlen($options['skills-section-label']) ? $options['skills-section-label'] : '' ?>"></label><br />
                    <input type="radio" name="wp_resume_options[skills-section]" id="skills-section_above" value="above" <?php checked($options['skills-section'], 'above'); ?>/> <label for="skills-section_above"><?php _e('Show Skills Above Positions', 'wp-resume'); ?></label><br />
                    <input type="radio" name="wp_resume_options[skills-section]" id="skills-section_below" value="below" <?php checked($options['skills-section'], 'below'); ?>/> <label for="skills-section_below"><?php _e('Show Skills Below Positions', 'wp-resume'); ?></label><br />
    			    <input type="radio" name="wp_resume_options[skills-section]" id="skills-section_no" value="0" <?php checked($options['skills-section'], 0); ?> <?php checked($options['skills-section'], null); ?>/> <label for="skills-section_no"><?php _e('Hide Skills Section Summary', 'wp-resume'); ?></label><br />
                </span>
                <span class="input-group">
                    <input type="radio" name="wp_resume_options[skills-section-groups]" id="skills-section-groups_label" value="label" <?php checked($options['skills-section-groups'], 'label'); ?>/> <label for="skills-section-groups_label"><?php _e('Show Group Name Labels', 'wp-resume'); ?></label><br />
                    <input type="radio" name="wp_resume_options[skills-section-groups]" id="skills-section-groups_title" value="title" <?php checked($options['skills-section-groups'], 'title'); ?>/> <label for="skills-section-groups_title"><?php _e('Show Group Names as Skill mouseover Titles', 'wp-resume'); ?></label><br />
                    <input type="radio" name="wp_resume_options[skills-section-groups]" id="skills-section-groups_both" value="both" <?php checked($options['skills-section-groups'], 'both'); ?>/> <label for="skills-section-groups_both"><?php _e('Show Group Name Labels and Titles', 'wp-resume'); ?></label><br />
                    <input type="radio" name="wp_resume_options[skills-section-groups]" id="skills-section-groups_neither" value="neither" <?php checked($options['skills-section-groups'], 'neither'); ?>/> <label for="skills-section-groups_neither"><?php _e('Group Skills but hide labels', 'wp-resume'); ?></label><br />
    			    <input type="radio" name="wp_resume_options[skills-section-groups]" id="skills-section-groups_no" value="0" <?php checked($options['skills-section-groups'], 0); ?> <?php checked($options['skills-section-groups'], null); ?>/> <label for="skills-section-groups_no"><?php _e('Do not group Skills.', 'wp-resume'); ?></label><br />
                </span><br />
    			<p class="description"><?php _e('Shows a summary of all Skills created through the "Skills" admin panel. Skills are shown within a section named "Skills" which must be created separately using the "Sections" admin panel. If URL rewriting is enabled, skills will be links to an archive page showing positions associated with that skill. You may add your own positions to this "Skills" category and they will appear either above or below your skills summary, as defined by this setting', 'wp-resume'); ?>.</p>
    		</td>
    	</tr>
    	<tr valign="top">
    		<th scope="row">
    			<?php _e( 'Advanced Options', 'wp-resume' ); ?>
    		</th>
    		<td>
    			<a href="#" id="toggleHood"><?php _e('Show Advanced Options', 'wp-resume'); ?></a>
    		</td>
    	</tr>
    	<tr valign="top" class="underHood">
    		<th scope="row"><?php _e('Force IE HTML5 Support', 'wp-resume'); ?></th>
    		<td>
    			<input type="radio" name="wp_resume_options[fix_ie]" id="fix_ie_yes" value="1" <?php checked($options['fix_ie'], 1); ?>/> <label for="fix_ie_yes"><?php _e('Yes', 'wp-resume'); ?></label><br />
    			<input type="radio" name="wp_resume_options[fix_ie]" id="fix_ie_no" value="0" <?php checked($options['fix_ie'], 0); ?>/> <label for="fix_ie_no"><?php _e('No', 'wp-resume'); ?></label><br />
    			<span class="description"><?php _e('If Internet Explorer breaks your resume\'s formatting, conditionally including a short Javascript file should force IE to recognize html5 semantic tags', 'wp-resume'); ?>.</span>
    		</td>
    	</tr>
    	<tr valign="top" class="underHood">
    		<th scope="row"><?php _e('Hide Page Title', 'wp-resume'); ?></th>
    		<td>
    			<input type="radio" name="wp_resume_options[hide-title]" id="hide_yes" value="1" <?php checked($options['hide-title'], 1); ?>/> <label for="hide_yes"><?php _e('Yes', 'wp-resume'); ?></label><br />
    			<input type="radio" name="wp_resume_options[hide-title]" id="hide_no" value="0" <?php checked($options['hide-title'], 0); ?> <?php checked($options['hide-title'], null); ?>/> <label for="hide_no"><?php _e('No', 'wp-resume'); ?></label><br />
    			<span class="description"><?php _e('Hides the standard page title on pages (or posts) containing the <code>[wp_resume]</code> shortcode by adding a <code>hide-title</code> class', 'wp-resume'); ?>.</span>
    		</td>
    	</tr>
    	<tr valign="top" class="underHood">
    		<th scope="row"><?php _e('Enable URL Rewriting', 'wp-resume'); ?></th>
    		<td>
    			<input type="radio" name="wp_resume_options[rewrite]" id="rewrite_yes" value="1" <?php checked($options['rewrite'], 1); ?>/> <label for="rewrite_yes"><?php _e('Yes', 'wp-resume'); ?></label><br />
    			<input type="radio" name="wp_resume_options[rewrite]" id="rewrite_no" value="0" <?php checked($options['rewrite'], 0); ?> <?php checked($options['rewrite'], null); ?>/> <label for="rewrite_no"><?php _e('No', 'wp-resume'); ?></label><br />
    			<span class="description"><?php _e('Creates individual pages for each position, and index pages for each section, organization, and skill', 'wp-resume'); ?>.</span>
    		</td>
    	</tr>
        <tr valign="top" class="underHood">
    		<th scope="row"><?php _e('Show Projects', 'wp-resume'); ?></th>
    		<td>
    			<input type="radio" name="wp_resume_options[projects]" id="projects_list" value="list" <?php checked($options['projects'], 'list'); ?>/> <label for="projects_list"><?php _e('List', 'wp-resume'); ?></label> <i>(displays as an unordered HTML list)</i><br />
    			<input type="radio" name="wp_resume_options[projects]" id="projects_text_list" value="text_list" <?php checked($options['projects'], 'text_list'); ?>/> <label for="projects_text_list"><?php _e('Text List', 'wp-resume'); ?></label> <i>(displays as a comma-separated text list)</i><br />
    			<input type="radio" name="wp_resume_options[projects]" id="projects_no" value="0" <?php checked($options['projects'], 0); ?> <?php checked($options['projects'], null); ?>/> <label for="projects_no"><?php _e('No', 'wp-resume'); ?></label><br />
    			<span class="description"><?php _e('Shows a list of projects as either an HTML or text list.', 'wp-resume'); ?>.</span>
    		</td>
    	</tr>
    	<tr valign="top" class="underHood">
    		<th scope="row"><?php _e('Customizing WP Resume', 'wp-resume'); ?></th>
    		<td>
    			<Strong><?php _e('Style Sheets', 'wp-resume'); ?></strong><br />
    			<?php _e('Although some styling is included by default, you can customize the layout by modifying <a href="theme-editor.php">your theme\'s stylesheet</a>', 'wp-resume'); ?>.<br /><br />
    			
    			<strong><?php _e('Templates', 'wp-resume'); ?></strong> <br />
    			<?php _e("Any WP Resume template file (resume.php, resume-style.css, resume-text.php, etc.) found in your theme's directory will override the plugin's included template. Feel free to copy the file from the plugin directory into your theme's template directory and modify the file to meet your needs", 'wp-resume'); ?>.<br /><br />
    			
    			<strong><?php _e('Feeds', 'wp-resume'); ?></strong> <br />
    			<?php _e('WP Resume allows you to access your data in three machine-readable formats. By default, the resume outputs in an <a href="http://microformats.org/wiki/hresume">hResume</a> compatible format. A JSON feed can be generated by appending <code>?feed=json</code> to your resume page\'s URL and a plain-text alternative (useful for copying and pasting into applications and forms) is available by appending <code>?feed=text</code> to your resume page\'s URL', 'wp-resume'); ?>.
    		</td>
    	</tr>
  	    	<?php $this->donate(); ?>	
    	<?php } //end if manage_options ?>				
    </table>
    <p class="submit">
    	 <input type="submit" class="button-primary" value="<?php _e('Save Changes', 'wp-resume') ?>" />
    </p>
    </form>
</div>