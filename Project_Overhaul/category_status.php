
<?php 
	include "sql_common.php";
	session_start();
	if (!isset($_SESSION["username"])) {
		header("Location: login.php");
		exit();
	}
 ?>

<!DOCTYPE html>

<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<link rel="stylesheet" href="styles.css">

</head>

<body>
		
	<?php 
		if(null !== isset($date)) {$date = date("Y-m-d");} 
		include_once "new_nav.php" 
	?>

	<div>
		<input type="date" name="dateview" value=<?php echo $date; ?> >
	</div>

	<div>
	   <table border="1px">
	   	<tr>
	   		<td colspan="2" > <?php echo date('D, M d Y', strtotime($date)); ?></td>
	   	</tr>
	   	<tr>
	   		<th>Category</th>
	   		<th>Status</th>
	   	</tr>

	   	<?php $result = get_categories($date);
			 while ($row = $result->fetch_assoc()): ?>

		   		<tr>
		   			<td><form action="" method="post">
		   				<input type="submit" value=<?php echo $row["name"]; ?> class="button">
		   			</form></td>
		   			<td></td>
		   		</tr>

	   	<?php  endwhile ?>

	   	</table>
	</div>
		
	<div>
		<span ><strong>Expected Sales ($):</strong></span>
		<input type="number">
	</div>

	<div>
		<a href="">Change Password</a>
	</div>

</body>

</html>

