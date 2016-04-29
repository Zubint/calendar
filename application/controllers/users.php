<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller 
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model('User');
		date_default_timezone_set('America/chicago');

	}

	public function index()
	{
		$this->load->view('/users/index');
	}


	public function logout()
	{
		$this->session->set_userdata('loggedIn', false);
		$this->session->sess_destroy();
		redirect('/');
	}

	public function setSessionData($currentUser)
	{
			// var_dump($currentUser);
			// die();
		$this->session->set_userdata('id', $currentUser['id']);
		$this->session->set_userdata('name',$currentUser['name']);
		$this->session->set_userdata('email',$currentUser['email']);
		$this->session->set_userdata('dob',$currentUser['dob']);
		$this->session->set_userdata('loggedIn', TRUE);
	}

	public function login()
	{
		if (null!==$this->input->post() && !empty($this->input->post()))		
		{
			$userData = array(
				"email" => $this->input->post('login_email'),
				"password" => $this->input->post('login_password')
				);

			if ($this->User->validateLogin($userData)===true){
				// echo ("true");
				// die();
				$currentUser = $this->User->authenticate($userData);

				if ($currentUser !=false){

					$this->setSessionData($currentUser);
					redirect('/appointments/index');
				}
				else
				{
					$this->session->set_flashdata('login_error', "<p class='red'> Invalid username/password </p>");
					// redirect('/main/index');
					redirect('/');
				}
			}
			else{
				// echo ("false");
				$this->session->set_flashdata('login_error', "<p class='red'> Invalid username/password </p>");
				// redirect('/main/index');
				redirect('/');
			}
		}
		else
		{
			//nothing is posted.
			// return to the login screen
			$this->session->set_flashdata('login_error', "<p class='red'> Please enter your login information.  Both fields are required. </p>");
			redirect('/');
		}
	}


	public function dob_check($dob)
	{
		
		if(strtotime($this->input->post('dob'))===false)
		{
			$this->session->set_flashdata('dob', "<p class='red'>The date of birth not be blank</p>");
			return false;
		}

	}
		public function isFutureDate($strDate)
	{
		//returns true if the date provided is in the future
		//compared to today.
		$today = date('Y-m-d');
		$today= new DateTime($today);   //to compare, cast these as date objects
		$date = new DateTime($strDate); // compare dates without times to ensure the difference in 'days' will be measured.

		// var_dump($today);

		$difference = $today->diff($date);
		$daysDifference = $difference->format('%R%a');  //returns number of days with +/- indicator, difference is based on

		if ($daysDifference > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function newUser()
	{

		//call back validation can only be run in the controller.
		// $this->form_validation->set_rules("dob", "date of birth", 'callback_dob_check');
		
		if($this->input->post() !=false)
		{
			$userData = array(
				"name" => $this->input->post('name'),
				"email" => $this->input->post('email'),
				"dob" => $this->input->post('dob'),
				"password" => $this->input->post('password'),
				"conf_password" => $this->input->post('conf_password')
				);

				if ($this->dob_check($this->input->post('dob'))===false)
				{
					redirect('/');
				}

				if ($this->isFutureDate($this->input->post('dob'))===true)
				{
					$this->form_validation->set_message("dob", " Birthday cannot be in the future");
					$this->session->set_flashdata("dob", "<p class='red'> Birthday cannot be in the future</p>");
					redirect('/');
				}

				if ($this->User->validateNewUser($userData)==false){
					$this->load->view('/main/index');
					//return to the registrations page to show
					//validation errors.
				}
				else
				{
					//validation is successful write the record and redirect to the dashboard
					
					$currentUser = $this->User->createNewUser($userData); //write the data to the database

					if ($currentUser != false)
						{
							//you have the ID of the user -
							// var_dump($currentUser);
							
							$currentUser['dob'] = $currentUser['birth_date'];
							 //the database field name is birth_date, and this is returned from a get function.  In order to use set session you need to have an index 'dob';
							$this->setSessionData($currentUser);
							$this->load->view('/appointments/index');
							
						}
						else
						{
							//problem writing to db
							$this->session->set_flashdata('unknown_error', "There was a problem connecting to the database.  Please try again later.");
							redirect('/main/index');
							
						}
				}
		}
		else
		{
			$this->session->set_flashdata('unknown_error', "There was a problem submitting your information to our servers.  Please try again later.");
				redirect('/main/index');
			//this is an issue with not getting POST data for one reason or another.	
		}
	}
}


/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */