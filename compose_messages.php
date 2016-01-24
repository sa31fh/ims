<?php
    include "sql_common.php";
    session_start();
    if (!isset($_SESSION["username"])) {
        header("Location: login.php");
        exit();
    }
    if (isset($_POST["message"])) {
        new_conversation($_SESSION["username"], $_POST["recipient"], $_POST["title"], $_POST["message"], $_POST["messDate"], $_POST["attached"]);
        header("Location: received_messages.php" );
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
    <?php  $date = date("d-m-Y");?>

    <div>
        <form action="compose_messages.php" method="post">
            <input type="text" name="recipient" placeholder="Recipient" required><br/>
            <input type="text" name="title" placeholder="Title"><br/>
            <textarea name="message" id="" cols="30" rows="10" placeholder="Message" required></textarea><br/>
            <input type="hidden" name="messDate" value="<?php echo $date ?>"> 
            <input type="submit" value="Send" class="button">
            <?php if (isset($_POST["new_print_data"])): ?>
                <label for="attached">Attachment</label>
                <input type="hidden" name="attached" id="attached" value='<?php  echo $_POST["new_print_data"] ?>'>
            <?php endif ?>
        </form>
    </div>
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.js"></script>
<script>
    function showTable(){
        document.getElementById("print_table").innerHTML = document.getElementById("value_holder").value;
    }
</script>