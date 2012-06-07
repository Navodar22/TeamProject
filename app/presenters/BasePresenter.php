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
                
                $db_config = array(
                    'driver'   => 'mysql',
                    'host'     => 'localhost',
                    'username' => 'root',
                    'password' => '',
                    'database' => 'team',
                );
                
                NDebugger::barDump($db_config,"db_config");

//                dibi::connect(get_object_vars($db_config));

                dibi::connect($db_config);
                
                define('TABLE_ACL', 'gui_acl');
                define('TABLE_PRIVILEGES','gui_acl_privileges');
                define('TABLE_RESOURCES','gui_acl_resources');
                define('TABLE_ROLES','gui_acl_roles');
                define('TABLE_USERS','gui_users');
                define('TABLE_USERS_ROLES','gui_users_roles');

                define('ACL_RESOURCE','acl_permission');
                define('ACL_PRIVILEGE','acl_access');
                define('ACL_CACHING',false);
                define('ACL_PROG_MODE',true);
		
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
