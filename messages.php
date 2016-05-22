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
        <li id="compose_li"><button class="button" onclick=composeButton()>Compose</button></li>
        <li><a id="inbox" class="active" href="received_messages.php" target="message_frame" >Inbox <span id="status_view"></span></a></li>
        <li><a id="deleted" href="deleted_messages.php" target="message_frame">Deleted</a></li>
    </ul>
    <?php $page = "messages";
          include_once "new_nav.php" ?>

    <div class="main_top_side">
        <iframe class="iframe" src="received_messages.php" frameborder="0" name="message_frame" id="message_frame" onload="showUnreadCount(this);" ></iframe>
    </div>

     <div class="div_popup_back">
        <div class="div_popup popup_share">
            <div class="popup_titlebar">New Message
                <input type="button" class="popup_cancel white" id="popup_cancel" value="x">
            </div>
            <iframe id="popup_frame" name="popup_frame" src="compose_messages.php" frameborder="0"></iframe>
        </div>
    </div>

    <form action="compose_messages.php" method="post" id="print_form" target="message_frame">
         <input type="hidden" name="new_print_data" value='<?php if(isset($_POST["print_data"])){echo $_POST["print_data"];}?>'>
    </form>
    <input type="hidden" id="session_name" value="<?php echo $_SESSION['username']; ?>">
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-2.2.0.min.js"></script>
<script>
    function composeButton() {
        $(".div_popup_back").css("display", "block");
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

    $(document).ready(function() {
        $("#popup_cancel").click(function() {
            $(".div_popup_back").fadeOut(190, "linear");
        });

        $("a").click(function() {
            $("a").removeClass("active");
            $(this).addClass("active");
        });
    });
</script>

<?php if (isset($_POST["print_data"])): ?>
     <script> $(function(){ $("#print_form").submit();  });</script>
<?php endif ?>
