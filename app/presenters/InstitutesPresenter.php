<?php

/**
 * Institute presenter
 *
 * @author     Samuel Kelemen
 * @package    Petri net alocation system
 */
class InstitutesPresenter extends BaseLPresenter
{
	/** @var database object */
	public $institute;
	
	/** @var array		Non deleted faculties*/
	public $faculties;
	
	/** @var */
	public $faculty;
	
	
	public $backlink;
	
	
	
	
	/**
	 * Startup function
	 */
	public function startup() {
		parent::startup();

		$this->faculties = $this->getFaculties();
	}
	
	
	
	
	/**
	 * Render function to render institute list
	 * 
	 * @param int $id		ID of selected faculty or NULL for all institutes 
	 */
	public function renderDefault($id = NULL)
	{
		$faculty = $this->db->table('faculty')->where('id', $id)->fetch();
		
		if(!$faculty) {
			$this->template->all_institutes = $this->db->table('institute');
		} else {		
			$this->template->faculty = $faculty;
		}
	} 

	
	
	
	/**
	 * Edit action function.
	 * Edit selected institute
	 * 
	 * @param int $id				ID of selected institute
	 * @param int $faculty			ID of associate faculty
	 */
	public function actionEdit($id, $faculty = NULL, $backlink = NULL) {
		$this->institute = $this->db->table('institute')->where('id', $id)->fetch();
		$this->backlink = $backlink;
		
		if(!$this->institute) {
			throw new NBadRequestException;
		}
		
		//if get to this action from faculty institute render, need to redirect back
		if($faculty) {
			$this->faculty = $faculty;
		}
		
		$this['saveForm']->setDefaults($this->institute);
		$this->template->institute = $this->institute;
	}
	
	
	
	
	/**
	 * Delete function to delete selected institute
	 * 
	 * @param int $id				ID of selected institute
	 * @param int $faculty			ID of associate faculty
	 */
	public function actionDelete($id, $faculty = NULL) {
		$this->institute = $this->db->table('institute')->where('id', $id)->fetch();		
		
		if(!$this->institute) {
			throw new NBadRequestException;
		}
		
		try {
			//delete institute
			$result = $this->db->table('institute')->where('id', $this->institute->id)->delete();	
		} catch (PDOException $e) {
			$this->flashMessage('Ústav sa nepodarilo odstrániť', 'error');
			$this->redirect('default', $faculty);
		}
		
		if($result) {
			$this->calculateMoney();
			$this->flashMessage('Ústav bol odstránený', 'ok');
		} else {
			$this->flashMessage('Ústav sa nepodarilo odstrániť', 'error');
		}
		$this->redirect('default', $faculty);
	}
	
	
	
	
	/**
	 * Save form create function
	 * 
	 * @return NAppForm 
	 */
	public function createComponentSaveForm() {
		$form = new NAppForm();
		
		$form->addGroup();
		$form->addText('name', 'Názov ústavu')
				->addRule(NForm::FILLED, 'Musíte zadať názov ústavu.')
				->getControlPrototype()
					->class('w350');
		$form->addText('acronym', 'Skratka ústavu')
				->addRule(NForm::FILLED, 'Musíte zadať skratku ústavu.');
		
		$form->addSelect('faculty_id', 'Zaradiť pod katedru', $this->faculties);
		$form->addText('students', 'Počet študentov');
		
		$form->setCurrentGroup(NULL);
		$form->addSubmit('process', 'Ulož')
				->getControlPrototype()
					->class('design');
		$form->addSubmit('back', 'Naspäť')
				->setValidationScope(NULL)
				->getControlPrototype()
					->class('design');
		
		$form->onSuccess[] = callback($this, 'saveFormSubmitted');
		
		return $form;
	}
	
	
	
	
	/**
	 * Save form submit callback
	 * 
	 * @param NAppForm $form 
	 */
	public function saveFormSubmitted($form) {
		if($form['process']->isSubmittedBy()) {
			$values = $form->values;
			try {
				if($this->institute) {
					$this->db->table('institute')->where('id', $this->institute->id)->update($values);	
				} else {
					$this->db->table('institute')->insert($values);		
				}	
				$this->calculateMoney();
				$this->flashMessage('Ústav bol uložený.', 'ok');
			} catch (PDOException $e) {
				$this->flashMessage('Pri ukladaní dát do db nastala chyba.', 'error');
			}
		}
		
		if($this->backlink) {
			$this->application->restoreRequest($this->backlink);
		} else {
			$this->redirect('default', $this->faculty);	
		}
	}
	
	
	
	
	/**
	 * Function to get faculties in array - only non deleted
	 */
	public function getFaculties() {
		$faculties = $this->db->table('faculty');
		$faculties_array = array();
		
		foreach($faculties as $faculty) {
			$faculties_array[$faculty->id] = $faculty->acronym; 
		}
		
		return $faculties_array;
	}
}
