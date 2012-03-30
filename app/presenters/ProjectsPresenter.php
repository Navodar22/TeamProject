<?php

/**
 * Homepage presenter.
 *
 * @author     John Doe
 * @package    MyApplication
 */
class ProjectsPresenter extends BaseLPresenter
{
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
		
		$values['start'] = new DateTime($values['start']);
		$values['end'] = new DateTime($values['end']);
		
		if($values['start'] >= $values['end']) {
			$this->flashMessage('Projekt nemôže mať ukončenie pred svojím začiatkom.', 'error');
			$error = true;
		} 
		
		if(!$error) {
			$values['user_id'] = '1'; //TODO actual loged user id - ldap
			$values = $this->unsetEmpty($values);

			$test = $this->model('Project')->getTable()->insert($values);

			$this->flashMessage('Projekt bol úspešne vytvorený.', 'ok');
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
