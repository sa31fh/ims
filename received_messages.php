<?php 
include_once "functions.php";
require_once "database/conversation_table.php";
session_start();
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
        <table class="message_table" id="table">
            <?php $result = ConversationTable::get_received_conversations($_SESSION["username"]) ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr <?php if($row["sender"] == $_SESSION["username"] AND $row["sender_status"] == "unread" OR $row["receiver"] == $_SESSION["username"] AND $row["receiver_status"] == "unread" ) {
                              echo 'class="unread"';} ?> onclick=openMessage(this)>
                    <td class="name"> 
                        <input type="hidden" value="<?php echo $row['sender'] == $_SESSION['username'] ? $row['receiver'] : $row['sender']; ?>">
                        <?php echo $row["first_name"]." ".$row["last_name"]; ?> 
                    <td class="title"> <?php echo $row["title"] ?></td>
                    <td class="date"> <?php echo convert_date_timezone($row["timestamp"]); ?></td>
                    <td class="delete">
                        <form action="received_messages.php" method="post">
                        <input type="image" src="images/delete.png" alt="delete" width="30" height="30">
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

<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.js"></script>
<script>
    function openMessage(obj){
        var id = document.getElementById("table").rows[obj.rowIndex].cells[3].children[0].children[1].value;
        var receiver = document.getElementById("table").rows[obj.rowIndex].cells[0].children[0].value;
        document.getElementById("conversation_id").value = id;
        document.getElementById("receiver_name").value = receiver;
        document.getElementById("view_message").submit();
    }
</script>