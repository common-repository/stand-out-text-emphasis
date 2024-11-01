<?php
/*
Plugin Name: Stand Out - Text Emphasis
Plugin URI: http://wpbiz.co/
Description: Add emphasis and text effects to your content.
Author: Ryan Pilling
Version: 0.1
Author URI: http://wpbiz.co/
*/
	
// Define our plugin's wrapper class
if ( !class_exists( "WPBizStandOut" ) )
{
	class WPBizStandOut
	{

		static $styles_required;

		function WPBizStandOut() // Constructor
		{
			register_activation_hook( __FILE__, array($this, 'run_on_activate') );
			add_action('admin_init', array($this, 'init_admin'));

			// adds our plugin options page
			add_action('admin_menu', array($this, 'wpbiz_standout_addoptions_page_fn'));
			
			// Adding Custom boxes (Meta boxes) to Write panels (for Post, Page, and Custom Post Types)
			//add_action('add_meta_boxes', array($this, 'wpbiz_standout_add_custom_box_fn'));
			//add_action('save_post', array($this, 'wpbiz_standout_save_postdata_fn'));
			
			// filter lets Simplenote add notes to content when desired
			//add_filter( 'the_content', array( $this, 'wpbizstandout_addnote' ) );
			
			// This adds scripts for ANY admin screen
			add_action( 'admin_enqueue_scripts', array( $this, 'wpbizstandout_admin_scripts' ) );
			
			// This adds support for shortcodes
            add_shortcode( 'standout', array( $this, 'standout_shortcode_fn' ) );
            add_shortcode( 'standoutbox', array( $this, 'standout_shortcode_fn' ) );

            // enque styles in standby mode until shortcode is used
            //add_action('init', 'standout_register_styles');
			//add_action('wp_footer', 'standout_styles_in_use');

            // this is used to only call stylesheet on posts that use the shortcode.
			add_action('save_post', array( $this, 'save_option_shortcode_post_id_array') );
			add_action('wp_enqueue_scripts', array( $this, 'standout_add_scripts_and_styles') );

			//add shortcode menu to editor
			add_action('admin_head',  array( $this, 'shortcode_menu'));
		}
		
		// == Script and CSS Enqueuing

		// check if a page contains the shortcodes
		function find_shortcode_occurences($shortcode, $post_id){


			
			if (!$matching_ids = get_option('wpbiz-standout-usage')){
		    	$matching_ids = array();
		    }

		    $post = get_post($post_id);

		    $shortcode_in_use = false;

	    	if (is_array($shortcode)){
	    		foreach ($shortcode as $sc){
	    			if (false !== strpos($post->post_content, '['.$sc)) {
		            	$shortcode_in_use = true;
		        	}
	    		}

	    	} else {
		        if (false !== strpos($post->post_content, '['.$shortcode)) {
		            $shortcode_in_use = true;
		        }
	    	}

	    	if ($shortcode_in_use){ // add it to the array
	    		if (!in_array($post_id, $matching_ids)){
	    			$matching_ids[] = $post_id;
	    		}
	    	} else { //make sure it's not in the array
		    	if(($key = array_search($post_id, $matching_ids)) !== false) {
				    unset($matching_ids[$key]);
				}

	    	}
		    
		    return $matching_ids;
		}

		// as page is saved, mark whether or not it contains shortcodes
		function save_option_shortcode_post_id_array( $post_id ) {
		    if ( wp_is_post_revision( $post_id )) {
		        return;
		    }
		    $option_name = 'wpbiz-standout-usage';
		    $id_array = $this->find_shortcode_occurences(array('standout','standoutbox'), $post_id);
		    if (false == add_option($option_name, $id_array, '', 'yes')) update_option($option_name, $id_array);
		}

		// only add scripts for pages that are marked as using the shortcode
		function standout_add_scripts_and_styles() {
		    $page_id = get_the_ID();
		    $option_id_array = get_option('wpbiz-standout-usage');
		    if (!is_array($option_id_array)) $option_id_array = array();

		    // enque scripts only if page uses the, or we're looking at a preview
		    if (in_array($page_id, $option_id_array) || $_GET['preview']=='true') {
		        wp_register_style( 'standout-shortcodes', plugins_url( 'styles/shortcodes.css' , __FILE__ ));
		        wp_enqueue_style( 'standout-shortcodes');
		    }
		}

		function wpbizstandout_admin_scripts() {

			wp_register_style( 'standout-admin', plugins_url( 'styles/admin.css' , __FILE__ ));
        	wp_enqueue_style( 'standout-admin');

			// You might of course have other scripts enqueued here,
			// for functionality other than WordPress Pointers.

			// WordPress Pointer Handling
			// find out which pointer ids this user has already seen
			$seen_it = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
			// at first assume we don't want to show pointers
			$do_add_script = false;
			
			// Handle our first pointer announcing the plugin's new settings screen.
			// check for dismissal of wpbizstandout settings menu pointer 'wpbizso1'
			if ( ! in_array( 'wpbiz_standout_pointer1', $seen_it ) ) {
				// flip the flag enabling pointer scripts and styles to be added later
				$do_add_script = true;
				// hook to function that will output pointer script just for wpbizso1
				add_action( 'admin_print_footer_scripts', array( $this, 'pointer1_footer_script' ) );
			}
			
			if ( ! in_array( 'wpbiz_standout_pointer2', $seen_it ) ) {
				// flip the flag enabling pointer scripts and styles to be added later
				$do_add_script = true;
				// hook to function that will output pointer script just for wpbizso1
				add_action( 'admin_print_footer_scripts', array( $this, 'pointer2_footer_script' ) );
			}
			
			// now finally enqueue scripts and styles if we ended up with do_add_script == TRUE
			if ( $do_add_script ) {
				// add JavaScript for WP Pointers
				wp_enqueue_script( 'wp-pointer' );
				// add CSS for WP Pointers
				wp_enqueue_style( 'wp-pointer' );
			}
		}
		
		// Each pointer has its own function responsible for putting appropriate JavaScript into footer
		function pointer1_footer_script() {
			// Build the main content of your pointer balloon in a variable
			$pointer_content = '<h3>Stand Out has been activated!</h3>'; // Title should be <h3> for proper formatting.
			$pointer_content .= '<p>We are ready to emphasize your content. You can <a href="';
			$pointer_content .= bloginfo( 'wpurl' );
			$pointer_content .= '/wp-admin/themes.php?page=WPBizstandout_Options">learn how to use it</a> right here.</p>';
			// In JavaScript below:
			// 1. "#menu-plugins" needs to be the unique id of whatever DOM element in your HTML you want to attach your pointer balloon to.
			// 2. "wpbizso1" needs to be the unique id, for internal use, of this pointer
			// 3. "position" -- edge indicates which horizontal spot to hang on to; align indicates how to align with element vertically
			?>
			<script type="text/javascript">// <![CDATA[
			jQuery(document).ready(function($) {
				/* make sure pointers will actually work and have content */
				if(typeof(jQuery().pointer) != 'undefined') {
					$('#menu-appearance').pointer({
						content: '<?php echo $pointer_content; ?>',
						position: {
							edge: 'left',
							align: 'center'
						},
						close: function() {
							$.post( ajaxurl, {
								pointer: 'wpbiz_standout_pointer1',
								action: 'dismiss-wp-pointer'
							});
						}
					}).pointer('open');
				}
			});
			// ]]></script>
			<?php
		} // end simplenote_wpbizso1_footer_script()

		// Each pointer has its own function responsible for putting appropriate JavaScript into footer
		function pointer2_footer_script() {
			// Build the main content of your pointer balloon in a variable
			$pointer_content = '<h3>Make Your Text Stand Out</h3>'; // Title should be <h3> for proper formatting.
			$pointer_content .= '<p>Choose your emphasis effects from this menu.</p>';
			// In JavaScript below:
			// 1. "#menu-plugins" needs to be the unique id of whatever DOM element in your HTML you want to attach your pointer balloon to.
			// 2. "wpbizso1" needs to be the unique id, for internal use, of this pointer
			// 3. "position" -- edge indicates which horizontal spot to hang on to; align indicates how to align with element vertically
			?>
			<script type="text/javascript">// <![CDATA[
			jQuery(document).ready(function($) {
				/* make sure pointers will actually work and have content */
				if(typeof(jQuery().pointer) != 'undefined') {
					$('.standout-editor-icon').pointer({
						content: '<?php echo $pointer_content; ?>',
						position: {
							edge: 'left',
							align: 'center'
						},
						close: function() {
							$.post( ajaxurl, {
								pointer: 'wpbiz_standout_pointer2',
								action: 'dismiss-wp-pointer'
							});
						}
					}).pointer('open');
				}
			});
			// ]]></script>
			<?php
		} // end simplenote_wpbizso1_footer_script()



// == Options Field Drawing Functions for add_settings
		// Where do we want to show the note, relative to content?
		// show-where
		function draw_set_showwhere_fn()
		{
			$options = get_option('WPBizstandout_Options');
			$items = array(
							array('0', __("Do not show", 'wpbizstandout'), __("Do not show notes automatically.", 'wpbizstandout') ),
							array('1', __("Show before", 'wpbizstandout'), __("Show note before content.", 'wpbizstandout') ),
							array('2', __("Show after", 'wpbizstandout'), __("Show note after content.", 'wpbizstandout') )
							);
			foreach( $items as $item )
			{
				$checked = ($options['show-where'] == $item[0] ) ? ' checked="checked" ' : '';
				echo "<label><input ".$checked." value='$item[0]' name='WPBizstandout_Options[show-where]' type='radio' /> $item[1] &mdash; $item[2]</label><br />";
			}
		}

// == Administration Init Stuff
		function init_admin()
		{
			// Register a new setting GROUP
			register_setting(
				'WPBizstandout_Options_Group',
				'WPBizstandout_Options');
		}

// == Administration Menus Stuff
		function wpbiz_standout_addoptions_page_fn()
		{
			// Make sure we should be here
			if (!function_exists('current_user_can') || !current_user_can('manage_options') )
			return;
			// Add our plugin options page
			if ( function_exists( 'add_options_page' ) )
			{
				add_theme_page(
					__('Stand Out Options Page', 'wpbizstandout'),
					__('Stand Out', 'wpbizstandout'),
					'manage_options',
					'WPBizstandout_Options',
					array( $this, 'wpbiz_standout_adminpage' ) );
			}
		}
		
		// Show our Options page in Admin
		function wpbiz_standout_adminpage()
		{
			$WPBizstandout_Options = get_option('WPBizstandout_Options');
			include ('inc/admin-options.php');
		}
		

		// add Stand Out menue to text editor
		function shortcode_menu() {
		    global $typenow;
		    // check user permissions
		    if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
		   	return;
		    }
		    // verify the post type
		    if( ! in_array( $typenow, array( 'post', 'page' ) ) )
		        return;
			// check if WYSIWYG is enabled
			if ( get_user_option('rich_editing') == 'true') {
				add_filter('mce_external_plugins', array($this, 'standout_add_tinymce_plugin'));
				add_filter('mce_buttons', array($this, 'standout_register_my_tc_button'));
			}
		}

		function standout_add_tinymce_plugin($plugin_array) {
		   	$plugin_array['standout_tc_button'] = plugins_url( '/inc/tinymce-shortcode-button.js', __FILE__ ); // CHANGE THE BUTTON SCRIPT HERE
		   	return $plugin_array;
		}
		function standout_register_my_tc_button($buttons) {
		   array_push($buttons, "standout_tc_button");
		   return $buttons;
		}
		
		// == Set options to defaults - used during plugin activation
		function run_on_activate()
		{
			$options = get_option('WPBizstandout_Options');
			// Build an array of each option and its default setting
			$arr_defaults = array
			(
				"defaultfx" => "highlighter",
			    "show-pointers" => "1"
			);
			// check to see if we need to set defaults
			if( !is_array( $options )  )
			{
				update_option('WPBizstandout_Options', $arr_defaults);
			}
		}
	

// == FUNCTION TO ACTUALLY PRINT THE STAND OUT STYLED CONTENT
		
		// when the 'standout' shortcode is found, this function handles it
		function standout_shortcode_fn( $attributes, $content, $tag ) {

		    // get optional attributes and assign default values if not present
            extract( shortcode_atts( array(
                'fx' => 'highlighter',
                'style' => false,
            ), $attributes ) );
            // check to see if any wrapped text was passed in
            if( $content == '' ) {
                // no wrapped text was passed in, so let's do nothing until I get a better idea
                $standout_content = '';
            } else {
                // wrapped content is in $content
                // it's possible content contains other shortcodes needing handled
                $content = do_shortcode( $content );
                // let's style it and return it
                $standout_content = $this->standout_style_content( $content, $attributes, $tag );
            }
            if( $standout_content ) {
                return $standout_content;
            }
        }

        function standout_style_content($content, $attributes, $tag){

        	if (!isset($attributes['fx'])) $attributes['fx'] = 'highlighter'; // establish default effect

        	$class = array();
        	$style = array();

        	$effects = array_filter(explode(',', $attributes['fx']));

        	if ($tag=='standoutbox'){
        		$htmltag = 'div';
        		array_unshift($effects,'box');
        	} else {
        		$htmltag = 'span';
        	}

        	foreach ($effects as $effect){

        		//$class[] = 'standout_'.$effect;

        	}

        	$fxstyles = array_filter(explode(',', $attributes['style']));

        	foreach ($fxstyles as $fxstyle){
        		if (!isset($fxstyle)) continue;
        		$class[] = 'standout-'.$fxstyle;
        	}

        	$count = 0;
        	foreach ($effects as $effect){
        		// special circumstances first
        		if ($effect=='ribbon'){
        			$htmlbefore = $htmlbefore.'<div class="standout-ribbon-wrap"><div class="standout-ribbon_stitches_top"></div><strong class="standout-ribbon-content '.implode(' ', $class).'" style="'.$style[$count].'"><h1>';
        			$htmlafter = '</h1></strong><div class="standout-ribbon-stitches-bottom"></div></div>'.$htmlafter;
        		} else {
	        		$htmlbefore = $htmlbefore.'<'.$htmltag.' class="standout-'.$effect.' '.implode(' ', $class).'" style="'.$style[$count].'">';
	        		$htmlafter = '</'.$htmltag.'>'.$htmlafter;
        		}
        		$count++;
        	}

        	$styled_content = $htmlbefore.$content.$htmlafter;

			return $styled_content;
        }
	}
} // End Class

// Instantiating the Class
if (class_exists("WPBizStandOut")) {
	$WPBizStandOut = new WPBizStandOut();
}

if(!function_exists('_log')){
  function _log( $message ) {
    if( WP_DEBUG === true ){
      if( is_array( $message ) || is_object( $message ) ){
        error_log( print_r( $message, true ) );
      } else {
        error_log( $message );
      }
    }
  }
}