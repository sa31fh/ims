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


function add_category($category_name) {
    global $conn;
    connect_to_db();

    $sql = 'INSERT INTO inventory_system.Category (name) VALUES ("' .$category_name. '")';

    $result = $conn->query($sql); 
    if ($result == False) {
        echo "<br> Query failed <br>";
    }

    edit_categories();
}


function remove_category($category_name) {
    global $conn;
    connect_to_db();

    $sql = 'DELETE FROM inventory_system.Category WHERE name="' .$category_name. '"';

    $result = $conn->query($sql); 
    if ($result == False) {
        echo "<br> Query failed <br>";
    }

    edit_categories();
}


function update_items_category($category_name, $checked_items) {
    global $conn;
    connect_to_db();

    $sql = 'SELECT Category.id FROM Category WHERE Category.name = "' .$category_name. '"';
    $result = $conn->query($sql); 
    if ($result == False) {
        echo "<br> Query failed <br>";
    }

    $category_id = $result->fetch_assoc()['id'];

    foreach ($checked_items as $item) {
        if ($item == null) {
            continue;
        }
        $sql = 'UPDATE Item SET category_id = ' .$category_id. ' WHERE name = "' .$item. '"';
        $result = $conn->query($sql); 
        if ($result == False) {
            echo "<br> Query failed <br>";
        }
    }

    get_items($category_name);
}


function add_new_item($category_name, $item_name, $item_unit) {
    global $conn;
    connect_to_db();

    $sql = 'SELECT Category.id FROM Category WHERE Category.name = "' .$category_name. '"';
    $result = $conn->query($sql); 
    if ($result == False) {
        echo "<br> Query failed <br>";
    }

    $category_id = $result->fetch_assoc()['id'];

    if ($item_name != null) {
        $sql = 'INSERT INTO Item (category_id, name, unit) 
                VALUES(' .$category_id. ', "' .$item_name. '", "' .$item_unit. '") 
                ON DUPLICATE KEY UPDATE category_id = VALUES(category_id), unit = VALUES(unit)';
        $result = $conn->query($sql); 
        if ($result == False) {
            echo "<br> Query failed <br>";
        }
    }

    get_items($category_name);
}


function get_items($category_name) {
    global $conn;
    connect_to_db();

    $sql = 'SELECT Item.name FROM Item INNER JOIN Category ON Item.category_id = Category.id WHERE Category.name = "' .$category_name. '"';
    $result = $conn->query($sql); 
    if ($result == False) {
        echo "<br> Query failed <br>";
    }

    $category_items = [];
    while ($row = $result->fetch_assoc()) {
        array_push($category_items, $row['name']);
    }

    $sql = 'SELECT name FROM Item ORDER BY name ASC';
    $result = $conn->query($sql); 
    if ($result == False) {
        echo "<br> Query failed <br>";
    }

    echo '<form action="sql_common.php" method="post">';
    while ($row = $result->fetch_assoc()) {
        echo '<input type="checkbox" name="checked_items[]" value="' .$row["name"]. '"';
        if (in_array($row["name"], $category_items)) {
            echo 'checked';
        }
        echo '>' .$row["name"]. '<br>';
    }
    echo '<input type="hidden" name="func_name" value="update_items">';
    echo '<input type="hidden" name="category_name" value="' .$category_name. '">';
    echo '<input type="submit" value="Update">';
    echo '</form><br>';

    echo '<form action="sql_common.php" method="post">';
    echo '<b>Add/Update item</b><br>';
    echo 'Name: ';
    echo '<input type="textarea" name="item_name"><br>';
    echo 'Unit:  ';
    echo '<input type="textarea" name="item_unit"><br>';
    echo '<input type="hidden" name="func_name" value="add_new_item">';
    echo '<input type="hidden" name="category_name" value="' .$category_name. '">';
    echo '<input type="submit" value="Submit">';
    echo '</form>';
}


function edit_categories() {
    global $conn;
    connect_to_db();

    echo '<a href="index.php"> Home </a><br><br>';
    echo '<form action="sql_common.php" method="post">
          <input type="textarea" name="category" id="category_name">
          <input type="submit" name="edit_categories_button" value="Add">
          <input type="submit" name="edit_categories_button" value="Remove"><br>
          </form>';

    $sql = "SELECT * FROM Category ORDER BY id ASC";
    $result = $conn->query($sql); 
    if ($result == False) {
        echo "<br> Query failed <br>";
    }

    echo '<script>
    function categorySelect(obj) {
        document.getElementById("items_frame").src = obj.value;
        document.getElementById("category_name").value = obj.options[obj.selectedIndex].text;
    }
    </script>';

    echo '<form name="change">';
    echo '<select name="options" size="4" onchange=categorySelect(this)>';
    while ($row = $result->fetch_assoc()) {
        echo '<option value="sql_common.php?func_name=get_items&name=' .$row["name"]. '"> ' .$row["name"];
    }
    echo '</select><br><br>';
    echo '</form>';
    echo '<iframe name="iframe" id="items_frame" width="50%" height="50%" style="border:none" src=""></iframe>';
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
    echo '<br><a href="sql_common.php?func_name=edit_categories">Edit Categories</a><br>';
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

    echo '<a href="index.php"> Home </a><br><br>';
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

        echo '<form action="sql_common.php" method="post">
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

        $sql = 'INSERT INTO Inventory (`date`, `item_id`, `amount`)
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
} else if(strcmp($_GET['func_name'], 'edit_categories') == 0) {
    echo edit_categories();
} else if(strcmp($_GET['func_name'], 'get_items') == 0) {
    echo get_items($_GET['name']);
} else if(strcmp($_POST['func_name'], 'update_items') == 0) {
    update_items_category($_POST['category_name'], $_POST['checked_items']);
} else if(strcmp($_POST['func_name'], 'add_new_item') == 0) {
    add_new_item($_POST['category_name'], $_POST['item_name'], $_POST['item_unit']);
} else if(array_key_exists('edit_categories_button', $_POST)) {
    if (strcmp($_POST['edit_categories_button'], 'Add') == 0) {
        add_category($_POST['category']);
    } else if (strcmp($_POST['edit_categories_button'], 'Remove') == 0) {
        remove_category($_POST['category']);
    }
}

?>
