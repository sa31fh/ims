<?php 
session_start();
include "utilities.php";
require_once "database/conversation_table.php";

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
if (isset($_POST["conversation_id"])) {
    if(ConversationTable::change_conversation_status($_SESSION["username"], $_POST["conversation_id"], "read")){
       ConversationTable::set_destroy_date($_SESSION["username"], $_POST["conversation_id"], 'NULL');
    }
}
if (isset($_POST["checkbox"])) {
    ConversationTable::change_multiple_conversation_status($_SESSION["username"], $_POST["checkbox"], "read");
    ConversationTable::set_destroy_date($_SESSION["username"], $_POST["checkbox"], 'NULL');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Deleted</title>
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div>
        <div class="toolbar_main">
            <div class="toolbar_div">
                <input title="Select All" id="select_all" type="checkbox">
            </div>
            <div class="toolbar_div" id="button_div">
            <form action="deleted_messages.php" id="multi_delete_form" method="post">
                <span id="checked_count"></span>
                <input class="option" type="submit" id="multi_delete" name="multi_delete" value="Move to Inbox">
            </form>
            </div>
        </div>
        <table class="message_table" id="table">
            <?php $result = ConversationTable::get_deleted_conversations($_SESSION["username"]) ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr >
                    <td class="checkbox"><input type="checkbox" name="checkbox[]" form="multi_delete_form" value="<?php echo $row["id"] ?>"></td>
                    <td class="name" onclick=openMessage(this)>
                        <input type="hidden" value="<?php echo $row['sender'] == $_SESSION['username'] ? $row['receiver'] : $row['sender']; ?>"> 
                        <?php echo $row["first_name"]." ".$row["last_name"]; ?> </td>
                    <td class="title" onclick=openMessage(this)> <?php echo $row["title"] ?></td>
                    <td class="conversation" onclick=openMessage(this)> <?php echo $row["mSender"].": ".$row["message"]; ?></td>
                    <td class="date"> <?php echo convert_date_timezone($row["timestamp"]); ?></td>
                    <td class="delete"><form action="deleted_messages.php" method="post">
                        <input class="button" type="submit" value="undelete">
                        <input type="hidden" name="conversation_id" value="<?php echo $row["id"] ?>"></form>
                    </td>
                </tr>
            <?php endwhile ?>
        </table>

        <form action="message_view.php" id="view_message" method="post">
            <input type="hidden" id="conversation_id" name="conversation_id">
            <input type="hidden" id="receiver_name" name="receiver_name">
        </form>
   </div> 
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-2.2.0.min.js"></script>
<script>
    function openMessage(obj){
        var id = document.getElementById("table").rows[obj.parentNode.rowIndex].cells[5].children[0].children[1].value;
        var receiver = document.getElementById("table").rows[obj.parentNode.rowIndex].cells[1].children[0].value;
        document.getElementById("conversation_id").value = id;
        document.getElementById("receiver_name").value = receiver;
        document.getElementById("view_message").submit();
    }
    $(document).ready(function(){
        $("table tr").change(function() {
            if($("input[type='checkbox']", this).prop('checked') == true){
                $("#button_div").fadeIn(200, "linear");
                $("#button_div").css("display", "inline-block");
                count_checked();
            } else if($("input[type='checkbox']").filter(':checked').length == 0) {
                $("#button_div").fadeOut(200, "linear");
            }
        });

        $("#select_all").change(function(){
            $("input[type='checkbox']").prop("checked", $(this).prop("checked"));
            if ($("#select_all").prop("checked") == true) {
                $("#button_div").fadeIn(200, "linear");
                $("#button_div").css("display", "inline-block");
                count_checked();
            } else {
                $("#button_div").fadeOut(200, "linear");
            }
        });

        function count_checked(){
            var count = $("input[type='checkbox']:checked").length;
            if(count == 0) {
                $("#checked_count").text("");
            } else if (count > 1) {
                $("#checked_count").text(count + " items select");
            } else {
                $("#checked_count").text(count + " item select");
            }
        }
    });
</script>