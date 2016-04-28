<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Appointments extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Appointment');

	}

	public function updateAppointment()
	{

		if ($this->input->post() !=false)
			{
					//validate data
				// var_dump($this->input->post());
				// die();
					$appointmentData = array(
						"apt_date" => $this->input->post('apt_date'),
						"time"=> $this->input->post('time'),
						"task"=> $this->input->post('task'),
						"status"=> $this->input->post('status'),
						"id"=> $this->input->post('appointment_id')
						);

					if ($this->Appointment->validateUpdateData($appointmentData)===false)
					{	
						$id = $appointmentData['id'];
						redirect('/appointments/edit/'.$id);
					}
					else
					{
						//update the appointment
						// echo ("true");
						// die();
						$appointmentData['user_id'] = $this->session->userdata('id');
						$this->Appointment->updateAppointment($appointmentData);
						redirect('/appointments/index');

					}
			}
			else
			{
						$id = $appointmentData['id'];
						redirect('/appointments/edit/'.$id);
			}
		
	}

	public function edit($id)
	{
		$appointment['appointment'] = $this->Appointment->getAppointmentById($id);
		$this->load->view('/appointments/edit', $appointment);
	}
	public function index()
	{	
		$appointmentData['all'] = $this->Appointment->getAppointmentsForUser();
		$this->load->view('/appointments/index', $appointmentData);
	}

	public function delete($id)
	{
		$this->Appointment->deleteAppointment($id);
		redirect('/appointments/index');

	}
	public function addAppointment()
	{
		if ($this->input->post() !=false)
			{
					//validate data
					$appointmentData = array(
						"apt_date" => $this->input->post('apt_date'),
						"time"=> $this->input->post('time'),
						"task"=> $this->input->post('task')
						);

					if ($this->Appointment->validateAppointment($appointmentData)===false)
					{
						redirect('/appointments/index');
					}
					else
					{
						//add the appointment
						// echo ("true");
						// die();
						$appointmentData['user_id'] = $this->session->userdata('id');
						$this->Appointment->createAppointment($appointmentData);
						redirect('/appointments/index');

					}
			}
			else
			{
				redirect('/appointments/index');
			}
	}
}