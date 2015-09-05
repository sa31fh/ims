<?php
include_once 'sql_common.php';


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


function update_item_details($original_name, $new_name, $new_unit) {
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


function display_items() {
    global $conn;
    connect_to_db();

    echo '<a href="index.php">Back</a><br><br>';

    echo '<form action="edit_items.php" method="post">
    <b>Add New Item</b><br>
    <pre>';
    echo 'Name: <input type="textarea" name="item_name" required><br>';
    echo 'Unit: <input type="textarea" name="item_unit" required></pre>';
    echo '
    <input type="hidden" name="func_name" value="add_new_item">
    <input type="submit" value="Submit">
    </form>';

    $sql = 'SELECT name, unit FROM Item ORDER BY name ASC';
    
    $result = $conn->query($sql);
    if ($result == False) {
        echo '<br>Query Failed<br>';
    }

    echo '<head><style>
                td {text-align:center}
                input {text-align:center}
          </style></head>';


    echo '<table border="1px solid black">';
    echo '<th>Item</th>
          <th>Unit</th>';

    while ($row = $result->fetch_assoc()) {
        echo '<form action="edit_items.php" method="post">';
        echo '<tr><td><input type="text" value="' .$row['name']. '" name="item_name" onchange="this.form.submit()" required></td>
                  <td><input type="text" value="' .$row['unit']. '" name="item_unit" onchange="this.form.submit()" required></td></tr>';
        echo '<input type="hidden" name="original_item_name" value="' .$row['name']. '"></form>';
    }

    echo '</table>';

}

if(strcmp($_POST['func_name'], 'add_new_item') == 0) {
    add_new_item($_POST['item_name'], $_POST['item_unit']);
} else if(array_key_exists('original_item_name', $_POST)) {
    update_item_details($_POST['original_item_name'], $_POST['item_name'], $_POST['item_unit']);
} 
display_items();
?>
