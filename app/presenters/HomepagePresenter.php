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
	
	
	public $institute_id;
	
	
	
	
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
		
		$total_data = $this->db->table('project')->select('
				count(*) AS project_count,
				sum(cost) AS total_cost,
				sum(hr) AS total_hr,
				sum(approved_cost) AS approved_cost,
				sum(approved_hr) AS approved_hr
				')->fetch();
		
		$this->template->school_data = $result;
		$this->template->total_data = $total_data;
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
		
		$this->institute_id = $id;
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
		$institute['id'] = $db_institute->id;
		$institute['name'] = $db_institute->name;
		$institute['acronym'] = $db_institute->acronym;
		
		$this->template->institute = $institute;
		$this->template->faculty = $db_institute->faculty;
	}
	
	
	public function createComponentDataGrid() {

        $source = $this->db->table('project')->where('project_institute:institute_id', $this->institute_id);

		if($source->count('*') <= 0) {
			
			$dg = new DataGrid();
			$dg->setDataSource($source);

			$dg->template->empty = true;
			return $dg;
		}
		
        $dg = new DataGrid();
        $dg->setDataSource($source);
		
        $dg->addAction('edit', 'Uprav', 'Projects:edit', array('id'));

        $dg->addColumn('id', 'No.')->setIntFilter('project.id')->setStyle('width: 50px');
        $dg->addColumn('name', 'Name')->setTextFilter('project.name')->setStyle('text-align: left');
		$dg->addCustomColumn('cost', 'Fin. zdroje')->setIntFilter('project.cost')->setHtml(create_function('$row', '$helper =  new EmptyPrice(); return $helper->process($row->cost);'));                                                                 																										
		$dg->addCustomColumn('approved_cost', 'Schválené fin.zdroje')->setIntFilter('project.approved_cost')->setHtml(create_function('$row', '$helper =  new EmptyPrice(); return $helper->process($row->approved_cost);'));
		$dg->addCustomColumn('participation', 'Spoluúčasť')->setIntFilter('project.participation')->setHtml(create_function('$row', '$helper =  new EmptyPrice(); return $helper->process($row->participation);'));
		$dg->addCustomColumn('approved_participation', 'Schválená spoluúčasť')->setIntFilter('project.approved_participation')->setHtml(create_function('$row', '$helper =  new EmptyPrice(); return $helper->process($row->approved_participation);'));
		$dg->addCustomColumn('hr', 'Ľudské zdroje')->setIntFilter('project.hr')->setHtml(create_function('$row', '$helper =  new EmptyNumber(); return $helper->process($row->hr);'));																
		$dg->addCustomColumn('approved_hr', 'Schválené ľudské zdroje')->setIntFilter('project.approved_hr')->setHtml(create_function('$row', '$helper =  new EmptyNumber(); return $helper->process($row->approved_hr);'));
		return $dg;
	}	
	
}
