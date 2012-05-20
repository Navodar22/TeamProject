<?php

class ZeroPrice extends NObject
{

	public function process($number) {
		if(empty($number)) {
			return '0 €';
		} else {
			return number_format($number, '2', ',', ' ') . ' €';
		}
	}
	
}
