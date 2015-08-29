<?php

include_once 'sql_common.php';
include_once "category_status.php";

session_start();

function request_credentials() {
    if (isset($_SESSION['username'])) {
        return;
    }

    echo '
    <!DOCTYPE html>
    <html>
    <body>

    <head>
    <title>Inventory System Login</title>
    </head>

    <form action="login.php" method="post">
    <pre>
    Username: <input type="text" name="username" required>
    Password: <input type="password" name="password" required>

    <input type="submit" value="Login">
    </pre>
    </form>

    </body>
    </html>
    ';
}

function verify_credentials($username, $password) {
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
        echo "Error: Invalid username";
        return False;
    }

    if (!password_verify($password, $row['password_hash'])) {
        echo "Error: Invalid password";
        return False;
    }

    $_SESSION['username'] = $username;
    $_SESSION['userrole'] = $row['role'];
    return True;
}

function logout() {
    session_unset();
    session_destroy();
}


// Separate 'if' blocks used instead of if/else to maintain fall-through logic.
if (strcmp($_POST['func_name'], 'logout') == 0) {
    logout();
}

if (isset($_POST['username'])) {
    verify_credentials($_POST['username'], $_POST['password']);
} 

if (!isset($_SESSION['username'])) {
    request_credentials();
} else {
    get_categories();
}

?>
