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
		$this->template->school_data = $this->getSchoolData();
	} 
	
	
	
	
	/**
	 * Faculty render function.
	 * 
	 * @param int $id		ID of selected faculty 
	 */
	public function actionFaculty($id) {
		$this->template->faculty = $this->getFacultyData($id);
	}
	
	
	
	
	/**
	 * Institute render function.
	 * 
	 * @param int $id		ID of selected institute 
	 */
	public function actionInstitute($id) {
		$this->template->institute = $this->getInstituteData($id);
	}




	/**
	 * Function to get structured data of all faculties on school.
	 * 
	 * @return array 
	 */
	public function getSchoolData() {
		// Database queries = 1
		$faculties = $this->db->table('faculty');
		
		$result = array();
		foreach($faculties as $faculty) {
			$result[$faculty->id] = array(
				'acronym' => $faculty->acronym,
				'name' => $faculty->name,
				'cost' => 0,
				'approved_cost' => 0,
				'hr' => 0,
				'approved_hr' => 0,
				'projects_count' => 0,
				'projects' => array()
			);
			
			// Database queries = count of faculties - now 7
			foreach($this->db->table('project_institute')->where('institute.faculty.id', $faculty->id) as $project_institute) {
				$result[$faculty->id]['cost'] += $project_institute->cost;
				$result[$faculty->id]['hr'] += $project_institute->hr;
				
				if(!in_array($project_institute->project->id, $result[$faculty->id]['projects'])) {
					$result[$faculty->id]['projects_count']++;
					$result[$faculty->id]['projects'][] = $project_institute->project->id;
				}
				
				if(in_array($project_institute->state, $this->aStates)) {
					$result[$faculty->id]['approved_cost'] += $project_institute->cost;
					$result[$faculty->id]['approved_hr'] += $project_institute->hr;
				}
			}
		}		
		
		return $result;
	}
	
	
	
	
	/**
	 * Function to get structured data of one faculty. 
	 * Faculty overview and all institutes.
	 * 
	 * @param int $id		ID of selected faculty
	 * @return array
	 */
	public function getFacultyData($id) {
		// Database queries = 1
		$faculty = $this->db->table('faculty')->where('id', $id)->fetch();
		
		// Database queries = 1 - may be too many records
		$project_institutes = $this->objectToArray($this->db->table('project_institute'));
		
		if(!$faculty) {
			throw new NBadRequestException();
		}
		
		$result['total'] = array(
			'acronym' => $faculty->acronym,
			'name' => $faculty->name,
			'cost' => 0,
			'approved_cost' => 0,
			'hr' => 0,
			'approved_hr' => 0,
			'projects_count' => 0,
			'projects' => array()
		);
		
		$result['institute'] = array();

		// Database queries = 1
		foreach($faculty->related('institute') as $institute) {
			
			$result['institute'][$institute->id] = array(
				'acronym' => $institute->acronym,
				'name' => $institute->name,
				'cost' => 0,
				'approved_cost' => 0,
				'hr' => 0,
				'approved_hr' => 0,
				'projects_count' => 0,
				'projects' => array()
			);

			//iterate over array -> not too much query to database
			foreach($project_institutes as $project_institute) {
				if($project_institute['institute_id'] == $institute->id) {
					$result['total']['cost'] += $project_institute['cost'];
					$result['total']['hr'] += $project_institute['hr'];

					$result['institute'][$institute->id]['cost'] += $project_institute['cost'];
					$result['institute'][$institute->id]['hr'] += $project_institute['hr'];

					if(!in_array($project_institute['project_id'], $result['total']['projects'])) {
						$result['total']['projects_count']++;
						$result['total']['projects'][] = $project_institute['project_id'];
					}

					if(!in_array($project_institute['project_id'], $result['institute'][$institute->id]['projects'])) {
						$result['institute'][$project_institute['institute_id']]['projects_count']++;
						$result['institute'][$project_institute['institute_id']]['projects'][] = $project_institute['project_id'];
					}

					if(in_array($project_institute['state_id'], $this->aStates)) {
						$result['total']['approved_cost'] += $project_institute['cost'];
						$result['total']['approved_hr'] += $project_institute['hr'];

						$result['institute'][$project_institute['institute_id']]['approved_cost'] += $project_institute['cost'];
						$result['institute'][$project_institute['institute_id']]['approved_hr'] += $project_institute['hr'];
					}
				}
			}
		}
		
		return $result;
	}
	
	
	
	
	/**
	 * Function to get structured data of one institute.
	 * Institute overview and all projects.
	 * 
	 * @param int $id		ID of selected institute
	 * @return array 
	 */
	public function getInstituteData($id) {
		$institute = $this->db->table('institute')->where('id', $id)->fetch();
		$this->projects = $this->objectToArray($this->db->table('project'));
		$this->project_institutes = $this->objectToArray($this->db->table('project_institute'));
		
		if(!$institute) {
			throw new NBadRequestException();
		}
		
		$result['total'] = array(
			'acronym' => $institute->acronym,
			'name' => $institute->name,
			'cost' => 0,
			'approved_cost' => 0,
			'participation' => 0,
			'approved_participation' => 0,
			'hr' => 0,
			'approved_hr' => 0,
			'projects_count' => 0,
			'start' => new DateTime('31.12.3000'),
			'end' => new DateTime('1.1.1970'),
			'approved_start' => new DateTime('31.12.3000'),
			'approved_end' => new DateTime('1.1.1970'),
			'projects' => array()
		);
		
		$result['project'] = array();
		
		foreach($institute->related('project_institute')->where('institute.id', $institute->id) as $project_institute) {
			$result['total']['cost'] += $project_institute->cost;
			$result['total']['hr'] += $project_institute->hr;
			$result['total']['participation'] += $project_institute->participation;
			
			if($result['total']['start'] > $project_institute->start) {
				$result['total']['start'] = $project_institute->start;
			}

			if($result['total']['end'] < $project_institute->end) {
				$result['total']['end'] = $project_institute->end;
			}

			if(!in_array($project_institute->project->id, $result['total']['projects'])) {
				$result['total']['projects_count']++;
				$result['total']['projects'][] = $project_institute->project->id;
				$result['project'][$project_institute->project->id] = $this->getProjectData($project_institute->project->id);
			}

			if(in_array($project_institute->state, $this->aStates)) {
				$result['total']['approved_cost'] += $project_institute->cost;
				$result['total']['approved_hr'] += $project_institute->hr;
				$result['total']['approved_participation'] += $project_institute->participation;
				
				if($result['total']['approved_start'] > $project_institute->start) {
					$result['total']['approved_start'] = $project_institute->start;
				}

				if($result['total']['approved_end'] < $project_institute->end) {
					$result['total']['approved_end'] = $project_institute->end;
				}
			}
		}
		
		return $result;
	}
	
	
	
	
	/**
	 * Function to get structured data of one project.
	 * 
	 * @param int $id		ID of selected project
	 * @return array 
	 */
	public function getProjectData($id) {
		
		$project = false;
		foreach($this->projects as $p) {
			if($p['id'] == $id) {
				$project = $p;
			}
		}
		
		if(!$project) {
			throw new NBadRequestException();
		}
		
		$totals = array(
			'total' => array(
				'cost' => 0,
				'hr' => 0,
				'participation' => 0,
				'start' => new DateTime('31.12.3000'),
				'end' => new DateTime('1.1.1970')
			),
			'approved' => array(
				'cost' => 0,
				'hr' => 0,
				'participation' => 0,
				'start' => new DateTime('31.12.3000'),
				'end' => new DateTime('1.1.1970')
			)
		);
		
		$totals['name'] = $project['name'];
		
		//iterate over array
		foreach($this->project_institutes as $project_institute) {	
			if($project_institute['project_id'] == $project['id']) { 
		
				$totals['total']['cost'] += $project_institute['cost'];
				$totals['total']['hr'] += $project_institute['hr'];
				$totals['total']['participation'] += $project_institute['participation'];

				if($totals['total']['start'] > $project_institute['start']) {
					$totals['total']['start'] = $project_institute['start'];
				}

				if($totals['total']['end'] < $project_institute['end']) {
					$totals['total']['end'] = $project_institute['end'];
				}
				
				if(in_array($project_institute['state_id'], $this->aStates)) {

					$totals['approved']['cost'] += $project_institute['cost'];
					$totals['approved']['hr'] += $project_institute['hr'];
					$totals['approved']['participation'] += $project_institute['participation'];

					if($totals['approved']['start'] > $project_institute['start']) {
						$totals['approved']['start'] = $project_institute['start'];
					}

					if($totals['approved']['end'] < $project_institute['end']) {
						$totals['approved']['end'] = $project_institute['end'];
					}
				}
			}
		}
		return $totals;		
	}	
}
