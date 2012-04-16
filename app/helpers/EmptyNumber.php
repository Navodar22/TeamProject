<?php

class EmptyNumber extends NObject
{

	public function process($number) {
		if(empty($number)) {
			return '-';
		} else {
			return number_format($number, '0', '', ' ');
		}
	}
	
}
