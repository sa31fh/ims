<?php
session_start();
require_once "database/user_table.php";
require_once "database/conversation_table.php";

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
if (isset($_SESSION["last_activity"]) && $_SESSION["last_activity"] + $_SESSION["time_out"] * 60 < time()) {
    session_unset();
    session_destroy();
?>
    <script>
        window.parent.location.href = window.parent.location.href;
    </script>
<?php
exit();
}
$_SESSION["last_activity"] = time();

if (isset($_POST["message"])) {
    foreach ($_POST["recipient"] as $value) {
        ConversationTable::create_conversation($_SESSION["username"], $value, $_POST["title"],
            $_POST["message"], gmdate("Y-m-d H:i:s"),
            isset($_POST["attachment"]) ? $_POST["attachment"] : null,
            isset($_POST["attachment_title"]) ? $_POST["attachment_title"] : null, "read", "unread");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Compose</title>
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="main_iframe">
        <form id="compose_form" class="compose_form" onsubmit=submitMessage() action="compose_messages.php" method="post">
            <div class="compose_recipient">
                <label>To</label>
            </div>
            <div class="div_fade"></div>
            <div class="compose_title">
                <div class="name_drawer">
                    <ul>
                    <?php $result = UserTable::get_users(); ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php if ($row["username"] != $_SESSION["username"]): ?>
                            <li class="contact_li" value="<?php echo $row['username'] ?>"><?php echo $row["first_name"]." ".$row["last_name"]." (".$row["username"].")"; ?></li>
                        <?php endif ?>
                    <?php endwhile ?>
                    </ul>
                </div>
                <input type="text" name="title" placeholder="Title">
            </div>
            <div class="compose_text">
                <textarea name="message" placeholder="Message" required></textarea>
            </div>
            <div class="compose_attachment">
                <img src="images/paperclip.png" alt="" width="24px" height="21px">
            <?php if (isset($_POST["new_print_data"])): ?>
                <input id="name" name="attachment_title" value="<?php echo $_POST["print_table_name"]. ' - ' . $_POST["print_table_date"]?>"></input>
                <input type="hidden" name="attachment" id="attachment" value='<?php  echo $_POST["new_print_data"] ?>'>
            <?php endif ?>
            </div>
            <div class="compose_toolbar">
                <input type="submit" class="button"  value="Send">
            </div>
        </form>
    </div>
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-2.2.0.min.js"></script>
<script>
    function submitMessage() {
        window.parent.location.href = window.parent.location.href;
    }

    $(document).ready(function() {
        $(".compose_recipient").click(function() {
            $(".name_drawer").slideDown(180, "linear");
            $(".div_fade").css("display", "block");
        });

        $(".contact_li").click(function() {
            var contact = $(this).html();
            $(this).toggleClass(function() {
              if ($(this).hasClass("selected")) {
                $(".name_tag").each(function() {
                    if ($(this).children("span").html() == contact) {
                        $(this).remove();
                    }
                });
              } else {
                var span = "<div class='name_tag'>"+
                               "<span>"+contact+"</span>"+
                               "<input type='hidden' name='recipient[]' value='"+$(this).attr("value")+"'>"+
                           "</div>"
                $(".compose_recipient").append(span);
              }
              return "selected";
            });
        });

        $(".div_fade").click(function() {
            $(".div_fade").css("display", "none");
            $(".name_drawer").slideUp(180, "linear");
        });
    });
</script>
