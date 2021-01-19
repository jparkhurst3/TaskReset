<?php

session_start();	

echo "TASK UPDATE PAGE";

if (is_null($_SESSION["session_userID"])) {
	echo "User is not authenticated. Redirecting to login page...";
	header("Location: ../taskreset/login.php?");
	exit();
}

//Connect to the database
require 'connectdb.php';
echo "<br> Connected to DB";

//Get the values from url
echo "<br> TaskID is " . htmlspecialchars($_GET["v1"]);
$taskID = htmlspecialchars($_GET["v1"]);
echo "<br> Task field to update is " . htmlspecialchars($_GET["v2"]);
$taskField = htmlspecialchars($_GET["v2"]);
echo "<br> New data is " . htmlspecialchars($_GET["v3"]);
$taskData = htmlspecialchars($_GET["v3"]);
echo "<br> Redirect page is " . htmlspecialchars($_GET["v4"]);
$redirectPage = htmlspecialchars($_GET["v4"]);

//Update/delete task
echo "Updating tasks...";
$sql= "UPDATE tasks SET " . $taskField. "=? WHERE taskID=?;";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $taskData, $taskID);
if($stmt->execute()) {
	echo "<br> UPDATED task";
	//Redirect to edit_tasks.php
	if ($redirectPage = "viewPage") {
		header("Location: ../view_tasks.php?active=view");
		exit();
	} else {
		header("Location: ../edit_tasks.php?active=edit");
		exit();
	}
} else {
	echo "<br> Issue updating task";
	//TODO: Redirect elsewhere?
}

//TODO: Close connection/stream?