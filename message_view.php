<?php
session_start();
include "utilities.php";
require_once "database/conversation_table.php";
require_once "database/message_table.php";

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
if (isset($_POST["status_to_read"])) {
    ConversationTable::update_conversation_status($_SESSION["username"], $_POST["conversation_id"], "read");
}
if (isset($_POST["reply"])) {
    if(MessageTable::create_message($_SESSION["username"], $_POST["receiver_name"], $_POST["message"], $_POST["conversation_id"], gmdate("Y-m-d H:i:s"))){
        ConversationTable::update_conversation_status($_POST["receiver_name"], $_POST["conversation_id"], "unread");
        ConversationTable::set_destroy_date($_POST["receiver_name"], $_POST["conversation_id"], 'NULL');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="main_iframe">
        <div class="messages_div" id="messages_div">
        <?php $result = MessageTable::get_messages($_POST["conversation_id"]); ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="main_message_div">
                <div ><?php echo $row["attachment"] ?></div>
                <div class="message_name" <?php if($row["username"] == $_SESSION["username"]) {echo "style='float:right;'";} ?>>
                    <span id="name"><?php echo $row["first_name"]." ".$row["last_name"] ?></span>
                </div>
                <div class="message">
                    <pre id="message"><?php echo $row["message"] ?></pre>
                    <span id="time"><?php echo convert_date_timezone($row["timestamp"]);?></span>
                </div>
            </div>
        <?php endwhile ?>
        </div>
        <div class="reply_div">
            <form action="message_view.php" method="post">
                <textarea name="message" id="reply_text" autofocus></textarea>
                <input type="hidden" name="conversation_id" value="<?php echo $_POST["conversation_id"] ?>">
                <input type="hidden" name="receiver_name" value="<?php echo $_POST["receiver_name"]; ?>">
            <div class="reply_toolbar">
                <input class="button" type="submit" name="reply" value="Reply">
            </div>
            </form>
        </div>
    </div>
</body>
</html>

<script>
    document.getElementById("messages_div").scrollIntoView(false);
</script>