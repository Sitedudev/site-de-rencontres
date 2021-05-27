<?php
	/**
	 * @package			: Code source rencontres
	 * @version			: 1.0
	 * @author			: sitedudev aka clouder
	 * @link 			: https://sitedudev.com
	 * @since			: 2021
	 * @license			: Attribution-NomCommercial-ShareAlike 4.0 International (CC BY-NC-SA 4.0)
	 */
	include_once('include.php');
	
	session_destroy();
	
	header('Location: ' . URL);
	exit;