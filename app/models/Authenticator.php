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
	private $users;

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
}
