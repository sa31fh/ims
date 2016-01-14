<nav>
    <ul>
        <li>
            <a href="category_status.php">Home</a>
        </li>
        <li>
            <a href="edit_categories.php">Categories</a>
        </li>
        <li>
            <a href="edit_items.php">Items</a>
        </li>
        <li>
            <a href="manage_users.php">Users</a>
        </li>
        <ul id="nav_right">
            <li>
                <a href="">Print Preview</a>
            </li>
            <li id="userinfo" >
                <div id="username"><span><?php echo $_SESSION["username"]; ?></span></div>
                <div id="userrole"><span id="two"><?php echo $_SESSION["userrole"];?></span></div>
            </li>
            <li id="logout">
                <form action="new_nav.php" method="post">
                    <input type="submit" name="logout" value="logout">
                </form>
            </li>
        </ul>
    </ul>
</nav>

<?php
    if (isset($_POST["logout"])) {
        session_start();
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit();
    }
?>