<?php
	session_start();
	require "header.php";
	//TODO: Check if the URL has the 'saved' keyword, from coming from RESET?

	//Check that user is authenticated
	if (is_null($_SESSION["session_userID"])) {
		echo "User is not authenticated. Redirecting to login page...";
		header("Location: ../login.php?");
		exit();
	} else {
		//TEST - echo "Alright we got the userID!";
	}

	//Connect to the database
	require 'includes/connectdb.php';
	//TEST - echo "<br> Connected to DB";

	//Set user
	$currentUser = $_SESSION["session_userID"];
	if ($_SESSION['userType'] == 'admin') {
		$currentUser = $_SESSION["currentUserID"];
	}

	//Get users tasks, ordered by progress then ordervalue
	//TEST - echo "<br> Grabbing Tasks!";
	$sql = "SELECT * FROM tasks WHERE userID=? ORDER BY taskProgress, taskOrderValue, taskID ASC";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("i", $currentUser);
	$stmt->execute();
	$result = $stmt->get_result();
	$userTasks_Names = array();
	$userTasks_Priorities = array();
	$userTasks_Urgencies = array();
	$userTasks_Progress = array();
	if($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
	        $currTaskID = $row['taskID'];
	        $currTaskName = $row['taskName'];
	        $currTaskPriority = $row['taskPriority'];
	        $currTaskUrgency = $row['taskUrgency'];
	        $currTaskProgress = $row['taskProgress'];
	        $userTasks_Names[$currTaskID] = $currTaskName;
	        $userTasks_Priorities[$currTaskID] = $currTaskPriority;
	        $userTasks_Urgencies[$currTaskID] = $currTaskUrgency;
	        $userTasks_Progress[$currTaskID] = $currTaskProgress;
	   }
	   $stmt->free_result();
	   $stmt->close();
	} else {
		echo "Couldn't get tasks from tasks db";
		exit();
		//TODO: Redirect w/ error ?
		// header("Location: ../login.php?error=noauth");
		// exit();
	}
	?>
	<main>	
	<table>
		<thead>
			<th></th> <!-- COMPLETE BUTTON -->
			<th data-type="text-long">TASK<span class="resize-handle"></span></th>
			<th data-type="text-short">PRIORITY<span class="resize-handle"></span></th>
			<th data-type="text-short">URGENCY<span class="resize-handle"></span></th>
		</thead>
		<tbody>
			<!-- For each of the user's tasks, add a row to the table -->
			<?php 
				foreach($userTasks_Names as $taskID => $taskName) {
					$rowPriority = $userTasks_Priorities[$taskID];
					$rowUrgency = $userTasks_Urgencies[$taskID];
					$rowProgress = $userTasks_Progress[$taskID];
					?>
					<tr>
						<?php
						//COMPLETE BUTTON - show as checked (complete) if progress isn't "0"
								if ($rowProgress == 0) {
									echo "<td>";
									echo "<input onclick='checkboxChange(" . $taskID . ")' type='checkbox' id='progress_" . $taskID . "' id='progress_" . $taskID . "'>";
									echo "</td>";	
									echo "<td class='task-text'>" ;
								} else {
									echo "<td>";
									echo "<input onclick='checkboxChange(" . $taskID . ")' type='checkbox' id='progress_" . $taskID . "' id='progress_" . $taskID . "' checked class='completedTask'>";
									echo "</td>";
									echo "<td class='strikethrough-text task-text'>";
								}
						//TASK NAME
								echo $taskName;
								echo "</td>";
						//TASK PRIORITY	
								if ($rowProgress == 100) {
									echo "<td class='strikethrough-text task-text'>" . $rowPriority . "</td>";
									echo "<td class='strikethrough-text task-text'>" . $rowUrgency . "</td>";
								}  else {
									echo "<td class='pink-highlight'>" . $rowPriority . "</td>";
									echo "<td class='orange-highlight'>" . $rowUrgency . "</td>";
								}
						?>
					</tr>
				<?php
				}
				?>
		</tbody>
	</table>
	</main>
	<script src="scriptstuff.js"></script>
	<?php	
	require "footer.php";
?>