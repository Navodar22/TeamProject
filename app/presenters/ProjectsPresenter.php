<?php

/**
 * Project presenter.
 *
 * @author     Samuel Kelemen
 */
class ProjectsPresenter extends BaseLPresenter
{
	/** @var database object */
	public $project_institute;
	
	/** @var database object */
	public $project;
	
	/** @var array		Free faculties/insitutes for defined project - one institute can be only once in one project */
	public $free_institutes;
	
	/** @var array		Allowed states of project */
	public $states;
	
	
	public $user_id;
	
	public $button = FALSE;
	
	
	
	
	/**
	 * Startup function
	 */
	public function startup() {
		parent::startup();
		
		$this->getStates();
	}
	
	
	
	
	/**
	 * Default render function
	 * 
	 * Render all actualy logged user
	 */
	public function renderDefault() {
				
	} 
	
	
	
	
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
	 * Add institute function
	 * 
	 * @param int $id		ID of project -> where connect the institute
	 */
	public function actionAddInstitute($id) {
		$this->project = $this->db->table('project')->where('id', $id)->fetch();
		
		if(!$this->project) {
			throw new NBadRequestException;
		}
		
		//fetch array of free faculties/institutes for actual project
		$this->getFreeInstitutes($this->project->id);
		
		if(empty($this->free_institutes)) {
			$this->flashMessage('Neexistuje ústav ktorý by sa mohol pridať ku projektu.', 'error');
			$this->redirect('edit', $this->project->id);
		}
		
		$this->template->project = $this->project;
	}
	
	
	
	
	/**
	 * Edit institute function 
	 * 
	 * @param int $id		ID of project_insitute record to edit 
	 */
	public function actionEditInstitute($id) {
		$this->project_institute = $this->db->table('project_institute')->where('id', $id)->fetch();
				
		if(!$this->project_institute) {
			throw new NBadRequestException;
		}		
		
		//set default values for project dates - make it in layout with jQuery
		$project_dates = array(
			'start' => date("d.m.Y", strtotime($this->project_institute->start)),
			'end' => date("d.m.Y", strtotime($this->project_institute->end))
		);		
		$this->template->project_dates = $project_dates;
		
		//set other default values of institute to edit institute form
		$this['editInstituteForm']->setDefaults($this->project_institute);
		
		$this->template->institute = $this->project_institute->institute;
		$this->template->project = $this->project_institute->project;
	}
	
	
	
	
	/**
	 * Delete institute function
	 * 
	 * @param int $id		ID of project_institute record 
	 */
	public function actionDeleteInstitute($id) {
		$project_institute = $this->db->table('project_institute')->where('id', $id);
		
		if(!$project_institute) {
			throw new NBadRequestException;
		}	
		
		try {
			//store project_id before delete record
			$this->db->beginTransaction();
			$project_id = $project_institute->fetch()->project->id;
			$project_institute->delete();
			$this->calculateProjectData($project_id);
			$this->db->commit();

			//set message and redirect
			$this->flashMessage('Ústav bol úspešne odstránený z participácie na projekte.', 'ok');
			$this->redirect('edit', $project_id);
		} catch (PDOException $e) {			
			$this->db->rollback();
			$this->flashMessage('Odstránenie ústavu z projektu nebolo úspešné.', 'error');
			$this->redirect('edit', $project_id);
		}
	} 
	
	
	
	
	public function actionAddFinanceDetail($id) {
		$this->project_institute = $this->db->table('project_institute')->where('id', $id)->fetch();
		
		if(!$this->project_institute) {
			throw new NBadRequestException();
		}
		
		$this->template->project = $this->project_institute->project;
		$this->template->institute = $this->project_institute->institute;
	}
	
	
	
	
	public function actionEditFinanceDetail($id) {
		$this->project_institute = $this->db->table('project_institute')->where('id', $id)->fetch();
		
		if(!$this->project_institute) {
			throw new NBadRequestException();
		}
		
		$this->template->project = $this->project_institute->project;
		$this->template->institute = $this->project_institute->institute;
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
		$form->addSubmit('process', 'Uložiť projekt')
				->getControlPrototype()
					->class('design');
		$form->addSubmit('back', 'Návrat')
				->setValidationScope(NULL)
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

				$values['user_id'] = '1'; //@TODO actual loged user id - ldap

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
		$form->addSubmit('process', 'Uložiť projekt')
				->getControlPrototype()
					->class('design');
		$form->addSubmit('back', 'Návrat')
				->setValidationScope(NULL)
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
					$this->redirect('editInstitute', $project_institute->id);
				}
				
