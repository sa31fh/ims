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
        <a href="category_status.php" class="buttonBack">Back</a>
    </div>
    <div>
        <form action="change_password.php" method="post">
            <input type="password" id="current_password" name="current_password" placeholder="Current Password" class="password_view" oninput= verifyCurrentPassword() required ><br/>
            <input type="password" id="new_password" name="new_password" placeholder="New Password" class="password_view" oninput= verifyNewPassword() required><br/> 
            <input type="password" id="retype_password" name="retype_password" placeholder="Retype Password" class="password_view" oninput= verifyNewPassword() required><br/>
            <input type="submit" id="submit_password" name="submit_password" value="Submit" class="button" disabled>
            <input type="hidden" id="user_name" name="user_name" value="<?php echo $_SESSION['username']; ?>">
        </form>
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
</script>

<?php 
    if (isset($_POST["new_password"])) {
        if (update_user_password($_POST["user_name"], $_POST["new_password"])) {
            echo 'Password update successful!<br>';
            header('Refresh:1; category_status.php');
            exit();
        } else {
            echo 'Password update failed!<br>';
        }
    } 
?>