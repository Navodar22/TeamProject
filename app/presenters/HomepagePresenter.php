<?php

/**
 * Homepage presenter.
 *
 * @author     John Doe
 * @package    MyApplication
 */
class HomepagePresenter extends BaseLPresenter
{
	public function renderDefault()
	{
		$school_data = $this->getSchoolData();
		$this->template->school_data = $school_data;
	} 
	
	
	
	public function actionFaculty($id) {
		$faculty = $this->getFacultyData($id);
		$this->template->faculty = $faculty;
	}
	
	
	public function actionInstitute($id) {
		$institute = $this->getInstituteData($id);
		$this->template->institute = $institute;
	}






	public function getSchoolData() {
		$faculties = $this->db->table('faculty');
		
		$result = array();
		foreach($faculties as $faculty) {
			$result[$faculty->id]['acronym'] = $faculty->acronym;
			$result[$faculty->id]['name'] = $faculty->name;
			
			$result[$faculty->id]['cost'] = 0;
			$result[$faculty->id]['allowed_cost'] = 0;
			$result[$faculty->id]['hr'] = 0;
			$result[$faculty->id]['allowed_hr'] = 0;
			$result[$faculty->id]['projects_count'] = 0;
			$result[$faculty->id]['projects'] = array();
			
			foreach($this->db->table('project_institute')->where('institute.faculty.id', $faculty->id) as $project_institute) {
				$result[$faculty->id]['cost'] += $project_institute->cost;
				$result[$faculty->id]['hr'] += $project_institute->hr;
				
				if(!in_array($project_institute->project->id, $result[$faculty->id]['projects'])) {
					$result[$faculty->id]['projects_count']++;
					$result[$faculty->id]['projects'][] = $project_institute->project->id;
				}
				
				if(in_array($project_institute->state, $this->aStates)) {
					$result[$faculty->id]['allowed_cost'] += $project_institute->cost;
					$result[$faculty->id]['allowed_hr'] += $project_institute->hr;
				}
			}
		}
		
		return $result;
	}
	
	
	
	
	public function getFacultyData($id) {

		$faculty = $this->db->table('faculty')->where('id', $id)->fetch();
		
		if(!$faculty) {
			throw new NBadRequestException();
		}
		
		$result['total']['acronym'] = $faculty->acronym;
		$result['total']['name'] = $faculty->name;

		$result['total']['cost'] = 0;
		$result['total']['allowed_cost'] = 0;
		$result['total']['hr'] = 0;
		$result['total']['allowed_hr'] = 0;
		$result['total']['projects_count'] = 0;
		$result['total']['projects'] = array();
		$result['institute'] = array();

		foreach($faculty->related('institute') as $institute) {
			
			$result['institute'][$institute->id]['acronym'] = $institute->acronym;
			$result['institute'][$institute->id]['name'] = $institute->name;

			$result['institute'][$institute->id]['cost'] = 0;
			$result['institute'][$institute->id]['allowed_cost'] = 0;
			$result['institute'][$institute->id]['hr'] = 0;			
			$result['institute'][$institute->id]['allowed_hr'] = 0;
			$result['institute'][$institute->id]['projects_count'] = 0;
			$result['institute'][$institute->id]['projects'] = array();
		
			foreach($this->db->table('project_institute')->where('institute.id', $institute->id) as $project_institute) {
				$result['total']['cost'] += $project_institute->cost;
				$result['total']['hr'] += $project_institute->hr;
				
				$result['institute'][$institute->id]['cost'] += $project_institute->cost;
				$result['institute'][$institute->id]['hr'] += $project_institute->hr;

				if(!in_array($project_institute->project->id, $result['total']['projects'])) {
					$result['total']['projects_count']++;
					$result['total']['projects'][] = $project_institute->project->id;
				}
				
				if(!in_array($project_institute->project->id, $result['institute'][$institute->id]['projects'])) {
					$result['institute'][$project_institute->institute->id]['projects_count']++;
					$result['institute'][$project_institute->institute->id]['projects'][] = $project_institute->project->id;
				}

				if(in_array($project_institute->state, $this->aStates)) {
					$result['total']['allowed_cost'] += $project_institute->cost;
					$result['total']['allowed_hr'] += $project_institute->hr;

					$result['institute'][$project_institute->institute->id]['allowed_cost'] += $project_institute->cost;
					$result['institute'][$project_institute->institute->id]['allowed_hr'] += $project_institute->hr;
				}
			}
		}
		
		return $result;
	}
	
	
	
	
	public function getInstituteData($id) {
		$institute = $this->db->table('institute')->where('id', $id)->fetch();
		
		if(!$institute) {
			throw new NBadRequestException();
		}
		
		$result['total']['acronym'] = $institute->acronym;
		$result['total']['name'] = $institute->name;

		$result['total']['cost'] = 0;
		$result['total']['allowed_cost'] = 0;
		$result['total']['participation'] = 0;
		$result['total']['allowed_participation'] = 0;
		$result['total']['hr'] = 0;
		$result['total']['allowed_hr'] = 0;
		$result['total']['projects_count'] = 0;
		$result['total']['projects'] = array();
		$result['project'] = array();
		
		foreach($institute->related('project_institute')->where('institute.id', $institute->id) as $project_institute) {
			$result['total']['cost'] += $project_institute->cost;
			$result['total']['hr'] += $project_institute->hr;
			$result['total']['participation'] += $project_institute->participation;

			if(!in_array($project_institute->project->id, $result['total']['projects'])) {
				$result['total']['projects_count']++;
				$result['total']['projects'][] = $project_institute->project->id;
				$result['project'][$project_institute->project->id] = $this->getProjectData($project_institute->project->id);
			}

			if(in_array($project_institute->state, $this->aStates)) {
				$result['total']['allowed_cost'] += $project_institute->cost;
				$result['total']['allowed_hr'] += $project_institute->hr;
				$result['total']['allowed_participation'] += $project_institute->participation;
			}
		}
		
		return $result;
	}
	
	
	
	
	public function getProjectData($id) {
		$project = $this->db->table('project')->where('id', $id)->fetch();
		
		if(!$project) {
			throw new NBadRequestException();
		}
		
		$totals = array();
		$totals['name'] = $project->name;
		foreach($project->related('project_institute') as $project_institute) {	
			
			//not first project add values to previous 
			if(isSet($totals['total'])) {
				$totals['total']['cost'] += $project_institute->cost;
				$totals['total']['hr'] += $project_institute->hr;
				$totals['total']['participation'] += $project_institute->participation;

				if($totals['total']['start'] > $project_institute->start) {
					$totals['total']['start'] = $project_institute->start;
				}

				if($totals['total']['end'] < $project_institute->end) {
					$totals['total']['end'] = $project_institute->end;
				}
			} else { //if first project initialize array
				$totals['total']['cost'] = $project_institute->cost;
				$totals['total']['hr'] = $project_institute->hr;
				$totals['total']['participation'] = $project_institute->participation;
				$totals['total']['start'] = $project_institute->start;
				$totals['total']['end'] = $project_institute->end;
			}
			
			if(in_array($project_institute->state->id, $this->aStates)) {
				
				//not first project add values to previous 
				if(isSet($totals['approved'])) {
					$totals['approved']['cost'] += $project_institute->cost;
					$totals['approved']['hr'] += $project_institute->hr;
					$totals['approved']['participation'] += $project_institute->participation;
					
					if($totals['approved']['start'] > $project_institute->start) {
						$totals['approved']['start'] = $project_institute->start;
					}

					if($totals['approved']['end'] < $project_institute->end) {
						$totals['approved']['end'] = $project_institute->end;
					}
				} else { //if first project initialize array
					$totals['approved']['cost'] = $project_institute->cost;
					$totals['approved']['hr'] = $project_institute->hr;
					$totals['approved']['participation'] = $project_institute->participation;
					$totals['approved']['start'] = $project_institute->start;
					$totals['approved']['end'] = $project_institute->end;
				}
			}
		}
		
		return $totals;		
	}
	
}
