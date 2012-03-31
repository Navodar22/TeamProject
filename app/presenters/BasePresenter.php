<?php

/**
 *  Base presenter class
 * 
 *  @author Samuel Kelemen
 */    
abstract class BasePresenter extends NPresenter
{
	/** @var object		BaseModel instance */
	public $db;
	
    public function startup() {
		parent::startup();
		
		$this->db = $this->model('BaseModel')->getDBConnection();
		$this->template->title = 'Plánovací alokačný systém';
	}
}
