<?php

session_start();	

echo "ADD UPDATE PAGE";

if (is_null($_SESSION["session_userID"])) {
	echo "User is not authenticated. Redirecting to login page...";
	header("Location: ../taskreset/login.php?");
	exit();
}

//Connect to the database
require 'connectdb.php';
echo "<br> Connected to DB";

//Set user
$currentUser = $_SESSION["session_userID"];
if ($_SESSION['userType'] == 'admin') {
	$currentUser = $_SESSION["currentUserID"];
}

//Get the values from url
echo "<br> Task name is " . $_GET["taskName"];
$taskName = $_GET["taskName"];

// echo "<br> TaskID is " . htmlspecialchars($_GET["taskName"]);
// $taskName = htmlspecialchars($_GET["taskName"]);

//Get user's priorities and urgencies to set default
$sql = "SELECT * FROM prioritySettings WHERE userID=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $currentUser);
$stmt->execute();
$result = $stmt->get_result();
$userPriorities = array();

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
$counter = 1;
foreach ($userPriorities as $priority) {
	echo "<br> Priority #" . $counter . " is " . $priority;
	$counter++;
}

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
$counter = 1;
foreach ($userUrgencies as $urgency) {
	echo "<br> Urgency #" . $counter . " is " . $urgency;
	$counter++;
}


//Add task
echo "Adding task...";
$stmt = $conn->prepare("INSERT INTO tasks (userID, taskName, taskUrgency, taskPriority, taskProgress) VALUES (?, ?, ?, ?, 0);");
$stmt->bind_param("isss", $currentUser, $taskNameVar, $urgencyValue, $priorityValue);
$urgencyValue = end($userUrgencies);
echo "<br> urgency value is " . $urgencyValue;
$priorityValue = end($userPriorities);
echo "<br> priority value is " . $priorityValue;
$taskNameVar = trim($taskName);
echo "<br> task name is: " . $taskNameVar;
if($stmt->execute()) {
	echo "<br> " . $taskNameVar . " added to DB.";
	header("Location: ../edit_tasks.php?active=edit");
	exit();
} else {
	echo "There was an issue adding tasks.";
	exit();
}


//TODO: Close connection/stream?