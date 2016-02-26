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
        <li><a id="compose" href="compose_messages.php" target="message_frame" >Compose</a></li>
        <li><a id="inbox" class="active" href="received_messages.php" target="message_frame" >Inbox <span id="status_view"></span></a></li>
        <li><a id="deleted" href="deleted_messages.php" target="message_frame">Deleted</a></li>
    </ul>
    <?php $page = "messages";
          include_once "new_nav.php" ?>

    <div class="main_messages">
        <iframe src="received_messages.php" frameborder="0" name="message_frame" id="message_frame" scrolling="no" onload="adjustHeight(this); showUnreadCount(this); changeActiveClass(this);" ></iframe>
    </div>

    <form action="compose_messages.php" method="post" id="print_form" target="message_frame">
         <input type="hidden" name="new_print_data" value='<?php if(isset($_POST["print_data"])){echo $_POST["print_data"];}?>'>
    </form>
    <input type="hidden" id="session_name" value="<?php echo $_SESSION['username']; ?>">
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-2.2.0.min.js"></script>
<script>
    function adjustHeight(iframe) {
        iframe.height = 0 + "px";
        var nHeight = iframe.contentWindow.document .body.scrollHeight;
        iframe.height = (nHeight + 60) + "px";
    }

    function changeActiveClass(iframe) {
        if (iframe.contentDocument.title == "Compose") {
            document.getElementById("inbox").className = "";
            document.getElementById("deleted").className = "";
            document.getElementById("compose").className = "active";
        } else if(iframe.contentDocument.title == "Inbox") {
            document.getElementById("deleted").className = "";
            document.getElementById("compose").className = "";
            document.getElementById("inbox").className = "active";
        } else if (iframe.contentDocument.title == "Deleted") {
            document.getElementById("inbox").className = "";
            document.getElementById("compose").className = "";
            document.getElementById("deleted").className = "active";
        }
    }

    function showUnreadCount(iframe){
        if (iframe.contentDocument.title == "Inbox") {
            var unreadCount = $("#message_frame").contents().find(".unread").length;
            if (unreadCount > 0) {
                document.getElementById("status_view").innerHTML =  unreadCount;
                document.getElementById("status_view").style.visibility = "visible";
                document.getElementById("status_view").style.transform = "scale(1)";
            } else {
                document.getElementById("status_view").style.transform = "scale(0.1)";
                document.getElementById("status_view").style.visibility = "hidden";
            }
        }
    }
</script>

<?php if (isset($_POST["print_data"])): ?>
     <script> $(function(){ $("#print_form").submit();  });</script>
<?php endif ?>