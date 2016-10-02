<?php
/**
 * Functions for templating the Resume front end
 *
 * @author Benjamin J. Balter <ben@balter.com>
 * @package WP_Resume
 */

class WP_Resume_Templating {

	public $author;
	public $author_id;
	public $date_format = 'F Y';
	public $future_signifier;
	private $parent;

	/**
	 * Stores parent and author within class
	 * @param class $parent (reference) the parent class
	 */
	function __construct( &$parent ) {

		$this->parent = &$parent;

		$this->author = &$parent->author;
		$this->author_id = &$parent->author_id;
		add_action( 'plugins_loaded', array( &$this, 'i18n_init' ) );
	}

	/**
	 * Delay i18ning until all plugins have a chance to load
	 */
	function i18n_init() {

		//i18n: string appended to future date when translated
		$this->future_signifier = __( ' (Anticipated)', 'wp-resume' );

		if ( defined('QTRANS_INIT') || $this->parent->api->apply_filters( 'translate_date', false ) )
			add_filter( 'wp_resume_date', array( &$this, 'translate_date' ), 10, 2 );

	}


	/**
	 * Applies filter and returns author's name
	 * @uses $author
	 * @returns string the author's name
	 */
	function get_name() {

		$name = $this->parent->options->get_user_option( 'name', $this->author_id );

		$name = $this->parent->api->apply_deprecated_filters( 'resume_name', '2.5', 'name', $name );
		return $this->parent->api->apply_filters( 'name', $name );

	}


	/**
	 * Returns the title of the postition, or if rewriting is enabled, a link to the position
	 * @param int $ID the position ID
	 * @pararm bool $link (optional) whether to wrap title in link or not
	 * @return string the title, or the title link
	 */
	function get_title( $ID, $link = true ) {

		//rewriting is disabled globally, or linking explicitly disabled via 2nd argument
		// return just the text of the title
		if ( !$link || !$this->parent->options->get_option( 'rewrite' ) ) {
		
			$title = get_the_title();
		
		//return the title wrapped in a link to the position's permalink
		} else {
		
			$title = '<a title="' . get_the_title() . '" href="' . get_permalink() . '">' . get_the_title() . '</a>';
			$title = $this->parent->api->apply_deprecated_filters( 'resume_title_link', '2.5', 'title_link', $title );
			$title = $this->parent->api->apply_filters( 'title_link', $title );
		
		}

		$title = $this->parent->api->apply_deprecated_filters( 'resume_position_title', '2.5', 'position_title', $title );
		return $this->parent->api->apply_filters( 'position_title', $title );

	}


	/**
	 * Returns the section name, or a link to the section if rewrites are on
	 * @param object $section the section object
	 * @param bool $link (optional) whether to link if possible
	 * @returns string the section name or link to section
	 */
	function get_section_name( $section, $link = true ) {

		return $this->get_taxonomy_name( $section, 'section', $link );

	}


	/**
	 * Returns the organization name, or a link to the organization if rewrites are on
	 * @param object $organization the organization object
	 * @param bool $link (optional) whether to link if possible
	 * @returns string the organization name or link to organization
	 */
	function get_organization_name( $organization, $link = true ) {

		return $this->get_taxonomy_name( $organization, 'organization', $link );

	}


	/**
	 * Given a taxonomy object and taxonomy type, returns either the taxnomy name or a link to the taxnomy
	 * @param object $object the taxonomy object
	 * @param unknown $taxonomy
	 * @param bool $link whether to link if possible
	 * @returns string the formatted taxonomy name/link
	 */
	function get_taxonomy_name( $object, $taxonomy, $link ) {
		global $post;

		$rewrite = $this->parent->options->get_option( 'rewrite' );

		if ( !$link ) {
			$name = $this->parent->api->apply_deprecated_filters( "resume_{$taxonomy}_name", '2.5', "{$taxonomy}_name", $object->name );
			return $this->parent->api->apply_filters( "{$taxonomy}_name", $name );
		}

		//org link
		if ( $taxonomy == 'organization' && $this->parent->get_org_link( $object->term_id ) ) {
			$link = $this->parent->get_org_link( $object->term_id );

			//rewrite links
		} else if ( $rewrite ) {
				$link = get_term_link( $object, "resume_{$taxonomy}" );

				//no link
			} else {
			$name = $this->parent->api->apply_deprecated_filters( "resume_{$taxonomy}_name", '2.5', "{$taxonomy}_name", $object->name );
			return $this->parent->api->apply_filters( "{$taxonomy}_name", $name );
		}

        $title = '<a title="' . $object->name . '" itemprop="url" target="_new" href="' . $link . '">' . $object->name . '</a>';

		$title = $this->parent->api->apply_deprecated_filters( "resume_{$taxonomy}_link", '2.5', "{$taxonomy}_link", $title );
		$title = $this->parent->api->apply_filters( '{$taxonomy}_link', $title );
		$title = $this->parent->api->apply_deprecated_filters( "resume_{$taxonomy}_name", '2.5', "{$taxonomy}_name", $title );
		$title = $this->parent->api->apply_filters( '{$taxonomy}_name', $title );

		return $title;

	}


