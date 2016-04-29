<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters("<p class='red'>","</p>");
		date_default_timezone_set('America/chicago');
	}

	public function getUserByEmail($email)
	{
		return $this->db->query("SELECT * FROM users WHERE EMAIL=?", array("email"=>$email))->row_array();
	}

	public function validateLogin($userData)
	{
		$this->form_validation->set_rules("login_email", "email address", "required|valid_email|trim");
		$this->form_validation->set_rules("login_password", "password", "required|trim");


		if ($this->form_validation->run() === true ){
			return true;

		}
		else
		{
			return false;
		}
	}

	public function authenticate($userData)
	{

		$user = $this->User->getUserByEmail($userData['email']);

		if (count($user)>0)
		{
			// var_dump($user);
			// die();
			$passwordCompare = md5($user['salt'] + $userData['password']);

			if ($passwordCompare == $user['pwd'])
			{
				// echo("success");
				// die();
				$userData = array(

					"id"=> $user['id'],
					"name"=>$user['name'],
					"dob" => $user['birth_date'],
					"email"=>$user['email']
					);

				return $userData;
			}
			else{

				$this->form_validation->set_message("login_error", "Invalid username or password");
				return false;
				// echo ("problem");
				// die();
			}
		}
		else
		{
			$this->form_validation->set_message("login_error", "Invalid username or password");
			return false; //username error
		}

	}
	public function validateNewUser($data)
	{

		// var_dump($data);
		// die();

		//validate full name
		$this->form_validation->set_rules("name", "name", "required|trim");
		$this->form_validation->set_rules("email", "email", "required|valid_email|is_unique[users.email]trim");
		$this->form_validation->set_rules("password", "password", "required|min_length[8]|trim");
		$this->form_validation->set_rules("conf_password", "confirmation password", "required|matches[password]");


			if ($this->form_validation->run()===false)
			{
				return false;
			} 
			else
			{
				return true;
			}	
	}

	public function getUserById($id)
	{
		return $this->db->query("SELECT * FROM users WHERE id=?", array("id"=>$id))->row_array();

	}

	public function createNewUser($data)
	{
		//use this information to create a new user and
		//return the user ID & information to set session data if successful
		//otherwise, return false
			$query = "INSERT INTO users (name, email,birth_date, pwd, salt, created_at, updated_at)
					   VALUES (?,?,?,?,?,?,?)";
			$salt = openssl_random_pseudo_bytes(255);
			$saltedPassword =  $salt + $data['password'];
			$encryptedPassword = md5($saltedPassword);


			$values = array(
				"name"=>$data['name'],
				"email"=>$data['email'],
				"birthd_date"=>$data['dob'],
				"pwd"=>$encryptedPassword,
				"salt"=>$salt,
				"created_at"=> date('Y-m-d H:i:s'),
				"updated_at"=> date('Y-m-d H:i:s')
				);

			if ($this->db->query($query,$values)!=false){
					//you have written the data
					//get the user id
					//send data back to the controler
					//to send to the view
					return $this->User->getUserById($this->db->insert_id());
			}
			else
			{
				//there was an issue writing data, so return false
				return false;
			}
	}

}	