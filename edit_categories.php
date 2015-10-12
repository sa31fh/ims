<?php

include 'sql_common.php';


function add_category($category_name) {
    global $conn;
    connect_to_db();

    $sql = "SELECT * FROM inventory_system.Category 
            WHERE name = '{$category_name}' AND deletion_date IS NULL";
    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        $date = date('Y-m-d');
        $sql = "SELECT * FROM inventory_system.Category 
                WHERE name = '{$category_name}' AND deletion_date = '{$date}'";

        $result = $conn->query($sql); 
        if ($result->num_rows == 0) {
            $sql = "INSERT INTO inventory_system.Category (name, creation_date) 
                    VALUES ('{$category_name}', '{$date}')";
        } else {
            $sql = "UPDATE Category SET deletion_date = NULL 
                    WHERE name = '{$category_name}' and deletion_date = '{$date}'";
        }

        $result = $conn->query($sql); 
        if ($result == False) {
            echo "<br> Query failed <br>";
            echo $sql;
        }
    } else {
        echo "Category already exists! <br/>";
    }

    edit_categories();
}


function remove_category($category_name) {
    global $conn;
    connect_to_db();

    $sql = "UPDATE Category SET deletion_date = '" .date('Y-m-d'). "' 
            WHERE name = '{$category_name}' and deletion_date IS NULL";

    $result = $conn->query($sql); 
    if ($result == False) {
        echo "<br> Query failed <br>";
    }

    $sql = "UPDATE Item SET category_id = NULL  
            WHERE deletion_date IS NULL AND category_id = (SELECT id FROM Category WHERE name='{$category_name}')";

    $result = $conn->query($sql); 
    if ($result == False) {
        echo "<br> Query failed <br>";
    }

    edit_categories();
}


function update_items_category($category_name, $items) {
    global $conn;
    connect_to_db();

    $category_id = null;

    if ($category_name != null) {
        $sql = 'SELECT Category.id FROM Category 
                WHERE Category.name = "' .$category_name. '" AND deletion_date IS NULL';

        $result = $conn->query($sql); 
        if ($result == False) {
            echo "<br> Query failed <br>";
        }

        $category_id = $result->fetch_assoc()['id'];
    }

    foreach ($items as $item) {
        if ($item == null) {
            continue;
        }

        $sql = 'UPDATE Item SET category_id = ' .($category_id == null ? "null":$category_id). ' WHERE name = "' .$item. '"';
        $result = $conn->query($sql); 
        if ($result == False) {
            echo "<br> Query failed <br>";
        }
    }
}


function get_items($category_name) {
    global $conn;
    connect_to_db();

    echo '<head> 
    <script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.js"></script> 

    <script type="text/javascript">
        $(function(){
            $("#categorize_button").click(function(){
                $("#uncategorized_list > option:selected").each(function(){
                    $(this).remove().appendTo("#categorized_list");
                });
            });
            
            $("#uncategorize_button").click(function(){
                $("#categorized_list > option:selected").each(function(){
                    $(this).remove().appendTo("#uncategorized_list");
                });
            });
        });
    </script>
    </head>';


    $sql = "SELECT Item.name FROM Item 
            INNER JOIN Category ON Item.category_id = Category.id 
            WHERE Category.name = '{$category_name}' AND Item.deletion_date IS NULL";
    $result = $conn->query($sql); 
    if ($result == False) {
        echo "<br> Query failed <br>";
    }

    $category_items = [];
    while ($row = $result->fetch_assoc()) {
        array_push($category_items, $row['name']);
    }


    $sql = 'SELECT * FROM Item WHERE Item.category_id IS NULL AND deletion_date IS NULL';
    $result = $conn->query($sql); 
    if ($result == False) {
        echo "<br> Query failed <br>";
    }

    echo '<table style="display:inline"><th>Uncategorized</th><tr><td>';
    echo '<form action="edit_categories.php" method="post" style="display:inline">';
    echo '<select id="uncategorized_list" multiple="multiple" size=8 name="categorize_item[]">';
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' .$row["name"]. '">' .$row["name"]. ' (' .$row["unit"]. ') </option>';
    }
    echo '</select></td></tr></table>';

    echo '<input id="uncategorize_button" type="button" value="<-" onclick="this.form.submit()"/>
          <input id="categorize_button" type="button" value="->" onclick="this.form.submit()"/>';

    $sql = 'SELECT name, unit FROM Item ORDER BY name ASC';
    $result = $conn->query($sql); 
    if ($result == False) {
        echo "<br> Query failed <br>";
    }

    echo '<table style="display:inline"><th>Categorized</th><tr><td>';
    echo '<select id="categorized_list" multiple="multiple" size=8 name="uncategorize_item[]">';
    while ($row = $result->fetch_assoc()) {
        if (in_array($row["name"], $category_items)) {
            echo '<option value="' .$row["name"]. '">' .$row["name"]. ' (' .$row["unit"]. ') </option>';
        }
    }
    echo '</select></td></tr></table>';

    echo '<input type="hidden" name="func_name" value="update_items">';
    echo '<input type="hidden" name="category_name" value="' .$category_name. '">';
    echo '</form><br>';
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

    $sql = "SELECT * FROM Category 
            WHERE creation_date <='" .date('Y-m-d'). "' 
                AND (deletion_date > '" .date('Y-m-d'). "' OR deletion_date IS NULL) 
            ORDER BY name ASC";

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
    echo '<select name="options" size="10" onchange=categorySelect(this)>';
    while ($row = $result->fetch_assoc()) {
        echo '<option value="edit_categories.php?func_name=get_items&name=' .$row["name"]. '"> ' .$row["name"];
    }
    echo '</select><br><br>';
    echo '</form>';
    echo '<iframe name="iframe" id="items_frame" width="100%" height="50%" style="border:none" src=""></iframe>';
}


if(strcmp($_GET['func_name'], 'edit_categories') == 0) {
    echo edit_categories();
} else if(strcmp($_GET['func_name'], 'get_items') == 0) {
    echo get_items($_GET['name']);
} else if(strcmp($_POST['func_name'], 'update_items') == 0) {
    if (array_key_exists('categorize_item', $_POST)) {
        update_items_category($_POST['category_name'], $_POST['categorize_item']);
    } else if (array_key_exists('uncategorize_item', $_POST)) {
        update_items_category(null, $_POST['uncategorize_item']);
    }
    get_items($_POST['category_name']);
} else if(array_key_exists('edit_categories_button', $_POST)) {
    if (strcmp($_POST['edit_categories_button'], 'Add') == 0) {
        add_category($_POST['category']);
    } else if (strcmp($_POST['edit_categories_button'], 'Remove') == 0) {
        remove_category($_POST['category']);
    }
}

?>