	/**
	 * Returns the author's contact info
	 * @uses $author
	 * @returns array of contact info
	 */
	function get_contact_info() {
		$contact_info = $this->parent->options->get_user_option( 'contact_info', $this->author_id );
		$contact_info = $this->parent->api->apply_deprecated_filters( 'resume_contact_info', '2.5', 'contact_info', $contact_info );
		return $this->parent->api->apply_filters( 'contact_info', $contact_info );

	}


	/**
	 * Returns the resume summary, if any
	 * @uses $author
	 * @returns string the resume summary
	 */
	function get_summary() {

		$summary = $this->parent->options->get_user_option( 'summary', $this->author_id );
		$summary = $this->parent->api->apply_deprecated_filters( 'resume_summary', '2.5', 'summary', $summary );
		return $this->parent->api->apply_filters( 'summary', $summary );

	}


	/**
	 * Function used to parse the date meta and move to human-readable format
	 * 
	 * Both from and to are option, and if both are present, will be joined by an &ndash;
	 *
	 * @since 1.0a
	 * @uses resume_date
	 * @uses resume_date_formatted
	 * @param int $ID post ID to generate date for
	 * @return string the formatted date(s)
	 */
	function get_date( $ID ) {

		$date = '';
		
		foreach( array( 'from' => 'dtstart', 'to' => 'dtend' ) as $field => $class ) {
			
			$value = get_post_meta( $ID, "wp_resume_{$field}", true );
			$itemprop = ( $class == 'dtstart' ) ? 'startDate' : 'endDate';

			//we don't have this field, skip
			if ( !$value)
				continue;
			
			//to ensure compliance with hResume format, span should reflect ability to parse date
			//@link https://github.com/benbalter/WP-Resume/issues/7
			
			//if we can parse the date, append the proper class and formatted date to span
			if ( strtotime( $value ) ) 
				$date .= '<time itemprop="' . $itemprop . '" class="' . $class . '" datetime="' . date( 'Y-m-d', strtotime( $value ) ) . '" title="' . date( 'Y-m-d', strtotime( $value ) ) . '">';
			
			//if the position is current, append todays date to span
			else if ( $value == 'Present' )
				$date .= '<time datetime="' . date( 'Y-m-d' ) . ' title="' . date( 'Y-m-d' ) . '">';
				
			//if we can't parse the date, just output a standard span
			else
				$date .= '<time>';
	
			$date .= $this->parent->api->apply_filters( 'date', $value, $field );
			
			$date .= '</time>';		
			
			//this is the from field and there is a to field, append the dash
			//it's okay that we're calling get_post_meta twice on "to" because it's cached automatically
			if ( $field == 'from' && get_post_meta( $ID, 'wp_resume_to', true ) )
				$date .= ' &ndash; ';
				
		}

		return $this->parent->api->apply_filters( 'date_formatted', $date, $ID );

	}


	/**
	 * Always dates to be translated and localized, e.g., by qTranslate
	 *
	 * @param string $date the date as stored in post_meta
	 * @param string $type the type, either "from" or "to"
	 * @param string $from the from date
	 * @param string $to the to date
	 * @return string the i18n'd date
	 */
	function translate_date( $date, $type ) {

		//unix timestamp of date, false if not parsable
		$timestamp = strtotime( $date );

		//default date format
		$date_format = $this->parent->api->apply_filters( 'date_format', $this->date_format );

		//allow present to be translatable
		if ( strtolower( trim( $date ) ) == 'Present' )
			$date = __( 'Present', 'wp-resume' );

		//not parsable, can't translate so return whatever we've got
		if ( !$timestamp )
			return $date;

		//i18n date
		$date = date_i18n( $date_format, strtotime( $date ) );

		//we don't do anything else to start dates, so kick
		if ( $type == 'from' )
			return $date;

		//to date is not in the future, again, can't do anything, so kick
		if ( $timestamp < time() )
			return $date;

		//append e.g, ' (Anticipated)' to future dates
		//note: this string won't appear in .POT files, but should still hit qTranslate when run (I hope)
		$date .= __( $this->parent->api->apply_filters( 'future_signifier', $this->future_signifier ) );

		return $date;

	}
	
