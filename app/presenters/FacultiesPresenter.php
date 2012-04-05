<?php

/**
 * Faculties presenter
 *
 * @author     Samuel Kelemen
 * @package    Petri net alocation system
 */
class FacultiesPresenter extends BaseLPresenter
{
	public $faculty;
	
	
	
	
	
	public function renderDefault()
	{
		$this->template->faculties = $this->db->table('faculty')->where('del', FALSE);
	} 
	
	
	
	
	
	public function actionEdit($id) {
		$this->faculty = $this->db->table('faculty')->where('del', FALSE)->where('id', $id)->fetch();	
		
		if(!$this->faculty) {
			throw new NBadRequestException;
		}
		
		$this['saveForm']->setDefaults($this->faculty);
	}
	
	
	
	
	
	public function actionDelete($id) {
		$this->faculty = $this->db->table('faculty')->where('del', FALSE)->where('id', $id)->fetch();		
		
		if(!$this->faculty) {
			throw new NBadRequestException;
		}
		
		$delete = array('del' => TRUE);
		$result = $this->db->table('faculty')->where('id', $this->faculty->id)->update($delete);	
		
		foreach($this->faculty->related('institute') as $institute) {
			$this->db->table('institute')->where('id', $institute->id)->update($delete);	
		}
		
		if($result) {
			$this->flashMessage('Fakulta bola odstránená', 'ok');
		} else {
			$this->flashMessage('Fakultu sa nepodarilo odstrániť', 'error');
		}
		$this->redirect('default');
	}
	
	
	
	
	
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
