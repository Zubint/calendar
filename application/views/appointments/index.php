<?php
	if ($this->session->userdata('loggedIn') ===false)
	{
		redirect('/main/index');
	}
?>

<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<script src="/assets/js/jquery.min.js" type="text/javascript" charset="utf-8" async defer></script>
		<link rel="stylesheet" href="/assets/css/style.css">
		<title>Welcome</title>
	</head>
	<body>
		<div class="wrapper">
			<div class="nav">
				<ul id="nav" class="nav-links">
					<li class="nav-links" id="1" class="visible"></li>
					<li class="nav-links" id="2" class="visible"><a href="/users/logout">Logout</a></li>
				</ul>
			</div>

			<div class="container">
				<div class="user_info">
					<!-- <?php // var_dump($user_data); ?> -->

				</div>
				<h1> Hello <?= $this->session->userdata('name') ?> </h1> 
				<h2> Here are you appointments for today, <?php echo (date('F-d-Y')); ?> </h2>
				<table>
				<tr>
					<th>Tasks</th>
					<th>Time</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
				<?php 

					if (isset($all) && count($all)>0)
					{
						if (count($all['today'])>0)
						{
							//loop through and put it on the form

							foreach($all['today'] as $today)
							{
								
								$status = "";
								$blnEditable = true;


								switch ($today['status'])
									{

									case 1:
										$status="Pending";
										continue;

									case 2:
										$status="Done";
										$blnEditable =false;
										continue;


									case 3:
										$status="Missed";
										$blnEditable =false;
										continue;
									}

									$strHtml = "";

									$strHtml = "<tr>" . 
									"<td>" . $today['task'] . "</td>" .
									"<td>" . $today['time'] . "</td>" .
									"<td>" . $status . "</td>";

									if ($blnEditable ===true)
									{ 
										$strHtml .= "<td>" . "<a href='/appointments/edit/" . $today['id'] . "'>Edit</a> | <a href='/appointments/delete/" .$today['id'] . "'>Delete</a></td>" .
									"</tr>";
									}

									echo ($strHtml);
							}
							echo ("</table>");
						}
						else
						{
							//show there are no appointments
							echo (
									"				<tr>
													<td>No</td>
													<td>Appointments</td>
													<td>for </td>
													<td>Today</td>
												</tr>
												</table>"

								);
						}
					}

					// var_dump($all) ?>

				<h1> All other appointments </h1>

				<table>
				<tr>
					<th>Tasks</th>
					<th>Date</th>
					<th>Time</th>
				</tr>

				<?php 

						if (isset($all) && count($all['others'])>0)
						{
							//loop through

							
						

							foreach ($all['others'] as $others)
							{
								$date =  Date('F d',strtotime($others['date']));
								echo (
										"<tr>" . 
										"<td>" . $others['task'] . "</td>" .
										"<td>" . $date . "</td>" .
										"<td>" . $others['time'] . "</td>" .
										"</tr>"
									);

							}
							echo ("</table>");
						}
						else
						{
							echo (

									" 				<tr>
												<td>No Future Appointments</td>
												<td></td>
												<td></td>

											</tr>
											</table>"

								);
						}
						?>

				<h1> Add an appointment </h1>
				<form name="addAppointment" id="addAppointment" action="/appointments/addAppointment"method="POST">
					<label>Date: </label>
						<input type="date" name="apt_date">
						<?php if (null!==$this->session->flashdata('apt_date_error'))
							{
								echo ($this->session->flashdata('apt_date_error'));
							}
							?>

					<label>Time: </label>
						<input type="time" name="time">
						<?php if (null!==$this->session->flashdata('apt_time_error'))
							{
								echo ($this->session->flashdata('apt_time_error'));
							}
							?>
					<label>Task: </label>
						<input type="text" name="task"></input>
							<?php if (null!==$this->session->flashdata('apt_task_error'))
							{
								echo ($this->session->flashdata('apt_task_error'));
							}
							?>
					<button type="submit" class="primary">Add task</button>
				</form>
							<?php if (null!==$this->session->flashdata('apt_date_dup_error'))
							{
								echo ($this->session->flashdata('apt_date_dup_error'));
							}
							?>

				</div>
			</div>