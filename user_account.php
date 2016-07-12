<?php
session_start();
require_once "database/user_table.php";

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
if (isset($_SESSION["last_activity"]) && $_SESSION["last_activity"] + $_SESSION["time_out"] * 60 < time()) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
$_SESSION["last_activity"] = time();

if (isset($_POST["new_password"])) {
    UserTable::update_user_password($_POST["user_name"], $_POST["new_password"]);
}
if (isset($_POST["update_user"])) {
    $tz = $_SESSION["timezone"];
    if (!empty($_POST["city_select"])) {
        $tz = ($_POST["region_select"]. "/" .$_POST["city_select"]);
        $_SESSION["timezone"] = $tz;
    }
    if (UserTable::update_user_details($_SESSION["username"], $_POST["update_user"], $_POST["update_first"], $_POST["update_last"], $tz, $_POST["update_timeout"])) {
        $_SESSION["username"] = $_POST["update_user"];
        $_SESSION["time_out"] = $_POST["update_timeout"];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account</title>
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include_once "new_nav.php" ?>
    <div class="main">
        <div class="div_card">
            <h4>Edit Credentials<hr></h4>
            <?php $result = UserTable::get_user_details($_SESSION["username"]);
                  $row = $result->fetch_assoc(); ?>
            <form action="user_account.php" method="post">
                <div>
                    <label>First Name<input class="userinput" name="update_first" type="text" value="<?php echo $row["first_name"] ?>" placeholder="First Name"></label>
                    <label>Last Name<input class="userinput" name="update_last" type="text" value="<?php echo $row["last_name"] ?>" placeholder="Last Name"></label>
                    <label>User Name<input class="userinput" name="update_user" type="text" value="<?php echo $row["username"] ?>" placeholder="User Name"></label>
                </div>
                <div>
                    <label>Time zone<input class="userinput" type="text" value="<?php echo $_SESSION['timezone'] ?>" readonly>
                    <div class="inline none">
                        <select class="user_select" name="region_select" id="region_select" onchange=onTimeZoneSelect(this)>
                            <?php $oldregion = ""; ?>
                            <?php foreach (timezone_identifiers_list() as $tz): ?>
                                <?php $region = explode("/", $tz); ?>
                                <?php $removetz = array('Pacific', 'Antarctica', 'Arctic', 'UTC', 'Indian', 'Atlantic', $oldregion); ?>
                                <?php if (!in_array($region[0], $removetz, true)): ?>
                                         <option value="<?php echo $region[0] ?>"> <?php echo $region[0] ?></option>
                                <?php endif ?>
                            <?php $oldregion= $region[0]; endforeach ?>
                        </select>
                        <select class="user_select" name="city_select" id="city_select"></select>
                        </label>
                    </div>
                </div>
                <div>
                    <label for="update_timeout">Session Timeout</label>
                    <select class="user_select" name="update_timeout" >
                        <option value="15" <?php echo $row["time_out"] == 15? "selected" : "" ?>>15 mins</option>
                        <option value="30" <?php echo $row["time_out"] == 30? "selected" : "" ?>>30 mins</option>
                        <option value="60" <?php echo $row["time_out"] == 60? "selected" : "" ?>>60 mins</option>
                        <option value="120" <?php echo $row["time_out"] == 120? "selected" : "" ?>>120 mins</option>
                        <option value="150" <?php echo $row["time_out"] == 150? "selected" : "" ?>>150 mins</option>
                        <option value="300" <?php echo $row["time_out"] == 300? "selected" : "" ?>>300 mins</option>
                    </select>
                </div>
                <div>
                    <input class="button" id="credential_save" type="submit" value="Save">
                </div>
            </form>
        </div>

        <div class="div_card">
            <h4>Edit Password<hr></h4>
            <div>
                <form action="user_account.php" method="post">
                    <label>Current Password<input class="userinput password_view" type="password" id="current_password" name="current_password" oninput= verifyCurrentPassword() required ></label><br/>
                    <label>New Password<input class="userinput password_view" type="password" id="new_password" name="new_password" oninput= verifyNewPassword() required></label><br/>
                    <label>Retype Password<input class="userinput password_view" type="password" id="retype_password" name="retype_password" oninput= verifyNewPassword() required></label><br/>
                    <input class="button" type="submit" id="submit_password" name="submit_password" value="Submit" disabled>
                    <input type="hidden" id="user_name" name="user_name" value="<?php echo $_SESSION['username']; ?>">
                </form>
            </div>
        </div>
    </div>
    <span class="version_dark">v2.4.0</span>
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.js"></script>
<script>
    var verified;
    var newPass;
    var submit_pass = document.getElementById("submit_password");

    function verifyCurrentPassword(){
        var current_pass = document.getElementById("current_password");
        var userName = document.getElementById("user_name").value;

        $(function(){
            $.post("jq_ajax.php", {userName: userName, password: current_pass.value}, function(data,status){
               verified = data;
               style(data);
            });
        });
        function style(ver){
        if (ver == "true") {
            current_pass.style.backgroundColor= "PaleGreen";
        } else if (ver == "false") {
            current_pass.style.backgroundColor= "Tomato";
        }
        if (current_pass.value == "") {current_pass.style.backgroundColor= "white";}

        if (ver == "true" && newPass == "true") {
            submit_pass.disabled = false;
        } else {submit_pass.disabled = true;}}
    }

    function verifyNewPassword(){
        var new_pass = document.getElementById("new_password");
        var retype_pass = document.getElementById("retype_password");

        if (new_pass.value != retype_pass.value && retype_pass.value != "" && new_pass.value != "") {
            new_pass.style.backgroundColor = "Tomato ";
            retype_pass.style.backgroundColor = "Tomato ";
            newPass = "false";
        } else if (new_pass.value == retype_pass.value && new_pass.value != "") {
            new_pass.style.backgroundColor = "PaleGreen";
            retype_pass.style.backgroundColor = "PaleGreen";
            newPass = "true";
        } else {
            new_pass.style.backgroundColor = "white";
            retype_pass.style.backgroundColor = "white";
        }
        if (verified == "true" && newPass == "true") {
            submit_pass.disabled = false;
        } else {submit_pass.disabled = true;}
    }

     function onTimeZoneSelect(obj){
        var region = obj.value;

         $(function(){
            $.post("jq_ajax.php", {timeZoneRegion: region}, function(data,status){
                 document.getElementById("city_select").innerHTML = data;
            });
        });
    }
</script>