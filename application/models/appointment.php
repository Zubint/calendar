<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Appointment extends CI_Model
{

	public function __construct(){

		parent:: __construct();
		date_default_timezone_set('America/chicago');
	}

	public function getAppointmentById($id)
	{
		$query = ("Select * from appointments where id=?");
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
					$this->session->set_flashdata('apt_date_error', "Appointment date is mandatory");
					$errorCount +=1;
				}

				$strTime = $appointmentData['time'];

				if (strtotime($strTime)===false)
				{
					$this->session->set_flashdata('apt_time_error', "Appointment time is mandatory");
					$errorCount +=1;
				}

				$strTask = $appointmentData['task'];

				if (!trim($strTask))
				{
					$this->session->set_flashdata('apt_task_error', "Appointment task is mandatory");
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

		$query = "update appointments set date=?, time=?, task=?, status=? where id=?";
		$values = array (
			"date"=>$appointmentData['apt_date'],
			"time"=>$appointmentData['time'],
			"task"=>$appointmentData['task'],
			"status"=>$appointmentData['status'],
			"id"=>$appointmentData['id']
			); 

		return $this->db->query($query, $values);
		
	}

	public function getAppointmentsForUser()
	{
		$appointments = [];

		$query = "select * from appointments where date=? and user_id=?";
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
		$query= "select * from appointments where date > ? and user_id=?";
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

			$query = "insert into appointments (user_id, date, time, task, status) values (?,?,?,?,?)";
			$values = array(

				"user_id"=> $appointmentData['user_id'],
				"date"=>$appointmentData['apt_date'],
				"time"=>$appointmentData['time'],
				"task"=>$appointmentData['task'],
				"status"=>1); //status is automatically set to 1 = pending.

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
					$this->session->set_flashdata('apt_date_error', "Appointment date is mandatory");
					$errorCount +=1;
				}

				$strTime = $appointmentData['time'];

				if (strtotime($strTime)===false)
				{
					$this->session->set_flashdata('apt_time_error', "Appointment time is mandatory");
					$errorCount +=1;
				}

				$strTask = $appointmentData['task'];

				if (!trim($strTask))
				{
					$this->session->set_flashdata('apt_task_error', "Appointment task is mandatory");
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
						$this->session->set_flashdata('apt_date_dup_error', "Appointment must be unique for a given day");
						return false;
					}
					
				}
			}
	}