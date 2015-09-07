<?php

include_once 'sql_common.php';

session_start();


function request_credentials() {
    echo '<a href="index.php">Back</a><br><br>';

    if (!isset($_SESSION['username'])) {
        echo 'Error: Unknown user!';
        return;
    }

    echo '
    <!DOCTYPE html>
    <html>
    <body>

    <script>
    function verify_password() {
        var new_password = document.getElementsByName("new_password")[0];
        var retype_password = document.getElementsByName("retype_password")[0];

        if (new_password.value != retype_password.value) {
            new_password.style.backgroundColor = "red";
            retype_password.style.backgroundColor = "red";
            document.getElementsByName("button1")[0].disabled = true;
        } else {
            new_password.style.backgroundColor = "white";
            retype_password.style.backgroundColor = "white";
            document.getElementsByName("button1")[0].disabled = false;
        }
    }
    </script>

    <form action="change_password.php" method="post">
    <pre>
    Current Password:  <input type="password" name="current_password" required>
    New Password:      <input type="password" name="new_password" onchange="verify_password()" required>
    Re-type Password:  <input type="password" name="retype_password" onchange="verify_password()" required>

    <input disabled type="submit" name="button1" value="Submit">
    </pre>
    </form>

    </body>
    </html>
    ';
}

function verify_password($username, $password) {
    global $conn;
    connect_to_db();

    $sql = "SELECT * FROM User
            INNER JOIN UserRole ON User.userrole_id = UserRole.id
            WHERE username='" .$username. "'";
    $result = $conn->query($sql);
    if ($result == False) {
        echo '<br> Query failed <br>';
    }

    $row = $result->fetch_assoc();

    if ($row == null) {
        echo "Error: Invalid username<br>";
        return False;
    }

    if (!password_verify($password, $row['password_hash'])) {
        echo "Error: Invalid current password<br>";
        return False;
    }

    return True;
}


if (isset($_POST['new_password'])) {
    if (verify_password($_SESSION['username'], $_POST['current_password'])) {
        if (update_password($_SESSION['username'], $_POST['new_password'])) {
            echo 'Password update successful!<br>';
        } else {
            echo 'Password update failed!<br>';
        }
    }
}

request_credentials();

?>

