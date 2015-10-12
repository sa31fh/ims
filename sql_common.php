<?php

$conn = null;


function connect_to_db() {
    global $conn;
    if ($conn) {
        return; 
    }

    $servername = "localhost";
    $user_name = "root";
    $dbname = "inventory_system";

    $conn = new mysqli($servername, "root", null, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
}


function add_new_user($username, $password, $userrole) {
    global $conn;
    connect_to_db();

    if ($username == null) {
        echo 'Error: Username cannot be null!';
        return False;
    }

    $sql = 'SELECT * FROM User  
            WHERE  name = "' .$username. '"';

    $result = $conn->query($sql);
    if ($result != False) {
        echo '<br>Error: Username already exists!<br>';
        return False;
    }

    $sql = "INSERT INTO User (username, password_hash, userrole_id) 
            VALUES('{$username}', '" .password_hash($password, PASSWORD_DEFAULT). "', 
                    (SELECT id FROM UserRole WHERE role='{$userrole}')) 
            ON DUPLICATE KEY UPDATE password_hash=VALUES(password_hash), userrole_id=VALUES(userrole_id)";

    $result = $conn->query($sql); 
    if ($result == False) {
        echo "<br> Query failed <br>";
        return False;
    }

    return True;
}


function delete_user($username) {
    global $conn;
    connect_to_db();

    if ($username == null) {
        echo 'Error: Username cannot be null!';
        return False;
    }

    $sql = 'SELECT * FROM User  
            WHERE username = "' .$username. '"';

    $result = $conn->query($sql);
    if ($result == false) {
        echo '<br>Error: Username does not exists!<br>';
        return False;
    }

    $sql = "DELETE FROM User WHERE username='$username'";

    $result = $conn->query($sql); 
    if ($result == False) {
        echo "<br> Delete Query failed <br>";
        return False;
    }

    return True;
}



function update_user_password($username, $new_password) {
    global $conn;
    connect_to_db();

    $sql = 'UPDATE User
            SET password_hash="' .password_hash($new_password, PASSWORD_DEFAULT). '" 
            WHERE username="' .$username. '"';

    $result = $conn->query($sql);
    if ($result == False) {
        echo '<br> Query failed <br>';
        return False;
    }

    return True;
}


function update_user_role($username, $role) {
    global $conn;
    connect_to_db();

    $sql = "UPDATE User 
            SET userrole_id= (SELECT id FROM UserRole WHERE role='$role') 
            WHERE username='$username'";

    $result = $conn->query($sql);
    if ($result == False) {
        echo '<br> Query failed <br>';
        return False;
    }

    return True;
}


function add_new_item($item_name, $item_unit) {
    global $conn;
    connect_to_db();

    $date = date('Y-m-d');

    if ($item_name == null) {
        return;
    }

    $sql = "SELECT * FROM Item
            WHERE name = '{$item_name}' AND deletion_date IS NULL";

    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
        $sql = "SELECT * FROM Item 
                WHERE name = '{$item_name}' AND deletion_date = '{$date}'";
        $result = $conn->query($sql);
        if ($result->num_rows == 0) {
            $sql = "INSERT INTO Item (name, unit, creation_date) 
                    VALUES('{$item_name}', '{$item_unit}', '{$date}')";
        } else {
            $sql = "UPDATE Item 
                    SET deletion_date = null, unit = '{$item_unit}' 
                    WHERE name = '{$item_name}' AND deletion_date = '{$date}'";
        }
    } else {
        echo "Item already exists! <br/>";
    }

    $result = $conn->query($sql); 
    if ($result == False) {
        echo "<br> Query failed <br>";
    }
}


function delete_item($item_name) {
    global $conn;
    connect_to_db();

    $date = date('Y-m-d');

    if ($item_name != null) {
        $sql = "UPDATE Item 
                SET deletion_date = '{$date}' 
                WHERE name = '{$item_name}'";
        $result = $conn->query($sql); 
        if ($result == False) {
            echo "<br> Query failed <br>";
            echo $sql;
        }
    }
}


function update_item_details($original_name, $new_name, $new_unit, $base_quantity) {
    global $conn;
    connect_to_db();

    $sql = 'UPDATE Item 
            SET name="' .$new_name. '", 
                unit="' .$new_unit. '"
            WHERE name="' .$original_name. '"';

    $result = $conn->query($sql);
    if ($result == False) {
        echo '<br>Query Failed<br>';
    }
}


function get_base_quantity($item_name) {
    global $conn;
    connect_to_db();

    $sql = 'SELECT quantity FROM BaseQuantity  
            WHERE item_id = (SELECT id FROM Item WHERE name="' .$item_name. '")';

    $result = $conn->query($sql);
    if ($result == False) {
        echo '<br>get_base_quantity() Query Failed<br>';
        echo $sql;
    }

    return (int) $result->fetch_assoc()['quantity'];
}


function update_base_quantity($item_name, $quantity) {
    global $conn;
    connect_to_db();

    $sql = 'INSERT INTO BaseQuantity (item_id, quantity)  
            VALUES ((SELECT id FROM Item WHERE name="' .$item_name. '"), ' .$quantity. ') 
            ON DUPLICATE KEY UPDATE item_id = VALUES(item_id), quantity = VALUES(quantity)';

    $result = $conn->query($sql);
    if ($result == False) {
        echo '<br>Query Failed<br>';
    }
}


function get_base_sales() {
    global $conn;
    connect_to_db();

    $sql = 'SELECT value FROM Variables WHERE name="BaseSales"';

    $result = $conn->query($sql);
    if ($result == False) {
        echo '<br>Query Failed<br>';
    }

    return (int) $result->fetch_assoc()['value'];
}


function update_base_sales($base_sales) {
    global $conn;
    connect_to_db();
    
    $sql = 'INSERT INTO Variables (name, value)  
            VALUES ("BaseSales", ' .$base_sales. ') 
            ON DUPLICATE KEY UPDATE name = VALUES(name), value = VALUES(value)';

    $result = $conn->query($sql);
    if ($result == False) {
        echo '<br>Query Failed<br>';
    }
}


function get_expected_sales() {
    global $conn;
    connect_to_db();

    $sql = 'SELECT value FROM Variables WHERE name="ExpectedSales"';

    $result = $conn->query($sql);
    if ($result == False) {
        echo '<br>Query Failed<br>';
    }

    return (int) $result->fetch_assoc()['value'];
}


function update_expected_sales($expected_sales) {
    global $conn;
    connect_to_db();
    
    $sql = 'INSERT INTO Variables (name, value)  
            VALUES ("ExpectedSales", ' .$expected_sales. ') 
            ON DUPLICATE KEY UPDATE name = VALUES(name), value = VALUES(value)';

    $result = $conn->query($sql);
    if ($result == False) {
        echo '<br>Query Failed<br>';
    }
}

?>
