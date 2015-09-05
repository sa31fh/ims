<?php

include 'sql_common.php';


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

    $sql = 'SELECT name, unit FROM Item ORDER BY name ASC';
    $result = $conn->query($sql); 
    if ($result == False) {
        echo "<br> Query failed <br>";
    }

    echo '<form action="edit_categories.php" method="post">';
    while ($row = $result->fetch_assoc()) {
        echo '<input type="checkbox" name="checked_items[]" value="' .$row["name"]. '"';
        if (in_array($row["name"], $category_items)) {
            echo 'checked';
        }
        echo '>' .$row["name"]. ' (' .$row["unit"]. ') <br>';
    }
    echo '<input type="hidden" name="func_name" value="update_items">';
    echo '<input type="hidden" name="category_name" value="' .$category_name. '">';
    echo '<input type="submit" value="Update">';
    echo '</form><br>';

    echo '<form action="edit_categories.php" method="post">';
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

    echo '<a href="index.php">Back</a><br><br>';
    echo '<form action="edit_categories.php" method="post">
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
        echo '<option value="edit_categories.php?func_name=get_items&name=' .$row["name"]. '"> ' .$row["name"];
    }
    echo '</select><br><br>';
    echo '</form>';
    echo '<iframe name="iframe" id="items_frame" width="50%" height="50%" style="border:none" src=""></iframe>';
}


if(strcmp($_GET['func_name'], 'edit_categories') == 0) {
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
