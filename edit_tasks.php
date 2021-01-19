<?php

session_start();	

//TODO: Check that url includes 'submitted' key

require "header.php";

//Connect to the database
require "includes/connectdb.php";

//Check that user is authenticated
if (is_null($_SESSION["session_userID"])) {
	echo "User is not authenticated. Redirecting to login page...";
	header("Location: ../login.php?");
	exit();
} else {
	//TEST - echo "Alright we got the userID!";
}

//Set user
$currentUser = $_SESSION["session_userID"];
if ($_SESSION['userType'] == 'admin') {
	$currentUser = $_SESSION["currentUserID"];
}

//Get all user's priorities and add to array called 'userPriorities'
// TEST - echo "<br> Grabbing priorities!";
$sql = "SELECT * FROM prioritySettings WHERE userID=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $currentUser);
$stmt->execute();
$result = $stmt->get_result();
$userPriorities = array();

//echo "current user is " . $currentUser;

if($result->num_rows>0) {
	while ($row = $result->fetch_assoc()) {
        $currPri = $row['priorityName'];
        $currOrder = $row['priorityOrder'];
        $userPriorities[$currOrder] = $currPri;
   }
   $stmt->free_result();
   $stmt->close();
} else {
	echo "Couldn't get priorities from prioritySettings";
	exit();
	//TODO: Redirect w/ error ?
	// header("Location: ../login.php?error=noauth");
	// exit();
}

// TEST:
// $counter = 1;
// foreach ($userPriorities as $priority) {
// 	echo "<br> Priority #" . $counter . " is " . $priority;
// 	$counter++;
// }

//Get all user's urgency options and add to array called 'userUrgencies'
//TEST - echo "<br> Grabbing urgencies!";
$sql = "SELECT * FROM urgencySettings WHERE userID=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $currentUser);
$stmt->execute();
$result = $stmt->get_result();
$userUrgencies = array();

if($result->num_rows>0) {
	while ($row = $result->fetch_assoc()) {
        $currUrg = $row['urgencyName'];
        $currOrder = $row['urgencyOrder'];
        $userUrgencies[$currOrder] = $currUrg;
   }
   $stmt->free_result();
   $stmt->close();
} else {
	echo "Couldn't get urgencies from urgencySettings";
	exit();
	//TODO: Redirect w/ error ?
	// header("Location: ../login.php?error=noauth");
	// exit();
}

// TEST: 
// $counter = 1;
// foreach ($userUrgencies as $urgency) {
// 	echo "<br> Urgency #" . $counter . " is " . $urgency;
// 	$counter++;
// }


//Get all tasks for the user
// TEST - echo "<br> Grabbing Tasks!";
$sql = "SELECT * FROM tasks WHERE userID=? ORDER BY taskOrderValue, taskID ASC"; //TODO: Order by progress as well?
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $currentUser);
$stmt->execute();
$result = $stmt->get_result();
$userTasks_Names = array();
$userTasks_Priorities = array();
$userTasks_Urgencies = array();

if($result->num_rows>0) {
	while ($row = $result->fetch_assoc()) {
        $currTaskID = $row['taskID'];
        $currTaskName = $row['taskName'];
        $currTaskPriority = $row['taskPriority'];
        $currTaskUrgency = $row['taskUrgency'];
        $userTasks_Names[$currTaskID] = $currTaskName;
        $userTasks_Priorities[$currTaskID] = $currTaskPriority;
        $userTasks_Urgencies[$currTaskID] = $currTaskUrgency;
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

//TEST - echo "There are " . count($userTasks_Names) . " tasks in the array!";

?> 

<!-- //TODO: Display all tasks for the user-->
<div class="content-container">
	<h3 class="pink-highlight">Now that you've entered your tasks, select a priority and importance level for each. Add more tasks using the "Add task" button and press "Sort tasks" when done.</h3>
</div>
<div class="content-container">
<form method="POST">
	<table>
		<thead>
			<th></th> <!-- EDIT BUTTON -->
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
					?>
					<tr>
						<td><button <?php echo "onclick='clicked(" . $taskID . ")'"; ?> type="button" name="edit-task-button" class="mdc-icon-button material-icons md-24" id="editButtonModal">create</button></td>
					<?php
					echo "<td class='task-text'>". $taskName . "</td><td class='pink-highlight'><select onchange='updatePriority(" . $taskID . ")' id='priority_" . $taskID . "' name='priority_" . $taskID . "'>";
					foreach ($userPriorities as $priority) {
						echo "<option value='" . $priority . "' ";
						if($rowPriority == $priority) {
							echo "selected";	
						}
						echo ">" . $priority . "</option>";
					}
					echo "</select></td><td class='pink-highlight'><select onchange='updateUrgency(" . $taskID . ")' id='urgency_" . $taskID . "' name='urgency_" . $taskID . "'>";
					foreach ($userUrgencies as $urgency) {
						echo "<option value='" . $urgency . "'";
						if ($rowUrgency == $urgency) {
							echo "selected";
						}
						echo ">" . $urgency . "</option>";
					}
					echo "</select></td></tr>";
				}
			?>
		</tbody>
	</table>
	<br>
	<button class="big-button" type="submit" name="edit-save" formaction="includes/editTaskHandling.php">SORT TASKS</button>
</form>
	<br>
	<button class="big-button" onclick='addClicked()' name="add-task-button">ADD TASK</button>	

<!-- EDIT MODAL -->
	<div id="editModal" class="modal">
	  <div class="modal-content">
	    <span onclick='closeModal()' class="close">&times;</span>
	    	<input type="text" name="taskName_editModal"></input>
	    	<button id="edit-save-button" onclick='savebuttonclicked()'>SAVE</button>
	    	<br><br>	
	    	<button id="edit-delete-button" onclick='deletebuttonclicked()'>DELETE</button>
	  </div>
	</div>
</div>

<script src="scriptstuff.js"></script>

<script type='text/javascript'>
	<?php
		$js_array = json_encode($userTasks_Names);
		echo "var taskNames = ". $js_array . ";\n";
	?>
</script>

<?php
//TODO: Add edit button for each task name
//TODO: Add modal for editing task name and/or deleting

//TODO: close streams and stuff

?>