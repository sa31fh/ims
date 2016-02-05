<?php
    include "sql_common.php";
    session_start();
    if (!isset($_SESSION["username"])) {
        header("Location: login.php");
        exit();
    }
    if (isset($_POST["reply"])) {
        create_new_message($_SESSION["username"], $_POST["receiver_name"], $_POST["message"], $_POST["conversation_id"], gmdate("Y-m-d H:i:s"));
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
    <?php $result = get_messages($_POST["conversation_id"]); ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="message"><?php echo $row["attachment"] ?></div>
        <div class="message">
            <span id="name"><?php echo $row["first_name"]." ".$row["last_name"] ?></span><br/>
            <span id="message"><?php echo $row["message"] ?></span>
            <span id="time"><?php echo convert_date_timezone($row["timestamp"]);?></span>
        </div>
    <?php endwhile ?>
    <div class="reply_div">
        <form action="message_view.php" method="post">
            <textarea name="message" id="reply_text"></textarea>
            <input class="button" type="submit" name="reply" value="Reply">
            <input type="hidden" name="conversation_id" value="<?php echo $_POST["conversation_id"] ?>">
            <input type="hidden" name="receiver_name" value="<?php echo $_POST["receiver_name"]; ?>">
        </form>
    </div>
</body>
</html>