<?php

session_start();

if (is_null($_SESSION["session_userID"])) {
	echo "User is not authenticated. Redirecting to login page...";
	header("Location: ../taskreset/login.php?");
	exit();
}

//Connect to the database
require 'connectdb.php';
echo "<br> Connected to DB";

//Get the values from url
echo "<br> Task ID is " . $_POST["id"];
$taskID = $_POST["id"];

//Delete task
$sql = "DELETE FROM tasks WHERE taskID=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $taskID);
if ($stmt->execute()) {
	echo "success!";
	exit();
} else {
	echo "Issue while trying to delete task";	
}