<?php

/**
 *  Base presenter class
 * 
 *  @author Samuel Kelemen
 */    
abstract class BasePresenter extends NPresenter
{
	/** @var object		BaseModel instance */
	public $db;
	
	
	public $aStates = array(
		'2',	// Schválený
		'3',	// Pripravený na realizáciu
		'4',	// V realizácii
		'5',	// Úspešne ukončený
		'6',	// Dočaste zastavený
		'7'		// Neúspešne ukončený
	);
	
    public function startup() {
		parent::startup();
		
		$this->db = $this->model('BaseModel')->getDBConnection();
		$this->template->title = 'Plánovací alokačný systém';
	}
	
	
	public function model($model_name) {
        return $this->context->LoadModel->getModel($model_name);
    }
	
	
	
	
	/**
	 * Function convert NTableSelection object to array.
	 * Only one level !
	 * 
	 * @param NTableSelection $object
	 * @return array
	 */
	public function objectToArray(NTableSelection $object) {
		$result = array();
		$temp = array();
		
		foreach($object as $row) {
			$columns = array_keys(iterator_to_array($row));	
			break;
		}
		
		foreach($object as $row) {
			foreach($columns as $column) {
				$temp[$column] = $row->$column;
			}
			$result[] = $temp;
		}
		
		return $result;
	}
	
}
