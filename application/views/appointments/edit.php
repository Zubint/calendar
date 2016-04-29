<?php
	if ($this->session->userdata('loggedIn') ===false)
	{
		redirect('/main/index');
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>edit</title>
	<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
	<div class="wrapper">
			<div class="nav">
				<ul id="nav" class="nav-links">
					<li class="nav-links" id="1" class="visible"><a href="/appointments/index">Dashboard</a></li>
					<li class="nav-links" id="2" class="visible"><a href="/users/logout">Logout</a></li>
				</ul>
			</div>
		<h1> Update an appointment </h1>
				
				<form name="editAppointment" id="updateAppointment" action="/appointments/updateAppointment" method="POST">

					<label>Task: </label>
					
						<input type="text" name="task" <?= "value='" . $appointment['task'] . "'" ?> >
						</input>
							<?php if (null!==$this->session->flashdata('apt_task_error'))
							{
								echo ($this->session->flashdata('apt_task_error'));
							}
							?>
					<label>Set Appointment Status</label>
					<select name="status" id="status" <?= "value='" . $appointment['status'] ."'" ?> >
						<option name="status" value=1>Pending</option>
						<option name ="status" value=2>Done</option>
						<option name="status" value=3>Missed</option>
					</select>

					<label>Date: </label>
						<input type="date" name="apt_date" <?PHP echo ("value='" . date('Y-m-d', strtotime($appointment['date']))) . "'"; ?>>
						<?php if (null!==$this->session->flashdata('apt_date_error'))
							{
								echo ($this->session->flashdata('apt_date_error'));
							}
							?>

					<label>Time: </label>
						<input type="time" name="time" <?php echo("value='" . $appointment['time'] . "'"); ?>>
						<?php if (null!==$this->session->flashdata('apt_time_error'))
							{
								echo ($this->session->flashdata('apt_time_error'));
							}
							?>
						<input type="hidden" name="appointment_id" <?php echo ("value='" .$appointment['id'] . "'"); ?> >
					<button type="submit" class="primary">Update task</button>
				</form>
							<?php if (null!==$this->session->flashdata('apt_date_dup_error'))
							{
								echo ($this->session->flashdata('apt_date_dup_error'));
							}
							?>
		</div>
</body>
</html>