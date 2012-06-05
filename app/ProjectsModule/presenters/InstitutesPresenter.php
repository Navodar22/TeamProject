<?php

/**
 * Project presenter.
 *
 * @author     Samuel Kelemen
 */
class Projects_InstitutesPresenter extends BaseLPresenter
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
	 * Add institute function
	 * 
	 * @param int $id		ID of project -> where connect the institute
	 */
	public function actionAdd($id) {
		$this->project = $this->db->table('project')->where('id', $id)->fetch();
		
		if(!$this->project) {
			throw new NBadRequestException;
		}
		
		//fetch array of free faculties/institutes for actual project
		$this->getFreeInstitutes($this->project->id);
		
		if(empty($this->free_institutes)) {
			$this->flashMessage('Neexistuje ústav ktorý by sa mohol pridať ku projektu.', 'error');
			$this->redirect('Projects:edit', $this->project->id);
		}
		
		$this->template->project = $this->project;
	}
	
	
	
	
	/**
	 * Edit institute function 
	 * 
	 * @param int $id		ID of project_insitute record to edit 
	 */
	public function actionEdit($id) {
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
		$this['editForm']->setDefaults($this->project_institute);
		
		$this->template->institute = $this->project_institute->institute;
		$this->template->project = $this->project_institute->project;
	}
	
	
	
	
	/**
	 * Delete institute function
	 * 
	 * @param int $id		ID of project_institute record 
	 */
	public function actionDelete($id) {
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
			$this->redirect('Projects:edit', $project_id);
		} catch (PDOException $e) {			
			$this->db->rollback();
			$this->flashMessage('Odstránenie ústavu z projektu nebolo úspešné.', 'error');
			$this->redirect('Projects:edit', $project_id);
		}
	} 
	
	
	
	
	
	/**
	 * Add institute form
	 * 
	 * @return NAppForm 
	 */
	public function createComponentAddForm() {	
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
		
		$form->onSuccess[] = callback($this, 'addFormSubmitted');
		
		return $form;
	}
	
	
	
	
	/**
	 * Add institute form - submit function
	 * 
	 * @param NAppForm $form 
	 */
	public function addFormSubmitted($form) {	
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
					$this->redirect('Finances:add', $new_project_institute->id);	
				}
			} else { //if submitted by back only redirect
				$this->redirect('Projects:edit', $this->project->id);
			}
		} catch (PDOException $e) {
			$this->db->rollback();
			$this->flashMessage('Ústav sa nepodarilo pridať k projektu.', 'error');
			$this->redirect('Projects:edit', $this->project->id);
		}
	}
	
	
	
	
	/**
	 * Edit institute form
	 * 
	 * @return NAppForm 
	 */
	public function createComponentEditForm() {	
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
					$this->redirect('Finances:edit', $this->project_institute->id);	
				}

			} else {
				$this->redirect('Projects:edit', $this->project_institute->project->id);
			}
		} catch (PDOException $e) {
			$this->db->rollback();
			$this->flashMessage('Dáta ústavu sa nepodarilo zmeniť.', 'error');
			$this->redirect('Projects:edit', $this->project_institute->project->id);
		}
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
	
	
	
	
	
}
