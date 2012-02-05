<?php

/**
 *  Base presenter class
 * 
 *  @author Samuel Kelemen
 */    
abstract class BasePresenter extends NPresenter
{
    public function startup() {
		parent::startup();
		
		$this->template->title = 'Plánovací alokačný systém';
	}
	
	public function model($model_name) {
        return $this->context->LoadModel->getModel($model_name);
    }
}
