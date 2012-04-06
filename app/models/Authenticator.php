<?php
 
 /**
 * Users authenticator.
 *
 * @author     John Doe
 * @package    MyApplication
 */
class Authenticator extends NObject implements IAuthenticator
{
	/** @var TableSelection */
	private $users = array(
		'normal' => array(
			'username' => 'normal',
			'password' => 'normal'
		),
		'admin' => array(
			'username' => 'admin',
			'password' => 'admin',
		)
	);
	
	

 	public function __construct()
	{
		
	}
  
	/**
	 * Performs an authentication
	 * @param  array
	 * @return Identity
	 * @throws AuthenticationException
	 */
	public function authenticate(array $credentials)
	{         
		/* ldap login
		list($username, $password) = $credentials;
                $user = json_decode(file_get_contents("http://vmsk50774.fei.stuba.sk/ldap/ldap.php?user=$username&pass=$password"));
		
		if (!$user) {
			throw new NAuthenticationException("Login failed.", self::IDENTITY_NOT_FOUND);
		}

                $data = array(
                        'name' => $user->name,
                        'role' => $user->role
                );
		return new NIdentity('1', $user->role, $data);		
		}
		*/
		
		list($username, $password) = $credentials;
        
		$auth_user = false;
		foreach($this->users as $user) {
			if(($user['username'] == $username) && ($user['password'] == $password)) {
				$auth_user = array(
					'name' => $user['username'],
					'role' => $user['username']
				);
			}
		}
		
		if (!$auth_user) {
			throw new NAuthenticationException("Neplatné prihlasovacie údaje.", self::IDENTITY_NOT_FOUND);
		}
		
		return new NIdentity('1', $auth_user['role'], $auth_user);	
	}
}
