<?php
	session_start();	

// If the quiz has been submitted...
if(isset($_POST['submit'])) {

	//Check that user is authenticated
	if (is_null($_SESSION["session_userID"])) {
		echo "User is not authenticated. Redirecting to login page...";
		header("Location: ../taskreset/login.php?");
		exit();
	}

	//Show loading message for user confirmation
	echo "Submitted! Customizing your task list...";

	//Connect to the database
	require 'connectdb.php';

	//Create an error counter to help with debugging
	$errorCount = 0;

	// - - - UPDATE USER TYPE
	// Updates userType to 'existing' after tasking quiz so returning users aren't routed through it each time
	// if ($_SESSION['userType'] == 'new') {
	// 	$sql = "UPDATE users SET userType='existing' WHERE userID=?";
	// 	$stmt->bind_param("i", $_SESSION["session_userID"]);
	// 	$userIDVar = $_SESSION["session_userID"];
	// 	if($stmt->execute()) {
	// 		echo "existing priorities deleted.";
	// 	} else {
	// 		echo "what the heck";
	// 		$errorCount++;
	// 	}
	// }

	// - - - PRIORITIES - - -

	//Delete any existing priorities (submission of this form means starting fresh)
	$stmt = $conn->prepare("DELETE FROM prioritySettings WHERE userID=?");
	$stmt->bind_param("i", $userIDVar);
	$userIDVar = $_SESSION["session_userID"];
	if($stmt->execute()) {
		echo "existing priorities deleted.";
	} else {
		echo "what the heck";
		$errorCount++;
	}

	//Get the priorities from the form
	$pri1 = trim($_POST['priority1']);
	$pri2 = trim($_POST['priority2']);
	$pri3 = trim($_POST['priority3']);

	//Validate/sanitize priority names
	if(empty($pri1) || empty($pri2) || empty($pri3)) {
		//Redirect back to quiz with error msg
		header("Location: ../reset_quiz.php?error=emptyPriority&active=reset");
		exit();
	} else {
		//Strip slashes
		$priority1 = stripslashes($pri1);
		$priority2 = stripslashes($pri2);
		$priority3 = stripslashes($pri3);
	}

	//Define default 'other' priority
	$priority4 = "Other";

	//Put the priorities in the prioritySettings table for the user
	$stmt = $conn->prepare("INSERT INTO prioritySettings (userID, priorityName, priorityOrder) VALUES (?, ?, ?);");
	$stmt->bind_param("isi", $userIDVar, $priorityNameVar, $priorityOrderVar);
	$userIDVar = $_SESSION["session_userID"];

	$priorityNameVar = $priority1;
	$priorityOrderVar = 1;
	if($stmt->execute()) {
		//TEST - echo "priority1 added.";
	} else {
		echo "Issue adding priority 1.";
		$errorCount++;
	}

	$priorityNameVar = $priority2;
	$priorityOrderVar = 2;
	if($stmt->execute()) {
		//TEST - echo "priority2 added.";
	} else {
		echo "Issue adding priority 2.";
		$errorCount++;
	}

	$priorityNameVar = $priority3;
	$priorityOrderVar = 3;
	if($stmt->execute()) {
		//TEST - echo "priority3 added.";
	} else {
		echo "Issue adding priority 3.";
		$errorCount++;
	}

	$priorityNameVar = $priority4;
	$priorityOrderVar = 4;
	if($stmt->execute()) {
		//TEST - echo "priority4 added.";
	} else {
		echo "Issue adding priority 4.";
		$errorCount++;
	}

	// - - - URGENCY - - -

	//Validate that there is a urgency
	if(is_null($_POST['planningFrequency'])) {
		//Redirect back to quiz with error note
		header("Location: ../reset_quiz.php?error=emptyUrgency");
		exit();
	}

	//Delete any existing urgencies (submission of this form means starting fresh)
	$stmt = $conn->prepare("DELETE FROM urgencySettings WHERE userID=?");
	$stmt->bind_param("i", $userIDVar);
	$userIDVar = $_SESSION["session_userID"];
	if($stmt->execute()) {
		//Test - echo "Existing urgencies deleted.";
	} else {
		echo "Issue deleting existing urgencies.";
		$errorCount++;
	}

	//Based on the urgency choice, pick default urgency options
	if ($_POST['planningFrequency'] == 'daily') {
		echo "Planning frequency is daily.";
		$urgencyValue1 = 'ASAP';
		$urgencyValue2 = 'Today';
		$urgencyValue3 = 'This week';
		$urgencyValue4 = 'Eventually';
	} elseif ($_POST['planningFrequency'] == 'weekly') {
		echo "Planning frequency is weekly.";
		$urgencyValue1 = 'Today';
		$urgencyValue2 = 'This week';
		$urgencyValue3 = 'Soon';
		$urgencyValue4 = 'Eventually';
	} elseif ($_POST['planningFrequency'] == 'lessFrequent') {
		echo "Planning frequency is lessFrequent.";
		$urgencyValue1 = 'ASAP';
		$urgencyValue2 = 'Urgent';
		$urgencyValue3 = 'Soon';
		$urgencyValue4 = 'Not urgent';
	} else {
		echo "Planning frequency is whenNeeded.";
		$urgencyValue1 = 'ASAP';
		$urgencyValue2 = 'Urgent';
		$urgencyValue3 = 'Soon';
		$urgencyValue4 = 'Not urgent';
	}

	//Put the urgency options in the urgencySettings table for the user
	$stmt = $conn->prepare("INSERT INTO urgencySettings (userID, urgencyName, urgencyOrder) VALUES (?, ?, ?);");
	$stmt->bind_param("isi", $userIDVar, $urgencyNameVar, $urgencyOrderVar);
	$userIDVar = $_SESSION["session_userID"];

	$urgencyNameVar = $urgencyValue1;
	$urgencyOrderVar = 1;
	if($stmt->execute()) {
		//TEST - echo "Urgency1 added.";
	} else {
		echo "Issue adding urgency 1.";
		$errorCount++;
	}

	$urgencyNameVar = $urgencyValue2;
	$urgencyOrderVar = 2;
	if($stmt->execute()) {
		// TEST - echo "Urgency2 added.";
	} else {
		echo "Issue adding urgency 2.";
		$errorCount++;
	}

	$urgencyNameVar = $urgencyValue3;
	$urgencyOrderVar = 3;
	if($stmt->execute()) {
		//TEST - echo "Urgency3 added.";
	} else {
		echo "Issue adding urgency 3.";
		$errorCount++;
	}

	$urgencyNameVar = $urgencyValue4;
	$urgencyOrderVar = 4;
	if($stmt->execute()) {
		//TEST - echo "Urgency4 added.";
	} else {
		echo "Issue adding urgency 4.";
		$errorCount++;
	}

	// - - - ORDERING - - -

	//Default urgency and priority weights: 50%
	$urgency_weight = 50;
	$priority_weight = 50;

	//Delete any previous user settings
	$stmt = $conn->prepare("DELETE FROM userSettings WHERE userID=?");
	$stmt->bind_param("i", $userIDVar);
	$userIDVar = $_SESSION["session_userID"];
	if($stmt->execute()) {
		//Test - echo "Existing user settings deleted.";
	} else {
		echo "Issue deleting existing user settings.";
		$errorCount++;
	}

	//Add new settings
	$stmt = $conn->prepare("INSERT INTO userSettings (userID, urgencyWeight, priorityWeight) VALUES (?, ?, ?);");
	$stmt->bind_param("iii", $userIDVar, $urgency_weight, $priority_weight);
	$userIDVar = $_SESSION["session_userID"];
	if($stmt->execute()) {
		//TEST - echo "User settings updated.";
	} else {
		echo "There was an issue updating user settings.";
		$errorCount++;
		exit();
	}

	// - - - TASKS - - -

	//Delete tasks if there are existing tasks (if user resets tasks?)
	$stmt = $conn->prepare("DELETE FROM tasks WHERE userID=?");
	$stmt->bind_param("i", $userIDVar);
	$userIDVar = $_SESSION["session_userID"];
	if($stmt->execute()) {
		//Test - echo "Existing tasks deleted.";
	} else {
		echo "Issue deleting existing tasks.";
		$errorCount++;
	}

	//Get the task text from the form
	$allTheTasks = $_POST['inputTasks'];

	//Parse the task text into individual tasks
	$taskArray = explode("\n", $allTheTasks);

	//Put the tasks in the Tasks table for the user with default urgency, priority, and progress values
	$stmt = $conn->prepare("INSERT INTO tasks (userID, taskName, taskUrgency, taskPriority, taskOrderValue, taskProgress) VALUES (?, ?, ?, ?, ?, 0);");
	$stmt->bind_param("isssd", $userIDVar, $taskNameVar, $urgencyValue4, $priority4, $defaultTaskOrderValue);
	$userIDVar = $_SESSION["session_userID"];
	$taskCounter = 0;
	$defaultTaskOrderValue = (4 * $urgencyWeight) * (4 * $priorityWeight);
	echo "Calculated default taskOrderValue.";

	foreach ($taskArray as $task) {
		$taskNameVar = trim($task);
		if (empty($taskNameVar) ) {
			//TEST - echo "<br> Skipping this empty task...";
		} else {
			if($stmt->execute()) {
				//TEST - echo "<br> " . $taskNameVar . " added to DB.";
				$taskCounter++;
			} else {
				echo "There was an issue adding tasks.";
				$errorCount++;
				exit();
			}
		}
	}

	echo "<br>" . $taskCounter . " tasks added to the database.";

	//Close connection
	$conn->close();

	//Direct to the editTasks page if there are no issues
	if ($errorCount == 0) {
		header("Location: ../edit_tasks.php?active=edit");
		exit();
	}

} else {
	//If quiz wasn't submitted, direct back to quiz
	echo "Quiz not submitted.";
	header("Location: ../reset_quiz.php?active=reset");
	exit();
}

?>