<?php 

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SWPB_Ajax {
	
	function __construct() {		
		add_action("wp_ajax_swpb_ajax", array( $this, "controller" ) );	
	    add_filter( 'swpb_get_request', array( $this, 'get_request' ) );	
	    add_filter( 'swpb_get_response', array( $this, 'get_response' ), 5, 3 );
	}
	function get_request() {
		if( isset( $_REQUEST["SWPB_AJAX_PARAM"] ) ) {	
			$payload = json_decode( str_replace('\"','"', $_REQUEST["SWPB_AJAX_PARAM"] ), true );	
			return array (
				"type" => $payload["request"],
				"context" => $payload["context"],
				"post" => $payload["post"],
				"payload" => $payload["payload"]
			);
		}
	}
	function get_response( $status, $msg, $data ) {
		return json_encode( array ( 
			"status" => $status, 
			"message"=>$msg, 
			"data"=>$data )
		);
	}	
	function controller() {
		/* Parse the incoming request */

		swpb()->request = apply_filters( 'swpb_get_request', array() );		
		/* Handle the request */
		$message = "";
		$products = array();
		if( swpb()->request["context"] == "search" ) {
			if( swpb()->request["type"] == "GET" ) {
				$result = apply_filters( 'swpb/build/products_search', array() );
				swpb()->response = apply_filters( 'swpb_get_response', true, "Success", $result );
			}
		} else if( swpb()->request["context"] == "add-to-bundle" ) {	
			$res = apply_filters( 'swpb/add_to_bundle/products', array() );

			if( $res ) {
				$message = "Successfully Added to Bundled";				
				$products = apply_filters( 'swpb_included_products', swpb()->request['post'], swpb()->request['payload'] );
			} else {
				$message = "Failed to Add to Bundled";
			}
			swpb()->response = apply_filters( 'swpb_get_response', $res, $message, $products );
		} else if( swpb()->request["context"] == "remove-from-bundle" ) {
			$res = apply_filters( 'swpb/remove_from_bundle/products', array() );
			if( $res ) {
				$message = "Successfully Removed from Bundle";				
			} else {
				$message = "Failed to Remove from Bundle";
			}
			swpb()->response = apply_filters( 'swpb_get_response', $res, $message, $products );
		}
		else if( swpb()->request["context"] == "update-from-bundle" ) {
			$res = apply_filters( 'swpb/update_from_bundle/products', array() );
			if( $res ) {
				$message = "Successfully Updated from Bundle";				
			} else {
				$message = "Failed to Updated from Bundle";
			}
			swpb()->response = apply_filters( 'swpb_get_response', $res, $message, $products );
		}
		/* Respond the request */
		echo swpb()->response;
		/* end the request - response cycle */
		die();
	}
	
}

/* Init wccpf ajax object */
new SWPB_Ajax();

?>