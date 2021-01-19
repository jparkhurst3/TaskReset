<?php

session_start();

$newCurrentUserName = $_POST['userSwitchDropdown'];
$_SESSION["currentUserName"] = $newCurrentUser;
$userArray_byName = $_SESSION['userList_byName'];
$newCurrentUserID = $userArray_byName[$newCurrentUserName];
$_SESSION['currentUserID'] = $newCurrentUserID;

//echo "new current user is: " . $_SESSION["currentUser"];
header("Location: ../view_tasks.php?switchedUser");
exit();

?>