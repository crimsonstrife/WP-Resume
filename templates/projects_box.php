<?php 
/**
 * Template for project data inputs
 * @package WP_Resume
 */
 
 	if( !defined('project_fields_html') ){
		function project_fields_html($i, $project = null){
			$project_fields = array(
				'name' => 'The official title of this project.',
				'type' => 'The type of project (game, book, movie, etc.)',
				'link' => 'A valid link to use when clicking on the name. Can be a relative or absolute URL.',
				'description' => 'A description which can be displayed as a mouseover or underneath the title.'
			);
			$html = '<li class="project-form">';
			$html .= '<table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td valign="top" rowspan="4" width="100%">';
			foreach( $project_fields as $k => $v ){
				$input_name = "projects[$i][$k]";
				$input_label = __( ucfirst($k), 'wp-resume' );
				$input_value = is_array($project) && isset($project[$k]) ? esc_attr($project[$k]) : '';
				$html .= "<label for='$input_name' title='$v' class='project-{$k}'>$input_label";
				if( $k == 'description' ){
					$html .= "<textarea name='$input_name'>$input_value";
				} else {
					$html .= "<input type='text' name='$input_name' id='project{$i}{$k}' value='{$input_value}'>";
				}
				if( $k == 'description' ){
					$html .= "</textarea>";
				}
				$html .= '</label>';
			}
	
			$html .= <<<HTML
				</td><td align="right" valign="center" width="30" height="25%">
						<span class="project-up dashicons dashicons-arrow-up-alt2"></span>
						</td></tr><tr><td align="right" valign="center" width="30" height="25%">
						<span class="project-remove dashicons dashicons-dismiss"></span>
						</td></tr><tr><td align="right" valign="center" width="30" height="25%">
						<span class="project-add dashicons dashicons-plus-alt"></span>
						</td></tr><tr><td align="right" valign="center" width="30" height="25%">
						<span class="project-down dashicons dashicons-arrow-down-alt2"></span>
				</td></tr></table></li>
HTML;
			return $html;
		}
	}
	
	if( !defined('project_rows_html') ){
		function project_rows_html($projects = array()){
			$c = count($projects);
			$html = '';
			for( $i = 0; $i < max($c, 1); $i++ ){
				$html .= project_fields_html($i, $projects[$i]);
			}
			return $html;
		}	
	}
?>
<ul class="project-rows">
	<?php echo project_rows_html($projects); ?>
</ul>