<?php
    include "sql_common.php";
    session_start();
    if (!isset($_SESSION["username"])) {
        header("Location: login.php");
        exit();
    }
    if (isset($_POST["new_username"])) {
        add_new_user($_POST['new_username'], $_POST['new_password'], $_POST['userrole']);
    }
    if(isset($_POST["delete_user"])){
        delete_user($_POST["delete_select"]);
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
    <?php include_once "new_nav.php" ?>

    <div>
        <div><h4>Add New User</h4></div>
        <div class="user_view">
            <span>UserName</span><br>
            <span>Password</span><br>
            <span>UserRole</span>
        </div>
        <div class="user_view">
            <form action="manage_users.php" method="post">
                <input type="text" name="new_username" required><br/>
                <input type="password" name="new_password" required><br/>
                <select name="userrole" class="none">
                    <?php $result = get_role(); ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <option   value="<?php echo $row["role"]?>" > <?php echo $row["role"] ?> </option>
                    <?php endwhile ?>
                </select>
                <input type="submit" value="Add" class="button">
            </form>
        </div>
    </div>

    <div>
        <div><h4>User List</h4></div>
        <table border="1px">
            <tr>
                <th>User</th>
                <th>Role</th>
            </tr>
            <?php $result2 = get_users(); ?>
            <?php while ($userdata = $result2->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $userdata["username"]; ?> </td>
                    <td>
                        <select name="" id=""class="none">
                            <?php $result = get_role(); ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <option value="<?php $row["role"]?>" > <?php echo $row["role"] ?> </option>
                            <?php endwhile ?>
                        </select>
                    </td>
                </tr>
            <?php endwhile ?>
        </table>
    </div>

    <div>
        <div><h4>Delete User</h4></div>
        <form action="manage_users.php" method="post">
            <select name="delete_select" class="none">
                <?php $result = get_users(); ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <option value="<?php echo $row["username"] ?>" > <?php echo $row["username"] ?> </option>
                <?php endwhile ?>
            </select>
            <input type="submit" name="delete_user" value ="Delete" class="button">
        </form>
    </div>

</body>
</html>

