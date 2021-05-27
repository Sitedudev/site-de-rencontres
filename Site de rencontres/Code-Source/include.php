<?php
	/**
	 * @package			: Code source rencontres
	 * @version			: 0.7
	 * @author			: sitedudev aka clouder
	 * @link 			: https://sitedudev.com
	 * @since			: 2021
	 * @license			: Attribution-NomCommercial-ShareAlike 4.0 International (CC BY-NC-SA 4.0)
	 */
	
	session_start();
	
	include ('bd/connexionDB.php');
	include ('_function/domaine.php');
	include ('_function/guid.php');
	include ('_function/password.php');
	include ('_function/user.php');
	include ('_function/online.php');
	include ('_function/visiteur.php');
	include ('_function/mail.php');
	include ('_function/insulte.php');
	include ('_function/notification.php');
	include ('_function/time.php');
	
	include('library/vendor/autoload.php');
	
	use \Defuse\Crypto\Crypto;
	use \Defuse\Crypto\Key;
	
	$__Secret__key = "votreclé";
	
	$__Domain 			= new Domain;
	$__Crypto 			= new Crypto;
	$__Crypt_password 	= new Password;
	$__Visiteur 		= new Visiteur;
	$__User				= new User;
	$__Online			= new Online;
	$__Notification 	= new Notification;
	$__GUID 			= new Guid;
	$__Email			= new Email;
	$__Insulte			= new Insulte;
	$__Time				= new Time;
	
	define("URL", $__Domain->domain());
	define("CURRENT_URL",  $__Domain->current_url());
	
	$__Visiteur->check_visiteur();
	
	$__Online->online();
	
?>