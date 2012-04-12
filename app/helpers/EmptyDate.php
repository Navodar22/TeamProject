<?php

class EmptyDate extends NObject
{

	public function process($date, $format) {
		Ndebugger::barDump($date);
		if(empty($date)) {
			return '-';
		} else {
			return $date->format($format);
		}
	}
	
}
