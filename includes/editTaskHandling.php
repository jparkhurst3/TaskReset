<?php
session_start();

echo "EDIT TASK HANDLING";
//Create an errorCount to make sure there are no issues throughout
$errorCount = 0;

//Connect to the database
echo "Connecting to db...";
require "connectdb.php";

// - - - Save any priority/urgency values that are different from existing

//If the save button has been hit
if(isset($_POST['edit-save'])) {
	//Set user
	$currentUser = $_SESSION["session_userID"];
	if ($_SESSION['userType'] == 'admin') {
		$currentUser = $_SESSION["currentUserID"];
	}

	//Get all user's existing priorities and add to array called 'userPriorities'
	echo "<br> Grabbing priorities!";
	$sql = "SELECT * FROM prioritySettings WHERE userID=? ORDER BY priorityOrder;";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("i", $currentUser);
	$stmt->execute();
	$result = $stmt->get_result();
	$userPriorities = array();
	$userPriorities_byPri = array();

	if($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
	        $currPri = $row['priorityName'];
	        $currOrder = $row['priorityOrder'];
	        $userPriorities[$currOrder] = $currPri;
	        $userPriorities_byPri[$currPri] = $currOrder;
	   }
	   // $stmt->free_result();
	   // $stmt->close();
	   //TODO: ^^ add these statements everywhere else
	} else {
		echo "Couldn't get priorities from prioritySettings";
		exit();
		//TODO: Redirect w/ error ?
		// header("Location: ../login.php?error=noauth");
		// exit();
	}

	$counter = 1;
	foreach ($userPriorities as $priority) {
		echo "<br> Priority #" . $counter . " is " . $priority;
		$counter++;
	}

	//Get all user's existing urgency options and add to array called 'userUrgencies'
	echo "<br> Grabbing urgencies!";
	$sql = "SELECT * FROM urgencySettings WHERE userID=? ORDER BY urgencyOrder;";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("i", $currentUser);
	$stmt->execute();
	$result = $stmt->get_result();
	$userUrgencies = array();
	$userUrgencies_byUrg = array();

	if($result->num_rows>0) {
		while ($row = $result->fetch_assoc()) {
	        $currUrg = $row['urgencyName'];
	        $currOrder = $row['urgencyOrder'];
	        $userUrgencies[$currOrder] = $currUrg;
	        $userUrgencies_byUrg[$currUrg] = $currOrder;
	   }
	   // $stmt->free_result();
	   // $stmt->close();
	} else {
		echo "Couldn't get urgencies from urgencySettings";
		exit();
		//TODO: Redirect w/ error ?
		// header("Location: ../login.php?error=noauth");
		// exit();
	}

	//Get current user's tasks (id, name, urgency, and priority)
	echo "<br> Grabbing Tasks!";
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


	//// - - - CALCULATE NEW ORDER VALUE FOR ALL TASKS - - -

	//Get user settings for priority and urgency weight
	echo "<br> Getting user's settings...";
	$sql = "SELECT * FROM userSettings WHERE userID=?;";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("i", $currentUser);
	$stmt->execute();
	$result = $stmt->get_result();

	if($result->num_rows>0) {
		$row = $result->fetch_assoc();
		$urgency_weight = $row['urgencyWeight'];
		$priority_weight = $row['priorityWeight'];
	} else {
		echo "Had an issue getting user settings.";
		//TODO: What to do here?
		// header("Location: ../login.php?error=noauth");
		// exit();	
	}

	//Update each tasks taskValueOrder
	foreach($userTasks_Names as $taskID =>$taskName) {
		//Get each task's priority and urgency
		$priority_current = $userTasks_Priorities[$taskID];
		$urgency_current = $userTasks_Urgencies[$taskID];

		//Get priority and urgency # for those values
		$priorityOrderNumber = $userPriorities_byPri[$priority_current];
		$urgencyOrderNumber = $userUrgencies_byUrg[$urgency_current];

		//Calculate new order value
		$taskOrderValue = ($priorityOrderNumber * $priority_weight) * ($urgencyOrderNumber);

		//Update for task
		$stmt = $conn->prepare("UPDATE tasks SET taskOrderValue=? WHERE taskID=?;");
		$stmt->bind_param("di", $taskOrderValue, $taskID);
		if($stmt->execute()) {
			//TEST - echo "<br> UPDATED task order value";
		} else {
			echo "Issue updating task order value";
			$errorCount++;
		}
	}

	//Direct to the viewTasks page if there are no issues
	if ($errorCount == 0) {
		header("Location: ../view_tasks.php?active=view);
		exit();
	}

} else {
	//TODO: Redirect back to... ?
}

?>