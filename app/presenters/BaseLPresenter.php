<?php

/**
 *  Base presenter class
 * 
 *  @author Samuel Kelemen
 */    
abstract class BaseLPresenter extends BasePresenter
{
    public function startup() {
		parent::startup();
		
		if(!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:in');
		} else {
			$this->template->user = $this->getUser()->getIdentity();
		}
	}
}
