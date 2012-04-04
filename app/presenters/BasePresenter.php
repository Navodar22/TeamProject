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
	
	
	public $aStates = array(
		'2',	// schválený
		'4',	// pripravený na realizáciu
		'5',	// v ralizácii
		'6'		// úspešne ukončený
	);
	
    public function startup() {
		parent::startup();
		
		$this->db = $this->model('BaseModel')->getDBConnection();
		$this->template->title = 'Plánovací alokačný systém';
	}
	
	
	public function model($model_name) {
        return $this->context->LoadModel->getModel($model_name);
    }
	
}
