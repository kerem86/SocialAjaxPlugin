<?php

include '../db/db.php';
include 'User.php';

error_reporting(E_ALL);
ini_set('display_errors', '1');

global $user;

if (isset($_GET['s']))
{
	$service = $_GET['s'];

	switch ($service) {
		case 'facebook':
			$user = new FacebookUser($_POST);
			break;
		case 'google':
			$user = new GoogleUser($_POST);
			break;
		default:
			echo json_encode(array('response' => 'error'));
			exit;
	}

	$user->checkLogin();
}
