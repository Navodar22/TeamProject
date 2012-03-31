<?php

/**
 * Homepage presenter.
 *
 * @author     John Doe
 * @package    MyApplication
 */
class ProjectsPresenter extends BaseLPresenter
{
	
	public $faculty_institute;
	
	
	public function startup() {
		parent::startup();
		
		$faculties = $this->db->table('faculty')->where('del', FALSE);
		$result = array();
		
		foreach($faculties as $faculty) {
			foreach($faculty->related('institute')->where('del', FALSE) as $institute) {
				$result[$faculty->id . '-' . $institute->id] = $faculty->name . ' - ' . $institute->name; 
			}			
		}
		
		$this->faculty_institute = $result;
	}
	
	public function renderDefault()
	{
		
	} 
	
	
	public function createComponentAddForm() {
		$form = new NAppForm();
		
		$form->addText('name', 'Názov projektu')
				->addRule(NForm::FILLED, 'Musíte zadať názov projektu.');
		$form->addTextArea('description', 'Popis projektu', '50','5')
				->addRule(NForm::FILLED, 'Musíte zadať popis projektu.');
		$form->addTextArea('note', 'Poznámka', '50', '5');
		$form->addMultiSelect('faculty_institute', 'Fakulta', $this->faculty_institute);
		$form->addText('cost', 'Celková cena projektu')
				->addRule(NForm::FILLED, 'Musíte zadať cenu projektu')
				->addRule(NForm::FLOAT, 'Cena projektu musí byť číslo');
		$form->addText('start', 'Začiatok projektu')
				->addRule(NForm::FILLED, 'Musíte vyplniť začiatok projektu.')
				->getControlPrototype()->class('datepicker');
		$form->addText('end', 'Koniec projektu')
				->addRUle(NForm::FILLED, 'Musíte vyplniť koniec projektu.')
				->getControlPrototype()->class('datepicker');
		
		$form->addSubmit('process', 'Ulož');
		$form->addSubmit('back', 'Naspäť')
				->setValidationScope(NULL);
		
		$form->onSuccess[] = callback($this, 'addFormSubmitted');
		
		return $form;
	}
	
	public function addFormSubmitted($form) {
		$values = $form->values;
		
		$error = false;
		$values['start'] = new DateTime($values['start']);
		$values['end'] = new DateTime($values['end']);
		
		if($values['start'] >= $values['end']) {
			$this->flashMessage('Projekt nemôže mať ukončenie pred svojím začiatkom.', 'error');
			$error = true;
		} 
		
		try {
			if(!$error) {

				$this->db->beginTransaction();

					$values['user_id'] = '1'; //TODO actual loged user id - ldap
					$values = $this->unsetEmpty($values);
					
					$faculty_institute = $values['faculty_institute'];
					unset($values['faculty_institute']);

					$this->db->table('project')->insert($values);

					$project_id = $this->db->lastInsertId();

					foreach($faculty_institute as $key => $value) {
						$explode = explode('-', $value);

						$pair['project_id'] = $project_id;
						$pair['institute_id'] = $explode[1];

						$this->db->table('project_x_institute')->insert($pair);
					}
				$this->db->commit();

				$this->flashMessage('Projekt bol úspešne vytvorený.', 'ok');
				$this->redirect('this');
			}
		} catch (PDOException $e) {
			$this->db->rollback();

			NDebugger::log($e, NDebugger::ERROR);
			$this->flashMessage('Projekt sa nepodarilo vytvoriť.', 'error');
			$this->redirect('this');
		}
		
	}
	
	
	public function unsetEmpty($values) {
		foreach($values as $key => $value) {
			if(empty($value)) {
				unset($values[$key]);
			}
		}
		
		return $values;
	}
}
