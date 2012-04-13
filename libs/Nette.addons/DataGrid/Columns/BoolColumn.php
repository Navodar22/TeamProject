<?php

class BoolColumn extends Column 
{	
	
	public function __construct($parent, $name) {
		parent::__construct($parent, $name);
		
		$this->kind = 'bool';
	}		
	
}
