<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	
	$moz_all_domains 	 =  array();
	$average_reffering_domains = 0;
	$average_domain_authority  = 0;
	$average_page_authority  = 0;

	if( 'success' == $verified_entry['type'] ) {		
		$entry = $verified_entry['entry'];
	    include('shortcode-html-output.php');	
	} else{
		$entry_id =  absint(wpforms()->process->entry_id);
		$entry = wpforms()->entry->get( $entry_id );	   
		include('shortcode-html-output.php');
	}
	
?>

