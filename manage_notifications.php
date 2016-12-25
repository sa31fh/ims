<?php
session_start();
require_once "database/user_table.php";
require_once "database/notification_list_table.php";
require_once "database/sub_notification_list_table.php";
require_once "database/notification_status_table.php";
require_once "database/sub_notification_status_table.php";

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

if (isset($_POST["user_email"])) {
    UserTable::update_user_email($_SESSION["username"], $_POST["user_email"]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Notifications</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="main_iframe font_open_sans">
        <div class="div_manage">
            <?php $result = NotificationListTable::get_notification_list();
            while ($noti_list = $result->fetch_assoc()): ?>
                <div class="div_main_option">
                    <div class="container">
                        <span class="noti_name"><?php echo $noti_list["name"] ?></span>
                        <?php $noti_status = NotificationStatusTable::get_notification_status($_SESSION["username"], $noti_list["id"])->fetch_assoc(); ?>
                        <label class="switch">
                            <input class="switch-input" type="checkbox" onclick="showSubOption(this); setNotiStatus(this);" <?php echo $noti_status["status"] == 1 ? "checked" : "" ?>/>
                            <span class="switch-label" data-on="on" data-off="off"></span>
                            <span class="switch-handle"></span>
                        </label>
                        <input type="hidden" value="<?php echo $noti_list["id"] ?>">
                    </div>
                    <div class="div_sub">
                        <span class="hint">select notifications to receive alerts from</span>
                        <?php $rows = SubNotificationListTable::get_notification_list();
                        while ($sub_noti_list = $rows->fetch_assoc()): ?>
                            <div class="div_sub_option">
                                <span class="noti_name"><?php echo $sub_noti_list["name"] ?></span>
                                <?php $sub_noti_status = SubNotificationStatusTable::get_notification_status($_SESSION["username"], $sub_noti_list["id"], $noti_list["id"])->fetch_assoc(); ?>
                                 <label class="switch">
                                    <input class="switch-input" type="checkbox" <?php echo $sub_noti_status["status"] == 1 ? "checked" : "" ?>/>
                                    <span class="switch-label" data-on="on" data-off="off"></span>
                                    <span class="switch-handle"></span>
                                </label>
                                <input type="hidden" value="<?php echo $sub_noti_list["id"] ?>">
                                <input type="hidden" value="<?php echo $noti_list["id"] ?>">
                            </div>
                        <?php endwhile ?>
                        <?php if ($noti_list["name"] == "notify by email"): ?>
                        <div class="div_sub_option">
                            <span class="noti_name">Email</span>
                            <form action="manage_notifications.php" method="post">
                           <?php  $email = UserTable::get_user_details($_SESSION["username"])->fetch_assoc()["email"];?>
                                <input type="text" name="user_email" value="<?php echo $email ?>" onchange="this.form.submit();" placeholder="no email added">
                            </form>
                            <span class="hint">
                                email where notifications will be sent
                            </span>
                        </div>
                        <?php endif ?>
                    </div>
                </div>
            <?php endwhile ?>
        </div>
    </div>
    <input type="hidden" id="user_name" value="<?php echo $_SESSION["username"] ?>">
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-2.2.0.min.js"></script>
<script>

    function setNotiStatus(obj) {
        var notiId = obj.parentNode.parentNode.children[2].value;
        var userName = document.getElementById("user_name").value;
        var status = obj.checked;
        status = status == true ? 1 : 0;
        $.post("jq_ajax.php", {setNotiStatus: "", user_name: userName, notification_id: notiId, status: status});
    }

    function setSubNotiStatus(obj) {
        var notiId = obj.parentNode.parentNode.children[2].value;
        var parentNotiId = obj.parentNode.parentNode.children[3].value;
        var userName = document.getElementById("user_name").value;
        var status = obj.checked;
        status = status == true ? 1 : 0;
        $.post("jq_ajax.php", {setSubNotiStatus: "", user_name: userName, notification_id: notiId, status: status, parent_noti_id: parentNotiId});
    }

    function showSubOption(obj) {
        if (obj.checked) {
            $(obj).parent().parent().parent().children(".div_sub").show();
        } else {
            $(obj).parent().parent().parent().children(".div_sub").hide();
        }
    }

    $(document).ready(function() {
        $(".container .switch-input").each(function() {
            if ($(this).prop("checked")) {
                $(this).parent().parent().next().show();
            }
        });

        $(".container .switch-input").change(function() {
            $(this).parents(".div_main_option").find(".div_sub .switch-input").prop("checked", $(this).prop("checked")).change();
        });

        $(".div_sub .switch-input").change(function() {
            setSubNotiStatus($(this)[0]);
        })
    });
</script>
