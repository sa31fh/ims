<?php 
    include "sql_common.php";
    session_start();
    if (!isset($_SESSION["username"])) {
        header("Location: login.php");
        exit();
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
    <div>
        <table class="message_table" id="table">
            <?php $result = get_deleted_conversations($_SESSION["username"]) ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr onclick=openMessage(this)>
                    <td class="name"> <?php if ($row["sender"] == $_SESSION["username"]) {
                                              echo $row["receiver"];
                                            }else{ echo $row["sender"]; } ?> </td>
                    <td class="title"> <?php echo $row["title"] ?></td>
                    <td class="date"> <?php echo $row["timestamp"] ?></td>
                    <input type="hidden" name="conversation_id" value="<?php echo $row["id"] ?>"></form>
                </tr>
            <?php endwhile ?>
        </table>

        <form action="message_view.php" id="view_message" method="post">
            <input type="hidden" id="con_id" name="con_id" value="">
            <input type="hidden" id="receiver_name" name="receiver_name">
        </form>
   </div> 
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.js"></script>
<script>
    function openMessage(obj){
        var id = document.getElementById("table").rows[obj.rowIndex].children[3].value;
        var receiver = document.getElementById("table").rows[obj.rowIndex].children[0].value;
        document.getElementById("con_id").value = id;
        document.getElementById("receiver_name").value = receiver;
        document.getElementById("view_message").submit();
    }
</script>