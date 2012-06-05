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
	
	/** @var int		Institute id */
	public $institute_id;
	
	
	
	
	/**
	 * Default render function.
	 */
	public function renderDefault()	{		
		$faculties = $this->db->table('faculty');
		$result = null;
		$school_total_money = 0;
		
		//if date range is not set
		if(empty($this->dateRange)) {		
			//iterate over all faculties
			foreach($faculties as $faculty) {
				$faculty_money = 0;
			
				//db query to get data sumary of every faculty
				$result[$faculty->id] = $this->db->table('project_institute_date')
						->where('project_institute.institute.faculty.id', $faculty->id)
						->select('
								count(DISTINCT project_institute.project_id) AS project_count,
								sum(project_institute_date.participation) AS total_participation,
								sum(project_institute_date.hr) AS total_hr,
								sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute_date.participation, 0)) AS approved_participation,
								sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute_date.hr, 0)) AS approved_hr
						')->fetch();
				
				//db query to get NTableSelection of total cost of all projects
				$select = $this->db->table('project_institute_date')
						->where('project_institute.institute.faculty.id', $faculty->id)
						->select('
								DISTINCT project_institute.project_id AS project_id,
								project_institute.cost AS cost
						');
				
				$result[$faculty->id]['total_cost'] = 0;
				
				//count total cost of all projects of faculty
				foreach($select as $s) {
					$result[$faculty->id]['total_cost'] += $s->cost; 
				}
			
				//calculate facaulty money
				foreach ($faculty->related('institute') as $institute)
					$faculty_money += $institute->money;
            
				//get other data of faculty
				$result[$faculty->id]['free_money'] = $faculty_money - $result[$faculty->id]['approved_participation'];  
				$result[$faculty->id]['name'] = $faculty->name;
				$result[$faculty->id]['acronym'] = $faculty->acronym;
				$school_total_money += $faculty_money;
			}	
		} else { //if we have set date range
			foreach($faculties as $faculty) {
				$faculty_money = 0;		
				
				//get sumary data of faculty with date range aplication
				//part of project have to be between date range - project_institute_date
				$result[$faculty->id] = $this->db->table('project_institute_date')
						->where('project_institute.institute.faculty.id', $faculty->id)
						->where('project_institute_date.start >= ?', $this->dateRange->from)
						->where('project_institute_date.end <= ?', $this->dateRange->to)->select('
								count(DISTINCT project_institute.project_id) AS project_count,
								sum(project_institute_date.participation) AS total_participation,
								sum(project_institute_date.hr) AS total_hr,
								sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute_date.participation, 0)) AS approved_participation,
								sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute_date.hr, 0)) AS approved_hr
						')->fetch();
				
				//db query to get NTableSelection of total cost of all projects - with aplication of date range
				$select = $this->db->table('project_institute_date')
						->where('project_institute.institute.faculty.id', $faculty->id)
						->where('project_institute_date.start >= ?', $this->dateRange->from)
						->where('project_institute_date.end <= ?', $this->dateRange->to)->select('
								DISTINCT project_institute.project_id AS project_id,
								project_institute.cost AS cost
						');
				
				$result[$faculty->id]['total_cost'] = 0;
				
				//count total cost of all faculty projects
				foreach($select as $s) {
					$result[$faculty->id]['total_cost'] += $s->cost; 
				}
				
				//calculate faculty money
				foreach ($faculty->related('institute') as $institute)
					$faculty_money += $institute->money;  
				
				//get other faculty data
				$result[$faculty->id]['free_money'] = $faculty_money - $result[$faculty->id]['approved_participation'];     
				$result[$faculty->id]['name'] = $faculty->name;
				$result[$faculty->id]['acronym'] = $faculty->acronym;
				$school_total_money += $faculty_money;
			}	
		}
		
		//get total school data
		if(empty($this->dateRange)) {
			$db_total_data = $this->db->table('project_institute_date');
		} else {
			$db_total_data = $this->db->table('project_institute_date')
										->where('project_institute_date.start >= ?', $this->dateRange->from)
										->where('project_institute_date.end <= ?', $this->dateRange->to);
		}
		
		//select projects count of school - with or without date range - previous section
		$total_data = $db_total_data->select('
								count(DISTINCT project_institute.project_id) AS project_count
						')->fetch();
		
		//initialization of values
		$total_data['total_cost'] = 0;
		$total_data['total_participation'] = 0;
		$total_data['total_hr'] = 0;
		$total_data['approved_participation'] = 0;
		$total_data['approved_hr'] = 0;
		
		//caluclate values
		foreach($result as $faculty_result) {
			$total_data['total_cost'] += $faculty_result['total_cost'];
			$total_data['total_participation'] += $faculty_result['total_participation'];
			$total_data['total_hr'] += $faculty_result['total_hr'];
			$total_data['approved_participation'] += $faculty_result['approved_participation'];
			$total_data['approved_hr'] += $faculty_result['approved_hr'];
		}		
		
		//calculate free school money
		$total_data['free_money'] = $school_total_money - $total_data['approved_participation'];

		//set template variables
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
    
		//if date range is not set
		if(empty($this->dateRange)) {
			//iterate over all institutes of faculty
			foreach($db_faculty->related('institute') as $institute) {
				
				//db query to get sumary data of institute
				$institutes[$institute->id] = $this->db->table('project_institute_date')
						->where('project_institute.institute.id', $institute->id)
						->select('
							count(DISTINCT project_institute.project_id) AS project_count,
							sum(project_institute_date.participation) AS total_participation,
							sum(project_institute_date.hr) AS total_hr,
							sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute_date.participation, 0)) AS approved_participation,
							sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute_date.hr, 0)) AS approved_hr
						')->fetch();
				
				//db query to get NTableSelection of total cost of all projects 
				$select = $this->db->table('project_institute_date')
						->where('project_institute.institute.id', $institute->id)
						->select('
								DISTINCT project_institute.project_id AS project_id,
								project_institute.cost AS cost
						');
				
				$institutes[$institute->id]['total_cost'] = 0;
				
				//count total cost of all institute projects
				foreach($select as $s) {
					$institutes[$institute->id]['total_cost'] += $s->cost; 
				}
				
				//get other data of institute
				$institutes[$institute->id]['name'] = $institute->name;
				$institutes[$institute->id]['acronym'] = $institute->acronym;
				$institutes[$institute->id]['free_money'] = $institute->money - $institutes[$institute->id]['approved_participation'];
			}		
		} else { //if date range is set
			//iterate over all institutes of faculty
			foreach($db_faculty->related('institute') as $institute) {	
				
				//db query to get sumary data of institute
				$institutes[$institute->id] = $this->db->table('project_institute_date')
						->where('project_institute.institute.id', $institute->id)
						->where('project_institute_date.start >= ?', $this->dateRange->from)
						->where('project_institute_date.end <= ?', $this->dateRange->to)
						->select('
							count(DISTINCT project_institute.project_id) AS project_count,
							sum(project_institute_date.participation) AS total_participation,
							sum(project_institute_date.hr) AS total_hr,
							sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute_date.participation, 0)) AS approved_participation,
							sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute_date.hr, 0)) AS approved_hr
						')->fetch();

				//db query to get NTableSelection of total cost of all projects - with aplication of date range
				$select = $this->db->table('project_institute_date')
						->where('project_institute.institute.id', $institute->id)
						->where('project_institute_date.start >= ?', $this->dateRange->from)
						->where('project_institute_date.end <= ?', $this->dateRange->to)
						->select('
								DISTINCT project_institute.project_id AS project_id,
								project_institute.cost AS cost
						');
				
				$institutes[$institute->id]['total_cost'] = 0;
				
				//count total cost of all faculty projects
				foreach($select as $s) {
					$institutes[$institute->id]['total_cost'] += $s->cost; 
				}

				//get other data of institute
				$institutes[$institute->id]['name'] = $institute->name;
				$institutes[$institute->id]['acronym'] = $institute->acronym;
				$institutes[$institute->id]['free_money'] = $institute->money - $institutes[$institute->id]['approved_participation'];
			}
		}
		
		//calculate total data of faculty
		if(empty($this->dateRange)) {
			$faculty_date = $this->db->table('project_institute_date')
										->where('project_institute.institute.faculty.id', $db_faculty->id);
		} else {
			$faculty_date = $this->db->table('project_institute_date')
										->where('project_institute.institute.faculty.id', $db_faculty->id)
										->where('project_institute_date.start >= ?', $this->dateRange->from)
										->where('project_institute_date.end <= ?', $this->dateRange->to);
		}
		
		//db query to get projects count of faculty
		$faculty = $faculty_date->select('
				count(DISTINCT project_institute.project_id) AS project_count
        ')->fetch();
		
		//initialize values
		$faculty['total_cost'] = 0;
		$faculty['total_participation'] = 0;
		$faculty['total_hr'] = 0;
		$faculty['approved_participation'] = 0;
		$faculty['approved_hr'] = 0;
		
		//calculate values
		foreach($institutes as $institute_result) {
			$faculty['total_cost'] += $institute_result['total_cost'];
			$faculty['total_participation'] += $institute_result['total_participation'];
			$faculty['total_hr'] += $institute_result['total_hr'];
			$faculty['approved_participation'] += $institute_result['approved_participation'];
			$faculty['approved_hr'] += $institute_result['approved_hr'];
		}		
		
		//set other data of faculty
		$faculty['name'] = $db_faculty->name;
		$faculty['acronym'] = $db_faculty->acronym;
		$faculty['id'] = $db_faculty->id;
		
		//caluclate faculty money
		foreach ($db_faculty->related('institute') as $institute) {
			$faculty_money += $institute->money;
		}
		
		//caluclate free faculty money
		$faculty['free_money'] = $faculty_money - $faculty['approved_participation'];

		//set template variables
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
		
		//db query to get insitute data with or without date range
		if(empty($this->dateRange)) {
			$institute_date = $this->db->table('project_institute_date')
											->where('project_institute.institute.id', $db_institute->id);
		} else {
			$institute_date = $this->db->table('project_institute_date')
											->where('project_institute.institute.id', $db_institute->id)
											->where('project_institute_date.start >= ?', $this->dateRange->from)
											->where('project_institute_date.end <= ?', $this->dateRange->to);
		}
			
		//db query to get institute sumary data
		$institute = $institute_date->select('
				count(DISTINCT project_institute.project_id) AS project_count,
				sum(project_institute_date.hr) AS total_hr,
				sum(project_institute_date.participation) AS total_participation,
				sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute_date.hr, 0)) AS approved_hr,
				sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute_date.participation, 0)) AS approved_participation,
				min(project_institute.start) AS start,
				max(project_institute.end) AS end,
				min(CASE WHEN project_institute.state_id IN (' . implode(',', $this->aStates) . ') THEN project_institute.start ELSE NULL END) AS approved_start,
				max(CASE WHEN project_institute.state_id IN (' . implode(',', $this->aStates) . ') THEN project_institute.end ELSE NULL END) AS approved_end
        ')->fetch();
		
		//db query to get NTableSelection of total cost of all projects - with or without date range
		if(empty($this->dateRange)) {
			$select = $this->db->table('project_institute_date')
									->where('project_institute.institute.id', $db_institute->id);
		} else {
			$select = $this->db->table('project_institute_date')
									->where('project_institute.institute.id', $db_institute->id)
									->where('project_institute_date.start >= ?', $this->dateRange->from)
									->where('project_institute_date.end <= ?', $this->dateRange->to);
		}
		
		//continute of db query
		$select->select('
					DISTINCT project_institute.project_id AS project_id,
					IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute.cost, 0) AS approved_cost,
					project_institute.cost AS cost
				');

		//initialize of values
		$institute['total_cost'] = 0;
		$institute['approved_cost'] = 0;

		//count total cost of all institute projects
		foreach($select as $s) {
			$institute['total_cost'] += $s->cost; 
			$institute['approved_cost'] += $s->approved_cost;
		}		
		
		//get other institute data
		$institute['id'] = $db_institute->id;
		$institute['name'] = $db_institute->name;
		$institute['acronym'] = $db_institute->acronym;
		$institute['free_money'] = $db_institute->money - $institute['approved_participation'];
		
		//set template variables
		$this->template->institute = $institute;
		$this->template->faculty = $db_institute->faculty;
	}
	
	
	
	
	/**
	 * Rendeer date change function
	 */
	public function renderDateRange() {
		if(!empty($this->dateRange)) {
			$date_range_data = array(
				'from' => $this->dateRange->from->format("d.m.Y"),
				'to' => $this->dateRange->to->format("d.m.Y")
			);
			
			$this->template->date_range_data = $date_range_data;
		}
	}
	
	
	
	
	/**
	 * Form to change date range
	 * @return NAppForm 
	 */
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
	
	
	
	
	/**
	 * Submit function form date range form
	 * @param type $form 
	 */
	public function dateRangeFormSubmit($form) {
		$values = $form->values;
		
		//set session variable
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
	
	
	
	
	/**
	 * DataGrid of institute projects
	 * Show projects with or without data range filter.
	 * All projects with or without data range filter show total values. Not only date range values.
	 * @return DataGrid 
	 */
	public function createComponentDataGrid() {

		if(empty($this->dateRange)) {
			$source = $this->db->table('project')
					->where('project_institute:institute_id', $this->institute_id);
		} else {
			$source = $this->db->table('project')
					->where('project_institute:institute_id', $this->institute_id)
					->where('project_institute:project_institute_date:start >= ?', $this->dateRange->from)
					->where('project_institute:project_institute_date:end <= ?', $this->dateRange->to)
					->select('
						DISTINCT project.id,
						project.name,
						project.cost,
						project.approved_cost,
						project.participation,
						project.approved_participation,
						project.hr,
						project.approved_hr
					');
		}

		if($source->count('*') <= 0) {
			
			$dg = new DataGrid();
			$dg->setDataSource($source);

			$dg->template->empty = true;
			return $dg;
		}
		
        $dg = new DataGrid();
        $dg->setDataSource($source);
		
        $dg->addAction('edit', 'Uprav', ':Projects:Projects:edit', array('id'));

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
