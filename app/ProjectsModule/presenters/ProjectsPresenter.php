<?php

/**
 * Project presenter.
 *
 * @author     Samuel Kelemen
 */
class Projects_ProjectsPresenter extends Projects_BasePresenter
{
	/** @var database object */
	public $project;

	/** @var int		User id to show projects */
	public $user_id;
	
	
	
	
	/**
	 * Startup function
	 * 
	 * Unset project session data
	 */
	public function startup() {
		parent::startup();
		
		$this->unsetProjectsSession();
	}
	
	
		
	/**
	 * Default render function
	 * 
	 * Render all projects
	 */
	public function renderDefault() {
				
	} 
	
	
	
	
	/**
	 * MyProjects render function
	 * 
	 * Render projects of actualy loged user
	 */
	public function renderMyProjects() {
		$this->user_id = $this->getUser()->getIdentity()->getId();
	}

	

	
	/**
	 * Function to prerender edit project layout
	 * 
	 * @param int $id	ID of edited project
	 */
	public function actionEdit($id) {
		$this->project = $this->db->table('project')->where('id', $id)->fetch();
		
		//bad project id ? throw 404 exception
		if(!$this->project) {
			throw new NBadRequestException;
		}
		
		$form = $this['editForm'];

		//set default values of project
		$form->setDefaults($this->project);
		
		//send values to template
		$this->template->total_values = $this->calculateTotalValues();		
		$this->template->project = $this->project;				
		$this->template->form = $this['editForm'];
		
		$participate_faculties = array();
		
		foreach($this->project->related('project_institute') as $project_institute) {
			if(!isSet($participate_faculties[$project_institute->institute->faculty->id])) {
				$participate_faculties[$project_institute->institute->faculty->id] = $project_institute->institute->faculty;
			}
		}

		$this->template->participate_faculties = $participate_faculties;
	}
	
	
	
		
	/**
	 * Add project form
	 * 
	 * @return NAppForm 
	 */
	public function createComponentAddForm() {
		$form = new NAppForm();
		
		$form->addGroup();
		$form->addText('name', 'Názov projektu')
				->addRule(NForm::FILLED, 'Musíte zadať názov projektu.')
				->getControlPrototype()
					->class('w350');;
		$form->addTextArea('description', 'Popis projektu', '55','5')
				->addRule(NForm::FILLED, 'Musíte zadať popis projektu.');
		
		$form->setCurrentGroup(NULL);
		$form->addSubmit('back', 'Návrat')
				->setValidationScope(NULL)
				->getControlPrototype()
					->class('design');
		$form->addSubmit('process', 'Uložiť projekt')
				->getControlPrototype()
					->class('design');
		
		$form->addSubmit('add_institute', 'Pridať ústav')->setDisabled()
				->getControlPrototype()
					->class('design');
		
		$form->onSuccess[] = callback($this, 'addFormSubmitted');
		
		return $form;
	}
	
	
	
	
	/**
	 * Add project form - submit function
	 * 
	 * @param NAppForm $form 
	 */
	public function addFormSubmitted($form) {
		try {
			//submit by save or back ?
			if($form['process']->isSubmittedBy()) {
				$values = $form->values;

				$values['user_id'] = $this->getUser()->getIdentity()->getId(); //@TODO actual loged user id - ldap

				$this->db->table('project')->insert($values);
				
				$project_id = $this->db->lastInsertId();

				$this->flashMessage('Projekt bol úspešne vytvorený.', 'ok');
				$this->redirect('edit', $project_id);
			} else {
				$this->redirect('default');
			}
		} catch (PDOException $e) {
			$this->flashMessage('Projekt sa nepodarilo vytvoriť.', 'error');
			$this->redirect('default');
		}
	}
				
	
	
	
	/**
	 * Edit project form
	 * 
	 * @return NAppForm 
	 */
	public function createComponentEditForm() {
		$form = new NAppForm();
		
		$form->addGroup();
		$form->addText('name', 'Názov projektu')
				->addRule(NForm::FILLED, 'Musíte zadať názov projektu.')
				->getControlPrototype()
					->class('w350');
		$form->addTextArea('description', 'Popis projektu', '55','5')
				->addRule(NForm::FILLED, 'Musíte zadať popis projektu.');
		
		//foreach related institute render a control buttons for this institute
		foreach($this->project->related('project_institute') as $project_institute) {
			$form->addSubmit("edit_$project_institute->id", ' ')
					->getControlPrototype()->class('more');
			$form->addSubmit("finance_detail_$project_institute->id", ' ')
					->getControlPrototype()->class('finance-detail');
			$form->addSubmit("delete_$project_institute->id", ' ')
					->getControlPrototype()->class('delete');
		}
		
		$form->setCurrentGroup(NULL);
		$form->addSubmit('back', 'Návrat')
				->setValidationScope(NULL)
				->getControlPrototype()
					->class('design');
		$form->addSubmit('process', 'Uložiť projekt')
				->getControlPrototype()
					->class('design');
		
		$form->addSubmit('add_institute', 'Pridať ústav')
				->getControlPrototype()
					->class('design');
		
		$form->onSuccess[] = callback($this, 'editFormSubmitted');
		
		return $form;
	}
	
	
	
	
	/**
	 * Edit project form - submit function
	 *  
	 * @param NAppForm $form 
	 */
	public function editFormSubmitted($form) {
		try {
			$values = $form->values;

			//projects submit buttons handlers
			foreach($this->project->related('project_institute') as $project_institute) {

				//handlers for edit buttons
				if($form["edit_$project_institute->id"]->isSubmittedBy()) {
					$this->redirect('Institutes:edit', $project_institute->id);
				}
				
				//handlers for money buttons
				if($form["finance_detail_$project_institute->id"]->isSubmittedBy()) {
					$this->redirect('Finances:editFinances', $project_institute->id);
				}

				//handlers for delete buttons
				if($form["delete_$project_institute->id"]->isSubmittedBy()) {
					$this->redirect('Institutes:delete', $project_institute->id);
				}
			}

			//form submit by save, back or add_institute ?
			if($form['process']->isSubmittedBy()) {
				$values['user_id'] = '1'; //@TODO actual loged user id - ldap

				$this->db->table('project')->where('id', $this->project->id)->update($values);

				$this->flashMessage('Projekt bol úspešne upravený.', 'ok');
				$this->redirect('default');
			} else if ($form['add_institute']->isSubmittedBy()) {
				$this->redirect('Institutes:add', $this->project->id);
			} else {
				$this->redirect('default');
			}
		} catch (PDOException $e) {
			$this->flashMessage('Projekt sa nepodarilo upraviť.', 'error');
			$this->redirect('default');
		}
	}


	
	
