<?php 
    include "sql_common.php";
    session_start();
     if (isset($_SESSION["username"])) {
        header("Location: category_status.php");
        exit();
    }
    if(isset($_POST["username"])){

        if (verify_credentials($_POST["username"], $_POST["password"])) {
            set_session_variables($_POST["username"]);
            set_destroy_status($_SESSION["username"], gmdate("Y-m-d"));
            header("Location: category_status.php");
            exit();
        } else{
            echo '<div class="error">Invalid Username or Password</div>';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div>
        <form action="login.php" method="post">
            <input class="userinput" type="text" name="username" placeholder="Username" required>
            <input class="userinput" type="password" name="password" placeholder="Password" required>
            <input type="submit" value="login" class="button">
        </form>
    </div>
</body>
</html>
