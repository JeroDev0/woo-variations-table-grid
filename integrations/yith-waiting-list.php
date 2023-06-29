<?php

add_filter( 'vartable_sortable_filter', 'ywl_vartable_sortable_filter' );
function ywl_vartable_sortable_filter( $sortable ) {
	
	$sortable[ 'vartable_ywl' ] = __('Waiting List', 'vartable');
	
	return $sortable;
}


add_action( 'vartable_after_extra_options', 'ywl_vartable_after_extra_options' );
function ywl_vartable_after_extra_options() {
	
	$vartable_ywl = get_option( 'vartable_ywl' );
	
	
	 echo '
	<div class="fieldwrap">
	  <label class="vm_label" for="vartable_ywl">'. __('Enable YITH Waiting List', 'vartable') .'</label>
	  <select name="vartable_ywl" id="vartable_ywl">
		<option value="1" '. ($vartable_ywl == 1 ? 'selected="selected"' : '') .'>'. __('Yes', 'vartable') .'</option>
		<option value="0" '. ($vartable_ywl == 0 ? 'selected="selected"' : '') .'>'. __('No', 'vartable') .'</option>
	  </select>
	</div>
	  <hr />
	';
	
}


add_filter( 'vartable_allcolumns', 'ywl_vartable_allcolumns', 9, 4 );
function ywl_vartable_allcolumns( $allcolumns, $value, $attrnames, $product ) {
	
	
	$vartable_ywl = get_option( 'vartable_ywl' );
	
	if ( (int) $vartable_ywl === 1 && function_exists( 'YITH_WCWTL' ) ) {
		
		$headname = '';
		if ( isset( $headenames['vartable_ywl'] ) ) {
			$headname = $headenames['vartable_ywl'];
		}
		
		$allcolumns['vartable_ywl'] = '
		<td class="vartable_ywl" data-label="' . apply_filters('vartable_dl_ywl', $headname, $value) . '">
		  ' . YITH_WCWTL_Frontend()->shortcode_the_form( array( 'product_id' => $value['variation_id'] ) ) . '
		</td>';
	}
	
	
	return $allcolumns;
}


add_filter( 'header_vartable_ywl', 'ywl_header_vartable_ywl', 10, 5 );
function ywl_header_vartable_ywl( $orderedheader, $headenames, $sortingval, $vokey, $product ) {
	
	$vartable_ywl = get_option( 'vartable_ywl' );
	
	if ( (int) $vartable_ywl === 1 && function_exists( 'YITH_WCWTL' ) ) {
		
		$orderedheader[$vokey] = '<th ' . $sortingval . ' class="' . $vokey . '" ><span>' . apply_filters('vartable_header_text', $headenames[$vokey], $product->get_id()) . '</span></th>';
		
	}
	
	
	return $orderedheader;
	
}