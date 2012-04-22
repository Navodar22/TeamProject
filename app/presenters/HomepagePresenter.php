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
		$result = null;
    $school_total_money = 0;
		if(empty($this->dateRange)) {		  
			foreach($faculties as $faculty) {
        $faculty_money = 0;
				$result[$faculty->id] = $this->db->table('project_institute')->where('institute.faculty.id', $faculty->id)->select('
					count(DISTINCT project_id) AS project_count,
					sum(project_institute.cost) AS total_cost,
					sum(project_institute.hr) AS total_hr,
					sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute.cost, 0)) AS approved_cost,
					sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute.hr, 0)) AS approved_hr					
          ')->fetch();
        foreach ($faculty->related('institute') as $institute)
          $faculty_money += $institute->money;
            
        $result[$faculty->id]['free_money'] = $faculty_money - $result[$faculty->id]['approved_cost'];  
				$result[$faculty->id]['name'] = $faculty->name;
				$result[$faculty->id]['acronym'] = $faculty->acronym;
				$school_total_money += $faculty_money;
			}	
		} else {
			foreach($faculties as $faculty) {
			  $faculty_money = 0;
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
				foreach ($faculty->related('institute') as $institute)
          $faculty_money += $institute->money;            
        $result[$faculty->id]['free_money'] = $faculty_money - $result[$faculty->id]['approved_cost'];     
				$result[$faculty->id]['name'] = $faculty->name;
				$result[$faculty->id]['acronym'] = $faculty->acronym;
        $school_total_money += $faculty_money;
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
    $total_data['free_money'] = $school_total_money - $total_data['approved_cost'];


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
		$faculty_money = 0;
		
		if(empty($this->dateRange)) {
			$faculty_date = $this->db->table('project_institute')->where('institute.faculty.id', $db_faculty->id);
		} else {
			$faculty_date = $this->db->table('project_institute')
					->where('institute.faculty.id', $db_faculty->id)
					->where('start >= ?', $this->dateRange->from)
					->where('end <= ?', $this->dateRange->to);
		}
		
		$faculty = $faculty_date->select('
				count(DISTINCT project_id) AS project_count,
				sum(project_institute.cost) AS total_cost,
				sum(project_institute.hr) AS total_hr,
				sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute.cost, 0)) AS approved_cost,
				sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute.hr, 0)) AS approved_hr
        ')->fetch();
		$faculty['name'] = $db_faculty->name;
		$faculty['acronym'] = $db_faculty->acronym;
		$faculty['id'] = $db_faculty->id;
		foreach ($db_faculty->related('institute') as $institute)
      $faculty_money += $institute->money;
    $faculty['free_money'] = $faculty_money - $faculty['approved_cost'];
    
		if(empty($this->dateRange)) {
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
				$institutes[$institute->id]['free_money'] = $institute->money - $institutes[$institute->id]['approved_cost'];
			}		
		} else {
			foreach($db_faculty->related('institute') as $institute) {
			$institutes[$institute->id] = $this->db->table('project_institute')
					->where('institute.id', $institute->id)
					->where('start >= ?', $this->dateRange->from)
					->where('end <= ?', $this->dateRange->to)
					->select('
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
				$institutes[$institute->id]['free_money'] = $institute->money - $institutes[$institute->id]['approved_cost'];
			}
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
		
		if(empty($this->dateRange)) {
			$institute_date = $this->db->table('project_institute')->where('institute.id', $db_institute->id);
		} else {
			$institute_date = $this->db->table('project_institute')
					->where('institute.id', $db_institute->id)
					->where('start >= ?', $this->dateRange->from)
					->where('end <= ?', $this->dateRange->to);
		}
			
		
		$institute = $institute_date->select('
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
		$institute['free_money'] = $db_institute->money - $institute['approved_cost'];
		
		$this->template->institute = $institute;
		$this->template->faculty = $db_institute->faculty;
	}
	
	
	
	public function renderDateRange() {
		if(!empty($this->dateRange)) {
			$date_range_data = array(
				'from' => $this->dateRange->from->format("d.m.Y"),
				'to' => $this->dateRange->to->format("d.m.Y")
			);
			
			$this->template->date_range_data = $date_range_data;
		}
	}
	
	
	
	public function createComponentDateRangeForm() {
		$form = new NAppForm();
		
		$form->addGroup();
		$form->addText('from', 'Zobrazovať projekty od')
				->addRule(NForm::FILLED, 'Vyplňte od akého dátumu sa majú projekty zobrazovať.')
				->getControlPrototype()
					->class('datepicker');
		$form->addText('to', 'Zobrazovať projekty do')
				->addRule(NForm::FILLED, 'Vyplňte do akého dátumu sa majú projekty zobrazovať.')
				->getControlPrototype()
					->class('datepicker');
		
		$form->setCurrentGroup(NULL);
	
		$form->addSubmit('process', 'Nastav')
				->getControlPrototype()
				->class('design');
		$form->addSubmit('set_default', 'Zobraz všekto')
				->setValidationScope(NULL)
				->getControlPrototype()
				->class('design');
		$form->addSubmit('back', 'Naspäť')
				->setValidationScope(NULL)
				->getControlPrototype()
				->class('design');
		
		$form->onSuccess[] = callback($this, 'dateRangeFormSubmit');
		
		return $form;
	}
	
	
	public function dateRangeFormSubmit($form) {
		$values = $form->values;
		
		$session = $this->context->session;
		$dateRange = $session->getSection('dateRange');
			
		if($form['process']->isSubmittedBy()) {			

			$dateRange->from = new DateTime($values['from']);
			$dateRange->to = new DateTime($values['to']);

			$dateRange->setExpiration(0);
		} elseif ($form['set_default']->isSubmittedBy()) {
			foreach($dateRange as $key => $value) {
				unset($dateRange[$key]);
			}
		}
		
		$this->redirect('default');
	}
	
	
	
	public function createComponentDataGrid() {

		if(empty($this->dateRange)) {
			$source = $this->db->table('project')->where('project_institute:institute_id', $this->institute_id);
		} else {
			$source = $this->db->table('project')
					->where('project_institute:institute_id', $this->institute_id)
					->where('project.start >= ?', $this->dateRange->from)
					->where('project.end <= ?', $this->dateRange->to);
		}

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
