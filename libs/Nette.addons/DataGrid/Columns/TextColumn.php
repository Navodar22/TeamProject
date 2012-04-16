<?php

class TextColumn extends Column
{
	
	public function __construct($parent, $name) {
		parent::__construct($parent, $name);

		$this->kind = 'text';
	}
	
}
