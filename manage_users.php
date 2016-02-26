<?php
session_start(); 
require_once "database/user_table.php";
require_once "database/user_role_table.php";

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION["userrole"] != "admin") {
    header("Location: login.php");
    exit();
}
if (isset($_POST["new_username"])) {
    try {
        if (!UserTable::add_new_user($_POST['new_username'], $_POST["new_firstname"], $_POST["new_lastname"], $_POST['new_password'], $_POST['userrole'])) {
            echo '<div class="error">Username already exists</div>';
        }
    } catch (Exception $e) {
        echo '<div class="error">'.$e->getMessage().'</div>';
    }
}

if(isset($_POST["delete_username"])){
    UserTable::delete_user($_POST["delete_username"]);
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
    <div class="user_table_div">
        <table class="user_table" id="table" >
            <tr>
                <th>User</th>
                <th>FirstName</th>
                <th>LastName</th>
                <th>Role</th>
                <th></th>
            </tr>
            <?php $result = UserTable::get_users(); ?>
            <?php while ($userdata = $result->fetch_assoc()): ?>
                <tr>
                    <td id="name"><?php echo $userdata["username"]; ?></td>
                    <td> <?php echo $userdata["first_name"]; ?></td>
                    <td> <?php echo $userdata["last_name"]; ?></td>
                    <td id="role">
                        <select onchange=updateRole(this) id=""class="none" <?php if ($userdata["username"] == $_SESSION["username"]) {echo "disabled";} ?>>
                            <?php $result2 = UserRoleTable::get_roles(); ?>
                            <?php while ($row = $result2->fetch_assoc()): ?>
                                <option  value="<?php echo $row["role"]?>" <?php if ($userdata["role"] == $row["role"]) {echo "selected";}?> > <?php echo $row["role"] ?> </option>
                            <?php endwhile ?>
                        </select>
                    </td>
                    <td id="delete">
                        <form action="manage_users.php" method="post" onsubmit="return confirm('delete this user?');">
                            <input type="hidden" name="delete_username" value="<?php echo $userdata["username"] ?>">
                            <input type="submit" value="delete" class="button" <?php if ($userdata["username"] == $_SESSION["username"]) { echo "disabled";} ?>>
                        </form>
                    </td>
                </tr>
            <?php endwhile ?>
        </table>
    </div>

    <div class="user_add_div">
        <h4>Add New User</h4>
        <form action="manage_users.php" method="post">
            <input class="userinput" type="text" name="new_username" placeholder="Username" required>
            <input class="userinput" type="text" name="new_firstname" placeholder="First Name" required>
            <input class="userinput" type="text" name="new_lastname" placeholder="Last Name" required>
            <input class="userinput" type="password" name="new_password" placeholder="Password" required>
            <label for="userrole">User Role:</label><select name="userrole" class="none role_select">
                <?php $result = UserRoleTable::get_roles(); ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <option   value="<?php echo $row["role"]?>" > <?php echo $row["role"] ?> </option>
                <?php endwhile ?>
            </select>
            <input type="submit" value="Add" class="button">
        </form>
    </div>
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.js"></script>
<script>
     function updateRole(obj){
        var role = obj.value;
        var rowIndex = obj.parentNode.parentNode.rowIndex;
        var roleUserName = document.getElementById("table").rows[rowIndex].cells[0].innerHTML;
        
        $(function(){
            $.post("jq_ajax.php", {newRole: role, roleUserName: roleUserName});
        });
    }
</script>

