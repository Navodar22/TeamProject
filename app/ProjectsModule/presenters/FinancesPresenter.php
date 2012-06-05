<?php

/**
 * Project presenter.
 *
 * @author     Samuel Kelemen
 */
class Projects_FinancesPresenter extends BaseLPresenter
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
	
	
	
	

	
	public function actionAdd($id) {
		$this->project_institute = $this->db->table('project_institute')->where('id', $id)->fetch();
		
		if(!$this->project_institute) {
			throw new NBadRequestException();
		}
		
		$this->template->project = $this->project_institute->project;
		$this->template->institute = $this->project_institute->institute;
	}
	
	
	
	
	public function actionEdit($id) {
		$this->project_institute = $this->db->table('project_institute')->where('id', $id)->fetch();
		
		if(!$this->project_institute) {
			throw new NBadRequestException();
		}
		
		$this->template->project = $this->project_institute->project;
		$this->template->institute = $this->project_institute->institute;

		$defaults = array();
		foreach($this->project_institute->related('project_institute_date') as $project_institute_date) {
			$year = $project_institute_date->start->format('Y');
			$defaults[$year] = array(
				'participation' => $project_institute_date->participation,
				'hr' => $project_institute_date->hr
			);
		}
		
		$form = $this['editForm'];
		$form->setDefaults($defaults);
	}
	
	
	
	
	
	public function createComponentAddForm() {	
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
		$form->addSubmit('process', 'Ulož')
				->getControlPrototype()
					->class('design');
		
		$form->onSuccess[] = callback($this, 'addFormSubmitted');
		
		return $form;
	}
	
	
	
	
	public function createComponentEditForm() {	
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
		$form->addSubmit('process', 'Ulož')
				->getControlPrototype()
					->class('design');
		
		$form->onSuccess[] = callback($this, 'editFormSubmitted');
		
		return $form;
	}
	
	
	
	
	public function addFormSubmitted($form) {
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
		
		$this->redirect('Projects:edit', $this->project_institute->project->id);
	}
	
	
	
	
	public function editFormSubmitted($form) {
		$containers = $form->getValues();
		
		$this->project_institute->related('project_institute_date')->delete();
		
		$firstYearDay = function($year) {
			return new \DateTime("1.1.$year 01:00:00");
		};
		
		$lastYearDay = function($year) {
			return new \DateTime("31.12.$year 01:00:00");
		};
		
		$startDateYear = $this->project_institute->start->format('Y');
		$endDateYear = $this->project_institute->end->format('Y');
		
		$total_values = array(
			'participation' => 0,
			'hr' => 0
		);
		
		foreach($containers as $key => $container) {
			$total_values['participation'] += $container->participation;
			$total_values['hr'] += $container->hr;
		}
		
		if($total_values['participation'] != $this->project_institute->participation) {
			$this->flashMessage($total_values['participation'] . '/' . $this->project_institute->participation, 'error');
		}else if($total_values['hr'] != $this->project_institute->hr) {
			$this->flashMessage('ee2', 'error');
		} else {
		
			foreach($containers as $key => $container) {

				if($key == $startDateYear) {
					$data = array(
						'start' => $this->project_institute->start,
						'end' => $lastYearDay($key),
						'participation' => (int) $container->participation,
						'hr' => (int) $container->hr,
						'project_institute_id' => $this->project_institute->id
					);
				} elseif ($key == $endDateYear) {
					$data = array(
						'start' => $firstYearDay($key),
						'end' => $this->project_institute->end,
						'participation' => (int) $container->participation,
						'hr' => (int) $container->hr,
						'project_institute_id' => $this->project_institute->id
					);
				} else {				
					$data = array(
						'start' => $firstYearDay($key),
						'end' => $lastYearDay($key),
						'participation' => (int) $container->participation,
						'hr' => (int) $container->hr,
						'project_institute_id' => $this->project_institute->id
					);
				}			
				$this->db->table('project_institute_date')->insert($data);
			}

			$this->redirect('Projects:edit', $this->project_institute->project->id);
		}
	}

	
	
}
