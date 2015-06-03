<?php
class TM_EPO_FIELDS_date extends TM_EPO_FIELDS {

	public function display_field( $element=array(), $args=array() ) {
		return array(
				'textafterprice' 	=> isset( $element['text_after_price'] )?$element['text_after_price']:"",
				'hide_amount'  		=> isset( $element['hide_amount'] )?" ".$element['hide_amount']:"",
				'style' 			=> isset( $element['button_type'] )?$element['button_type']:"",
				'format' 			=> !empty( $element['format'] )?$element['format']:0,
				'start_year' 		=> !empty( $element['start_year'] )?$element['start_year']:"1900",
				'end_year' 			=> !empty( $element['end_year'] )?$element['end_year']:(date("Y")+10),
				'min_date' 			=> !empty( $element['min_date'] )?$element['min_date']:"",
				'max_date' 			=> !empty( $element['max_date'] )?$element['max_date']:"",
				'disabled_dates' 	=> !empty( $element['disabled_dates'] )?$element['disabled_dates']:"",
				'disabled_weekdays' => isset( $element['disabled_weekdays'] )?$element['disabled_weekdays']:"",
				'tranlation_day' 	=> !empty( $element['tranlation_day'] )?$element['tranlation_day']:"",
				'tranlation_month' 	=> !empty( $element['tranlation_month'] )?$element['tranlation_month']:"",
				'tranlation_year' 	=> !empty( $element['tranlation_year'] )?$element['tranlation_year']:"",
				'quantity' 		=> isset( $element['quantity'] )?$element['quantity']:"",
			);
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