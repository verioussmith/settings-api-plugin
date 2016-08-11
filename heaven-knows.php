<?php  
/*
* Plugin Name: Heaven Knows
* Plugin URI: http://philoveracity.com
* Description: Heaven Knows
* Author: Verious B. Smith III
* Author URI: http://verioussmith.com
* Version: 1.0
* License: GPLv2
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


// function pvd_remove_dashboard_widget() {
// 	remove_meta_box('dashboard_primary', 'dashboard', 'side');
// }
// add_action('wp_dashboard_setup', 'pvd_remove_dashboard_widget');

// function pvd_add_google_link() {
// 	global $wp_admin_bar;

// 	$wp_admin_bar->add_menu( array(
// 		'id' 		=> 'google_analytics',
// 		'title' 	=> 'Google Analytics',
// 		'href' 		=> 'http://google.com/analytics'
		
// 	));
// }
// add_action('wp_before_admin_bar_render', 'pvd_add_google_link');




/*===============================================================*
* Menus (Options Pages/ Tabs)
/*===============================================================*/


/*
* Adds the 'Demo Theme Options' to the 'Settings' menu in the WordPress Dashboard
*/

function demo_add_options_page() {
	
	// Introduces a new top-level menu page
	add_menu_page( 
		'Demo Plugin',  			// $page_title, 
		'Demo Plugin',  			// $menu_title, 
		'manage_options',  				// $capability, 
		'demo-plugin-options',  		// $menu_slug, 
		'demo_plugin_options_display',	// $function (callback)
		''								// Provides a default Icon for the menu
	);

	add_submenu_page( 
		'demo-plugin-options',						// $parent_slug, 
		'Header Options',							// $page_title, 
		'Header Options',							// $menu_title, 
		'manage_options',							// $capability, 
		'header-options',							// $menu_slug, 
		'demo_plugin_options_display'		// $function 
	);

	add_submenu_page( 
		'demo-plugin-options',						// $parent_slug, 
		'Footer Options',							// $page_title, 
		'Footer Options',							// $menu_title, 
		'manage_options',							// $capability, 
		'footer-options',							// $menu_slug, 
		'demo_plugin_options_display'		// $function 
	);

} // end demo_add_options_page
add_action( 'admin_menu', 'demo_add_options_page');






/*===============================================================*
* Sections, Settings & Fields
/*===============================================================*/


/*
* Registers a new settings field on the 'General Settings' page of the WordPress Dashboard.
*/
function demo_initialize_plugin_options() {

	add_settings_section(
		'header_section',								// ID for this section in the attribute tags
		'Header Options',								// The title of the section rendered to the screen
		'demo_plugin_header_description_display',		// The function used to render the options for this section 
		'demo-plugin-header-options'					// ID of the page on which this section is rendered
	);

	//Introduce a section to be rendered on the new options page
	add_settings_section( 
		'footer_section', 								// $id (the name of the field)
		'Footer Options', 								// $title (This is what is rendered to the screen)
		'demo_footer_options_display', 					// $callback (function used to render the options to the page)
		'demo-plugin-footer-options'  					// $page (The ID of the pag on which this section is rendered)

	);





	// Define the settings field
	add_settings_field(
		'display_header',				// ID for this section in the attribute tags
		'Display Header Text',			// ID for this section in the attribute tags
		'demo_header_text_display',		// The function used to render the options for this section
		'demo-plugin-header-options',	// The ID of the page on which this section is rendered
		'header_section'				// The section to which this field belongs
	);
	
	add_settings_field( 
		'footer_message',              	//$id - The ID (or the name) of the field
		'Theme Footer Message',        	//$title - The text used to label the field
		'demo_footer_message_display', 	//$callback -  The callback function used to render the field
		'demo-plugin-footer-options',			//$page - The page on which we'll be rendering this field
		'footer_section'               	//$section -  The section to which we are adding the setting
	);




	register_setting(
		'header_section',
		'header_options',
		'demo_sanitize_header_options'
	);

	//Register the 'footer_message' setting
	register_setting(
		'footer_section',			// The name of the Group of settings
		'footer_options',			// The name of the actual option
		'demo_sanitize_footer_options'  // Sanitize Callback
	);

}
add_action( 'admin_init', 'demo_initialize_plugin_options' );





/*===============================================================*
* Callbacks
/*===============================================================*/



function demo_plugin_options_display() {
?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"></div>
		<h2>Demo Plugin Options</h2>
		
		<?php settings_errors(); ?>
		
		<?php 

			$active_tab = 'header-options';
			if( isset( $_GET['page'] ) ){

				$active_tab = $_GET['page'];

			}

		?>
		
		<h2 class="nav-tab-wrapper">
			<a href="?page=header-options" class="nav-tab <?php echo 'header-options' == $active_tab || 'demo-plugin-options' == $active_tab ? 'nav-tab-active' : ''; ?>">Header Options</a>
			<a href="?page=footer-options" class="nav-tab <?php echo $active_tab == 'footer-options' ? 'nav-tab-active' : ''; ?>">Footer Options</a>

		</h2>

		<form method="post" action="options.php">
			<?php 

				if( 'footer-options' == $active_tab ) {

					settings_fields( 'footer_section' );
					do_settings_sections( 'demo-plugin-footer-options' );

				} else {

					settings_fields( 'header_section' );
					do_settings_sections( 'demo-plugin-header-options' );

				} //end if/else

				// Add the submit button to serialize the options
				submit_button();
			?>
		</form>
	</div>
<?php
}  // end demo_plugin_options_display




/*
** Renders the description of the setting below the title of the header section and above the actual settings.
*/

function demo_plugin_header_description_display() {
	echo "These are the options designed to help you control whether or not you display your header.";
} // end demo_plugin_header_description_display


/*
** Renders the input filed for the 'Header Display' setting.
*/
function demo_header_text_display() {

	$options = (array)get_option('header_options');
	$display = $options['display'];

	$html = '<label for="header_options[display]">';
		$html .= '<input type="checkbox" name="header_options[display]" id="header_options[display]" value="1" ' . checked( 1, $display, false) . '/>';
		$html .= '&nbsp;';
		$html .= 'Display the header text.';
	$html .= '</label>';

	echo $html;
}  // end demo_header_text_display()



/*
*	Renders the descriptions the input field for the 'Footer Message'
*/
function demo_footer_options_display() {
	echo "These options are designed to help you control what's in your footer";
}


/**
* Renders the input field for the 'Footer Message' setting in the "General Settings" Section
**/
function demo_footer_message_display() {
	$options = (array)get_option('footer_options');
	$message = $options['message'];
	echo '<input type="text" name="footer_options[message]" id="footer_options_message" value="' . $message . '" />';
}  // End demo_footer_message_display



/**
*  sanitizes the checkbox that's saved in the header options
*
*  @param 	array 	$options 				The array of options to be sanitized
*  @return 	array 	$sanitized_options		The array of sanitized options
*/

function demo_sanitize_header_options( $options ) {

	$sanitized_options = array();

	if( 1 == $options['display'] ) {
		$sanitized_options['display'] = 1;
	} else {
		$sanitized_options['display'] = '';
	}

	return $sanitized_options;
}



/**
*  sanitizes the text that's saved in the footer options
*
*  @param 	array 	$options 				The array of options to be sanitized
*  @return 	array 	$sanitized_options		The array of sanitized options
*/

function demo_sanitize_footer_options( $options) {

	$sanitized_options = array();

	foreach ($options as $option_key => $option_val ) {
		# code...
		$sanitized_options[ $option_key ] = strip_tags( stripslashes( $option_val ) );
	}

	return $sanitized_options;

}

