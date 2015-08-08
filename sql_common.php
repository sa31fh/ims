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

function get_total_items($category_id) {
    global $conn;
    connect_to_db();

    $sql = "SELECT COUNT(Item.name) AS num
            from Category INNER JOIN Item 
            ON Category.id = Item.category_id
            WHERE Category.id = " . $category_id;
    $result = $conn->query($sql); 
    if ($result == False) {
        echo "<br> Query failed <br>";
    }

    return $result->fetch_assoc()['num'];
}


// Date format is YYYY-MM-DD
function get_updated_items_count($category_id, $date) {
    global $conn;
    connect_to_db();

    $sql = 'SELECT COUNT(Item.name) as num
            from Category INNER JOIN Item ON Category.id = Item.category_id 
            LEFT JOIN Inventory ON Item.id = Inventory.item_id 
            WHERE Category.id = ' .$category_id. ' and Inventory.date = "' .$date. '"';

    $result = $conn->query($sql); 
    if ($result == False) {
        echo "<br> Query failed <br>";
    }

    return $result->fetch_assoc()['num'];
}


function get_categories() {
    global $conn;
    connect_to_db();

    $sql = "SELECT * FROM Category ORDER BY id ASC";
    $result = $conn->query($sql); 
    if ($result == False) {
        echo "<br> Query failed <br>";

    }

    echo "<table border=\"1px solid black\">";
    echo "<th>Category</th>";
    echo "<th>Status</th>";
    while ($row = $result->fetch_assoc()) {
        echo '<tr><td><form action="sql_common.php" method="post">
        <input type="hidden" name="func_name" value="get_inventory">
        <input type="hidden" name="category_id" value="' . $row["id"] . '">
        <input type="submit" value="' . $row["name"]. '"></form></td>
        <td>' .get_updated_items_count($row['id'], date('Y-m-d')). '/' . get_total_items($row['id']) . '</td></tr>';
    }
    echo "</table>";
}

function get_inventory($category_id) {
    global $conn;
    connect_to_db();

    $sql = 'SELECT Item.name as name, Item.unit as unit, IFNULL(Inventory.amount, "-") as amount
            from Category INNER JOIN Item ON Category.id = Item.category_id 
            LEFT JOIN Inventory ON Item.id = Inventory.item_id 
            WHERE Category.id = ' . $category_id . ' and (Inventory.date = "' . date('Y-m-d') . '" or Inventory.date is null)';
    $result = $conn->query($sql); 
    if ($result == False) {
        echo "<br> Query failed <br>";

    }

    echo "<table border=\"1px solid black\">";
    echo "<th>Item</th>";
    echo "<th>Unit</th>";
    echo "<th>Amount</th>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["name"]. "</td>
                  <td>" . $row["unit"]. "</td>
                  <td>" . $row["amount"]. "</td></tr>";
    }
    echo "</table>";
}

if (strcmp($_POST['func_name'], 'get_inventory') == 0) {
    get_inventory($_POST['category_id']);
}

?>
