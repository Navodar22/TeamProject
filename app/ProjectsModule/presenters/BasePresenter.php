<?php

/**
 *  Base Projects presenter class
 * 
 *  @author Samuel Kelemen
 */    
abstract class Projects_BasePresenter extends BaseLPresenter
{

	public function unsetProjectsSession() {
		$session = $this->context->getService('session');
		$project_institute_add = $session->getSection('project_institute_add');
		$project_institute_edit= $session->getSection('project_institute_edit');
		
		unset($project_institute_add->values);
		unset($project_institute_edit->values);
	}
	
}
