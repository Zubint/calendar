<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Appointment extends CI_Model
{

	public function __construct(){

		parent:: __construct();
		date_default_timezone_set('America/chicago');
	}

	public function getAppointmentById($id)
	{
		$query = ("Select * from appointments where id=? ORDER by date asc");
		$value = array ("id"=>$id);

		return $this->db->query($query, $value)->row_array();

	}
	public function deleteAppointment($appointmentId)
	{
		$query = "delete from appointments where id=?";
		$values = array ("id"=>$appointmentId);

		return $this->db->query($query, $values);

	}

	public function validateUpdateData($appointmentData)
	{

				$errorCount =0;
				// var_dump($appointmentData);
				// die();

				$strDate = $appointmentData['apt_date'];

				if (strtotime($strDate)===false)	
				{

					$this->session->set_flashdata('apt_date_error', "<p class='red' >Appointment date is mandatory</p>");
					$errorCount +=1;
				}
				else
				{
					//check to make sure the date is not in the past

					// var_dump($appointmentData['apt_date']);

					$date = new DateTime($strDate);
					$today = new DateTime('now');

					$difference = $today->diff($date)->format('%R%a');  //gives you days with a sign either + / - and the total number of days including days in months / years preceeding.  

					
					if ($difference < 0)
					{
						$this->session->set_flashdata('apt_date_error', "<p class='red' >Appointment cannot be in the past</p>");
						$errorCount +=1;
					}
				}

				$strTime = $appointmentData['time'];

				if (strtotime($strTime)===false)
				{
					$this->session->set_flashdata('apt_time_error', "<p class='red' >Appointment time is mandatory</p>");
					$errorCount +=1;
				}

				$strTask = $appointmentData['task'];

				if (!trim($strTask))
				{
					$this->session->set_flashdata('apt_task_error', "<p class='red' >Appointment task is mandatory</p>");
					$errorCount +=1;
				}

				if ($errorCount > 0)
				{
					return false; 
				}
				else
				{
					return true;
				}
	}
	public function updateAppointment($appointmentData)
	{
		// var_dump($appointmentData);

		$query = "update appointments set date=?, time=?, task=?, status=?, updated_at=? where id=?";
		$values = array (
			"date"=>$appointmentData['apt_date'],
			"time"=>$appointmentData['time'],
			"task"=>$appointmentData['task'],
			"status"=>$appointmentData['status'],
			"updated_at"=> date('Y-m-d H:i:s'),
			"id"=>$appointmentData['id']
			); 

		return $this->db->query($query, $values);
		
	}

	public function getAppointmentsForUser()
	{
		$appointments = [];

		$query = "select * from appointments where date=? and user_id=? order by time asc;";
		$values = array(
			"date" => date('Y-m-d'),
			"user_id"=> $this->session->userdata('id')
			);
		// var_dump($values);
		// die();
		
		$appointments['today'] = $this->db->query($query, $values)->result_array();

		$appointments['others'] = $this->Appointment->getAllAppointmentsForUser();

		// var_dump($appointments);
		// die();
		return $appointments;

	}

	public function getAllAppointmentsForUser()
	{
		$query= "select * from appointments where date > ? and user_id=? ORDER by date asc, time asc";
		$values = 
				$values = array(
			"date" => date('Y-m-d'),
			"user_id"=> $this->session->userdata('id')
			);
		return $this->db->query($query, $values)->result_array();

	}

	public function createAppointment($appointmentData)
	{
			// var_dump($appointmentData);
			// die();

			$query = "insert into appointments (user_id, date, time, task, status, created_at, updated_at) values (?,?,?,?,?,?,?)";
			$values = array(

				"user_id"=> $appointmentData['user_id'],
				"date"=>$appointmentData['apt_date'],
				"time"=>$appointmentData['time'],
				"task"=>$appointmentData['task'],
				"status"=>1,
				"created_at"=> date('Y-m-d H:i:s'),
				"updated_at"=> date('Y-m-d H:i:s')
						); //status is automatically set to 1 = pending.


			return $this->db->query($query, $values);

	}
	public function validateAppointment($appointmentData) 
	{
				$errorCount =0;
				// var_dump($appointmentData);
				// die();

				$strDate = $appointmentData['apt_date'];

				if (strtotime($strDate)===false)	
				{
					$this->session->set_flashdata('apt_date_error', "<p class='red' >Appointment date is mandatory</p>");
					$errorCount +=1;
				}
				else
				{
					$appointmentDate = new DateTime($strDate);
					$today = new DateTime('now');

					$dateCompare = $today->diff($appointmentDate)->format('%R%a');

					if ($dateCompare < 0)
					{
						$this->session->set_flashdata('apt_date_error', "<p class='red' >Appointment date cannot be in the past</p>");
						$errorCount +=1;
					}

				}



				$strTime = $appointmentData['time'];

				if (strtotime($strTime)===false)
				{
					$this->session->set_flashdata('apt_time_error', "<p class='red' >Appointment time is mandatory</p>");
					$errorCount +=1;
				}

				$strTask = $appointmentData['task'];

				if (!trim($strTask))
				{
					$this->session->set_flashdata('apt_task_error', "<p class='red' >Appointment task is mandatory</p>");
					$errorCount +=1;
				}



				if ($errorCount > 0)
				{
					return false; 
				}
				else
				{
					//check and see if this is a unique appointment for the day

					$query = "select * from appointments where user_id=? and date=? and time=?";
					$values = array ("user_id"=>$this->session->userdata('id'), "date"=>$strDate, "time"=> $strTime);

					$queryResult = $this->db->query($query, $values);

					if ($queryResult->num_rows()===0)
					{
						// echo ("true");
						// die();

						return true;
					}
					else
					{
						// echo ("false");
						// die();
						$this->session->set_flashdata('apt_date_dup_error', "<p class='red' >Appointment must be unique for a given day</p>");
						return false;
					}

					
				}
			}
	}