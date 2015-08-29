<?php

include_once "sql_common.php";


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

    echo '<br><form action="category_status.php" method="post">
    <input type="hidden" name="func_name" value="get_categories">
    <input type="date" name="date" value="' .$date. '">
    <input type="submit"> </form><br>';

    echo "<table border=\"1px solid black\">";
    echo "<th>Category</th>";
    echo "<th>Status</th>";
    while ($row = $result->fetch_assoc()) {
        echo '<tr><td><form action="update_inventory.php" method="post">
        <input type="hidden" name="func_name" value="get_inventory">
        <input type="hidden" name="date" value="' .$date. '">
        <input type="hidden" name="category_id" value="' . $row["id"] . '">
        <input type="submit" value="' . $row["name"]. '"></form></td>
        <td>' .get_updated_items_count($row['id'], $date). '/' . get_total_items($row['id']) . '</td></tr>';
    }
    echo "</table>";
    echo '<br><a href="edit_categories.php?func_name=edit_categories">Edit Categories</a><br><br><br>';

    echo '<br><form action="login.php" method="post">
    <input type="hidden" name="func_name" value="logout">
    <b>' .$_SESSION['username']. '</b>
    <input type="submit" value="Logout"> </form><br>';

}

if(strcmp($_POST['func_name'], 'get_categories') == 0) {
    get_categories($_POST['date']);
} 

?>
