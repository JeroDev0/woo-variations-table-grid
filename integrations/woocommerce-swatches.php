<?php

add_filter( 'vartable_attributes_term_output', 'vartable_woocommerce_swatches', 10, 6 );
function vartable_woocommerce_swatches( $cell, $value, $attr_slug, $attr_td_value, $attrnames, $product ) {
	
	if ( intval( get_option( 'vartable_swatches' ) ) == 0 || !isset( $value[ 'attributes' ][ $attr_slug ] ) ) {
		return $cell;
	}
	
	$term_slug = $value[ 'attributes' ][ $attr_slug ];
	
	$attribute = str_replace( 'attribute_', '', $attr_slug	);
	
	$config = new WC_Swatches_Attribute_Configuration_Object( $product, $attribute );
	
	if ( $config->get_size() === null ) {
		return $cell;
	}
	
	if ( $product && taxonomy_exists( $attribute ) ) {
		
		if ( $config->get_type() == 'product_custom' ) {
			
			$swatch_term = new WC_Product_Swatch_Term( $config, $term_slug, $attr_slug, false, $config->get_size() );
		} else {
			
			$term = get_term_by( 'slug', $term_slug, $attribute );
			$swatch_term = new WC_Swatch_Term( $config, $term->term_id, $term->taxonomy, false, $config->get_size() );
		}
	} else {
		$swatch_term = new WC_Product_Swatch_Term( $config, $term_slug, $attr_slug, false, $config->get_size() );
		
	}
	
	if ( isset( $swatch_term->type ) && ( $swatch_term->type == '-1' || empty( $swatch_term->type ) ) ) {
		return $cell;
	}
	
	$swatch = '';
	
	$args = array(
		'options' => array( $term_slug ),
		'attribute' => $attribute,
		'product' => $product,
	);
	
	ob_start();
	
	wc_swatches_variation_attribute_options( $args );
	
	$swatch = vt_strip_tags_content( ob_get_contents(), '<div><a>');
	ob_clean();
	
	if ( $swatch ) {
		
		if ( $config->get_label_layout() == 'label_above' ) {
			
			$swatch = $cell.$swatch;
			
		}
		if ( $config->get_label_layout() == 'label_below' ) {
			
			$swatch = $swatch.$cell;
			
		}
		
		return $swatch;
	}
	
	
	return $cell;
}


add_filter( 'vartable_not_sortable_filter', 'vartable_swatches_option' );

function vartable_swatches_option( $notsoratble ) {
		  
	if ( function_exists( 'wc_swatches_variation_attribute_options' ) ) {
		$notsoratble[ 'vartable_swatches' ] = __('Enable WooCommerce Swatches support', 'vartable');
	}
  
	return $notsoratble;
}

add_action( 'vartable_after_extra_options', 'vartable_swatches_after_extra_options' );
function vartable_swatches_after_extra_options () {
	
	$vartable_swatches = get_option( 'vartable_swatches', 0 );
	
	echo '
		<div class="fieldwrap">
			<label class="vm_label" for="vartable_swatches">'. __('Enable WooCommerce Swatches Support', 'vartable') .'</label>
			<select name="vartable_swatches" id="vartable_swatches">
				<option value="1" '. ($vartable_swatches == 1 ? 'selected="selected"' : '') .'>'. __('Yes', 'vartable') .'</option>
				<option value="0" '. ($vartable_swatches == 0 ? 'selected="selected"' : '') .'>'. __('No', 'vartable') .'</option>
			</select>
		</div>
	<hr />
	';
}


function vt_strip_tags_content( $text, $tags = '', $invert = FALSE ) {

  preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
  $tags = array_unique($tags[1]);
   
  if(is_array($tags) AND count($tags) > 0) {
    if($invert == FALSE) {
      return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
    }
    else {
      return preg_replace('@<('. implode('|', $tags) .')\b.*?>.*?</\1>@si', '', $text);
    }
  }
  elseif($invert == FALSE) {
    return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
  }
  return $text;
}