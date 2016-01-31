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
    <title>Messages</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <ul class="sidenav" id="sideNav">
        <li><a href="compose_messages.php" target="message_frame" >Compose</a></li>
        <li><a class="active" href="received_messages.php" target="message_frame" >Inbox</a></li>
        <li><a href="deleted_messages.php" target="message_frame">Deleted</a></li>
    </ul>
    <?php $page = "messages";
          include_once "new_nav.php" ?>

    <div class="main_messages">
        <iframe src="received_messages.php" frameborder="0" name="message_frame" id="message_frame" scrolling="no" onload=adjustHeight(id) ></iframe>
    </div>

    <form action="compose_messages.php" method="post" id="print_form" target="message_frame">
         <input type="hidden" name="new_print_data" value='<?php if(isset($_POST["print_data"])){echo $_POST["print_data"];}?>'>
    </form>
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.js"></script>
<script>
    function adjustHeight(iframeID){
        var iframe = document.getElementById(iframeID);
        var nHeight = iframe.contentWindow.document .body.scrollHeight;
        iframe.height = (nHeight + 60) + "px";
    }

    $(function() {
        $('#sideNav li a').click(function() {
           $('#sideNav li a').removeClass();
           $(this).addClass('active');
        });
     });
</script>

<?php if (isset($_POST["print_data"])): ?>
     <script> $(function(){ $("#print_form").submit();  });</script>
<?php endif ?>