	/** 
	 * DataGrid render function - show all projects
	 * Show projects with or without data range filter.
	 * All projects with or without data range filter show total values. Not only date range values.
	 * 
	 * @return DataGrid 
	 */
	public function createComponentDataGrid() {

		if(empty($this->dateRange)) {
			$source = $this->db->table('project');
		} else {
			$source = $this->db->table('project')
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
		
        $dg->addAction('edit', 'Uprav', 'Projects:edit', array('id'));

        $dg->addColumn('id', 'No.')->setIntFilter('project.id')->setStyle('width: 50px');
        $dg->addColumn('name', 'Názov projektu')->setTextFilter('project.name')->setStyle('text-align: left');
		$dg->addCustomColumn('cost', 'Fin. zdroje')->setIntFilter('project.cost')->setHtml(create_function('$row', '$helper =  new EmptyPrice(); return $helper->process($row->cost);'));                                                                 																										
		$dg->addCustomColumn('approved_cost', 'Schválené fin.zdroje')->setIntFilter('project.approved_cost')->setHtml(create_function('$row', '$helper =  new EmptyPrice(); return $helper->process($row->approved_cost);'));
		$dg->addCustomColumn('participation', 'Spoluúčasť')->setIntFilter('project.participation')->setHtml(create_function('$row', '$helper =  new EmptyPrice(); return $helper->process($row->participation);'));
		$dg->addCustomColumn('approved_participation', 'Schválená spoluúčasť')->setIntFilter('project.approved_participation')->setHtml(create_function('$row', '$helper =  new EmptyPrice(); return $helper->process($row->approved_participation);'));
		$dg->addCustomColumn('hr', 'Ľudské zdroje')->setIntFilter('project.hr')->setHtml(create_function('$row', '$helper =  new EmptyNumber(); return $helper->process($row->hr);'));																
		$dg->addCustomColumn('approved_hr', 'Schválené ľudské zdroje')->setIntFilter('project.approved_hr')->setHtml(create_function('$row', '$helper =  new EmptyNumber(); return $helper->process($row->approved_hr);'));
		return $dg;
	}	
	
	
	
	
	/**
	 * DataGrid render function - show only logged user projects
	 * Show projects with or without data range filter.
	 * All projects with or without data range filter show total values. Not only date range values.
	 * 
	 * @return DataGrid 
	 */
	public function createComponentDataGridMyProjects() {
		
		if(empty($this->dateRange)) {
			$source = $this->db->table('project')
					->where('project.user_id', $this->user->getId());
		} else {
			$source = $this->db->table('project')
					->where('project.user_id', $this->user->getId())
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
		
        $dg->addAction('edit', 'Uprav', 'Projects:edit', array('id'));

        $dg->addColumn('id', 'ID')->setIntFilter('project.id')->setStyle('width: 50px');
        $dg->addColumn('name', 'Názov projektu')->setTextFilter('project.name')->setStyle('text-align: left');
		$dg->addCustomColumn('cost', 'Fin. zdroje')->setIntFilter('project.cost')->setHtml(create_function('$row', '$helper =  new EmptyPrice(); return $helper->process($row->cost);'));                                                                 																										
		$dg->addCustomColumn('approved_cost', 'Schválené fin.zdroje')->setIntFilter('project.approved_cost')->setHtml(create_function('$row', '$helper =  new EmptyPrice(); return $helper->process($row->approved_cost);'));
		$dg->addCustomColumn('participation', 'Spoluúčasť')->setIntFilter('project.participation')->setHtml(create_function('$row', '$helper =  new EmptyPrice(); return $helper->process($row->participation);'));
		$dg->addCustomColumn('approved_participation', 'Schválená spoluúčasť')->setIntFilter('project.approved_participation')->setHtml(create_function('$row', '$helper =  new EmptyPrice(); return $helper->process($row->approved_participation);'));
        $dg->addCustomColumn('hr', 'Ľudské zdroje')->setIntFilter('project.hr')->setHtml(create_function('$row', '$helper =  new EmptyNumber(); return $helper->process($row->hr);'));																
		$dg->addCustomColumn('approved_hr', 'Schválené ľudské zdroje')->setIntFilter('project.approved_hr')->setHtml(create_function('$row', '$helper =  new EmptyNumber(); return $helper->process($row->approved_hr);'));
		return $dg;
	}	
	
	
	
	
	/**
	 * Function to calculate total values of project.
	 * Total values - all states
	 * Approved values - projects with statuses defined in aStates variable
	 * 
	 * @return array
	 */
	public function calculateTotalValues() {
		$totals = array();

		foreach($this->project->related('project_institute') as $project_institute) {	
			
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
