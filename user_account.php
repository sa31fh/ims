<?php
    include "sql_common.php";
    session_start();
    if (!isset($_SESSION["username"])) {
        header("Location: login.php");
        exit();
    }
    if (isset($_POST["new_password"])) {
        if (update_user_password($_POST["user_name"], $_POST["new_password"])) {
            echo 'Password update successful!<br>';
        } else {
            echo 'Password update failed!<br>';
        }
    } 
    if (isset($_POST["update_user"])) {
        $tz = null;
        if (!empty($_POST["city_select"])) {
            $tz = ($_POST["region_select"]. "/" .$_POST["city_select"]);
            $_SESSION["timezone"] = $tz;
        }
        update_user_details($_POST["update_user"], $_POST["update_first"], $_POST["update_last"], $tz);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include_once "new_nav.php" ?>
    <div class="main">

        <div>
            <h4>Edit Credentials</h4>
            <?php $result = get_user_details($_SESSION["username"]); ?>
            <?php $row = $result->fetch_assoc(); ?>
            <form action="user_account.php" method="post">
                <input class="userinput" name="update_user" type="text" value="<?php echo $row["username"] ?>" placeholder="User Name"><br/>
                <input class="userinput" name="update_first" type="text" value="<?php echo $row["first_name"] ?>" placeholder="First Name"><br/>
                <input class="userinput" name="update_last" type="text" value="<?php echo $row["last_name"] ?>" placeholder="Last Name"><br/>
                <select name="region_select" id="region_select" onchange=onTimeZoneSelect(this)>
                    <?php $oldregion = ""; ?>
                    <?php foreach (timezone_identifiers_list() as $tz): ?>
                        <?php $region = explode("/", $tz); ?>
                        <?php $removetz = array('Pacific', 'Antarctica', 'Arctic', 'UTC', 'Indian', 'Atlantic', $oldregion); ?>
                        <?php if (!in_array($region[0], $removetz, true)): ?>
                            <option value="<?php echo $region[0] ?>"> <?php echo $region[0] ?></option>
                        <?php endif ?>
                    <?php $oldregion= $region[0]; endforeach ?>
                </select>
                <select name="city_select" id="city_select">
                </select><br/>
                <input class="button" type="submit" value="Save">
            </form>
        </div>
        

        <div>
            <h4>Edit Password</h4>
            <form action="user_account.php" method="post">
                <input class="userinput password_view" type="password" id="current_password" name="current_password" placeholder="Current Password" oninput= verifyCurrentPassword() required ><br/>
                <input class="userinput password_view" type="password" id="new_password" name="new_password" placeholder="New Password" oninput= verifyNewPassword() required><br/> 
                <input class="userinput password_view" type="password" id="retype_password" name="retype_password" placeholder="Retype Password" oninput= verifyNewPassword() required><br/>
                <input class="button" type="submit" id="submit_password" name="submit_password" value="Submit" disabled>
                <input type="hidden" id="user_name" name="user_name" value="<?php echo $_SESSION['username']; ?>">
            </form>
        </div>
    </div>
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
            $.post("sql_common.php", {userName: userName, password: current_pass.value}, function(data,status){
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

        if (new_pass.value != retype_pass.value && retype_pass.value != "" && new_pass.value != ""){
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
            $.post("sql_common.php", {timeZoneRegion: region}, function(data,status){
                 document.getElementById("city_select").innerHTML = data;
            });
        });
    }
</script>