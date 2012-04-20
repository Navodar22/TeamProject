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

                
                
                $faculties = $this->db->table('faculty');
		$result = null;


                foreach($faculties as $faculty) {
                        $result[$faculty->id] = $this->db->table('institute')->where('faculty.id', $faculty->id)->select('
                                sum(institute.students) AS total_students,
                                sum(institute.money) AS total_money
                                ')->fetch();
                        $result[$faculty->id]['name'] = $faculty->name;
                        $result[$faculty->id]['acronym'] = $faculty->acronym;
                }



                $db_total_data = $this->db->table('project');


		$this->template->school_data = $result;
                $this->template->schools = $this->db->table('school');
            }else{
                $this->redirect('Homepage:');
            }
		
	} 



        /**
	 * Faculty render function.
	 *
	 * @param int $id		ID of selected faculty
	 */
	public function actionFaculty($id) {
		$db_faculty = $this->db->table('faculty')->where('id', $id)->fetch();


                $faculty_date = $this->db->table('institute')->where('faculty.id', $db_faculty->id);


                foreach($db_faculty->related('institute') as $institute) {
                $institutes[$institute->id] = $this->db->table('institute')->where('id', $institute->id)->select('
                                money AS money,
                                students AS students
                                ')->fetch();
                        $institutes[$institute->id]['name'] = $institute->name;
                        $institutes[$institute->id]['acronym'] = $institute->acronym;
                }
		

		 $this->template->institutes = $institutes;
		 $this->template->faculty = $db_faculty;
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
			//$this->calculateMoney();
			$this->flashMessage('Fakulta bola odstránená', 'ok');
		} else {
			$this->flashMessage('Fakultu sa nepodarilo odstrániť', 'error');
		}
		$this->redirect('default');
	}


        /**
	 * Edit action function.
	 * Edit selected faculty.
	 *
	 * @param int $id		ID of editing faculty
	 */
	public function actionEditInstitute($id) {
		$this->settings = $this->db->table('institute')->where('id', $id)->fetch();



		$this['saveFormInstitute']->setDefaults($this->settings);
                $this->template->institute = $this->settings;
	}
	
	
	
	
	/**
	 * Save form create function
	 * 
	 * @return NAppForm 
	 */
	public function createComponentSaveFormInstitute() {
		$form = new NAppForm();
		
		$form->addGroup();
		$form->addText('money', 'Financieee')
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
		
		$form->onSuccess[] = callback($this, 'addInstituteFormSubmitted');
		
		return $form;
	}
	
	
	
	
	/**
	 * Save form submit callback
	 * 
	 * @param type $form 
	 */
	public function addInstituteFormSubmitted($form) {
		if($form['process']->isSubmittedBy()) {
			$values = $form->values;
			try {

                                if ( $this->checkMoney( $this->settings->id, $values['money'] ) ) {
                                
                                    if($this->settings) {
                                            $this->db->table('institute')->where('id', $this->settings->id)->update($values);
                                            $this->flashMessage('Zdroje boli úspešne zmenené.', 'ok');

                                    } else {
                                            $this->db->table('faculty')->insert($values);
                                            $this->flashMessage('Zdroje boli úspešne pridané .', 'ok');
                                    }
                                }else{
                                    $this->flashMessage('Prekročený rozpočet.', 'error');
                                }
			} catch (PDOException $e) {
				$this->flashMessage('Pri ukladaní dát do db nastala chyba.', 'error');
			}
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
