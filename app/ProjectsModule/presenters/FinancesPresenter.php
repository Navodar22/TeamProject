<?php

/**
 * Project presenter.
 *
 * @author     Samuel Kelemen
 */
class Projects_FinancesPresenter extends Projects_BasePresenter
{
	/** @var database object */
	public $project_institute;
	
	/** @var database object */
	public $project;
	
	/** var session	object */
	public $project_institute_session;
	
	
	
	
	/**
	 * Add function
	 * 
	 * Add institute to project
	 */
	public function actionAdd() {
		//get session variable
		$session = $this->context->getService('session');
		$this->project_institute_session = $session->getSection('project_institute_add');
		
		//if we havent session - redirect to previous step
		if(!isSet($this->project_institute_session->values)) {
			$this->redirect(':Projects:Institutes:add');
		}
		
		//set template variables
		$this->template->project = $this->db->table('project')->get($this->project_institute_session->values->project_id);
		$this->template->institute = $this->db->table('institute')->get($this->project_institute_session->values->institute_id);
		$this->template->total_participation = $this->project_institute_session->values->participation;
		$this->template->total_hr = $this->project_institute_session->values->hr;
		
	}
	
	
	
	
	/**
	 * Edit function
	 * 
	 * Edit institute related with project
	 */
	public function actionEdit($id) {		
		$this->project_institute = $this->db->table('project_institute')->where('id', $id)->fetch();
		
		if(!$this->project_institute) {
			throw new NBadRequestException();
		}
		
		//get session variable
		$session = $this->context->getService('session');
		$this->project_institute_session = $session->getSection('project_institute_edit');
		
		//if we havent session - redirect to previous step
		if(!isSet($this->project_institute_session->values)) {
			$this->redirect(':Projects:Institutes:edit', $this->project_institute->id);
		}

		//set template variables
		$this->template->project = $this->project_institute->project;
		$this->template->project_institute = $this->project_institute;
		$this->template->total_participation = $this->project_institute_session->values->participation;
		$this->template->total_hr = $this->project_institute_session->values->hr;

		//set default variables to form
		$defaults = array();
		foreach($this->project_institute->related('project_institute_date') as $project_institute_date) {
			//parse year form date
			$year = $project_institute_date->start->format('Y');
			$defaults[$year] = array(
				'participation' => $project_institute_date->participation,
				'hr' => $project_institute_date->hr
			);
		}
		
		$form = $this['editForm'];
		$form->setDefaults($defaults);
	}
	
	
	
	
	/**
	 * Edit finances function - only second step of edit proces
	 * 
	 * @param int $id 
	 */
	public function actionEditFinances($id) {
		$this->project_institute = $this->db->table('project_institute')->where('id', $id)->fetch();
		
		if(!$this->project_institute) {
			throw new NBadRequestException();
		}
		
		//set template variables
		$this->template->project = $this->project_institute->project;
		$this->template->project_institute = $this->project_institute;
		$this->template->total_participation = $this->project_institute->participation;
		$this->template->total_hr = $this->project_institute->hr;

		//set form default variables
		$defaults = array();
		foreach($this->project_institute->related('project_institute_date') as $project_institute_date) {
			$year = $project_institute_date->start->format('Y');
			$defaults[$year] = array(
				'participation' => $project_institute_date->participation,
				'hr' => $project_institute_date->hr
			);
		}
		
		$form = $this['editFinancesForm'];
		$form->setDefaults($defaults);
	}
	
	
	
	
	/**
	 * Add form
	 * @return NAppForm 
	 */
	public function createComponentAddForm() {	
		$form = new NAppForm();
		
		//get start and end dates
		$startDate = $this->project_institute_session->values->start;
		$endDate = $this->project_institute_session->values->end;
		
		//parse start and end years
		$startDateYear = $startDate->format('Y');
		$endDateYear = $endDate->format('Y');
		
		//calculate diff
		$diff = $endDateYear - $startDateYear;
		
		$year = (int)$startDateYear;
		$finance_years = array();
		
		//create array of all project years
		while($diff >= 0) {
			array_push($finance_years, $year);
			$year++;
			$diff--;
		}
		
		//create form elements		
		foreach($finance_years as $finance_year) {
			$form->addGroup('Rok ' . (string)$finance_year);
			$form->addContainer($finance_year);
			$form[$finance_year]->addText('participation', 'Finančná spoluúčasť')
					->setDefaultValue('0')
					->addCondition(NForm::FILLED)
						->addRule(NForm::FLOAT, 'Participácia na projekte musí byť číslo.');
			$form[$finance_year]->addText('hr', 'Ľudské zdroje')
					->setDefaultValue('0')
					->addCondition(NForm::FILLED)
						->addRule(NForm::INTEGER, 'Ľudské zdroje musia byť číslo.');
		}
		
		$form->setCurrentGroup(NULL);
		$form->addSubmit('back', 'Návrat')
				->setValidationScope(NULL)
				->getControlPrototype()
					->class('design');
		
		$form->addSubmit('process', 'Ulož')
				->getControlPrototype()
					->class('design');
		
		$form->onSuccess[] = callback($this, 'addFormSubmitted');
		
		return $form;
	}
	
	
	
	
	/**
	 * Edit form 
	 * @return NAppForm 
	 */
	public function createComponentEditForm() {	
		$form = new NAppForm();
	
		//get start and end dates
		$startDate = $this->project_institute_session->values->start;
		$endDate = $this->project_institute_session->values->end;
		
		//parse start and end year
		$startDateYear = $startDate->format('Y');
		$endDateYear = $endDate->format('Y');
		
		//calculate diff
		$diff = $endDateYear - $startDateYear;
		
		$year = (int)$startDateYear;
		$finance_years = array();
		
		//crate array of project years
		while($diff >= 0) {
			array_push($finance_years, $year);
			$year++;
			$diff--;
		}
		
		//crate form elements
		foreach($finance_years as $finance_year) {
			$form->addGroup('Rok ' . (string)$finance_year);
			$form->addContainer($finance_year);
			$form[$finance_year]->addText('participation', 'Finančná spoluúčasť')
					->setDefaultValue('0')
					->addCondition(NForm::FILLED)
						->addRule(NForm::FLOAT, 'Participácia na projekte musí byť číslo.');
			$form[$finance_year]->addText('hr', 'Ľudské zdroje')
					->setDefaultValue('0')
					->addCondition(NForm::FILLED)
						->addRule(NForm::INTEGER, 'Ľudské zdroje musia byť číslo.');
		}
		
		$form->setCurrentGroup(NULL);
		$form->addSubmit('back', 'Návrat')
				->setValidationScope(NULL)
				->getControlPrototype()
					->class('design');
		
		$form->addSubmit('process', 'Ulož')
				->getControlPrototype()
					->class('design');	
		
		$form->onSuccess[] = callback($this, 'editFormSubmitted');
		
		return $form;
	}
	
	
	
	
	/**
	 * Edit finances form - only second step of edit proces
	 * @return NAppForm 
	 */
	public function createComponentEditFinancesForm() {	
		$form = new NAppForm();
	
		//get start and end dates
		$startDate = new DateTime($this->project_institute->start);
		$endDate = new DateTime($this->project_institute->end);
		
		//parse start and end years
		$startDateYear = $startDate->format('Y');
		$endDateYear = $endDate->format('Y');
		
		//calculate diff
		$diff = $endDateYear - $startDateYear;
		
		$year = (int)$startDateYear;
		$finance_years = array();
		
		//create array of project dates
		while($diff >= 0) {
			array_push($finance_years, $year);
			$year++;
			$diff--;
		}
		
		//craete form elements
		foreach($finance_years as $finance_year) {
			$form->addGroup('Rok ' . (string)$finance_year);
			$form->addContainer($finance_year);
			$form[$finance_year]->addText('participation', 'Finančná spoluúčasť')
					->setDefaultValue('0')
					->addCondition(NForm::FILLED)
						->addRule(NForm::FLOAT, 'Participácia na projekte musí byť číslo.');
			$form[$finance_year]->addText('hr', 'Ľudské zdroje')
					->setDefaultValue('0')
					->addCondition(NForm::FILLED)
						->addRule(NForm::INTEGER, 'Ľudské zdroje musia byť číslo.');
		}
		
		$form->setCurrentGroup(NULL);
		$form->addSubmit('back', 'Návrat')
				->setValidationScope(NULL)
				->getControlPrototype()
					->class('design');
		
		$form->addSubmit('process', 'Ulož')
				->getControlPrototype()
					->class('design');	
		
		$form->onSuccess[] = callback($this, 'editFinancesFormSubmitted');
		
		return $form;
	}
	
	
	
	
	/**
	 * Add form submitted function
	 * @param NAppForm $form
	 */
	public function addFormSubmitted($form) {
		if($form['process']->isSubmittedBy()) {
			$containers = $form->getValues();

			//lambda function
			$firstYearDay = function($year) {
				return new \DateTime("1.1.$year 01:00:00");
			};

			//lambda function
			$lastYearDay = function($year) {
				return new \DateTime("31.12.$year 01:00:00");
			};

			//parse start and end dates
			$startDateYear = $this->project_institute_session->values->start->format('Y');
			$endDateYear = $this->project_institute_session->values->end->format('Y');

			$total_values = array(
				'participation' => 0,
				'hr' => 0
			);

			//calculate total values
			foreach($containers as $key => $container) {
				$total_values['participation'] += $container->participation;
				$total_values['hr'] += $container->hr;
			}

			//check total values and form values
			if($total_values['participation'] != $this->project_institute_session->values->participation) {
				$this->flashMessage('Zadané financovanie: ' . $total_values['participation'] . ' €/ Celkové financovanie: ' . $this->project_institute_session->values->participation . ' €', 'error');
			}else if($total_values['hr'] != $this->project_institute_session->values->hr) {
				$this->flashMessage('Zadané ľudské zdroje: ' . $total_values['hr'] . ' / Celkové ľudské zdroje: ' . $this->project_institute_session->values->hr, 'error');
			} else {		
				try {
					//start transaction
					$this->db->beginTransaction();

					//insert new institute data and recalculate project data
					$new_project_institute = $this->db->table('project_institute')
														->insert($this->project_institute_session->values);
					$this->calculateProjectData($this->project_institute_session->values->project_id);

					//insert new date dates
					foreach($containers as $key => $container) {
						if($key == $startDateYear) {
							$data = array(
								'start' => $this->project_institute_session->values->start,
								'end' => $lastYearDay($key),
								'participation' => $container->participation,
								'hr' => $container->hr,
								'project_institute_id' => $new_project_institute->id
							);
						} elseif ($key == $endDateYear) {
							$data = array(
								'start' => $firstYearDay($key),
								'end' => $this->project_institute_session->values->end,
								'participation' => $container->participation,
								'hr' => $container->hr,
								'project_institute_id' => $new_project_institute->id
							);
						} else {				
							$data = array(
								'start' => $firstYearDay($key),
								'end' => $lastYearDay($key),
								'participation' => $container->participation,
								'hr' => $container->hr,
								'project_institute_id' => $new_project_institute->id
							);
						}			
						$this->db->table('project_institute_date')->insert($data);
					}
					$project_id = $this->project_institute_session->values->project_id;
					unset($this->project_institute_session->values);
					$this->db->commit();
					$this->flashMessage('Ústav bol úspešne pripojený k projektu.', 'ok');
				} catch(PDOException $e) {
					$this->db->rollback();
					$this->flashMessage('Ústav sa nepodarilo pridať k projektu.', 'error');
					$this->redirect('default');
				}
				$this->redirect('Projects:edit', $project_id);
			}
		} else {
			$this->redirect(':Projects:Institutes:add', $this->project_institute_session->values->project_id);
		}
	}
	
	
	
	
	/**
	 * Edit form submitted function
	 * @param NAppForm $form
	 */
	public function editFormSubmitted($form) {
		if($form['process']->isSubmittedBy()) {
			$containers = $form->getValues();

			//lambda function
			$firstYearDay = function($year) {
				return new \DateTime("1.1.$year 01:00:00");
			};

			//lambda function
			$lastYearDay = function($year) {
				return new \DateTime("31.12.$year 01:00:00");
			};

			//parse start and end years
			$startDateYear = $this->project_institute_session->values->start->format('Y');
			$endDateYear = $this->project_institute_session->values->end->format('Y');

			$total_values = array(
				'participation' => 0,
				'hr' => 0
			);

			//calculate total values
			foreach($containers as $key => $container) {
				$total_values['participation'] += $container->participation;
				$total_values['hr'] += $container->hr;
			}

			//check total values and form values
			if($total_values['participation'] != $this->project_institute_session->values->participation) {
				$this->flashMessage('Zadané financovanie: ' . $total_values['participation'] . ' €/ Celkové financovanie: ' . $this->project_institute_session->values->participation . ' €', 'error');
			}else if($total_values['hr'] != $this->project_institute_session->values->hr) {
				$this->flashMessage('Zadané ľudské zdroje: ' . $total_values['hr'] . ' / Celkové ľudské zdroje: ' . $this->project_institute_session->values->hr, 'error');
			} else {		
				try {
					$this->db->beginTransaction();
					
					//delete date dates form db
					$this->project_institute->related('project_institute_date')->delete();
					
					//update institutes data
					$this->db->table('project_institute')->where('id', $this->project_institute->id)->update($this->project_institute_session->values);
					$this->calculateProjectData($this->project_institute->project->id);

					//insert new date data
					foreach($containers as $key => $container) {
						if($key == $startDateYear) {
							$data = array(
								'start' => $this->project_institute_session->values->start,
								'end' => $lastYearDay($key),
								'participation' => $container->participation,
								'hr' => $container->hr,
								'project_institute_id' => $this->project_institute->id
							);
						} elseif ($key == $endDateYear) {
							$data = array(
								'start' => $firstYearDay($key),
								'end' => $this->project_institute_session->values->end,
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
					unset($this->project_institute_session->values);
					$this->db->commit();
					$this->flashMessage('Úprava prebehla úspešne.', 'ok');
				} catch(PDOException $e) {
					$this->db->rollback();
					$this->flashMessage('Úprava sa nepodarila.', 'error');
					$this->redirect('default');
				}
				$this->redirect('Projects:edit', $this->project_institute->project->id);
			}
		} else {
			$this->redirect(':Projects:Institutes:edit', $this->project_institute->id);
		}
	}	
	
	
	
	
	/**
	 * Edit finances form submitted function
	 * @param NAppForm $form
	 */
	public function editFinancesFormSubmitted($form) {
		if($form['process']->isSubmittedBy()) {
			$containers = $form->getValues();

			//lambda function
			$firstYearDay = function($year) {
				return new \DateTime("1.1.$year 01:00:00");
			};

			//lambda function
			$lastYearDay = function($year) {
				return new \DateTime("31.12.$year 01:00:00");
			};

			//parse start and end year
			$startDateYear = $this->project_institute->start->format('Y');
			$endDateYear = $this->project_institute->end->format('Y');

			$total_values = array(
				'participation' => 0,
				'hr' => 0
			);

			//calculate total values
			foreach($containers as $key => $container) {
				$total_values['participation'] += $container->participation;
				$total_values['hr'] += $container->hr;
			}

			//check total values and form values
			if($total_values['participation'] != $this->project_institute->participation) {
				$this->flashMessage('Zadané financovanie: ' . $total_values['participation'] . ' €/ Celkové financovanie: ' . $this->project_institute->participation . ' €', 'error');
			}else if($total_values['hr'] != $this->project_institute->hr) {
				$this->flashMessage('Zadané ľudské zdroje: ' . $total_values['hr'] . ' / Celkové ľudské zdroje: ' . $this->project_institute->hr, 'error');
			} else {		
				try {
					$this->db->beginTransaction();
					
					//delete all date data
					$this->project_institute->related('project_institute_date')->delete();

					//insert new date data
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
					$this->db->commit();
					$this->flashMessage('Úprava prebehla úspešne.', 'ok');
				} catch(PDOException $e) {
					$this->db->rollback();
					$this->flashMessage('Úprava sa nepodarila.', 'error');
					$this->redirect('default');
				}
				$this->redirect('Projects:edit', $this->project_institute->project->id);
			}
		} else {
			$this->redirect(':Projects:Projects:edit', $this->project_institute->project->id);
		}
	}
}
