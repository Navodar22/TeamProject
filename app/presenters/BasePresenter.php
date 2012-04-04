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
		'2',	// Schválený
		'3',	// Pripravený na realizáciu
		'4',	// V realizácii
		'5',	// Úspešne ukončený
		'6',	// Dočaste zastavený
		'7'		// Neúspešne ukončený
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
