<?php
session_start();
require_once "database/variables_table.php";
require_once "database/timeslot_table.php";
require_once "mpdf/vendor/autoload.php";

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
if (isset($_POST["expected_sales"])) {
    VariablesTable::update_expected_sales($_POST["expected_sales"]);
}
if (isset($_POST["table_data"])) {
    $mpdf = new mPDF("", "A4", 0, 'roboto', 0, 0, 0, 0, 0, 0);
    $stylesheet = file_get_contents("styles.css");
    $mpdf->useSubstitutions=false;
    $mpdf->simpleTables = true;
    $mpdf->WriteHtml($stylesheet, 1);
    $mpdf->WriteHtml($_POST["table_data"], 2);
    $mpdf->Output($_POST["table_name"]." - ".$_POST["table_date"].".pdf", "D");
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
            <label class="switch float_left" id="toolbar_toggle">
                <input class="switch-input" type="checkbox" onclick=checkRequired() />
                <span class="switch-label" data-on="Required" data-off="All"></span>
                <span class="switch-handle"></span>
            </label>
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
            <a id="print_share" class="option" onclick=sendPrint()>Share</a>
        </div>
        <div class="divider"></div>
        <div class="toolbar_div">
            <a id="print_pdf" class="option" onclick=printPdf()>PDF</a>
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
                    <th colspan="5">
                        <span><?php echo date_format((date_add(date_create($_SESSION["date"]), date_interval_create_from_date_string("1 day"))), 'D, M d Y'); ?></span>
                        <div class="print_table_date"><?php echo "created on ".date('D, M d Y', strtotime($_SESSION["date"])); ?></div>
                    </th>
                </tr>
            </table>
        </div>
    </div>

    <div class="div_popup_back">
        <div class="div_popup popup_share">
            <div class="popup_titlebar">New Message
                <input type="button" class="popup_cancel white" id="popup_cancel" value="x">
            </div>
            <iframe id="popup_frame" name="popup_frame" src="" frameborder="0"></iframe>
        </div>
    </div>

    <input type="hidden" id="session_date" value="<?php echo $_SESSION["date"] ?>">

    <form action="compose_messages.php" method="post" id="print_form" target="popup_frame">
        <input type="hidden" id="print_table_date" name="print_table_date">
        <input type="hidden" id="print_table_name" name="print_table_name">
        <input type="hidden" id="new_print_data" name="new_print_data">
    </form>

    <form action="print_preview.php" method="post" id="test_form" name="test_form">
        <input type="hidden" id="table_data" name="table_data">
        <input type="hidden" id="table_date" name="table_date">
        <input type="hidden" id="table_name" name="table_name">
    </form>
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-2.2.0.min.js"></script>
<script>
    function  getTab(tabName) {
        var timeSlotName = tabName.innerHTML;
        var date = document.getElementById("session_date").value;
        if (timeSlotName == "Full Day") {
            $.post("jq_ajax.php", {getPrintPreview: "", date: date}, function(data, status) {
                $(".print_tbody").remove();
                $("#print").append(data);
                checkRequired();
            });
        } else {
            $.post("jq_ajax.php", {getPrintPreviewTimeslots: "", date: date, timeSlotName: timeSlotName}, function(data, status) {
                $(".print_tbody").remove();
                $("#print").append(data);
                checkRequired();
            });
        }
    }

    function printPdf() {
        var table = document.createElement("table");
        table.setAttribute("class", "table_view");
        $(".table_view tr").each(function() {
            if($(this).css('display') != 'none') {
                table.innerHTML += this.outerHTML;
            }
        });
        $("#table_data").val(table.outerHTML);
        document.getElementById("table_name").value = $(".tab_li.selected").children().html();
        document.getElementById("table_date").value = $("#print_date").children().children().html();
        $("#test_form").submit();
    }

    function goBack() {
        location.assign("category_status.php");
    }

    function sendPrint() {
        var data = document.getElementById("div_print_table").innerHTML;
        document.getElementById("new_print_data").value = data;
        document.getElementById("print_table_name").value = $(".tab_li.selected").children().html();
        document.getElementById("print_table_date").value = $("#print_date").children().children().html();
        $(".div_popup_back").css("display", "block");
        $("#print_form").submit();
    }

    function checkRequired() {
        if ($(".switch-input").prop("checked")) {
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
                    $(this).children().hide();
                }
            });
        } else {
            $(".print_tbody").each(function() {
                $(this).show();
                $(this).find("tr").show();
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

        $("#popup_cancel").click(function() {
            $(".main_iframe").removeClass("blur");
            $(".div_popup_back").fadeOut(190, "linear");
        });
    });
</script>