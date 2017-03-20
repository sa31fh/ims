<?php
session_start();
require_once "database/user_table.php";
require_once "database/user_role_table.php";
require_once "database/cash_closing_table.php";

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

if (isset($_POST["add_row"])) {
    CashClosingTable::add_row($_SESSION["date"]);
}
if (isset($_POST["checkbox"])) {
    CashClosingTable::delete_rows($_POST["checkbox"], $_SESSION["date"]);
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
    <div class="main_iframe font_open_sans">
        <div class="div_table">
            <div class="flex_row div_table_heading">
                <div class="flex_1 side_option">
                    <div class="checkbox">
                        <input type="checkbox" id="select_all">
                        <span class="checkbox_style"></span>
                    </div>
                </div>
                <div class="flex_2 ">title</div>
                <div class="flex_2 ">type</div>
            </div>
            <ul class="flex_col" id="div_rows">
                <?php $result = CashClosingTable::get_rows($_SESSION["date"]); ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li class="flex_row" id="<?php echo $row['id'] ?>">
                        <div class="flex_1 side_option">
                            <div class="checkbox">
                                <input type="checkbox" name="checkbox[]" form="multi_delete_form" value="<?php echo $row["id"] ?>">
                                <span class="checkbox_style"></span>
                            </div>
                            <div class="reorder">
                                <span class="fa-bars"></span>
                            </div>
                        </div>
                        <div class="flex_2 div_cell">
                            <input type="text" onchange=updateName(this)  placeholder='enter title' value="<?php echo $row["name"]; ?>" >
                        </div>
                        <div class="flex_2 div_cell">
                            <select name="" id="" onchange=updateType(this)>
                                <option value="0" <?php echo $row["type"] == "0" ? "selected" : "" ?> >add</option>
                                <option value="1" <?php echo $row["type"] == "1" ? "selected" : "" ?> >subtract</option>
                                <option value="" <?php echo $row["type"] == "" ? "selected" : "" ?>>no action</option>
                            </select>
                        </div>
                        <input type="hidden" id="row_id" value=<?php echo $row["id"] ?>>
                    </li>
                <?php endwhile ?>
            </ul>
            <div class="toolbar_print">
                <div class="toolbar_div option">
                    <form action="cash_closing.php" id="multi_delete_form" method="post" onclick="this.submit();">
                        <span class="icon_small entypo-trash"></span>
                        <span class="icon_small_text">delete</span>
                    </form>
                </div>
                <div class="toolbar_div option">
                    <form action="cash_closing.php" method="post">
                        <button name="add_row" class="button_round entypo-plus" id="add_button" ></button>
                    </form>
                </div>
                <div class="toolbar_div">
                    <div class="option" id="reorder_button">
                        <span class="icon_small fa-list-ol"></span>
                        <span class="icon_small_text">reorder</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-2.2.0.min.js"></script>
<script src="https://cdn.rawgit.com/alertifyjs/alertify.js/v1.0.10/dist/js/alertify.js"></script>
<script
      src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"
      integrity="sha256-xNjb53/rY+WmG+4L6tTl9m6PpqknWZvRt0rO1SRnJzw="
      crossorigin="anonymous"></script>
<script src="touch_punch.js"></script>
<script>
    function addRow() {
        var divMain = document.createElement("div");
        var div = document.createElement("div");
        var divInput = document.createElement("div");
        var divSelect = document.createElement("div");
        var input = document.createElement("input");
        var select = document.createElement("select");
        var optionNames = ["add", "subtract", "nothing"];
        divMain.className = "flex_row";
        div.className = "flex_1 side_option";
        divInput.className = "flex_2 div_cell";
        input.onchange = updateName;
        divSelect.className = "flex_2 div_cell";
        for (var i = 0; i < 3; i++) {
            var option = document.createElement("option");
            option.text = optionNames[i];
            option.value = i;
            select.add(option);
        }
        divInput.append(input);
        divSelect.append(select)
        divMain.append(div);
        divMain.append(divInput);
        divMain.append(divSelect);

        $("#div_rows").append(divMain);
    }

    function updateName(obj) {
        var id = $(obj).parent().parent().find("#row_id").val();
        var name = obj.value;
        $.post("jq_ajax.php", {updateCashRowName: "", id: id, name: name});
    }

    function updateType(obj) {
        var id = $(obj).parent().parent().find("#row_id").val();
        var type = obj.value;
        $.post("jq_ajax.php", {updateCashRowType: "", id: id, type: type});
    }

    $(document).ready(function() {

        $("#select_all").change(function(){
            $("input[type='checkbox']").prop("checked", $(this).prop("checked"));
            // if ($("#select_all").prop("checked")) {
            //     $("#button_div").fadeIn(200, "linear");
            //     $("#button_div").css("display", "inline-block");
            // } else {
            //     $("#button_div").fadeOut(200, "linear");
            // }
        });

        $("#div_rows").sortable({
            revert: 150,
            containment: "#div_rows",
            handle: $(".reorder"),
            start: function(event, ui) {
                $("#div_rows li").not(ui.item).addClass("not_dragging");
                ui.item.addClass("dragging");

            },
            stop: function (event, ui) {
                ui.item.removeClass("dragging");
                $("#div_rows li").removeClass("not_dragging");
            },
            update: function(event, ui) {
                var ids = $(this).sortable("toArray");
                $.post("jq_ajax.php", {UpdateCashClosingOrder: "", rowIds: ids});
            }
        });

        $("#reorder_button").click(function() {
            $(this).toggleClass("selected");
            if ($(this).hasClass("selected")) {
                $(".checkbox").css("display", "none");
                $(".reorder").css("display", "inline-block");
            } else {
                $(".reorder").css("display", "none");
                $(".checkbox").css("display", "inline-block");

            }
        });

    });
</script>