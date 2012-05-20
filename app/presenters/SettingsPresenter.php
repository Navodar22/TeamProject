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
	
	
	
	
	/** 
	 * Default render function.
	 * Render all faculties in system
	 */
	public function renderDefault()
	{
            $user = $this->getUser()->getIdentity();
                
            if( $user->privileges[0] | $user->privileges[1] | $user->privileges[2] | $user->privileges[3] ){                
                
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
            } else {
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
			
			if(!$db_faculty) {
				throw new NBadRequestException();
			}

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
	 * Function to edit school data
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
	 * Function to edit institute data
	 *
	 * @param int $id		ID of editing faculty
	 */
	public function actionEditInstitute($id) {
		$this->settings = $this->db->table('institute')->where('id', $id)->fetch();

		if(!$this->settings) {
			throw new NBadRequestException;
		}

		$this['saveFormInstitute']->setDefaults($this->settings);
        $this->template->institute = $this->settings;
	}
	
	
	
		
	/**
	 * Save form for institute data
	 * 
	 * @return NAppForm 
	 */
	public function createComponentSaveFormInstitute() {
		$form = new NAppForm();
		
		$form->addGroup();
		$form->addText('money', 'Volné finančné zdroje')
				->addRule(NForm::FILLED, 'Musíte vyplniť volné finančné zdroje.')
				->addRule(NForm::FLOAT, 'Volné finančné zdroje musia byť číslo')
				->addRule(NForm::RANGE, 'Volné finančné zdroje musia byť kladné číslo', array(0, 9999999999))
				->getControlPrototype()
					->class('w350');
		$form->addText('students', 'Počet študentov')
				->addRule(NForm::FILLED, 'Musíte zadať počet študentov.')
				->addRule(NForm::FLOAT, 'Počet študentov musí byť číslo')
				->addRule(NForm::RANGE, 'Počet študentov musí byť kladné číslo', array(0, 9999999999));
		
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
	 * Save function for institute data
	 * 
	 * @param type $form 
	 */
	public function addInstituteFormSubmitted($form) {
		if($form['process']->isSubmittedBy()) {
			$values = $form->values;
			try {				
				if ($this->checkResources($this->settings->id, $values)) {

					if($this->settings) {
							$this->db->table('institute')->where('id', $this->settings->id)->update($values);
							$this->flashMessage('Zdroje boli úspešne zmenené.', 'ok');

					} else {
							$this->db->table('faculty')->insert($values);
							$this->flashMessage('Zdroje boli úspešne pridané .', 'ok');
					}
					
					$this->redirect('default');	
				} else {
					$this->flashMessage('Prekročený rozpočet alebo počet študentov.', 'error');
				}
			} catch (PDOException $e) {
				NDebugger::log($e);
				$this->flashMessage('Pri ukladaní dát do db nastala chyba.', 'error');
				$this->redirect('default');	
			}
		}
	}


	
	
	/**
	* Save form for school data
	*
	* @return NAppForm
	*/
	public function createComponentSaveForm() {
		$form = new NAppForm();

		$form->addGroup();
		$form->addText('money', 'Volné finančné zdroje')
				->addRule(NForm::FILLED, 'Musíte zadať volné finančné zdroje')
				->addRule(NForm::FLOAT, 'Volné finančné zdroje musia byť číslo')
				->addRule(NForm::RANGE, 'Volné finančné zdroje musia byť kladné číslo', array(0, 9999999999))
				->getControlPrototype()
					->class('w350');
		$form->addText('students', 'Počet študentov')
				->addRule(NForm::FILLED, 'Musíte zadať počet študentov.')
				->addRule(NForm::FLOAT, 'Počet študentov musí byť číslo')
				->addRule(NForm::RANGE, 'Počet študentov musí byť kladné číslo', array(0, 9999999999));

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
