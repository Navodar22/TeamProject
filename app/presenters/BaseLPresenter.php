<?php

/**
 *  Base presenter class
 * 
 *  @author Samuel Kelemen
 */    
abstract class BaseLPresenter extends BasePresenter
{
	
	public $dateRange;
	private $user;
	
	
    public function startup() {
		parent::startup();
		
		if(!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:in');
		} else {
			$this->template->user = $this->user = $this->getUser()->getIdentity();
			
                        
			$this->dateRange = $this->getDateRange();
			$this->template->dateRange = $this->dateRange;
			
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
	
	
	
	
	/**
	 * 
	 */
	public function calculateProjectData($id) {
		$values = $this->db->table('project_institute')->where('project_id', $id)->select('
			sum(project_institute.cost) AS cost,
			sum(project_institute.hr) AS hr,
			sum(project_institute.participation) AS participation,
			sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute.cost, 0)) AS approved_cost,
			sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute.hr, 0)) AS approved_hr,
			sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute.participation, 0)) AS approved_participation,
			min(project_institute.start) AS start,
			max(project_institute.end) AS end,
			min(CASE WHEN project_institute.state_id IN (' . implode(',', $this->aStates) . ') THEN project_institute.start ELSE NULL END) AS approved_start,
			max(CASE WHEN project_institute.state_id IN (' . implode(',', $this->aStates) . ') THEN project_institute.end ELSE NULL END) AS approved_end
		')->fetch();
		
//		Ndebugger::dump($values);exit;
		
		$this->db->table('project')->where('id', $id)->update($values);
	}
	
	
	
	
	public function getDateRange() {
		$session = $this->context->session;
		
		if($session->hasSection('dateRange')) {
			$dateRange = $session->getSection('dateRange');
		} else {
			$dateRange = NULL;
		}
		
		return $dateRange;
	}
}
