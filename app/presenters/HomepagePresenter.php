<?php

/**
 * Homepage presenter.
 *
 * @author     Samuel Kelemen
 */
class HomepagePresenter extends BaseLPresenter
{
	/** @var array		Array of all projects */
	public $projects;
	
	/** @var array		Array of table project_institute */
	public $project_institutes;
	
	
	
	
	/**
	 * Default render function.
	 */
	public function renderDefault()	{		
		$faculties = $this->db->table('faculty');
		
		foreach($faculties as $faculty) {
		$result[$faculty->id] = $this->db->table('project_institute')->where('institute.faculty.id', $faculty->id)->select('
				count(DISTINCT project_id) AS project_count,
				sum(project_institute.cost) AS total_cost,
				sum(project_institute.hr) AS total_hr,
				sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute.cost, 0)) AS approved_cost,
				sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute.hr, 0)) AS approved_hr
				')->fetch();
			$result[$faculty->id]['name'] = $faculty->name;
			$result[$faculty->id]['acronym'] = $faculty->acronym;
		}		
		
		$this->template->school_data = $result;
	} 
	
	
	
	
	/**
	 * Faculty render function.
	 * 
	 * @param int $id		ID of selected faculty 
	 */
	public function actionFaculty($id) {
		$db_faculty = $this->db->table('faculty')->where('id', $id)->fetch();
		
		$faculty = $this->db->table('project_institute')->where('institute.faculty.id', $db_faculty->id)->select('
				count(DISTINCT project_id) AS project_count,
				sum(project_institute.cost) AS total_cost,
				sum(project_institute.hr) AS total_hr,
				sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute.cost, 0)) AS approved_cost,
				sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute.hr, 0)) AS approved_hr
				')->fetch();
		$faculty['name'] = $db_faculty->name;
		$faculty['acronym'] = $db_faculty->acronym;
		$faculty['id'] = $db_faculty->id;
			
		
		foreach($db_faculty->related('institute') as $institute) {
		$institutes[$institute->id] = $this->db->table('project_institute')->where('institute.id', $institute->id)->select('
				count(DISTINCT project_id) AS project_count,
				sum(project_institute.cost) AS total_cost,
				sum(project_institute.hr) AS total_hr,
				sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute.cost, 0)) AS approved_cost,
				sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute.hr, 0)) AS approved_hr,
				min(project_institute.start) AS start,
				max(project_institute.end) AS end,
				min(CASE WHEN project_institute.state_id IN (' . implode(',', $this->aStates) . ') THEN project_institute.start ELSE NULL END) AS approved_start,
				max(CASE WHEN project_institute.state_id IN (' . implode(',', $this->aStates) . ') THEN project_institute.end ELSE NULL END) AS approved_end
				')->fetch();
			$institutes[$institute->id]['name'] = $institute->name;
			$institutes[$institute->id]['acronym'] = $institute->acronym;
		}		

		 $this->template->institutes = $institutes;
		 $this->template->faculty = $faculty;
	}
	
	
	
	
	/**
	 * Institute render function.
	 * 
	 * @param int $id		ID of selected institute 
	 */
	public function actionInstitute($id) {
		//$this->template->institute = $this->getInstituteData($id);
		
		$db_institute = $this->db->table('institute')->where('id', $id)->fetch();
		
		$institute = $this->db->table('project_institute')->where('institute.id', $db_institute->id)->select('
				count(DISTINCT project_id) AS project_count,
				sum(project_institute.cost) AS total_cost,
				sum(project_institute.hr) AS total_hr,
				sum(project_institute.participation) AS total_participation,
				sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute.cost, 0)) AS approved_cost,
				sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute.hr, 0)) AS approved_hr,
				sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute.participation, 0)) AS approved_participation,
				min(project_institute.start) AS start,
				max(project_institute.end) AS end,
				min(CASE WHEN project_institute.state_id IN (' . implode(',', $this->aStates) . ') THEN project_institute.start ELSE NULL END) AS approved_start,
				max(CASE WHEN project_institute.state_id IN (' . implode(',', $this->aStates) . ') THEN project_institute.end ELSE NULL END) AS approved_end
				')->fetch();
		$institute['name'] = $db_institute->name;
		$institute['acronym'] = $db_institute->acronym;
		
		$projects = $this->db->table('project')->where('project_institute:institute_id', $db_institute->id);
		
		$this->template->institute = $institute;
		$this->template->projects = $projects;
	}
}
