<?php
class TM_EPO_FIELDS_select extends TM_EPO_FIELDS {

	public function display_field( $element=array(), $args=array() ) {
		$display =  array(
				'options'   			=> '',
				'use_url'				=> isset( $element['use_url'] )?$element['use_url']:"",
				'textafterprice' 		=> isset( $element['text_after_price'] )?$element['text_after_price']:"",
				'hide_amount'  			=> isset( $element['hide_amount'] )?" ".$element['hide_amount']:"",
				'changes_product_image' => empty( $element['changes_product_image'] )?"":$element['changes_product_image'],
				'quantity' 		=> isset( $element['quantity'] )?$element['quantity']:"",
			);

		$_default_value_counter=0;
		if (!empty($element['placeholder'])){
			$display['options'] .='<option value="" data-price="" data-rules="" data-rulestype="">'.
				wptexturize( apply_filters( 'woocommerce_tm_epo_option_name', $element['placeholder'] ) ).'</option>';								
		}
		foreach ( $element['options'] as $value=>$label ) {							
			$default_value=isset( $element['default_value'] )
			?
				(($element['default_value']!=="")
				?((int) $element['default_value'] == $_default_value_counter)
				:false)
			:false;

			$selected=false;
			
			if (isset($_POST['tmcp_'.$args['name_inc']]) && esc_attr(stripcslashes($_POST['tmcp_'.$args['name_inc']]))==esc_attr( ( $value ) ) ){
				$selected=true;
			}
			elseif (isset($_GET['tmcp_'.$args['name_inc']]) && esc_attr(stripcslashes($_GET['tmcp_'.$args['name_inc']]))==esc_attr( ( $value ) ) ){
				$selected=true;
			}
			elseif (empty($_POST) && isset($default_value)){
				if ($default_value){
					$selected=true;
				}
			}
			$data_url=isset($element['url'][$_default_value_counter])?$element['url'][$_default_value_counter]:"";

			$display['options'] .='<option '.
				selected( $selected, true, 0 ).
				' value="'.esc_attr( $value ).'"'.
				(!empty($data_url)?' data-url="'.esc_attr($data_url).'"':'').
				' data-imagep="'.( isset( $element['imagesp'][$_default_value_counter] )?$element['imagesp'][$_default_value_counter]:"").'"'.
				' data-price="'.( isset( $element['rules_filtered'][$value][0] )?$element['rules_filtered'][$value][0]:0).'"'.
				' data-rules="'.( isset( $element['rules_filtered'][$value] )?esc_html( json_encode( ( $element['rules_filtered'][$value] ) ) ):'' ).'"'.
				' data-rulestype="'.( isset( $element['rules_type'][$value] )?esc_html( json_encode( ( $element['rules_type'][$value] ) ) ):'' ).'">'.
				wptexturize( apply_filters( 'woocommerce_tm_epo_option_name', $label ) ).'</option>';
			
			$_default_value_counter++;
		}
		return $display;
	}

	public function validate() {

		$passed = true;
									
		foreach ( $this->field_names as $attribute ) {
			if ( !isset( $this->epo_post_fields[$attribute] ) ||  $this->epo_post_fields[$attribute]=="" ) {
				$passed = false;
				break;
			}										
		}

		return $passed;
	}
	
}