	/**
	 * Translates hresume field names to author/postalAddress microformat fields
	 * @param string $field the hresume field
	 * @return string the schema.org compliant field
	 */
	function get_contact_info_itemprop( $field ) {
		
		$trans = array( 
			'tel'            => 'itemprop="telephone"',
			'email'          => 'itemprop="email"',
			'adr'            => 'itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"',
			'street-address' => 'itemprop="streetAddress"',
			'locality'       => 'itemprop="addressLocality"',
			'region'         => 'itemprop="addressRegion"',
			'postal-code'    => 'itemprop="postalCode"',
			'country-name'   => 'itemprop="addressCountry"',
		);
		
		if ( !array_key_exists( $field, $trans ) )
			return;
			
		return $trans[ $field ];
		
	}
	
	/**
	 * Retrieves and echos contact info itemprop property
	 * @param string $field the hresume field	 
	 */
	function contact_info_itemprop( $field ) {
		echo $this->get_contact_info_itemprop( $field );
	}

	/**
	 * Sort adr array keys from given order
	 * @param array $contact_info 
	 * @param $sorted (optional) to order in given order
	 */
	function contact_info_sort_adr( $contact_info, $sorter = array() ) {
		$sorted = array();

		if( empty($sorter) ){
			$sorter = array_keys($this->parent->contact_fields()['adr']);
		}

		if( isset( $contact_info['adr'] ) && !empty( $contact_info['adr'] ) ){
			$temp = array();

			foreach ( $sorter as $field ) {
				if ( array_key_exists( $field, $contact_info['adr'] ) )
			        $temp[$field] = $contact_info['adr'][$field];
			}

			$sorted = $temp;

		}

		return $sorted;
	}


	/**
	 * Produces HTML to represent a skill level as a progress bar to be styled by CSS.
	 * @param int $level a percentage of skill mastery from 0-100
	 * @since 2.5.8a
	 */
	function skill_bar_html($level){
		$slug = str_replace( ' ', '-', $level );
		$html = '';
		if( $level || is_int($level) ) $html .= "<span class='skill-level skill-level-$slug' role='progressbar'";
		if( is_int($level) ) $html .= " aria-valuenow='$level' aria-valuemin='0' aria-valuemax='100' style='width: $level%'";
		if( $level || is_int($level) ) $html .= "></span>";
		return $html;
	}

 	/**
	 * Produces HTML to represent a skill as a list item with a skill_bar and a label
	 * @param object $skill the skill represented as a WP taxonomy term object
	 * @since 2.5.8a
	 */
	function skill_html($skill, $options){
		$html = '';
		$level = (int) $skill->description;	
		if( $options['rewrite'] ) $html .= "<a href='/skills/{$skill->slug}'>";
		$html .= "<li rel='tag' class='skill skill-{$skill->slug}" . ($options['rewrite'] ? ' linked' : '') . "'>";
		$html .= "<label itemprop='itemListElement'";
		if( $options['title'] ) $html .= " title='{$options['title']}'";
		$html .= ">{$skill->name}</label>";
		$html .= $this->skill_bar_html($level);
		$html .= "</li>";
		if( $options['rewrite'] ) $html .= "</a>";
		return $html;
	}
		
