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
		$user_id = $this->getUser()->getIdentity()->getId();		
		$this->template->user_projects = $this->db->table('project')->where('user_id', $user_id);
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
			$project_id = $project_institute->fetch()->project->id;
			$project_institute->delete();

			//set message and redirect
			$this->flashMessage('Ústav bol úspešne odstránený z participácie na projekte.', 'ok');
			$this->redirect('edit', $project_id);
		} catch (PDOException $e) {			
			$this->flashMessage('Odstránenie ústavu z projektu nebolo úspešné.', 'error');
			$this->redirect('edit', $project_id);
		}
	} 
	
	
	
	
	/**
	 * Add project form
	 * 
	 * @return NAppForm 
	 */
	public function createComponentAddForm() {
		$form = new NAppForm();
		
		$form->addText('name', 'Názov projektu')
				->addRule(NForm::FILLED, 'Musíte zadať názov projektu.');
		$form->addTextArea('description', 'Popis projektu', '50','5')
				->addRule(NForm::FILLED, 'Musíte zadať popis projektu.');
		
		$form->addSubmit('process', 'Uložiť projekt');
		$form->addSubmit('back', 'Návrat')
				->setValidationScope(NULL);
		
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

				$this->flashMessage('Projekt bol úspešne vytvorený.', 'ok');
				$this->redirect('default');
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
		
		$form->addText('name', 'Názov projektu')
				->addRule(NForm::FILLED, 'Musíte zadať názov projektu.');
		$form->addTextArea('description', 'Popis projektu', '50','5')
				->addRule(NForm::FILLED, 'Musíte zadať popis projektu.');
		
		//foreach related institute render a control buttons for this institute
		foreach($this->project->related('project_institute') as $project_institute) {
			$form->addSubmit("edit_$project_institute->id", 'Uprav');
			$form->addSubmit("delete_$project_institute->id", 'Vymaž');
		}
		
		$form->addSubmit('process', 'Uložiť projekt');
		$form->addSubmit('back', 'Návrat')
				->setValidationScope(NULL);
		
		$form->addSubmit('add_institute', 'Pridať ústav');
		
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

		$form->addSelect('state_id', 'Stav projektu', $this->states); //@TODO make global function to get states by role
		$form->addSelect('institute_id', 'Ústav', $this->free_institutes);
		
		$form->addText('hr', 'Celková cena hr')
				->addRule(NForm::FILLED, 'Musíte zadať hr projektu')
				->addRule(NForm::INTEGER, 'HR projektu musí byť číslo');
		$form->addText('cost', 'Celková cena projektu')
				->addRule(NForm::FILLED, 'Musíte zadať cenu projektu')
				->addRule(NForm::FLOAT, 'Cena projektu musí byť číslo');
		$form->addText('participation', 'Spoluúčasť na projekte')
				->addRule(NForm::FILLED, 'Musíte zadať spoluúčasť na projekte')
				->addRule(NForm::FLOAT, 'Spoluúčasť na projekte musí byť číslo');
		$form->addText('fonds', 'Fondy');		
		$form->addText('start', 'Začiatok projektu')
				->addRule(NForm::FILLED, 'Musíte vyplniť začiatok projektu.')
				->getControlPrototype()->class('datepicker');
		$form->addText('end', 'Koniec projektu')
				->addRUle(NForm::FILLED, 'Musíte vyplniť koniec projektu.')
				->getControlPrototype()->class('datepicker');
		
		$form->addSubmit('process', 'Pridaj ústav');		
		$form->addSubmit('back', 'Návrat')
				->setValidationScope(NULL);
		
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

				//check dates
				if($values['start'] >= $values['end']) {
					$this->flashMessage('Projekt nemôže mať ukončenie pred svojím začiatkom.', 'error');
					$error = true;
				} 
				
				//chect participation and cost value -> participation must by only part of total cost of project
				if($values['cost'] < $values['participation']) {
					$this->flashMessage('Participácia na projekte nemôže byť menšia ako jeho celková cena.', 'error');
					$error = true;
				}

				//is form error free ?
				if(!$error) {
					$this->db->table('project_institute')->insert($values);

					$this->flashMessage('Ústav bol pridaný k projektu.', 'ok');
					$this->redirect('edit', $this->project->id);	
				}
			} else { //if submitted by back only redirect
				$this->redirect('edit', $this->project->id);
			}
		} catch (PDOException $e) {
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

		$form->addSelect('state_id', 'Stav projektu', $this->states); //@TOTO prev todo
		
		$form->addText('hr', 'Celková cena hr')
				->addRule(NForm::FILLED, 'Musíte zadať hr projektu')
				->addRule(NForm::INTEGER, 'HR projektu musí byť číslo');
		$form->addText('cost', 'Celková cena projektu')
				->addRule(NForm::FILLED, 'Musíte zadať cenu projektu')
				->addRule(NForm::FLOAT, 'Cena projektu musí byť číslo');
		$form->addText('participation', 'Spoluúčasť na projekte')
				->addRule(NForm::FILLED, 'Musíte zadať spoluúčasť na projekte')
				->addRule(NForm::FLOAT, 'Spoluúčasť na projekte musí byť číslo');
		$form->addText('fonds', 'Fondy');		
		$form->addText('start', 'Začiatok projektu')
				->addRule(NForm::FILLED, 'Musíte vyplniť začiatok projektu.')
				->getControlPrototype()->class('datepicker');
		$form->addText('end', 'Koniec projektu')
				->addRUle(NForm::FILLED, 'Musíte vyplniť koniec projektu.')
				->getControlPrototype()->class('datepicker');
		
		$form->addSubmit('process', 'Ulož');		
		$form->addSubmit('back', 'Návrat')
				->setValidationScope(NULL);
		
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

				//check dates
				if($values['start'] >= $values['end']) {
					$this->flashMessage('Projekt nemôže mať ukončenie pred svojím začiatkom.', 'error');
					$error = true;
				} 
				
				//chect participation and cost value -> participation must by only part of total cost of project
				if($values['cost'] < $values['participation']) {
					$this->flashMessage('Participácia na projekte nemôže byť menšia ako jeho celková cena.', 'error');
					$error = true;
				}

				//is form error free ?
				if(!$error) {
					$this->db->table('project_institute')->where('id', $this->project_institute->id)->update($values);

					$this->flashMessage('Dáta ústavu úspešne zmenené.', 'ok');
					$this->redirect('edit', $this->project_institute->project->id);	
				}

			} else {
				$this->redirect('edit', $this->project_institute->project->id);
			}
		} catch (PDOException $e) {
			$this->flashMessage('Dáta ústavu sa nepodarilo zmeniť.', 'error');
			$this->redirect('edit', $this->project_institute->project->id);
		}
	}

	
	

	/**
	 * Function to get free faculties/institutes for project.
	 * Every institute can be assigned to project only once.
	 * 
	 * @param int $project_id		ID of project
	 */
	public function getFreeInstitutes($project_id) {
		//get db values
		$faculties = $this->db->table('faculty')->where('del', FALSE)->order('name');
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
			foreach($faculty->related('institute')->where('del', FALSE)->order('name') as $institute) {
				if(!in_array($institute->id, $banned)) {
					$result[$institute->id] = $faculty->name . ' - ' . $institute->name; 
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
