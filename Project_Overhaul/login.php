<?php 
    include "sql_common.php";
    session_start();
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
        <div class="user_view">
            <span>UserName</span><br>
            <span>Password</span>
        </div>
        <div class="user_view">
            <form action="login.php" method="post">
                <input type="text" name="username" required><br/>
                <input type="password" name="password" required><br/>
                <input type="submit" value="login" class="button">
            </form>
        </div>
    </div>
</body>
</html>

<?php
    if(isset($_POST["username"])){
        $result = verify_credentials($_POST["username"], $_POST["password"]);

        if ($result == true) {
            header("Location: category_status.php");
            exit();
        } else{
            echo '<div class="error">Invalid Username or Password</div>';
        }
    }
?>