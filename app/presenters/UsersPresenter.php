<?php

/**
 * GUI for Acl
 *
 * @copyright  Copyright (c) 2010 Tomas Marcanik
 * @package    GUI for Acl
 */

//namespace AclModule;

use Nette\Application\UI\Form;

/**
 * Presenter for user management
 *
 */
class UsersPresenter extends BaseLPresenter{

    /** @var string */
    private $search = '';

    public $faculties;
    public $institutes;
    
    /**
     * Init method
     */
    public function startup(){
        parent::startup();
        $this -> checkAccess();
                
        $this->faculties = $this->getFaculties();
        $this->institutes = $this->getInstitutes();
//        $this->institute = $this->get;
    }

    /*     * ****************
     * Default
     * **************** */

    public function renderDefault(){
        $form = $this -> getComponent('search');
        $this -> template -> form = $form;
        $users_data = array();

        // paginator
        $vp = new \VisualPaginator($this,'vp');
        $paginator = $vp -> getPaginator();
        $paginator -> itemsPerPage = 20;

        $sql = \dibi::query('SELECT id, name FROM ['.TABLE_USERS.'] '.( ! empty($this -> search) ? 'WHERE name LIKE %s ' : '').'ORDER BY name;',$this -> search);
        $paginator -> itemCount = count($sql);
        if( ! empty($this -> search)){ // disable paginator
            $paginator -> itemsPerPage = $paginator -> itemCount;
        }
        $users = $sql -> fetchAll($paginator -> offset,$paginator -> itemsPerPage);
        foreach($users as $user){
            $users_data[$user -> id]['name'] = $user -> name;
            $sql2 = \dibi::query('SELECT r.id, r.name
                                    FROM ['.TABLE_ROLES.'] AS r
                                    JOIN ['.TABLE_USERS_ROLES.'] AS u ON r.id=u.role_id
                                    WHERE u.user_id=%i
                                    ORDER BY r.name;',$user -> id);
            $roles = $sql2 -> fetchAll();
            $users_data[$user -> id]['roles'] = array();
            foreach($roles as $role){
                $users_data[$user -> id]['roles'][$role -> id] = $role -> name;
            }
            
            $sql3 = \dibi::query('SELECT r.id, r.acronym
                                    FROM faculty AS r
                                    JOIN user_faculty AS u ON r.id=u.faculty_id
                                    WHERE u.user_id=%i
                                    ORDER BY r.acronym;',$user -> id);
            $faculties = $sql3 -> fetchAll();
            $users_data[$user -> id]['faculties'] = array();
            foreach($faculties as $faculty){
                $users_data[$user -> id]['faculties'][$faculty -> id] = $faculty -> acronym;
            }
            
            $sql4 = \dibi::query('SELECT r.id, r.acronym
                                    FROM institute AS r
                                    JOIN user_institute AS u ON r.id=u.institute_id
                                    WHERE u.user_id=%i
                                    ORDER BY r.acronym;',$user -> id);
            $institutes = $sql4 -> fetchAll();
            $users_data[$user -> id]['institutes'] = array();
            foreach($institutes as $institute){
                $users_data[$user -> id]['institutes'][$institute -> id] = $institute -> acronym;
            }
            
        }
        
        
        $this -> template -> users = $users_data;
    }

    protected function createComponentSearch($name){
        $form = new NForm;
        //$form->addGroup('Search');
        $form -> addText('name','Name:',30)
                -> addRule(NForm::FILLED,'You have to fill name.');
        $form -> addSubmit('search','Search');
        $form -> onSubmit[] = array($this,'searchOnFormSubmitted');
        return $form;
    }

    public function searchOnFormSubmitted($form){
        $values = $form -> getValues();
        $this -> search = strtr($values['name'],"*","%");
    }

    /*     * ****************
     * Add and Edit
     * **************** */

    public function actionAdd(){
        
    }

    public function actionEdit($id){
        $sql = \dibi::query('SELECT name FROM ['.TABLE_USERS.'] WHERE id=%i;',$id);
        $form = $this -> getComponent('addEdit');
        if(count($sql)){
            $name = $sql -> fetchSingle();
            $sql = \dibi::query('SELECT role_id AS roles FROM ['.TABLE_USERS_ROLES.'] WHERE user_id=%i;',$id);
            $roles = $sql -> fetchPairs();
            
            $sql = \dibi::query('SELECT faculty_id AS faculties FROM user_faculty WHERE user_id=%i;',$id);
            $faculties = $sql -> fetchPairs();
            
            $sql = \dibi::query('SELECT institute_id AS institutes FROM user_institute WHERE user_id=%i;',$id);
            $institutes = $sql -> fetchPairs();
            
            $form -> setDefaults(array('name' => $name,'roles' => $roles, 'faculties' => $faculties, 'institutes' => $institutes ) );
        }
        else
            $form -> addError('This user does not exist.');
    }

    protected function createComponentAddEdit($name){
        $mroles = new \RolesModel();
        $roles = $mroles -> getTreeValues();

        $form = new NAppForm();
		
        $form->addGroup();
        $form->addText('name', 'Meno')
                        ->addRule(NForm::FILLED, 'Musíte zadať meno.')
                        ->getControlPrototype()
                                ->class('w350');
        if($this -> getAction() == 'add'){
            $form -> addPassword('heslo','Heslo',30)
                    -> addRule(NForm::FILLED,'Musíte vyplniť heslo.')
                    ->getControlPrototype()->class('w350');
                    
            $form -> addPassword('heslo2','Heslo znovu',30)
                    -> addRule(NForm::FILLED,'Musíte vyplniť heslo.')
                    -> addRule(NForm::EQUAL,'Heslá sa neyhodujú.',$form['heslo'])
                    ->getControlPrototype()->class('w350');
        }
        
        $form -> addMultiSelect('roles','Role',$roles, 5)
                ->getControlPrototype()->class('w350');
        
        $form -> addMultiSelect('faculties','Fakulta',$this->faculties, 7)
                ->getControlPrototype()->class('w350');
        
        $form -> addMultiSelect('institutes','Ústav',$this->institutes, 10)
                ->getControlPrototype()->class('w350');
        
        
        $form->addSubmit('back', 'Naspäť')
                        ->setValidationScope(NULL)
                        ->getControlPrototype()
                                ->class('design');
        
        
        if($this -> getAction() == 'add'){
            $form -> addSubmit('add','Pridaj')
                    ->getControlPrototype()
                                ->class('design');
        }else{
            $form -> addSubmit('edit','Edit')
                ->getControlPrototype()
                                ->class('design');
        }
        
        
        $form -> onSuccess[] = array($this,'addEditOnFormSubmitted');

        return $form;
        
    }

    public function addEditOnFormSubmitted($form){
        $error = false;
        \dibi::begin();
        // add action
        if( isset($form['add']) )
        if( $form['add'] -> isSubmittedBy()){
            try{
                $values = $form -> getValues();
                $roles = $values['roles'];
                $faculties = $values['faculties'];
                $institutes = $values['institutes'];
                
                unset($values['heslo2'],$values['roles']);
                $values['heslo'] = md5($values['heslo']);
                NDebugger::barDump($values);
                dibi::query('INSERT INTO ['.TABLE_USERS.'] %v',array("name"=>$values["name"], "password"=>$values["heslo"]));
                $user_id = \dibi::getInsertId();
                if(count($roles)){
                    foreach($roles as $role){
                        dibi::query('INSERT INTO ['.TABLE_USERS_ROLES.'] (user_id, role_id) VALUES (%i, %i);',$user_id,$role);
                    }
                }
                
                if(count($faculties)){
                    foreach($faculties as $faculty){
                        dibi::query('INSERT INTO user_faculty (user_id, faculty_id) VALUES (%i, %i);',$user_id,$faculty);
                    }
                }
                
                if(count($institutes)){
                    foreach($institutes as $institute){
                        dibi::query('INSERT INTO user_institute (user_id, institute_id) VALUES (%i, %i);',$user_id,$institute);
                    }
                }
                
                $this -> flashMessage('The user has been added.','ok');
                dibi::commit();
                if(ACL_CACHING){
                    unset($this -> cache['gui_acl']); // invalidate cache
                }
                $this -> redirect('Users:');
            }catch(Exception $e){
                $error = true;
                $form -> addError('Užívateľ nemohol byť pridaný.');
                throw $e;
            }
        }
        
        if( isset($form['edit']) ){
            if( $form['edit'] -> isSubmittedBy()  ){ // edit action
                $id = $this -> getParam('id');
                try{
                    $values = $form -> getValues();
                    $roles = $values['roles'];
                    $faculties = $values['faculties'];
                    $institutes = $values['institutes'];
                    
                    unset($values['roles']);
                    \dibi::query('UPDATE ['.TABLE_USERS.'] SET %a WHERE id=%i;',array("name"=>$values["name"]),$id);
                    \dibi::query('DELETE FROM ['.TABLE_USERS_ROLES.'] WHERE user_id=%i;',$id);
                    if(count($roles)){
                        foreach($roles as $role){
                            \dibi::query('INSERT INTO ['.TABLE_USERS_ROLES.'] (user_id, role_id) VALUES (%i, %i);',$id,$role);
                        }
                    }
                    
                    \dibi::query('DELETE FROM user_faculty WHERE user_id=%i;',$id);
                    if(count($faculties)){
                        foreach($faculties as $faculty){
                            \dibi::query('INSERT INTO user_faculty (user_id, faculty_id) VALUES (%i, %i);',$id,$faculty);
                        }
                    }
                    
                    
                    \dibi::query('DELETE FROM user_institute WHERE user_id=%i;',$id);
                    if(count($institutes)){
                        foreach($institutes as $institute){
                            \dibi::query('INSERT INTO user_institute (user_id, institute_id) VALUES (%i, %i);',$id,$institute);
                        }
                    }
                    
                    
                    $this -> flashMessage('Používateľ bol zmenený.','ok');
                    \dibi::commit();
                    if(ACL_CACHING){
                        unset($this -> cache['gui_acl']); // invalidate cache
                    }
                    $this -> redirect('Users:');
                }catch(Exception $e){
                    $error = true;
                    $form -> addError('The user has not been edited.');
                    throw $e;
                }
            }
        }
        
        if( $form['back'] -> isSubmittedBy() ){
            $this -> redirect('Users:');            
        }

        if($error)
            \dibi::rollback();
    }

    /*     * ****************
     * Delete
     * **************** */

    public function actionDelete($id){
        $sql = dibi::query('SELECT name FROM ['.TABLE_USERS.'] WHERE id=%i;',$id);
        if(count($sql)){
            $this -> template -> user_name = $sql -> fetchSingle();
        }else{
            $this -> flashMessage('This user does not exist.');
            $this -> redirect('Users:');
        }
    }

    protected function createComponentDelete($name){
        $form = new NAppForm();
		
        $form->addGroup();

        $form->setCurrentGroup(NULL);
        $form->addSubmit('process', 'Zmaž')
                        ->getControlPrototype()
                                ->class('design');
        $form->addSubmit('back', 'Naspäť')
                        ->setValidationScope(NULL)
                        ->getControlPrototype()
                                ->class('design');

        $form->onSuccess[] = callback($this, 'deleteOnFormSubmitted');

        return $form;
    }

    public function deleteOnFormSubmitted($form){
        
        if($form['process'] -> isSubmittedBy()){
            try{
                $id = $this -> getParam('id');
                dibi::query('DELETE FROM ['.TABLE_USERS.'] WHERE id=%i;',$id);
                $this -> flashMessage('The user has been deleted.','ok');
                if(ACL_CACHING){
                    unset($this -> cache['gui_acl']); // invalidate cache
                }
                $this -> redirect('Users:');
            }catch(Exception $e){
                $form -> addError('The user has not been deleted.');
                throw $e;
            }
        }
        else
            $this -> redirect('Users:');
    }

    /*     * ****************
     * Access
     * **************** */

    public function actionAccess($id){
        $nodes = new \RolesModel();
        $this -> template -> nodes = $nodes;
        $this -> template -> parents = $nodes -> getChildNodes(NULL);

        $user = \dibi::fetchSingle('SELECT name FROM ['.TABLE_USERS.'] WHERE id=%i;',$id);
        $this -> template -> user_name = $user;

        $roles = \dibi::fetchAll('SELECT r.key_name FROM ['.TABLE_ROLES.'] AS r
                                    RIGHT JOIN ['.TABLE_USERS_ROLES.'] AS ur ON r.id=ur.role_id
                                    WHERE ur.user_id=%i;',$id);

        $access = new \AccessModel($roles);
        $this -> template -> access = $access -> getAccess();
    }
    
    /**
    * Function to get faculties in array - only non deleted
    */
    public function getFaculties() {
            $faculties = $this->db->table('faculty');
            $faculties_array = array();

            foreach($faculties as $faculty) {
                    $faculties_array[$faculty->id] = $faculty->name; 
            }

            return $faculties_array;
    }
    
    /**
    * Function to get faculties in array - only non deleted
    */
    public function getInstitutes() {
            $institutes = $this->db->table('institute');
            $institutes_array = array();

            foreach($institutes as $institute) {
                    $institutes_array[$institute->id] = $institute->name; 
            }

            return $institutes_array;
    }
    

}
