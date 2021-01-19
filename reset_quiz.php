<?php
	require "header.php";
	//Should the header check whether the user is authenticated?

	//TODO: If there's an error code, show the error
?>

	<main>
		<div class="content-container">
			<form action="includes/quizHandling.php" method="POST">
				<h3>What are your current top three priorities?</h3>
				<p>(These can be changed later)</p>
					<input type="text" name="priority1" placeholder="Ex: Art History Class">
					<br><br>
					<input type="text" name="priority2" placeholder="Ex: Wedding Planning">
					<br><br>
					<input type="text" name="priority3" placeholder="Ex: Job Search">
					<br><br>

				<h3>How often do you typically plan out your tasks?</h3>
					<input type="radio" id="daily" name="planningFrequency" value="daily">
					<label for="daily"><a>Daily</a></label><br>
					<input type="radio" id="weekly" name="planningFrequency" value="weekly">
					<label for="weekly"><a>Weekly</a></label><br>
					<input type="radio" id="lessFrequent" name="planningFrequency" value="lessFrequent">
					<label for="lessFrequent"><a>Less frequently than weekly</a></label><br>
					<input type="radio" id="whenNeeded" name="planningFrequency" value="whenNeeded">
					<label for="whenNeeded"><a>When needed</a></label><br><br>

				<!-- <h3>Which is more important to you?</h3>
					<input type="radio" id="urgency" name="sortingFormula" value="urgency">
					<label for="urgency">Finishing tasks on time</label><br>
					<input type="radio" id="priority" name="sortingFormula" value="priority">
					<label for="priority">Working on high priority tasks first</label><br><br> -->

				<h3>List out your tasks that are top of mind, with each task on a new line.</h3>
				<p>(You can add and edit these tasks later)</p>
					<textarea type="text" name="inputTasks" rows="4" cols="50">Ex: Check email</textarea>
					<br><br>

				<button class="big-button" type="submit" name="submit">Submit</button>
			</form>
		</div>
	</main>

<?php
	require "footer.php";
?>