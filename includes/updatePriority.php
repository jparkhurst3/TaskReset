<?php
session_start();	

echo "PRIORITY UPDATE PAGE";

//Check that user is authenticated
if (is_null($_SESSION["session_userID"])) {
	echo "User is not authenticated. Redirecting to login page...";
	header("Location: ../taskreset/login.php?");
	exit();
}

//Connect to the database
require 'connectdb.php';
echo "<br> Connected to DB";

//Get the values from url
echo "<br> TaskID is " . $_POST["id"];
$taskID = htmlspecialchars($_POST["id"]);
echo "<br> Task field to update is taskPriority";
$taskField = "taskPriority";
echo "<br> New data is " . htmlspecialchars($_POST["priority"]);
$taskData = htmlspecialchars($_POST["priority"]);

//Update task
echo "Updating tasks...";
$sql= "UPDATE tasks SET taskPriority=? WHERE taskID=?;";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $taskData, $taskID);
if($stmt->execute()) {
	echo "<br> UPDATED task";
} else {
	echo "<br> Issue updating task";
	//TODO: Redirect elsewhere?
}

//TODO: Close connection/stream?