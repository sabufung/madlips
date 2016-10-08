<?php

class ASG_VisualElement{
	
	function render_attributes($attr = array()){
		foreach($attr as $name => $value){
				$value = esc_attr($value);
				echo $this->format_attribute($name, $value);
		}
	}
	function format_attribute($name, $value){
			return "$name=\"" . esc_attr($value) . "\" ";
	}
}