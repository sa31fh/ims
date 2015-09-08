<?php
session_start();

include_once 'sql_common.php';


function display_users() {
    global $conn;
    connect_to_db();

    echo '<br><a href="index.php">Back</a><br><br><br>';

    $userroles = array();

    $sql = 'SELECT role FROM UserRole 
            ORDER BY role ASC';
    
    $result = $conn->query($sql);
    if ($result == False) {
        echo '<br>Query Failed<br>';
    }


    echo '<form action="manage_users.php" method="post">
    <b>Add New User</b><br>
    <pre>';
    echo 'Username: <input type="textarea" name="new_username" required><br>';
    echo 'Password: <input type="password" name="password" required></pre>';

    echo '<select name="userrole">';
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' .$row['role']. '">' .$row['role']. '</option>';
        array_push($userroles, $row['role']);
    }
    echo '</select>';

    echo '
    <input type="hidden" name="func_name" value="add_new_user">
    <input type="submit" value="Submit">
    </form>';

    $sql = 'SELECT username, role FROM User 
            INNER JOIN UserRole ON User.userrole_id = UserRole.id
            ORDER BY username ASC';
    
    $result = $conn->query($sql);
    if ($result == False) {
        echo '<br>Query Failed<br>';
    }

    echo '<head><style>
                td {text-align:center}
                input {text-align:center}
          </style></head>';

    echo '<br/><br/><table border="1px solid black" width=300>';
    echo '<th>Username</th>
          <th>Role</th>';

    $users = array();

    while ($row = $result->fetch_assoc()) {
        array_push($users, $row['username']);

        echo '<tr><td>' .$row['username']. '</td>';
        echo '<td><form action="manage_users.php" method="post" style="display:inline">
                  <select name="new_userrole"';
                    if ($row['username'] == $_SESSION['username']) {
                        echo ' disabled ';
                    }
                    echo ' onchange="this.form.submit()">';
                    foreach ($userroles as $role){
                        echo '<option value="' .$role. '"';
                        if ($role == $row['role']) {
                            echo ' selected ';
                        }

                        echo '>' .$role. '</option>';
                    }
        echo '<input type="hidden" name="username" value="' .$row['username']. '">';
        echo '</select></form></td>';
        echo '</tr>';
    }

    echo '</table>';

    echo '<br><form action="manage_users.php" method="post" style="display:inline">
          <select name="delete_user">';
          foreach ($users as $user) {
                if ($user == $_SESSION['username']) {
                    continue;
                }
                echo '<option value="' .$user. '">' .$user. '</option>';
          }

    echo '</select> <input type="submit" value="Delete User"></form>';
}


if (array_key_exists('delete_user', $_POST)) {
    delete_user($_POST['delete_user']);
} else if (array_key_exists('new_username', $_POST)) {
    add_new_user($_POST['new_username'], $_POST['password'], $_POST['userrole']);
} else if (array_key_exists('new_userrole', $_POST)) {
    update_user_role($_POST['username'], $_POST['new_userrole']);
}

display_users();
?>

