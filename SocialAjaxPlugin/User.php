<?php
class User
{
	public $id = 0;
	public $type = 'user';
	public $field = '';

	public $username = '';
	public $first_name = '';
	public $last_name = '';
	public $email = '';
	public $country = '';

	public function getById() {
		$query = "SELECT * FROM users WHERE ".$this->field." = '".$this->id.'\'';
		$result = mysql_query($query);
		if (!$result) {
			return mysql_fetch_assoc($result);
		} else {
			return null;
		}
	}

	public function getByEmail() {
		$email = $this->email;
		$query = "SELECT * FROM users WHERE email = '$email'";
		$result = mysql_query($query);
		if ($result) {
			return mysql_fetch_assoc($result);
		} else {
			return null;
		}
	}

	public function saveUser($return = true) {
		$id = $this->id;
		$field = $this->field;
		$type = $this->type;
		$username = $this->username;
		$email = $this->email;
		//$password = $this->password;
		$first_name = $this->first_name;
		$last_name = $this->last_name;
		$country = $this->country;
		$query = "INSERT INTO users ($field, username, email, first_name, last_name, country, is_active) VALUES ('$id', '$username', '$email', '$first_name', '$last_name', '$country', 1)";
		if (mysql_query($query)) {
			echo json_encode(array('response' => 'OK'));
		} else {
			echo json_encode(array('response' => $query));
		}
	}

	public function checkLogin() {
		$user = $this->getByEmail();
		if ($user == null) {
			$this->saveUser();
		} else {
			echo json_encode(array('response' => 'OK'));
		}

	}
}

class FacebookUser extends User
{
	function __construct($post_data) {
		$this->id = $post_data['id'];
		$this->type = 'facebook';
		$this->field = 'fbId';

		if (isset($post_data['username'])) $this->username = $post_data['username'];
		if (isset($post_data['first_name'])) $this->first_name = $post_data['first_name'];
		if (isset($post_data['last_name'])) $this->last_name = $post_data['last_name'];
		if (isset($post_data['email'])) $this->email = $post_data['email'];
		if (isset($post_data['country'])) $this->country = $post_data['country'];
	}
}

class GoogleUser extends User
{
	function __construct($post_data) {
		$this->id = $post_data['id'];
		$this->username = $post_data['id'];
		$this->type = 'google';
		$this->field = 'googleId';

		if (isset($post_data['first_name'])) $this->first_name = $post_data['first_name'];
		if (isset($post_data['last_name'])) $this->last_name = $post_data['last_name'];
		if (isset($post_data['email'])) $this->email = $post_data['email'];
		if (isset($post_data['country'])) $this->country = $post_data['country'];
	}
}
