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
		
		$faculties = $this->model('Faculty')->getTable()->where('del', FALSE);
		$faculties_array = array();
		
		foreach($faculties as $faculty) {
			$faculties_array[$faculty->id] = $faculty->name; 
		}
		
		$this->faculties = $faculties_array;
	}
	
	
	
	
	
	public function renderDefault($id = NULL)
	{
		$faculty = $this->model('Faculty')->getTable()->where('del', FALSE)->where('id', $id)->fetch();
		
		if(!$faculty) {
			$this->template->all_institutes = $this->model('Institute')->getTable()->where('del', FALSE);
		} else {		
			$this->template->faculty = $faculty;
		}
	} 

	
	
	
	
	
	public function actionEdit($id, $faculty = NULL) {
		$this->institute = $this->model('Institute')->getTable()->where('del', FALSE)->where('id', $id)->fetch();	
		
		if(!$this->institute) {
			throw new NBadRequestException;
		}
		
		if($faculty) {
			$this->faculty = $faculty;
		}
		
		$this['saveForm']->setDefaults($this->institute);
	}
	
	
	
	
	
	public function actionDelete($id, $faculty = NULL) {
		$this->institute = $this->model('Institute')->getTable()->where('del', FALSE)->where('id', $id)->fetch();		
		
		if(!$this->institute) {
			throw new NBadRequestException;
		}
		
		$delete = array('del' => TRUE);
		$result = $this->model('Institute')->getTable()->where('id', $this->institute->id)->update($delete);	
		
		if($result) {
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
		
		$form->addSelect('faculty_id', 'Zaradiť pod katedru', $this->faculties);
		
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
					$this->model('Institute')->getTable()->where('id', $this->institute->id)->update($values);
					$this->flashMessage('Ústav bol upravený.', 'ok');
				} else {
					$this->model('Institute')->getTable()->insert($values);					
					$this->flashMessage('Ústav bol pridaný.', 'ok');
				}
			} catch (PDOException $e) {
				$this->flashMessage('Pri ukladaní dát do db nastala chyba.', 'error');
			}
		}
			$this->redirect('default', $this->faculty);	
	}
}
