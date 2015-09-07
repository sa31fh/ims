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


function update_password($username, $new_password) {
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


function add_new_item($item_name, $item_unit) {
    global $conn;
    connect_to_db();

    if ($item_name != null) {
        $sql = 'INSERT INTO Item (name, unit) 
                VALUES("' .$item_name. '", "' .$item_unit. '") 
                ON DUPLICATE KEY UPDATE name = VALUES(name), unit = VALUES(unit)';
        $result = $conn->query($sql); 
        if ($result == False) {
            echo "<br> Query failed <br>";
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
