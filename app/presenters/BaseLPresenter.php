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
			$this->template->registerHelper('emptyPrice', callback(new EmptyPrice, 'process'));
			$this->template->registerHelper('emptyNumber', callback(new EmptyNumber, 'process'));
			$this->template->registerHelper('emptyDate', callback(new EmptyDate, 'process'));
		}
	}
	
	
	
	public function getStatuses() {
		//implement return statuses array by role
	}
	
	
	
	public function calculateMoney() {
		$total_students = 0;
		foreach($this->db->table('institute') as $institute) {
			$total_students += $institute->students;
		}

		$this->db->table('school')->update(array('students' => $total_students));
		$school = $this->db->table('school')->where('id', '1')->fetch();

		if($school->students <= 0) {
			$money_index = 0;
		} else {
			$money_index = $school->money/$school->students;
		}

		foreach($this->db->table('institute') as $institute) {
			$institute_money = $institute->students * $money_index;
			$institute->update(array('money' => $institute_money));
		}	
	}
}
