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


function get_categories($date = null) {
    global $conn;
    connect_to_db();

    $sql = "SELECT * FROM Category ORDER BY id ASC";
    $result = $conn->query($sql); 
    if ($result == False) {
        echo "<br> Query failed <br>";

    }

    if ($date == null) {
        $date = date('Y-m-d');
    }

    echo '<br><form action="sql_common.php" method="post">
    <input type="hidden" name="func_name" value="get_categories">
    <input type="date" name="date" value="' .$date. '">
    <input type="submit"> </form><br>';

    echo "<table border=\"1px solid black\">";
    echo "<th>Category</th>";
    echo "<th>Status</th>";
    while ($row = $result->fetch_assoc()) {
        echo '<tr><td><form action="sql_common.php" method="post">
        <input type="hidden" name="func_name" value="get_inventory">
        <input type="hidden" name="date" value="' .$date. '">
        <input type="hidden" name="category_id" value="' . $row["id"] . '">
        <input type="submit" value="' . $row["name"]. '"></form></td>
        <td>' .get_updated_items_count($row['id'], $date). '/' . get_total_items($row['id']) . '</td></tr>';
    }
    echo "</table>";
}

function get_inventory($category_id, $date) {
    global $conn;
    connect_to_db();

    $sql = 'SELECT T2.item_id AS id, T2.item_name AS name, T2.item_unit AS unit, IFNULL(T1.amount, "-") AS amount FROM
            (SELECT * from Inventory
            WHERE Inventory.date = "' .$date. '") AS T1
            RIGHT JOIN
            (SELECT Item.id AS item_id, Item.name AS item_name, Item.unit AS item_unit from Item
            INNER JOIN Category ON Item.category_id = Category.id
            WHERE Category.id = ' .$category_id. ') AS T2  ON T2.item_id = T1.item_id';

    $result = $conn->query($sql); 
    if ($result == False) {
        echo "<br> Query failed <br>";
    }

    echo '<br><form action="sql_common.php" method="post">
    <input type="hidden" name="category_id" value="' .$category_id. '">
    <input type="hidden" name="func_name" value="get_inventory">
    <input type="date" name="date" value="' .$date. '">
    <input type="submit"> </form><br>';

    echo "<table border=\"1px solid black\">";
    echo "<th>Item</th>";
    echo "<th>Unit</th>";
    echo "<th>Amount</th>";

    $i = 0;
    $inventory_items = [];

    while ($row = $result->fetch_assoc()) {
        $inventory_items[$i] = $row["id"];

        echo '<br><form action="sql_common.php" method="post">
              <tr><td>' . $row["name"]. '</td>
                  <td>' . $row["unit"]. '</td>
                  <td>' . $row["amount"]. '</td>
                  <td><input type="number" min="0" name="values[]"</td></tr>';
        ++$i;
    }
    echo '</table><br>
    <input type="hidden" name="func_name" value="update_inventory">
    <input type="hidden" name="date" value="' .$date. '">
    <input type="hidden" name="category_id" value="' .$category_id. '">
    <input type="hidden" name="inventory_items" value=' .serialize($inventory_items). '/>
    <input type="submit" value="Update"> </form><br>';
}

function update_inventory($category_id, $date, $items, $values) {
    global $conn;
    connect_to_db();

    for ($i = 0; $i < count($items); $i++) {
        if ($values[$i] == null) {
            continue;
        }

        $sql = ' INSERT INTO Inventory (`date`, `item_id`, `amount`)
                 VALUES ("' .$date. '", ' .$items[$i]. ', ' .$values[$i]. ')
                 ON DUPLICATE KEY UPDATE 
                 date=VALUES(date), item_id = VALUES(item_id), amount = VALUES(amount)';

        $result = $conn->query($sql); 
        if ($result == False) {
            echo "<br> Query failed <br>";
        }
    }

    get_inventory($category_id, $date);
}

if (strcmp($_POST['func_name'], 'get_inventory') == 0) {
    get_inventory($_POST['category_id'], $_POST['date']);
} else if(strcmp($_POST['func_name'], 'get_categories') == 0) {
    get_categories($_POST['date']);
} else if(strcmp($_POST['func_name'], 'update_inventory') == 0) {
    $items = unserialize($_POST['inventory_items']);
    $values = $_POST['values'];
    $date = $_POST['date'];
    $category_id = $_POST['category_id'];

    update_inventory($category_id, $date, $items, $values);
}

?>
