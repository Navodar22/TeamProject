<?php

/**
 *  Base presenter class
 * 
 *  @author Samuel Kelemen
 */    
abstract class BaseLPresenter extends BasePresenter
{
	
	public $dateRange;
        public $cache;
        public $oldLayoutMode = FALSE;
        public $oldModuleMode = FALSE;
        
//	private $user;
	
	
    public function startup() {
        parent::startup();

        $this->template->prog_mode = (ACL_PROG_MODE ? true : false);

        // cache
        if (ACL_CACHING) {
            $this->cache = \Nette\Environment::getCache();
            if (!isset($this->cache['gui_acl'])) {
                $this->cache->save('gui_acl', new Acl(), array(
                    'files' => array(APP_DIR . '/config.ini'),
                ));
            }
            $this->user->setAuthorizator($this->cache['gui_acl']);
        }
        else
            $this->user->setAuthorizator(new Acl());
        
        
        if (!$this->user->isLoggedIn()) {
            $this->redirect(':Sign:in');
        } else {
//           $this -> template -> identity = $this -> user -> getIdentity(); 

            $this->template->user = $this->getUser()->getIdentity();
            
            $this->template->user_object = $this->user;

            // po uprave rolest v settings asi nepotrebne
            $this->template->current = $this->getPresenter()->getName();

            $this->dateRange = $this->getDateRange();
            $this->template->dateRange = $this->dateRange;

            $this->template->registerHelper('emptyPrice', callback(new EmptyPrice, 'process'));
            $this->template->registerHelper('emptyNumber', callback(new EmptyNumber, 'process'));
            $this->template->registerHelper('emptyDate', callback(new EmptyDate, 'process'));			
            $this->template->registerHelper('zeroPrice', callback(new ZeroPrice, 'process'));
        }
    }
	
	
	
	public function getStatuses() {
		//implement return statuses array by role
	}
	
	
	
	public function calculateMoney() {
		$total_students = 0;
		foreach($this->db->table('institute') as $institute) {
			$total_students += $institute->students;
		}

		$this->db->table('school')->update(array('students' => $total_students));
		$school = $this->db->table('school')->where('id', '1')->fetch();

		if($school->students <= 0) {
			$money_index = 0;
		} else {
			$money_index = $school->money/$school->students;
		}

		foreach($this->db->table('institute') as $institute) {
			$institute_money = $institute->students * $money_index;
			$institute->update(array('money' => $institute_money));
		}	
	}

	
	
	
    public function checkResources($id, $values){
		$school = $this->db->table('school')->where('id', '1')->fetch();
		
		$used_resources = $this->db->table('institute')
									->where('id <> ?', $id)
									->select('
										SUM(money) AS money,
										SUM(students) AS students
									')->fetch();
		
		$used_resources['money'] += $values['money'];
		$used_resources['students'] += $values['students'];

		if($used_resources['money'] <= $school['money']  && $used_resources['students'] <= $school['students']) {
			return true;
		}

		return false;            
    }
	
	
	
	
	/**
	 * 
	 */
	public function calculateProjectData($id) {
		$values = $this->db->table('project_institute')->where('project_id', $id)->select('
			sum(project_institute.cost) AS cost,
			sum(project_institute.hr) AS hr,
			sum(project_institute.participation) AS participation,
			sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute.cost, 0)) AS approved_cost,
			sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute.hr, 0)) AS approved_hr,
			sum(IF(project_institute.state_id IN (' . implode(',', $this->aStates) . '), project_institute.participation, 0)) AS approved_participation,
			min(project_institute.start) AS start,
			max(project_institute.end) AS end,
			min(CASE WHEN project_institute.state_id IN (' . implode(',', $this->aStates) . ') THEN project_institute.start ELSE NULL END) AS approved_start,
			max(CASE WHEN project_institute.state_id IN (' . implode(',', $this->aStates) . ') THEN project_institute.end ELSE NULL END) AS approved_end
		')->fetch();
		
		$this->db->table('project')->where('id', $id)->update($values);
	}
	
	
	
	
	public function getDateRange() {
		$session = $this->context->session;
		
		if($session->hasSection('dateRange')) {
			$dateRange = $session->getSection('dateRange');
		} else {
			$dateRange = NULL;
		}
		
		return $dateRange;
	}
        
    /**
     * Check if the user has permissions to enter this section.
     * If not, then it is redirected.
     *
     */
    public function checkAccess() {
        // if the user is not allowed access to the ACL, then redirect him
        if (!$this->user->isAllowed(ACL_RESOURCE, ACL_PRIVILEGE)) {
            // @todo change redirect to login page
            $this->redirect('Denied:');
        }
    }
    
    
    /**
     * Check if the user has permissions to enter this faculty.
     * If not, then it is redirected.
     *
     */    
    public function allowFaculty( $faculty_id ){
        $sql = \dibi::query('SELECT Count(id) as Pocet FROM user_faculty
                                    WHERE user_id = %i and faculty_id = %i;',$this->user->getId(), $faculty_id  );
        $count = $sql->fetchSingle();
        
        if( $count > 0 ){
            return true;
        }else{
            return false;
        }        
    }
    
    /**
     * Check if the user has permissions to enter this institute.
     * If not, then it is redirected.
     *
     */    
    public function allowInstitute( $institute_id ){
        $sql = \dibi::query('SELECT Count(id) as Pocet FROM user_institute
                                    WHERE user_id = %i and institute_id = %i;',$this->user->getId(), $institute_id  );
        $count = $sql->fetchSingle();
        
        if( $count > 0 ){
            return true;
        }else{
            return false;
        } 
        
        
        
    }
    
    
    
    
}
