<?php
session_start();
require_once "database/variables_table.php";
require_once "database/timeslot_table.php";

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
if (isset($_POST["expected_sales"])) {
    VariablesTable::update_expected_sales($_POST["expected_sales"]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Preview</title>
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="toolbar_print">
        <input class="option" type="button" onClick=goBack() value="Back">
        <div class="divider"></div>
        <div class="toolbar_div">
            <input class="toolbar_checkbox" type="checkbox" id="hide_checkbox" onclick=checkRequired()> <span id="hide_label">All</span>
        </div> <div class="divider"></div>
        <?php if ($_SESSION["userrole"] == "admin"): ?>
        <div class="toolbar_div">
            <form action="print_preview.php" method="post">
            <span >Expected Sales ($):</span>
            <input class="print_expected" type="number" name="expected_sales" value="<?php echo VariablesTable::get_expected_sales() ?>" onchange="this.form.submit()">
            </form>
        </div>
        <div class="divider"></div>
        <?php endif ?>
        <div class="toolbar_div">
            <a  id="print_share" class="option" onclick=sendPrint()>Share</a>
        </div>
    </div>

    <div class="div_table" id="div_table">
        <div class="div_left_tabs">
            <ul class="tab_ul">
                <li class="tab_li"><span id="day_tab" onclick=getTab(this)><?php echo "Full Day" ?></span></li>
            </ul>
        </div>
        <div class="div_right_tabs">
            <ul class="tab_ul inline" id="timeslot_ul">
            <?php $result = TimeslotTable::get_timeslots(); ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="tab_div" timeslot-name="<?php echo $row['name'] ?>">
                    <li class="tab_li" ><span onclick=getTab(this)><?php echo $row["name"] ?></span></li>
                </div>
            <?php endwhile ?>
            </ul>
        </div>
        <div class="none" id="div_print_table">
            <table class="table_view" id="print">
                <tr id="print_date" class="row">
                    <th colspan="5"><?php echo date('D, M d Y', strtotime($_SESSION["date"])); ?></th>
                </tr>
            </table>
        </div>
    </div>
    <form action="messages.php" id="print_form" method="post">
        <input type="hidden" id="print_data" name="print_data">
    </form>
    <input type="hidden" id="session_date" value="<?php echo $_SESSION["date"] ?>">
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-2.2.0.min.js"></script>
<script>
    function  getTab(tabName) {
        var timeSlotName = tabName.innerHTML;
        var date = document.getElementById("session_date").value;
        if (timeSlotName == "Full Day") {
            $.post("jq_ajax.php", {getPrintPreview: "", date: date}, function(data, status) {
                document.getElementById("print").innerHTML = data;
                checkRequired();
            });
        } else {
            $.post("jq_ajax.php", {getPrintPreviewTimeslots: "", date: date, timeSlotName: timeSlotName}, function(data, status) {
                document.getElementById("print").innerHTML = data;
                checkRequired();
            });
        }
    }

    function goBack() {
        location.assign("category_status.php");
    }

    function sendPrint() {
        var dat = document.getElementById("div_print_table").innerHTML;
        document.getElementById("print_data").value = dat;
        document.getElementById("print_form").submit();
    }

    function checkRequired() {
        if ($("#hide_checkbox").prop("checked")) {
            $(".print_tbody").each(function() {
                var total = $(this).find(".quantity_required").length;
                var remove = 0;
                $(this).find(".quantity_required").each(function() {
                  if (this.innerHTML <=0 || this.innerHTML == "-") {
                    $(this).parent().hide();
                    remove++;
                  }
                });
                if (total - remove == 0) {
                    $(this).hide();
                }
            });
            $("#hide_label").text("Required");
        } else {
            $(".print_tbody").each(function() {
                $(this).show();
                $(this).find("tr").show();
                $("#hide_label").text("All");
            });
        }
    }

    $(document).ready(function() {
        $(".tab_li span:first").each(function() {
           getTab($(this)[0]);
           $(this).parent().addClass("selected");
        });

        $(".tab_li").click(function() {
            $(".tab_li").removeClass("selected");
            $(this).addClass("selected");
        });
    });
</script>