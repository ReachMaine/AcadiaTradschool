<?php
/**
 * Functions - Child theme custom functions
 */


/*****************************************************************************************************************
************************** Caution: do not remove or edit anything within this section **************************/

/**
 * Loads the Divi parent stylesheet.
 * Do not remove this or your child theme will not work unless you include a @import rule in your child stylesheet.
 */
function dce_load_divi_stylesheet() {
    wp_enqueue_style( 'divi-parent-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'dce_load_divi_stylesheet' );

/**
 * Makes the Divi Children Engine available for the child theme.
 * Do not remove this or you will lose all the customization capabilities created by Divi Children Engine.
 */
require_once('divi-children-engine/divi_children_engine.php');

/****************************************************************************************************************/


/**
 * Patch to fix Divi issue: Duplicated Predefined Layouts.
 */
if ( function_exists( 'et_pb_update_predefined_layouts' ) ) {
	remove_action( 'admin_init', 'et_pb_update_predefined_layouts' );
	function Divichild_pb_update_predefined_layouts() {
			if ( 'on' === get_theme_mod( 'et_pb_predefined_layouts_updated_2_0' ) ) {
				return;
			}
			if ( ! get_theme_mod( 'et_pb_predefined_layouts_added' ) OR ( 'on' === get_theme_mod( 'et_pb_predefined_layouts_added' ) )) {	
				et_pb_delete_predefined_layouts();
			}
			et_pb_add_predefined_layouts();
			set_theme_mod( 'et_pb_predefined_layouts_updated_2_0', 'on' );
	}
	add_action( 'admin_init', 'Divichild_pb_update_predefined_layouts' );
}

add_action( 'init', 'wcs_register_custom_taxonomies' );

function wcs_register_custom_taxonomies(){ 
register_taxonomy( 
'level', 
'class', 
array( 
'label' => esc_html__( 'Level', 'text-domain' ), 
'rewrite' => array( 'slug' => 'level' ) 
) 
); 
}


?>
