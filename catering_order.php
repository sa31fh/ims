<?php
session_start();
require_once "database/catering_order_table.php";

if (!isset($_SESSION["username"])) {
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

if (isset($_POST["order_name"]) AND !empty($_POST["order_name"]) AND !isset($_POST["order_id"])) {
    CateringOrderTable::add_order($_POST["order_name"], $_POST["selected_date"], $_SESSION["date"]);?>
    <script>window.parent.location.href = window.parent.location.href;</script> <?php
}
if (isset($_POST["order_id"])) {
    CateringOrderTable::edit_order($_POST["order_id"], $_POST["order_name"], $_POST["selected_date"]);?>
    <script>window.parent.location.href = window.parent.location.href;</script> <?php
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>new order</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="main_iframe font_open_sans">
        <form class="flex_1" action="catering_order.php" method="post" >
            <div class="div_catering">
                <div>
                    <div class="div_heading">order name</div>
                    <input name="order_name" type="text" placeholder="ENTER NAME"
                           value="<?php echo isset($_POST["edit_order_name"]) ? $_POST["edit_order_name"] : ""?>">
                </div>
                <div>
                    <div class="div_heading">set delivery date</div>
                    <div id="date_holder"></div>
                    <?php $date = isset($_POST["edit_order_date"]) ? $_POST["edit_order_date"] : $_SESSION["date"];?>
                    <input name="selected_date" type="hidden" id="selected_date" value="<?php echo $date ?>">
                </div>
                <div class="div_add">
                    <button class="button"><?php echo isset($_POST["edit_order_name"]) ? "save changes" : "create" ?></button>
                </div>
            </div>
            <?php if (isset($_POST["edit_order_id"])): ?>
                <input type="hidden" name="order_id" value="<?php echo $_POST["edit_order_id"] ?>">
            <?php endif ?>
        </form>
    </div>
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-2.2.0.min.js"></script>
<script
      src="http://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
      integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="
      crossorigin="anonymous"></script>
<script>

    function submitOrder() {
        window.parent.location.href = window.parent.location.href;
    }

    $(document).ready(function() {

        $("#date_holder").datepicker({
            dateFormat: "yy-mm-dd",
            defaultDate: $("#selected_date").val(),
            dayNamesMin: [ "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat" ],
            currentText: "close",
            prevText: "previous",
            onSelect: function(dateText) {
                $("#selected_date").val(dateText);
            }
        });
    });
</script>