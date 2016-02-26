<?php 
session_start();
include_once "utilities.php";
require_once "database/conversation_table.php";

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
} 
if (isset($_POST["conversation_id"])) {
    if(ConversationTable::change_conversation_status($_SESSION["username"], $_POST["conversation_id"], "deleted")) {
        $date = date_format((date_add(date_create(gmdate("Y-m-d")), date_interval_create_from_date_string("1 week"))), "Y-m-d");
        ConversationTable::set_destroy_date($_SESSION["username"], $_POST["conversation_id"], "'$date'");
    }
}
if (isset($_POST["checkbox"])) {
    ConversationTable::change_multiple_conversation_status($_SESSION["username"], $_POST["checkbox"], "deleted");
    $date = date_format((date_add(date_create(gmdate("Y-m-d")), date_interval_create_from_date_string("1 week"))), "Y-m-d");
    ConversationTable::set_multiple_destroy_date($_SESSION["username"], $_POST["checkbox"], "'$date'");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inbox</title>
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div>
        <div  class="toolbar_main">
            <div class="toolbar_div">
                <input title="Select All" id="select_all" type="checkbox">
            </div>
            <div class="toolbar_div" id="button_div">
                <form action="received_messages.php" id="multi_delete_form" method="post">
                    <span id="checked_count"></span>
                    <input class="option" type="submit" id="multi_delete" name="multi_delete" value="Delete">
                </form>
                <div class="dropdown_main">
                    <button class="option">Mark</button>
                    <div class="dropdown_div">
                        <a id="read"class="dropdown_content">Read</a>
                        <a id="unread"class="dropdown_content">Unread</a>
                    </div>
                </div>
            </div>
        </div>
        <table class="message_table" id="table">
            <?php $result = ConversationTable::get_received_conversations($_SESSION["username"]) ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr <?php if($row["sender"] == $_SESSION["username"] AND $row["sender_status"] == "unread" OR $row["receiver"] == $_SESSION["username"] AND $row["receiver_status"] == "unread" ) {
                              echo 'class="unread"';} ?> >
                    <td class="checkbox"><input type="checkbox" name="checkbox[]" form="multi_delete_form" value="<?php echo $row["id"] ?>"></td>
                    <td class="name" onclick=openMessage(this)> 
                        <input type="hidden" value="<?php echo $row['sender'] == $_SESSION['username'] ? $row['receiver'] : $row['sender']; ?>">
                        <?php echo $row["first_name"]." ".$row["last_name"]; ?> 
                    <td class="title" onclick=openMessage(this)> <?php echo $row["title"]; ?></td>
                    <td class="conversation" onclick=openMessage(this)> <?php echo $row["mSender"].": ".$row["message"]; ?></td>
                    <td class="date"> <?php echo convert_date_timezone($row["timestamp"]); ?></td>
                    <td class="delete_tr">
                        <form action="received_messages.php" method="post">
                        <input class="delete" type="image" src="images/delete.png" alt="delete" width="28px" height="28px">
                        <input type="hidden" name="conversation_id" value="<?php echo $row["id"] ?>"></form>
                    </td>
                </tr>
            <?php endwhile ?>
        </table>

        <form action="message_view.php" id="view_message" method="post">
            <input type="hidden" id="conversation_id" name="conversation_id">
            <input type="hidden" id="receiver_name" name="receiver_name">
            <input type="hidden" name="status_to_read">
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
        $("table tr").hover(function(){
            $(".delete", this).show();
        }, function(){
            $(".delete", this).hide();
        });

        $("table tr").change(function() {
            if($("input[type='checkbox']", this).prop('checked') == true){
                $("#button_div").fadeIn(200, "linear");
                $("#button_div").css("display", "inline-block");
                count_checked();
            } else if($("input[type='checkbox']").filter(':checked').length == 0) {
                $("#button_div").fadeOut(200, "linear");
            } else {
                count_checked();
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

        $(".dropdown_div #read").click(function(){
            $("input[type='checkbox']:checked").parents("tr").each(function(){
                $(this).removeClass();
                $(this).addClass("read");
            });
            var idVal = $("table input[type='checkbox']:checked").map(function(){
                return $(this).val();
            }).get();
            $.post("jq_ajax.php", {checkedId: idVal, newStatus: "read"});
            showUnreadCount();
        });
        $(".dropdown_div #unread").click(function(){
            $("input[type='checkbox']:checked").parents("tr").each(function(){
                $(this).removeClass();
                $(this).addClass("unread");
            });
            var idVal = $("table input[type='checkbox']:checked").map(function(){
                return $(this).val();
            }).get();
            $.post("jq_ajax.php", {checkedId: idVal, newStatus: "unread"});
            showUnreadCount();
        });
    });

    function showUnreadCount(){
        var unreadCount = $(".unread").length;
        if (unreadCount > 0) {
            window.parent.document.getElementById("status_view").innerHTML =  unreadCount;
            window.parent.document.getElementById("status_view").style.visibility = "visible";
            window.parent.document.getElementById("status_view").style.transform = "scale(1)";
        } else {
            window.parent.document.getElementById("status_view").style.transform = "scale(0.1)";
            window.parent.document.getElementById("status_view").style.visibility = "hidden";
        }
    }

    function count_checked(){
        var count = $("table input[type='checkbox']:checked").length;
        if(count == 0) {
            $("#checked_count").text("");
        } else if (count > 1) {
            $("#checked_count").text(count + " items selected");
        } else {
            $("#checked_count").text(count + " item selected");
        }
    }
</script>