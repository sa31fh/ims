<?php 
    include "sql_common.php";
    session_start();
    if (!isset($_SESSION["username"])) {
        header("Location: login.php");
        exit();
    }
    if (isset($_POST["conversation_id"])) {
        if(change_conversation_status($_SESSION["username"], $_POST["conversation_id"], "read")){
            set_destroy_date($_SESSION["username"], $_POST["conversation_id"], 'NULL');
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Deleted Messages</title>
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div>
        <table class="message_table" id="table">
            <?php $result = get_deleted_conversations($_SESSION["username"]) ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr onclick=openMessage(this)>
                    <td class="name">
                        <input type="hidden" value="<?php echo $row['sender'] == $_SESSION['username'] ? $row['receiver'] : $row['sender']; ?>"> 
                        <?php echo $row["first_name"]." ".$row["last_name"]; ?> </td>
                    <td class="title"> <?php echo $row["title"] ?></td>
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

<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.js"></script>
<script>
    function openMessage(obj){
        var id = document.getElementById("table").rows[obj.rowIndex].cells[3].children[0].children[1].value;
        var receiver = document.getElementById("table").rows[obj.rowIndex].children[0].value;
        document.getElementById("conversation_id").value = id;
        document.getElementById("receiver_name").value = receiver;
        document.getElementById("view_message").submit();
    }
</script>