	/**
	 * Return HTML to display a skill group and its child skills as a nested set.
	 * @param object(WP_Term) $group the parent wp_resume_skill
	 * @param array(object(WP_Term)) $skills an array of wp_resume_skill terms. Any terms which are not a child of $group will be ignored.
	 * @return the HTML for the skill group
	 */
	 function skill_group_html($group, $skills, $options) {	
		$html = '';
		$rewrite = $options['rewrite'];
		$level = (int) $group->description;
		$html .= "<li class='skill-group skill-group-{$group->slug}'>";
		$html .= $this->skill_bar_html($level);
		if( $options['show_groups'] == 'label' || $options['show_groups'] == 'both' ){
			if( $rewrite ) $html .= "<a href='/skills/{$group->slug}'>";
			$html .= "<label itemprop='about'>{$group->name}</label>";
			if( $rewrite ) $html .= "</a>";
		}
		$html .= '<ul class="skills">';
		foreach ( $skills as $skill ){
			if( $skill->parent != $group->term_id ) continue;
			$options['title'] = ( $options['show_groups'] == 'title' || $options['show_groups'] == 'both' ) ? $group->name : null;
			$html .= $this->skill_html($skill, $options);
		}
		$html .= '</ul>';
		$html .= '</li>';	
		return $html;	
	}
	
	/**
	 * Produces HTML to represent the entire skillset, which will either be a linear list of skills, or if groups are enabled and skills arranged hierarchically, with child skills inside boxes representing their parent skill groups. Skills and groups also have levels, and numeric skill levels are drawn as percentage bars below the skill names (leave blank to disable).
	 * @param int $postID the id of the wp_resume_position post to show skills for
	 * @since 2.5.8a
	 */
    function skills_html($postID = null, $options = array() ){
		$html = '';
		// get all selected skills
		$skills = $this->parent->get_skills( $postID );
		// get the parents of all selected skills
		// note: this list is not exclusive with $skills, if a parent is also selected itself
		$groups = $this->parent->get_skill_groups( $postID, $skills );
		// if groups are shown, top-level skills with no selected children will otherwise be ignored.
		// here they are included so they can be listed inside a "skill-group-none" group at the end.
		$orphan_skills = $this->parent->get_orphans( $skills );
		// user options are checked here and then passed as an array to the various html templates
		$defaults = array(
			'show_groups' => $this->parent->options->get_option(is_int($postID) ? 'position-skill-groups' : 'skills-section-groups'),
			'rewrite' => $this->parent->options->get_option('rewrite'),
			'sort' => 'name ASC',
			'post_id' => $postID,
			'label' => $this->parent->options->get_option(is_int($postID) ? 'position-skills-label' : 'skills-section-label'),
		);
		$options = wp_parse_args( $options, $defaults );
		if( is_array($skills) && count($skills) ){
			$grouping = false;
			$html .= '<section class="skillset" itemscope itemtype="http://schema.org/ItemList">';
			if( isset($options['label']) && strlen($options['label']) )
				$html .= "<label itemprop='name'>{$options['label']}</label>";
			if(  is_array($groups) && count($groups) ){ 
				$grouping = true;
				$html .= '<ul class="skill-groups">';
				foreach ( $groups as $group ){
					$html .= $this->skill_group_html($group, $skills, $options);
				}
			} else { 
				$html .= '<ul class="skills">'; 
			}
			if ( !$grouping ){ $orphan_skills = $skills; }
			if ( count($orphan_skills) && (!$grouping || !is_int($postID) ) ) {
				foreach ( $orphan_skills as $skill ){
					// Skip groups we have already rendered in all skills mode
					if ( $postID == null && in_array( $skill, $groups ) ) continue;
					$html .= $this->skill_html($skill, $options); 
				} 
			}
			$html .= '</ul>';
			$html .= '</section>';
		}
		return $html;
	}
	
	/**
	 * Return HTML to display the projects related to a position
	 * @param $post the ID of the wp_resume_position
	 * @return the HTML for its projects, or a blank string
	 */
	function projects_html($post, $type='text'){
		$html = '';
		$projects = get_post_meta( $post, 'projects', true );
		if( is_array($projects) && count($projects) ){
			for( $i = 0; $i < count($projects); $i++ ){
				$project = $projects[$i];
				if( $i > 0 && $type == 'text' ){
					$html .= ', ';
				}
				$html .= $this->project_html($project);
			}
		}
		return $html;
	}

	/**
	 * Return HTML to display a specific project
	 * @param $project the project metadata array with 'name', 'type', 'link', and 'description' fields.
	 * @return the HTML for the project
	 */
	function project_html($project){
		$html = '';
		if( isset($project['link']) && count($project['link']) ){
			$html .= "<a href='{$project['link']}'>";
		}
		$html .= $project['name'];
		if( count($project['type']) ){
			$html .= " ({$project['type']})";
		}
		if( isset($project['link']) && count($project['link']) ){
			$html .= "</a>";
		}
		return $html;
	}

}
