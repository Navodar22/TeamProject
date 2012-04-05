<?php

/**
 * Faculties presenter
 *
 * @author     Samuel Kelemen
 * @package    Petri net alocation system
 */
class FacultiesPresenter extends BaseLPresenter
{
	/** @var database object */
	public $faculty;
	
	
	
	
	/** 
	 * Default render function.
	 * Render all faculties in system
	 */
	public function renderDefault()
	{
		$this->template->faculties = $this->db->table('faculty')->where('del', FALSE);
	} 
	
	
	
	
	/**
	 * Edit action function.
	 * Edit selected faculty.
	 * 
	 * @param int $id		ID of editing faculty
	 */
	public function actionEdit($id) {
		$this->faculty = $this->db->table('faculty')->where('del', FALSE)->where('id', $id)->fetch();	
		
		if(!$this->faculty) {
			throw new NBadRequestException;
		}
		
		$this['saveForm']->setDefaults($this->faculty);
	}
	
	
	
	
	/**
	 * Delete action function.
	 * Delete selected faculty.
	 * 
	 * @param int $id		ID of selected faculty
	 */
	public function actionDelete($id) {
		$this->faculty = $this->db->table('faculty')->where('del', FALSE)->where('id', $id)->fetch();		
		
		if(!$this->faculty) {
			throw new NBadRequestException;
		}
		
		//set help value - not really delete, only set delete flag to true
		$delete = array('del' => TRUE);
		
		try {
			//perform delete action via transaction
			$this->db->beginTransaction();
			
			//delete faculty
			$result = $this->db->table('faculty')->where('id', $this->faculty->id)->update($delete);	

			//delete all realted institutes - set delete flag to true
			foreach($this->faculty->related('institute') as $institute) {
				$this->db->table('institute')->where('id', $institute->id)->update($delete);	
			}
			
			$this->db->commit();
		} catch (PDOException $e) {
			$this->db->rollback();
			$this->flashMessage('Fakultu sa nepodarilo vymazať zo systému', 'error');
			$this->redirect('default');
		}
		
		if($result) {
			//recalculate system money
			$this->calculateMoney();
			$this->flashMessage('Fakulta bola odstránená', 'ok');
		} else {
			$this->flashMessage('Fakultu sa nepodarilo odstrániť', 'error');
		}
		$this->redirect('default');
	}
	
	
	
	
	/**
	 * Save form create function
	 * 
	 * @return NAppForm 
	 */
	public function createComponentSaveForm() {
		$form = new NAppForm();
		
		$form->addText('name', 'Názov fakulty')
				->addRule(NForm::FILLED, 'Musíte zadať názov fakulty.');
		$form->addText('acronym', 'Skratka fakulty')
				->addRule(NForm::FILLED, 'Musíte zadať skratku fakulty.');
		
		$form->addSubmit('process', 'Ulož');
		$form->addSubmit('back', 'Naspäť')
				->setValidationScope(NULL);
		
		$form->onSuccess[] = callback($this, 'addFormSubmitted');
		
		return $form;
	}
	
	
	
	
	/**
	 * Save form submit callback
	 * 
	 * @param type $form 
	 */
	public function addFormSubmitted($form) {
		if($form['process']->isSubmittedBy()) {
			$values = $form->values;
			try {
				if($this->faculty) {
					$this->db->table('faculty')->where('id', $this->faculty->id)->update($values);
					$this->flashMessage('Fakulta bola úspešne zmenená.', 'ok');
				} else {
					$this->db->table('faculty')->insert($values);
					$this->flashMessage('Fakulta bola úspešne pridaná.', 'ok');
				}
			} catch (PDOException $e) {
				$this->flashMessage('Pri ukladaní dát do db nastala chyba.', 'error');
			}
		}
			$this->redirect('default');	
	}
}
