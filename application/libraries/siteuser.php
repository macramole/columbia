<?php 
/*
 Mâgico
 http://www.parleboo.com
 Copyright 2012 Leandro Garber <leandrogarber@gmail.com>
 Attribution-NonCommercial-ShareAlike 3.0 Unported (CC BY-NC-SA 3.0)
*/

if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed.' );

class SiteUser {
    
	const LOGGED_IN = 1;
	const NOT_LOGGED_IN = 2;
	const NOT_ACTIVE = 3;
	const NOT_LOGGED_IN_INVALID_PASS = 4;
	const ALREADY_LOGGED_IN = 5;
	
	const USER_SESSION = 'usuario';
	const SECRET = '_45g294B|B`*#/?#@er87';
	const TIPO_ALUMNO = 1;
	const TIPO_PROFESOR = 2;
	
	const AUTOLOGIN_COOKIE_NAME = 'columbiaAutologin';
	
	private $ci;
	private $buffer = array();
	
	function __construct()
	{
		$this->ci =& get_instance();
		
		if ( $this->isLogged() )
		{
			$this->fillBuffer();
		}
	}
	
	private function fillBuffer()
	{
		$sql = "
			SELECT
				*
			FROM
				alumnos
			WHERE
				MD5(CONCAT(id,?)) = ?
			LIMIT 1
		";
		
		$this->buffer = $this->ci->db->query($sql, array( self::SECRET, $_COOKIE[self::USER_SESSION] ))->row_array();
		
		if ( !$this->buffer )
			$this->logout();
	}
	
	function forceLogin($idAlumno)
	{
		$user = $this->ci->db->get_where('alumnos', array('id' => $idAlumno))->row_array();
		
		if ( $user )
		{
			$this->setUserData($user['id']);
		}
	}
	
	function login($user, $pass, $remember = false, $needMd5 = true)
	{
		if ( $needMd5 )
			$pass = md5($pass);
		
		$sql = "SELECT * FROM alumnos WHERE email=? LIMIT 1";
		$user = $this->ci->db->query($sql,array($user))->row_array();
		
		if ($user)
		{
			if ( $user['activo'] )
			{
				if ( $user['pass'] == $pass )
				{
					$this->setUserData($user['id']);

					$this->ci->db->query( "UPDATE alumnos SET ultimoLogin = NOW() WHERE id = ?", array($user['id']) );

					if ($remember)
					{
						setcookie(self::AUTOLOGIN_COOKIE_NAME, base64_encode($user['email'].':'.$pass), time()+60*60*24*30, '/'); //30 días
					}

					return self::LOGGED_IN;	
				}
				else {
					return self::NOT_LOGGED_IN_INVALID_PASS;
				}
			}
			else
				return self::NOT_ACTIVE;
			
		}
		else
		{
			return self::NOT_LOGGED_IN;
		}
	}
	
	public function setUserData($id)
	{
		setcookie(self::USER_SESSION, md5($id . self::SECRET) , 0, '/');
	}
	
	public function getUserData($key = null)
	{
		if ( $this->isLogged() )
		{
			$user = null;
			
			if ( !count($this->buffer) )
				$this->fillBuffer();
			
			$user = $this->buffer;
			
			if ( $key )
				return $user[$key];
			else
				return $user;
		}
		else
			return null;
	}
	
	public function isLogged()
	{
		return isset($_COOKIE[self::USER_SESSION]);
	}
	
	function cookieCheck()
	{
		if ( !$this->isLogged() )
		{
			if ( $_COOKIE[self::AUTOLOGIN_COOKIE_NAME] )
			{
				$autoLogin = base64_decode($_COOKIE[self::AUTOLOGIN_COOKIE_NAME]);
				list($user, $pass) = explode(':',$autoLogin);

				return $this->login($user, $pass, false, false);
			}

			return self::NOT_LOGGED_IN;
		}
		
		return self::ALREADY_LOGGED_IN;
	}
	
	function logout()
	{
		setcookie(self::USER_SESSION,'',time() - 3600, '/');
		setcookie(self::AUTOLOGIN_COOKIE_NAME,'',time() - 3600, '/');
	}
	
	public function resetPassword($userID)
	{
		$newPass = $this->generatePassword();
		$this->ci->db->update('alumnos', array('pass' => md5($newPass) ), array('id' => $userID) );
		
		return $newPass;
	}
	
	private function generatePassword()
	{
		$chars = "abcdefghijkmnopqrstuvwxyz023456789";
		srand((double)microtime()*1000000);
		
		$i = 0;
		$pass = '' ;

		while ($i <= 6) {

			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
			$i++;
		}
		return $pass;
	}
}
?>
