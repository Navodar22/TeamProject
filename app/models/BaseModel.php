<?php 

/**
 *  Base model class
 * 
 *  @author Samuel Kelemen  
 */
class BaseModel extends NObject
{
	/** @var  database connection */         
	protected $database;

	/** @var  container */
	protected $context;


	
	
	/** 
	*  Construct function for all models. Initialize db connection
	*  
	*  @var  $container
	*/                
	public function __construct($container)
	{
	$db = ($container->params['database']);
	$dns = $db['driver'].':host='.$db['host'].';dbname='.$db['database'];  
	$this->database = new NConnection($dns, $db['username'], $db['password']);
	$this->context = $container;
	}

	
	
	
	/**
	*  Get function for container in models
	*/       
	public function getContext() {
	return $this->context;
	}

	
	
	
	/** 
	*  Wraper function for DB connection 
	*/      
	public function getDBConnection() {
		return $this->database;
	}         
  
	
	
	
	/**
	*  Autentication service. Registered in config.neon
	*/       
	public function createAuthenticatorService()
	{
		return new Authenticator();
	}
	
	
	
	
	/**
	 *  Static function for hash calculating
	 */   	
	public static function calculateHash($password) {
		return md5($password . str_repeat('I8uhy6th7879GF8GGdytr986h', 10));
	}
}
