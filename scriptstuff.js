//Import jQuery
var selectedItemID = null
var selectedAction = null

//Get the task ID associated with the clicked button
function clicked(id) {
	selectedItemID= id;
    console.log(selectedItemID);
    console.log(taskNames[selectedItemID]);
    var taskName = taskNames[selectedItemID];

    selectedAction = 'edit'
    $('input[name=taskName_editModal]').val(taskName) //gives html element
    $('.modal').css("display", "block")
}

function addClicked() {
	selectedAction = 'add'
    $('input[name=taskName_editModal]').val("") //gives html element
    $('.modal').css("display", "block")
}

function savebuttonclicked() {
	if (selectedAction == "edit") {
		console.log("Save button clicked")
		var newTaskName = $('input[name=taskName_editModal]').val()
		//Call php file with newTaskName
		console.log(selectedItemID)
		console.log(newTaskName)
		updateTask(selectedItemID, "taskName", newTaskName, "editPage")
	} else if (selectedAction == "add") {
		console.log("Add button clicked")
		var newTaskName = $('input[name=taskName_editModal]').val()
		//Call php file with newTaskName
		console.log(newTaskName)
		addTask(newTaskName)
	}
}

function deletebuttonclicked() {
	console.log("Deletebutton clicked")
	//Call php file with taskid
	console.log(selectedItemID)
	deleteTask(selectedItemID)
}

// Get the button that opens the modal
var btn = document.getElementById("editButtonModal");

// Get the <span> element that closes the modal
function closeModal() {
	$('.modal').css("display", "none")
	console.log("Closed")
}

function updateTask(taskID, fieldToUpdate, newData, endPage){
	var id = taskID;
	var field = fieldToUpdate;
	var data = newData;
	var endPage = endPage; //Page to direct to after updating task
	//Redirect and include variables
	window.location.href = "includes/updateTask.php?v1="+id+"&v2="+field+"&v3="+data+"&v4="+endPage;
}

function addTask(newData) {
	var taskName = newData;
	//Redirect and include variables
	window.location.href = "includes/addTask.php?taskName="+taskName;
}

function updatePriority(taskId) {
	//put id and new priority value in 'data'
	var id = taskId;
	var priorityDropdownid = "priority_" + id;
	var newPriority = $('#'+priorityDropdownid).find(":selected").text();
	console.log(newPriority);
	console.log("priority changed");

	  $.ajax({
	    url: 'includes/updatePriority.php',
	    data : {
	    	'id': id,
	    	'priority': newPriority
	    },
	    type: 'POST',
	  });
}

function updateUrgency(taskId) {
	//put id and new urgency value in 'data'
	var id = taskId;
	var urgencyDropdownid = "urgency_" + id;
	var newUrgency = $('#'+urgencyDropdownid).find(":selected").text();
	console.log(newUrgency);
	console.log("urgency changed");
	
	  $.ajax({
	    url: 'includes/updateUrgency.php',
	    data : {
	    	'id': id,
	    	'urgency': newUrgency
	    },
	    type: 'POST',
	  });
}

function deleteTask(taskId) {
	var id = taskId
	console.log("dele1ting task...")
	$.ajax({
	    url: 'includes/deleteTask.php',
	    data : {
	    	'id': id,
	    },
	    type: 'POST',
	    success: function(response) {
	        console.log(response)
	        location.reload()
	    }
	});
}

function checkboxChange(taskID) {
	console.log("Checkbox changed")
	selectedItemId = taskID;
	var checkboxID = "progress_" + selectedItemId;
	console.log(checkboxID)
	if (document.getElementById(checkboxID).checked) {
		//Checkbox is checked - Update progress to 100
		updateTask(selectedItemId, "taskProgress", 100, "viewPage") 
	} else {
		//Checkbox is unchecked - Update progress to 0
		updateTask(selectedItemId, "taskProgress", 0, "viewPage")
	}
}