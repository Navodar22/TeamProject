<?php

/**
 * Faculties presenter
 *
 * @author     Samuel Kelemen
 * @package    Petri net alocation system
 */
class SettingsPresenter extends BaseLPresenter
{
	/** @var database object */
	public $settings;
	private $user;
	
	
	
	/** 
	 * Default render function.
	 * Render all faculties in system
	 */
	public function renderDefault()
	{
            $this->user = $this->getUser()->getIdentity();
                
            if( $this->user->privileges[0] | $this->user->privileges[1] | $this->user->privileges[2] | $this->user->privileges[3] ){

                $this->template->schools = $this->db->table('school');


            }else{
                $this->redirect('Homepage:');
            }
		
	} 
	
	
	
	
	/**
	 * Edit action function.
	 * Edit selected faculty.
	 * 
	 * @param int $id		ID of editing faculty
	 */
	public function actionEdit($id) {
		$this->settings = $this->db->table('school')->where('id', $id)->fetch();	
		
		if(!$this->settings) {
			throw new NBadRequestException;
		}
		
		$this['saveForm']->setDefaults($this->settings);
		$this->template->setting = $this->settings;
	}
	
	
	
	
	/**
	 * Delete action function.
	 * Delete selected faculty.
	 * 
	 * @param int $id		ID of selected faculty
	 */
	public function actionDelete($id) {
		$this->settings = $this->db->table('faculty')->where('id', $id)->fetch();		
		
		if(!$this->settings) {
			throw new NBadRequestException;
		}
		
		try {
			//perform delete action via transaction
			$this->db->beginTransaction();	

			//delete all realted institutes - and recalculate all related projects
			foreach($this->faculty->related('institute') as $institute) {
				$projects = $this->db->table('project_institute')
								->where('institute_id', $institute->id)
								->select('DISTINCT project_id AS id');
				
				foreach($projects as $project) {
					$project_ids[] = $project->id;
				}		
				
				$this->db->table('institute')->where('id', $institute->id)->delete();	
			}
			
			foreach($project_ids as $project_id) {
				$this->calculateProjectData($project_id);
			}
			
			//delete faculty
			$result = $this->db->table('faculty')->where('id', $this->faculty->id)->delete();
			
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
		
		$form->addGroup();
		$form->addText('money', 'Financie')
				->addRule(NForm::FILLED, 'Musíte zadať financie.')
				->getControlPrototype()
					->class('w350');
		$form->addText('students', 'Počet študentov')
				->addRule(NForm::FILLED, 'Musíte zadať počet študentov.');
		
		$form->setCurrentGroup(NULL);
		$form->addSubmit('process', 'Ulož')
				->getControlPrototype()
					->class('design');
		$form->addSubmit('back', 'Naspäť')
				->setValidationScope(NULL)
				->getControlPrototype()
					->class('design');
		
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
				if($this->settings) {
					$this->db->table('school')->where('id', $this->settings->id)->update($values);
					$this->flashMessage('Zdroje boli úspešne zmenené.', 'ok');
				} else {
					$this->db->table('faculty')->insert($values);
					$this->flashMessage('Zdroje boli úspešne pridané .', 'ok');
				}
			} catch (PDOException $e) {
				$this->flashMessage('Pri ukladaní dát do db nastala chyba.', 'error');
			}
		}
			$this->redirect('default');	
	}
}
