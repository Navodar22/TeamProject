<?php

/**
 * Statistics presenter.
 *
 * @author     Samuel Kelemen
 */
class StatisticsPresenter extends BaseLPresenter
{
	public $colors = array(
		'#305abc',	
		'#c3000e',	
		'#32c300',	
		'#6f24cc',	
		'#e07432',	
		'#8cd108',	
		'#b810cc',
		'#397d99',
		'#de5909',
		'#920156',
		'#530192',
		'#014592',
		'#459201',
		'#cf39d4',
		'#3982d4',
		'#39d485',
		'#b45e3c',
		'#3c74b4',
		'#6c3cb4',
		'#b43c6f',
		'#b43c3c',
		'#91b43c',
		'#e885a3',
		'#e8c185',
		'#8985e8',
	);
	
	
	
	public function renderDefault() {
		$faculties = $this->db->table('faculty');
		$this->template->faculties = $faculties;
		
		$school = array();
		foreach($faculties as $faculty) {
			$school[$faculty->id]['money'] = 0;
			$school[$faculty->id]['students'] = 0;
			$school[$faculty->id]['acronym'] = $faculty->acronym;
			foreach($faculty->related('institute') as $institute) {
				$school[$faculty->id]['money'] += $institute->money;
				$school[$faculty->id]['students'] += $institute->students;
			}
		}
		
		rsort($school);
		
		$this->template->school = $school;
		$this->template->colors = $this->colors;
		$this->template->backlink = $this->application->storeRequest();
	}
	
	
	public function actionInsertData() {
		$values = array(
			'institute_id' => '3',
			'state_id' => '1',
			'cost' => '200',
			'hr' => '20',
			'participation' => '100'
		);
		
		for($i = 5; $i <= 505; $i++) {
			$values['project_id'] = $i;
			$this->db->table('project_institute')->insert($values);
		}
		
		$this->redirect('default');
	}
	
	public function renderFacultystat() {
		$faculties = $this->db->table('faculty');
		
		if(empty($this->dateRange)) {
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
		} else {
			foreach($faculties as $faculty) {
				$result[$faculty->id] = $this->db->table('project_institute')
						->where('institute.faculty.id', $faculty->id)
						->where('start >= ?', $this->dateRange->from)
						->where('end <= ?', $this->dateRange->to)->select('
							count(DISTINCT project_id) AS project_count,
							sum(project_institute.cost) AS total_cost,
							sum(project_institute.hr) AS total_hr,
							sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute.cost, 0)) AS approved_cost,
							sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute.hr, 0)) AS approved_hr
						')->fetch();
				
				$result[$faculty->id]['name'] = $faculty->name;
				$result[$faculty->id]['acronym'] = $faculty->acronym;
			}	
		}
		
		if(empty($this->dateRange)) {
			$db_total_data = $this->db->table('project');
		} else {
			$db_total_data = $this->db->table('project')
					->where('start >= ?', $this->dateRange->from)
					->where('end <= ?', $this->dateRange->to);
		}
		
		$total_data = $db_total_data->select('
						count(*) AS project_count,
						sum(cost) AS total_cost,
						sum(hr) AS total_hr,
						sum(approved_cost) AS approved_cost,
						sum(approved_hr) AS approved_hr
					')->fetch();
		
		$this->template->school_data = $result;
		$this->template->total_data = $total_data;
			$this->template->colors = $this->colors;
		}
}
