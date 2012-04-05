<?php

/**
 * Institute presenter
 *
 * @author     Samuel Kelemen
 * @package    Petri net alocation system
 */
class InstitutesPresenter extends BaseLPresenter
{
	public $institute;
	
	public $faculties;
	
	public $faculty;
	
	
	
	
	
	public function startup() {
		parent::startup();
		
		$faculties = $this->db->table('faculty')->where('del', FALSE);
		$faculties_array = array();
		
		foreach($faculties as $faculty) {
			$faculties_array[$faculty->id] = $faculty->acronym; 
		}
		
		$this->faculties = $faculties_array;
	}
	
	
	
	
	
	public function renderDefault($id = NULL)
	{
		$faculty = $this->db->table('faculty')->where('del', FALSE)->where('id', $id)->fetch();
		
		if(!$faculty) {
			$this->template->all_institutes = $this->db->table('institute')->where('del', FALSE);
		} else {		
			$this->template->faculty = $faculty;
		}
	} 

	
	
	
	
	
	public function actionEdit($id, $faculty = NULL) {
		$this->institute = $this->db->table('institute')->where('del', FALSE)->where('id', $id)->fetch();	
		
		if(!$this->institute) {
			throw new NBadRequestException;
		}
		
		if($faculty) {
			$this->faculty = $faculty;
		}
		
		$this['saveForm']->setDefaults($this->institute);
	}
	
	
	
	
	
	public function actionDelete($id, $faculty = NULL) {
		$this->institute = $this->db->table('institute')->where('del', FALSE)->where('id', $id)->fetch();		
		
		if(!$this->institute) {
			throw new NBadRequestException;
		}
		
		$delete = array('del' => TRUE);
		$result = $this->db->table('institute')->where('id', $this->institute->id)->update($delete);	
		
		if($result) {
			$this->calculateMoney();
			$this->flashMessage('Ústav bol odstránený', 'ok');
		} else {
			$this->flashMessage('Ústav sa nepodarilo odstrániť', 'error');
		}
		$this->redirect('default', $faculty);
	}
	
	
	
	
	
	public function createComponentSaveForm() {
		$form = new NAppForm();
		
		$form->addText('name', 'Názov ústavu')
				->addRule(NForm::FILLED, 'Musíte zadať názov ústavu.');
		$form->addText('acronym', 'Skratka ústavu')
				->addRule(NForm::FILLED, 'Musíte zadať skratku ústavu.');
		
		$form->addSelect('faculty_id', 'Zaradiť pod katedru', $this->faculties);
		$form->addText('students', 'Počet študentov');
		
		$form->addSubmit('process', 'Ulož');
		$form->addSubmit('back', 'Naspäť')
				->setValidationScope(NULL);
		
		$form->onSuccess[] = callback($this, 'addFormSubmitted');
		
		return $form;
	}
	
	
	
	
	
	public function addFormSubmitted($form) {
		if($form['process']->isSubmittedBy()) {
			$values = $form->values;
			try {
				if($this->institute) {
					$this->db->table('institute')->where('id', $this->institute->id)->update($values);	
					$this->calculateMoney();
					$this->flashMessage('Ústav bol upravený.', 'ok');
				} else {
					$this->db->table('institute')->insert($values);		
					$this->calculateMoney();
					$this->flashMessage('Ústav bol pridaný.', 'ok');
				}				
			} catch (PDOException $e) {
				$this->flashMessage('Pri ukladaní dát do db nastala chyba.', 'error');
			}
		}
			$this->redirect('default', $this->faculty);	
	}
	
	
	
	public function calculateMoney() {
		$total_students = 0;
		foreach($this->db->table('institute')->where('del', FALSE) as $institute) {
			$total_students += $institute->students;
		}

		$this->db->table('school')->update(array('students' => $total_students));
		$school = $this->db->table('school')->where('id', '1')->fetch();

		$money_index = $school->money/$school->students;

		foreach($this->db->table('institute')->where('del', FALSE) as $institute) {
			$institute_money = $institute->students * $money_index;
			$institute->update(array('money' => $institute_money));
		}	
	}
}
