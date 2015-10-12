<?php

include_once "sql_common.php";

session_start();


function get_total_items($category_id) {
    global $conn;
    connect_to_db();

    $sql = "SELECT COUNT(Item.name) AS num
            from Category INNER JOIN Item 
            ON Category.id = Item.category_id
            WHERE Category.id = {$category_id} AND Category.deletion_date IS NULL 
                AND Item.deletion_date IS NULL";
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

    $sql = "SELECT COUNT(Item.name) as num
            from Category INNER JOIN Item ON Category.id = Item.category_id 
            LEFT JOIN Inventory ON Item.id = Inventory.item_id 
            WHERE Category.id = {$category_id} AND Inventory.date = '{$date}' 
                AND Category.deletion_date IS NULL AND Item.deletion_date IS NULL";

    $result = $conn->query($sql); 
    if ($result == False) {
        echo "<br> Query failed <br>";
    }

    return $result->fetch_assoc()['num'];
}


function get_categories($date = null) {
    global $conn;
    connect_to_db();

    if ($date == null) {
        $date = date('Y-m-d');
    }

    $sql = "SELECT * FROM Category 
            WHERE creation_date <= '{$date}' AND (deletion_date > '{$date}' 
                OR deletion_date IS NULL) ORDER BY name ASC";
    $result = $conn->query($sql); 
    if ($result == False) {
        echo "<br> Query failed <br>";
    }


    echo '<br><form action="category_status.php" method="post" style="display:inline">
    <input type="date" name="date" value="' .$date. '" onchange="this.form.submit()">
    <input type="hidden" name="get_categories">
    </form><br><br>';

    echo '<table border="1px solid black" width="200">';
    echo '<tr><td colspan="2" align="center" style="padding:5px">' .date('D, M d Y', strtotime($date)). '</td></tr>';
    echo '<th>Category</th>';
    echo '<th>Status</th>';
    while ($row = $result->fetch_assoc()) {
        echo '<tr><td align="center" style="padding:5px"><form action="update_inventory.php" method="post" style="display:inline">
            <input type="hidden" name="func_name" value="get_inventory">
            <input type="hidden" name="date" value="' .$date. '">
            <input type="hidden" name="category_id" value="' . $row["id"] . '">
            <input type="submit" value="' . $row["name"]. '"></form></td>
            <td align="center">' .get_updated_items_count($row['id'], $date). '/' . get_total_items($row['id']) . '</td></tr>';
    }
    echo "</table>";

    echo '<br><form action="category_status.php" method="post" style="display:inline">
    <p style="display:inline"><b>Expected Sales ($):</b></p>
    <input type="hidden" name="date" value="' .$date. '">
    <input type="number" name="expected_sales" value="' .get_expected_sales(). '" onchange="this.form.submit()"></form><br/>';

    echo '<br><form action="category_status.php" method="post" target="_blank" style="display:inline">
    <input type="hidden" name="date" value="' .$date. '">
    <input type="submit" name="print_preview" value="Print Preview"></form><br>';

    if (strcmp($_SESSION['userrole'], 'admin') == 0) {
        echo '<br><br><b>Admin Tasks</b><br>
            <a href="edit_categories.php?func_name=edit_categories">Manage Categories</a><br>
            <a href="edit_items.php?func_name=display_items">Manage Items</a><br>
            <a href="manage_users.php">Manage Users</a><br>';
    }

    echo '<br><br><br><form action="login.php" method="post">
    <input type="hidden" name="func_name" value="logout">
    <b>' .$_SESSION['username']. '</b>
    <input type="submit" value="Logout"> </form>';

    echo '<a href="change_password.php" style="font-size:80%">Change Password</a>';
}


function get_estimated_quantity($factor, $item_name) {
    if ($factor == null) {
        $factor = get_expected_sales() / get_base_sales();
    }

    $quantity = (int) $quantity;
    return round(get_base_quantity($item_name) * $factor, 2);
}


function print_preview($date = null) {
    global $conn;
    connect_to_db();

    if ($date == null) {
        $date = date('Y-m-d');
    }

    $sales_factor = get_expected_sales() / get_base_sales();

    $sql = "SELECT Category.name as category_name, Item.name as item_name, 
                IFNULL(unit, '-') as unit, IFNULL(quantity, '-') as quantity, Inv.notes as notes 
            FROM Category
            INNER JOIN Item ON Item.category_id = Category.id
            LEFT OUTER JOIN (SELECT * FROM Inventory WHERE date='{$date}') AS Inv ON Inv.item_id = Item.id 
            WHERE Category.deletion_date IS NULL AND Item.deletion_date IS NULL 
            ORDER BY Category.name, Item.name";

    $result = $conn->query($sql); 
    if ($result == False) {
        echo "<br> Query failed <br>";
    }

    $current_category = null;
    echo '<table border="2px solid black" width="800" align="center">';
    echo '<tr><td colspan="5" align="center">' .date('D, M d Y', strtotime($date)). '</td></tr>';
    while ($row = $result->fetch_assoc()) {
        if ($row['category_name'] != $current_category) {
            $current_category = $row['category_name'];
            echo '<th colspan="5" style="padding:10px;margin:100">' .$current_category. '</th>
                  <tr><td align="center"><b>Item<b></td>
                  <td align="center"><b>Unit<b></td>
                  <td align="center"><b>Quantity Present<b></td>
                  <td align="center"><b>Quantity Required<b></td>
                  <td align="center"><b>Notes<b></td></tr>';
        }

        echo '<tr>
        <td align="center">' .$row['item_name']. '</td>
        <td align="center">' .$row['unit']. '</td>
        <td align="center">' .$row['quantity']. '</td>
        <td align="center">' .(is_numeric($row['quantity']) ? get_estimated_quantity($sales_factor, $row['item_name']) - (int)$row['quantity'] : "-"). '</td>
        <td align="center">' .$row['notes']. '</td>
        </tr>';
    }
    echo "</table>";
}


if (isset($_POST['get_categories'])) {
    get_categories($_POST['date']);
} else if (isset($_POST['print_preview'])) {
    print_preview($_POST['date']);
} else if (isset($_POST['expected_sales'])) {
    update_expected_sales($_POST['expected_sales']);
    get_categories($_POST['date']);
}

?>
