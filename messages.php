<?php
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
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
</head>
<body>
    <ul class="sidenav" id="sideNav">
        <li><a href="compose_messages.php" target="message_frame" >Compose</a></li>
        <li><a class="active" href="received_messages.php" target="message_frame" >Inbox <span id="status_view"></span></a></li>
        <li><a href="deleted_messages.php" target="message_frame">Deleted</a></li>
    </ul>
    <?php $page = "messages";
          include_once "new_nav.php" ?>

    <div class="main_messages">
        <iframe src="received_messages.php" frameborder="0" name="message_frame" id="message_frame" scrolling="no" onload="adjustHeight(id); onclick=showUnreadCount();" ></iframe>
    </div>

    <form action="compose_messages.php" method="post" id="print_form" target="message_frame">
         <input type="hidden" name="new_print_data" value='<?php if(isset($_POST["print_data"])){echo $_POST["print_data"];}?>'>
    </form>
    <input type="hidden" id="session_name" value="<?php echo $_SESSION['username']; ?>">
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.js"></script>
<script>
    function adjustHeight(iframeID){
        var iframe = document.getElementById(iframeID);
        iframe.height = 0 + "px";
        var nHeight = iframe.contentWindow.document .body.scrollHeight;
        iframe.height = (nHeight + 60) + "px";
    }

    $(function() {
        $('#sideNav li a').click(function() {
           $('#sideNav li a').removeClass();
           $(this).addClass('active');
        });
     });

    function showUnreadCount(){
        var sessionName  = document.getElementById("session_name").value;

        $(function(){
            $.post("jq_ajax.php", {sessionName: sessionName, status: "unread"}, function(data,status){
                if (data > 0) {
                    document.getElementById("status_view").innerHTML = "(" + data + ")";
                } else {
                    document.getElementById("status_view").innerHTML =  "";
                };
            });
        });
    }
</script>

<?php if (isset($_POST["print_data"])): ?>
     <script> $(function(){ $("#print_form").submit();  });</script>
<?php endif ?>