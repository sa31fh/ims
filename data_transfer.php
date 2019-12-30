<script type="text/javascript" src="jq/jquery-3.2.1.min.js"></script>
<?php 
session_start();
require_once "database/item_table.php";

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION["userrole"] != "admin") {
    header("Location: login.php");
    exit();
}
if (isset($_SESSION["last_activity"]) && $_SESSION["last_activity"] + $_SESSION["time_out"] * 60 < time()) {
    session_unset();
    session_destroy();
?>
    <script>
        window.parent.location.href = window.parent.location.href;
    </script>
<?php
exit();
}
$_SESSION["last_activity"] = time();

if (isset($_POST["export_data"])) {
	$result = ItemTable::item_table_data($_SESSION["date"]);

	$fp = fopen('php://output', 'w');
	if ($fp && $result) {
	    header('Content-Type: text/csv');
	    header('Content-Disposition: attachment; filename="Items_table_data.csv"');
	    ob_end_clean();

	    while ($row = $result->fetch_array(MYSQLI_NUM)) {
	        fputcsv($fp, array_values($row));
	    }
	    die;
	}
}
?>
<?php
if (isset($_FILES["import_data"])) {
	$file_name = $_FILES["import_data"]["tmp_name"];
	$file_ext = pathinfo($_FILES["import_data"]["name"], PATHINFO_EXTENSION);
	$file = fopen($file_name, 'r');
	$row = 0;
	$error_count = 0;

	if ($file_ext == "csv") {

		while(!feof($file)) {
			$price = false;
			$deviation = false;
			$row++;
			$data_row = fgetcsv($file);
			if (is_numeric($data_row[2]) && $data_row[2] > 0 ) {
				$price = $data_row[2];
			} elseif (empty($data_row[2])) {
				$price = "NULL";
			} else {
				$price = false;
				$error_count++;
			}

			if (is_numeric($data_row[3]) && $data_row[3] > 0 ) {
				$deviation = $data_row[3];
			} elseif (empty($data_row[3])) {
				$deviation = "NULL";
			} else {
				$deviation = false;
				$error_count++;
			}

			if ((!is_null($data_row[0])) && (is_numeric($price) || $price == "NULL") && (is_numeric($deviation) || $deviation == "NULL")) {
				ItemTable::import_data($data_row[0], $data_row[1], $price ,$deviation, $_SESSION["date"]);
			} else {
	?>
				<?php if ($price == false): ?>
					<?php 
							$error 	= "Warning: Row '".$row."', Item '".$data_row[0]."' has an invalid price."
					?>
					<script>
						$(document).ready(function() {
							displayError(<?php echo '"'.$error.'"';?>);
						});
					</script>
				<?php endif ?>

				<?php if ($deviation == false): ?>
					<?php 
							$error 	= "Warning: Row '".$row."', Item '".$data_row[0]."' has an invalid deviation."
					?>
					<script>
						$(document).ready(function() {
							displayError(<?php echo '"'.$error.'"';?>);
						});
					</script>
				<?php endif ?>
	<?php
			}
		}
	?>

		<?php if ($error_count == 0): ?>
			<script>
				$(document).ready(function() {
					displaySuccess();
				});
			</script>
		<?php endif ?>

		<?php 
		fclose($file);
	} else {
		?>
		<script>
			$(document).ready(function() {
				fileTypeError();
			});
		</script>
		<?php
	}
} 

$item_table = ItemTable::get_items_categories($_SESSION["date"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
	<title>Data Transfer</title>
</head>
<body>
 	<div class="main_iframe">
 		<div class="div_table font_roboto" id="items_div_table">
 			<div class="toolbar_print">
 				<div class="toolbar_div option">
		 			<form action="" method="post">
		 				<label for="export_data" style="font-weight: normal; padding: 0; color: inherit;">
		 					<span class="icon_small fa-upload"></span>
		 					<span class="icon_small_text">Export</span>
			 				<input type="submit" name="export_data" id="export_data" value="Export" class="display_none">
		 				</label>
		 			</form>
 				</div>
 				<div class="toolbar_div option">
		 			<form action="" method="post"  enctype="multipart/form-data">
		 				<label for="import_data" style="font-weight: normal; padding: 0; color: inherit;">
	 						<span class="icon_small fa-download"></span>
	 						<span class="icon_small_text">Import</span>
			 				<input type="file" name="import_data" id="import_data" accept=".csv" onchange="form.submit()" style="display: none">
		 				</label>
		 			</form>
 				</div>
 			</div>
            <div id="div_print_table">
                <table class="table_view" id="item_table_view" border="1px" >
                    <tr>
                        <th>Item</th>
                        <th>Unit</th>
                        <th id="th_price">Price</th>
                        <th id="th_deviation">Deviation</th>
                    </tr>
                    <tbody id="item_tbody">
                    <?php  $current_category = 1;?>
                    <?php mysqli_data_seek($item_table, 0); ?>
                    <?php  while($row = $item_table->fetch_assoc()): ?>
                    <?php  if ($row["category_name"] != $current_category AND $row["category_name"] != null): ?>
                            <?php $current_category = $row["category_name"];?>
                            <tr class="item_category_tr">
                                <td id="category" colspan="7" class="table_heading"><?php echo $row["category_name"]?><span class="arrow_down float_right collapse_arrow"></span></td>
                            </tr>
                    <?php elseif ($row["category_name"] != $current_category AND $row["category_name"] == null): ?>
                    <?php  $current_category = $row["category_name"];?>
                            <tr class="item_category_tr">
                                <td id="category" colspan="7" class="table_heading">Uncategorized Items<span class="arrow_down float_right collapse_arrow"></span></td>
                            </tr>
                    <?php endif ?>
                        <tr>
                            <td><span type="text" name="item_name" class="align_center item_name"><?php echo $row["name"]?> </span></td>
                            <td><span type="text" name="item_unit"  class="align_center"><?php echo $row["unit"]?></span></td>
                            <td>
                                <span type="number" name="item_price" class="align_center number_view">
                                	<?php echo $val = ($row["price"] > 0) ? "$".$row["price"] : "-" ;?> 
                            	</span>
                            </td>
                            <td>
                            	<span type="number" name="item_deviation class="align_center number_view">
                            		<?php echo $val = ($row["deviation"] > 0) ? $row["deviation"]."%" : "-" ; ?>
                        		</span>
                        	</td>
                        </tr>
                    <?php endwhile ?>
                    </tbody>
                </table>
            </div>
        </div>
 	</div>

	
</body>
</html>

<script src="https://cdn.rawgit.com/alertifyjs/alertify.js/v1.0.10/dist/js/alertify.js"></script>
<script>
	function displayError(error) {
		alertify
		.maxLogItems(10)
		.delay(0)
        .closeLogOnClick(true)
		.error(error);
		
	}

	function displaySuccess() {
		alertify
		.delay(5000)
		.success("Data import completed.");
	}

	function fileTypeError() {
		alertify
		.delay(5000)
		.error("File type is incorrect. File extension must be .csv");
	}
</script>