				//handlers for money buttons
				if($form["finance_detail_$project_institute->id"]->isSubmittedBy()) {
					$this->redirect('editFinanceDetail', $project_institute->id);
				}

				//handlers for delete buttons
				if($form["delete_$project_institute->id"]->isSubmittedBy()) {
					$this->redirect('deleteInstitute', $project_institute->id);
				}
			}

			//form submit by save, back or add_institute ?
			if($form['process']->isSubmittedBy()) {
				$values['user_id'] = '1'; //@TODO actual loged user id - ldap

				$this->db->table('project')->where('id', $this->project->id)->update($values);

				$this->flashMessage('Projekt bol úspešne upravený.', 'ok');
				$this->redirect('default');
			} else if ($form['add_institute']->isSubmittedBy()) {
				$this->redirect('addInstitute', $this->project->id);
			} else {
				$this->redirect('default');
			}
		} catch (PDOException $e) {
			$this->flashMessage('Projekt sa nepodarilo upraviť.', 'error');
			$this->redirect('default');
		}
	}
	
	
	
	
	/**
	 * Add institute form
	 * 
	 * @return NAppForm 
	 */
	public function createComponentAddInstituteForm() {	
		$form = new NAppForm();

		$form->addGroup();
		$form->addSelect('state_id', 'Stav projektu', $this->states) //@TODO make global function to get states by role
				->getControlPrototype()->class('w250');
		$form->addSelect('institute_id', 'Ústav', $this->free_institutes)
				->getControlPrototype()->class('w250');
		
		$form->addText('cost', 'Finančné zdroje')
				->addRule(NForm::FILLED, 'Musíte zadať cenu projektu')
				->addRule(NForm::FLOAT, 'Finančné zdroje musia byť číslo')
				->addRule(NForm::RANGE, 'Finančné zdroje musia byť kladné číslo', array(0, 9999999999));
		$form->addText('participation', 'Spoluúčasť na projekte')
				->addRule(NForm::FILLED, 'Musíte zadať spoluúčasť na projekte')
				->addRule(NForm::FLOAT, 'Spoluúčasť na projekte musí byť číslo')
				->addRule(NForm::RANGE, 'Spoluúčasť na projekte musí byť kladné číslo', array(0, 9999999999));
		$form->addText('hr', 'Ľudské zdroje')
				->addRule(NForm::FILLED, 'Musíte zadať ľudské zdroje projektu')
				->addRule(NForm::INTEGER, 'Ľudské zdroje projektu musí byť číslo')
				->addRule(NForm::RANGE, 'Ľudské zdroje zapojené do projektu musia byť kladné číslo', array(0, 9999999999));
		$form->addText('fonds', 'Fondy')
				->getControlPrototype()->class('w250');
		$form->addText('start', 'Začiatok projektu')
				->addRule(NForm::FILLED, 'Musíte vyplniť začiatok projektu.')
				->getControlPrototype()->class('datepicker');
		$form->addText('end', 'Koniec projektu')
				->addRUle(NForm::FILLED, 'Musíte vyplniť koniec projektu.')
				->getControlPrototype()->class('datepicker');
		
		$form->setCurrentGroup(NULL);
		$form->addSubmit('process', 'Pridaj ústav')
				->getControlPrototype()
					->class('design');
		$form->addSubmit('back', 'Návrat')
				->setValidationScope(NULL)
				->getControlPrototype()
					->class('design');
		
		$form->onSuccess[] = callback($this, 'addInstituteFormSubmitted');
		
		return $form;
	}
	
	
	
	
	/**
	 * Add institute form - submit function
	 * 
	 * @param NAppForm $form 
	 */
	public function addInstituteFormSubmitted($form) {	
		try {
			//form submitted by save or back ?
			if($form['process']->isSubmittedBy()) {
				$values = $form->values;
				$values['project_id'] = $this->project->id;

				//help value to validate dates
				$error = false;

				//make date values as objects
				$values['start'] = new DateTime($values['start']);
				$values['end'] = new DateTime($values['end']);
				
				//check financial resources
				//if state is one of allowed state
				if(in_array($values['state_id'], $this->aStates)) {
					$institute = $this->db->table('institute')->where('id', $values['institute_id'])->fetch();
					
					//in free money dont sum actualy open project
					$project_institute = $this->db->table('project_institute')
													->where('institute_id', $values['institute_id'])
													->where('id <> ?', $values['institute_id'])
													->select('sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute.participation, 0)) AS approved_participation')
													->fetch();
					
					//calculate only width approved_participation not approved_cost - insitute need only participation money to have
					$free_money = $institute['money'] - $project_institute['approved_participation'];

					//if we havent enough money show error and actualy free money
					if($free_money < $values['participation']) {
						$this->flashMessage('Inštitút nemá dostatok volných finančných zdrojov. ( ' . $free_money . ' € )', 'error');
						$error = true;
					}
				}

				//check dates
				if($values['start'] >= $values['end']) {
					$this->flashMessage('Projekt nemôže mať ukončenie pred svojím začiatkom.', 'error');
					$error = true;
				} 
				
				//chect participation and cost value -> participation must by only part of total cost of project
				if($values['cost'] < $values['participation']) {
					$this->flashMessage('Participácia na projekte nemôže byť väčšia ako jeho celková cena.', 'error');
					$error = true;
				}
				
				//is form error free ?
				if(!$error) {
					$this->db->beginTransaction();
					$new_project_institute = $this->db->table('project_institute')->insert($values);
					$this->calculateProjectData($this->project->id);
					$this->db->commit();
					
					$this->flashMessage('Ústav bol pridaný k projektu.', 'ok');
					$this->redirect('addFinanceDetail', $new_project_institute->id);	
				}
			} else { //if submitted by back only redirect
				$this->redirect('edit', $this->project->id);
			}
		} catch (PDOException $e) {
			$this->db->rollback();
			$this->flashMessage('Ústav sa nepodarilo pridať k projektu.', 'error');
			$this->redirect('edit', $this->project->id);
		}
	}
	
	
	
	
	/**
	 * Edit institute form
	 * 
	 * @return NAppForm 
	 */
	public function createComponentEditInstituteForm() {	
		$form = new NAppForm();

		$form->addGroup();
		$form->addSelect('state_id', 'Stav projektu', $this->states); //@TOTO prev todo
		
		if ($this->button) {
			$form->addText('test', 'test');
		}
		
		$form->addText('cost', 'Finančné zdroje')
				->addRule(NForm::FILLED, 'Musíte zadať cenu projektu')
				->addRule(NForm::FLOAT, 'Finančné zdroje musia byť číslo')
				->addRule(NForm::RANGE, 'Finančné zdroje musia byť kladné číslo', array(0, 9999999999));
		$form->addText('participation', 'Spoluúčasť na projekte')
				->addRule(NForm::FILLED, 'Musíte zadať spoluúčasť na projekte')
				->addRule(NForm::FLOAT, 'Spoluúčasť na projekte musí byť číslo')
				->addRule(NForm::RANGE, 'Spoluúčasť na projekte musí byť kladné číslo', array(0, 9999999999));
		$form->addText('hr', 'Ľudské zdroje')
				->addRule(NForm::FILLED, 'Musíte zadať ľudské zdroje projektu')
				->addRule(NForm::INTEGER, 'Ľudské zdroje projektu musí byť číslo')
				->addRule(NForm::RANGE, 'Ľudské zdroje zapojené do projektu musia byť kladné číslo', array(0, 9999999999));
		$form->addText('fonds', 'Fondy')
				->getControlPrototype()->class('w250');
		$form->addText('start', 'Začiatok projektu')
				->addRule(NForm::FILLED, 'Musíte vyplniť začiatok projektu.')
				->getControlPrototype()->class('datepicker');
		$form->addText('end', 'Koniec projektu')
				->addRUle(NForm::FILLED, 'Musíte vyplniť koniec projektu.')
				->getControlPrototype()->class('datepicker');
		
		$form->setCurrentGroup(NULL);
		$form->addSubmit('process', 'Ulož')
				->getControlPrototype()
					->class('design');
		
		$form->addSubmit('back', 'Návrat')
				->setValidationScope(NULL)
				->getControlPrototype()
					->class('design');
		
		$form->onSuccess[] = callback($this, 'editInstituteFormSubmitted');
		
		return $form;
	}
	
	
	
	
	/**
	 * Edit project form - submit function
	 * 
	 * @param NAppForm $form 
	 */
	public function editInstituteFormSubmitted($form) {			
		try {
			//form submitted by save or back button ?
			if($form['process']->isSubmittedBy()) {
				$values = $form->values;

				//help variable for date validation
				$error = false;

				//set date variables to objects
				$values['start'] = new DateTime($values['start']);
				$values['end'] = new DateTime($values['end']);

				//check financial resources
				if(in_array($values['state_id'], $this->aStates)) {
					$institute = $this->db->table('institute')->where('id', $this->project_institute->institute_id)->fetch();
					$project_institute = $this->db->table('project_institute')
													->where('institute_id', $this->project_institute->institute_id)
													->where('id <> ?', $this->project_institute->id)
													->select('sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute.participation, 0)) AS approved_participation')
													->fetch();
					
					$free_money = $institute['money'] - $project_institute['approved_participation'];

					if($free_money < $values['participation']) {
						$this->flashMessage('Inštitút nemá dostatok volných finančných zdrojov. ( ' . $free_money . ' € )', 'error');
						$error = true;
					}
				}
				
				//check dates
				if($values['start'] >= $values['end']) {
					$this->flashMessage('Projekt nemôže mať ukončenie pred svojím začiatkom.', 'error');
					$error = true;
				} 
				
				//chect participation and cost value -> participation must by only part of total cost of project
				if($values['cost'] < $values['participation']) {
					$this->flashMessage('Participácia na projekte nemôže byť väčšia ako jeho celková cena.', 'error');
					$error = true;
				}

				//is form error free ?
				if(!$error) {
					$this->db->beginTransaction();
					$this->db->table('project_institute')->where('id', $this->project_institute->id)->update($values);
					$this->calculateProjectData($this->project_institute->project->id);
					$this->db->commit();
					
					$this->flashMessage('Dáta ústavu úspešne zmenené.', 'ok');
					$this->redirect('edit', $this->project_institute->project->id);	
				}

			} else {
				$this->redirect('edit', $this->project_institute->project->id);
			}
		} catch (PDOException $e) {
			$this->db->rollback();
			$this->flashMessage('Dáta ústavu sa nepodarilo zmeniť.', 'error');
			$this->redirect('edit', $this->project_institute->project->id);
		}
	}
	
	
	
	
	public function createComponentAddFinanceDetailForm() {	
		$form = new NAppForm();
	
		$startDate = new \DateTime($this->project_institute->start);
		$endDate = new \DateTime($this->project_institute->end);
		$startDateYear = $startDate->format('Y');
		$endDateYear = $endDate->format('Y');
		$diff = $endDateYear - $startDateYear;
		
		$year = (int)$startDateYear;
		$finance_years = array();
		
		while($diff >= 0) {
			array_push($finance_years, $year);
			$year++;
			$diff--;
		}
		
		foreach($finance_years as $finance_year) {
			$form->addGroup('Rok ' . (string)$finance_year);
			$form->addContainer($finance_year);
			$form[$finance_year]->addText('participation', 'Finančná spoluúčasť');
			$form[$finance_year]->addText('hr', 'Ľudské zdroje');
		}
		
		$form->setCurrentGroup(NULL);
		$form->addSubmit('process', 'Ulož')
				->getControlPrototype()
					->class('design');
		
		$form->onSuccess[] = callback($this, 'addFinanceDetailFormSubmitted');
		
		return $form;
	}
	
	
	
	
	public function createComponentEditFinanceDetailForm() {	
		$form = new NAppForm();
	
		$startDate = new \DateTime($this->project_institute->start);
		$endDate = new \DateTime($this->project_institute->end);
		$startDateYear = $startDate->format('Y');
		$endDateYear = $endDate->format('Y');
		$diff = $endDateYear - $startDateYear;
		
		$year = (int)$startDateYear;
		$finance_years = array();
		
		while($diff >= 0) {
			array_push($finance_years, $year);
			$year++;
			$diff--;
		}
		
		foreach($finance_years as $finance_year) {
			$form->addGroup('Rok ' . (string)$finance_year);
			$form->addContainer($finance_year);
			$form[$finance_year]->addText('participation', 'Finančná spoluúčasť');
			$form[$finance_year]->addText('hr', 'Ľudské zdroje');
		}
		
		$form->setCurrentGroup(NULL);
		$form->addSubmit('process', 'Ulož')
				->getControlPrototype()
					->class('design');
		
		$form->addSubmit('back', 'Návrat')
				->setValidationScope(NULL)
				->getControlPrototype()
					->class('design');
		
		$form->onSuccess[] = callback($this, 'editFinanceDetailFormSubmitted');
		
		return $form;
	}
	
	
	
	
	public function addFinanceDetailFormSubmitted($form) {
		$containers = $form->getValues();
		
		$firstYearDay = function($year) {
			return new \DateTime("1.1.$year 01:00:00");
		};
		
		$lastYearDay = function($year) {
			return new \DateTime("31.12.$year 01:00:00");
		};
		
		$startDateYear = $this->project_institute->start->format('Y');
		$endDateYear = $this->project_institute->end->format('Y');
		
		foreach($containers as $key => $container) {
			
			if($key == $startDateYear) {
				$data = array(
					'start' => $this->project_institute->start,
					'end' => $lastYearDay($key),
					'participation' => $container->participation,
					'hr' => $container->hr,
					'project_institute_id' => $this->project_institute->id
				);
			} elseif ($key == $endDateYear) {
				$data = array(
					'start' => $firstYearDay($key),
					'end' => $this->project_institute->end,
					'participation' => $container->participation,
					'hr' => $container->hr,
					'project_institute_id' => $this->project_institute->id
				);
			} else {				
				$data = array(
					'start' => $firstYearDay($key),
					'end' => $lastYearDay($key),
					'participation' => $container->participation,
					'hr' => $container->hr,
					'project_institute_id' => $this->project_institute->id
				);
			}			
			$this->db->table('project_institute_date')->insert($data);
		}
		
		$this->redirect('edit', $this->project_institute->project->id);
	}
	
	
	
	
	public function editFinanceDetailFormSubmitted($form) {
		$values = $form->getValues();
		
		$this->redirect('edit', $this->project_institute->project->id);
	}

	
	

	public function createComponentDataGrid() {

		if(empty($this->dateRange)) {
			$source = $this->db->table('project');
		} else {
			$source = $this->db->table('project')
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
	
	
	
	
	public function createComponentDataGridMyProjects() {

		if(empty($this->dateRange)) {
			$source = $this->db->table('project')->where('user_id', $this->user->getId());
		} else {
			$source = $this->db->table('project')
					->where('project.user_id', $this->user->getId())
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
	 * Function to get free faculties/institutes for project.
	 * Every institute can be assigned to project only once.
	 * 
	 * @param int $project_id		ID of project
	 */
	public function getFreeInstitutes($project_id) {
		//get db values
		$faculties = $this->db->table('faculty')->order('name');
		$project = $this->db->table('project')->where('id', $project_id)->fetch();
		
		//init array values
		$result = array();
		$banned = array();
		
		//get banned institutes
		foreach($project->related('project_institute') as $project_institute) {
			$banned[] = $project_institute->institute->id;
		}
		
		//get free institutes
		foreach($faculties as $faculty) {
			$result[$faculty->name] = array();
			foreach($faculty->related('institute')->order('name') as $institute) {
				if(!in_array($institute->id, $banned)) {
					$result[$faculty->name][$institute->id] = $institute->name . ' (' . $institute->acronym . ')'; 
				}
			}			
		}
		
		//set result to global value
		$this->free_institutes = $result;
	}
	
	
	
	
	/**
	 * Function to get states - for now all
	 */
	public function getStates() {
		$states = $this->db->table('state');
		$result = array();
		
		foreach($states as $state) {
			$result[$state->id] = $state->name; 		
		}
		
		$this->states = $result;
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
