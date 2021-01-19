<?php 
	session_start();

	//Connect to the database
	require 'connectdb.php';	

	//If the login form has been submitted...
	if(isset($_POST['login-submit'])) {

		//Get the username and password from the form
		$userName=$_POST['userName'];
		$userPwd=$_POST['userPwd'];
		$_SESSION['userList_byID'] = null;
		$_SESSION['userList_byName'] = null;
		$_SESSION["currentUserName"] = null;
		$_SESSION["currentUserID"] = null;

		if(empty($userName) || empty($userPwd)) {
			header("Location: ../login.php?error=emptyfields");
			//TODO: ADD HANDLING FOR RETURNING USERNAME OR PASS
			exit();
		} else {
			$sql = "SELECT * FROM users WHERE userName=? AND userPwd=?;";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("ss", $userName, $userPwd);
			$stmt->execute();
			$result = $stmt->get_result();

			if($result->num_rows>0) {
				$row = $result->fetch_assoc();
				$_SESSION["userType"] = $row['userType'];
				$_SESSION["session_userID"] = $row['userID'];
				$_SESSION["currentUserID"] = $_SESSION["session_userID"];
				echo "User is authenticated. Redirecting...";
				if($_SESSION["userType"] == 'admin') {
					$sql = "SELECT userName, userID FROM users;";
					$result = mysqli_query($conn, $sql);
					$userList_ID = array();
					$userList_Name = array();
					if (mysqli_num_rows($result) > 0) {
					  while($row = mysqli_fetch_assoc($result)) {
					    $userID = $row['userID'];
					    $userName = $row['userName'];
					    $userList_ID[$userID] = $userName;
					    $userList_Name[$userName] = $userID;
					    echo "User id: " . $userID . " added.";
					  }
					  $_SESSION['userList_byID'] = $userList_ID;
					  $_SESSION['userList_byName'] = $userList_Name;
					  echo "UserList updated";
					} else {
					  echo "Issue getting list of users";
					}
					echo "List of users retrieved";
					header("Location: ../view_tasks.php?active=view");
					exit();
				} elseif ($_SESSION["userType"] == 'new') {
					$_SESSION["currentUserName"] = $userName;
					header("Location: ../about_taskreset.php?active=about");
					exit();
				} else {
					$_SESSION["currentUserName"] = $userName;
					header("Location: ../tasks.php?active=view");
					exit();
				}
			} else {
				header("Location: ../login.php?error=noauth");
				exit();
			}
		} 

	} else {
		header("Location: ../login.php");
		exit();
	}
?>