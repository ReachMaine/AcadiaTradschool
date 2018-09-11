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

/**
 * Adds email and name columns to the attendee export data (CSV only).
 *
 * Filters via the tribe_events_tickets_attendees_csv_items hook; intended for use
 * with the initial 4.1 release of Event Tickets/Event Tickets Plus in combination
 * with WooCommerce only.
 *
 * @param  array $items
 * @return array
 */
function attendee_export_add_purchaser_email_name( $items ) {
	$count = 0;
	foreach ( $items as &$attendee_record ) {
		// Add the header columns
		if ( 1 === ++$count ) {
			$attendee_record[] = 'Customer Email Address';
			$attendee_record[] = 'Customer Name';
		}
		// Populate the new columns in each subsequent row
		else {
			// Assumes that the order ID lives in the first column
			$order = wc_get_order( (int) $attendee_record[0] );
			$attendee_record[] = $order->billing_email;
			$attendee_record[] = $order->billing_first_name . ' ' . $order->billing_last_name;
		}
	}
	return $items;
}
add_filter( 'tribe_events_tickets_attendees_csv_items', 'attendee_export_add_purchaser_email_name' );
add_filter( 'gettext', 'theme_sort_change', 20, 3 );
function theme_sort_change( $translated_text, $text, $domain ) {

    if ( is_woocommerce() ) {

        switch ( $translated_text ) {

            case 'Sort by newness' :

                $translated_text = __( 'Sort by date', 'theme_text_domain' );
                break;
        }

    }

    return $translated_text;
}
/**
 * @snippet       Disable Payment Method for Specific Category
 * @how-to        Watch tutorial @ https://businessbloomer.com/?p=19055
 * @sourcecode    https://businessbloomer.com/?p=19892
 * @author        Rodolfo Melogli
 * @testedwith    WooCommerce 2.5.2
 */
 
add_filter('woocommerce_available_payment_gateways','bbloomer_unset_gateway_by_category');
 
function bbloomer_unset_gateway_by_category($available_gateways){
global $woocommerce;
$category_IDs = array(34,63,118);
foreach ($woocommerce->cart->cart_contents as $key => $values ) {
$terms = get_the_terms( $values['product_id'], 'product_cat' );    
foreach ($terms as $term) {        
if(in_array($term->term_id, $category_IDs)){
    unset( $available_gateways['cheque'] );
            break;
        }
    break;
    }
 }
    return $available_gateways;
}

?>