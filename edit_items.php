<?php
include_once 'sql_common.php';


function display_items() {
    global $conn;
    connect_to_db();

    echo '<a href="index.php">Back</a><br><br><br/>';

    $sql = 'SELECT name, unit, quantity FROM Item 
            LEFT OUTER JOIN BaseQuantity ON BaseQuantity.item_id = Item.id
            WHERE Item.deletion_date IS NULL 
            ORDER BY name ASC';
    
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
          <th>Unit</th>
          <th>Quantity for sales ($)<br>
              <form action="edit_items.php" method="post" style="display:inline">
              <input type="number" value="' .get_base_sales(). '" name="base_sales" onchange="this.form.submit()" required>
          </form></th>';

    $item_names = array();

    while ($row = $result->fetch_assoc()) {
        array_push($item_names, $row['name']);
        echo '<form action="edit_items.php" method="post">';
        echo '<tr><td><input type="text" value="' .$row['name']. '" name="item_name" onchange="this.form.submit()" required></td>
                  <td><input type="text" value="' .$row['unit']. '" name="item_unit" onchange="this.form.submit()" required></td>
                  <td><input type="number" step="0.01" value="' .$row['quantity']. '" name="base_quantity" onchange="this.form.submit()" required></td></tr>';
        echo '<input type="hidden" name="original_item_name" value="' .$row['name']. '"></form>';
    }

    echo '</table>';

    echo '<br/><br/><form action="edit_items.php" method="post">
    <b>Add New Item</b><br>
    <pre>';
    echo 'Name: <input type="textarea" name="item_name" required><br>';
    echo 'Unit: <input type="textarea" name="item_unit" required></pre>';
    echo '
    <input type="hidden" name="func_name" value="add_new_item">
    <input type="submit" value="Submit">
    </form>';

    echo '<br><form action="edit_items.php" method="post" style="display:inline">
      <select name="delete_item">';
      foreach ($item_names as $item_name) {
            echo '<option value="' .$item_name. '">' .$item_name. '</option>';
      }

    echo '</select> <input type="submit" value="Delete Item"></form>';
}

if(strcmp($_POST['func_name'], 'add_new_item') == 0) {
    add_new_item($_POST['item_name'], $_POST['item_unit']);
} else if(array_key_exists('original_item_name', $_POST)) {
    update_item_details($_POST['original_item_name'], $_POST['item_name'], $_POST['item_unit']);
    update_base_quantity($_POST['original_item_name'], $_POST['base_quantity']);
} else if(array_key_exists('base_sales', $_POST)) {
    update_base_sales($_POST['base_sales']);
} else if(array_key_exists('delete_item', $_POST)) {
    delete_item($_POST['delete_item']);
}

display_items();
?>
