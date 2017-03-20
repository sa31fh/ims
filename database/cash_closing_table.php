<?php
require_once "database_table.php";


class CashClosingTable extends DataBaseTable {

    public static function add_row($date) {
        $sql = "INSERT INTO CashClosing (name, creation_date)
                VALUES (NULL, '$date')";

        return parent::query($sql);
    }

    public static function get_rows($date) {
        $sql = "SELECT * FROM CashClosing
                WHERE creation_date <= '$date' AND (deletion_date > '$date' OR deletion_date IS NULL)
                ORDER BY order_id";

        return parent::query($sql);
    }

    public static function get_row_data($date) {
        $sql = "SELECT CashClosing.id, name, type, quantity, notes  FROM CashClosing
                LEFT JOIN (SELECT * FROM CashClosingData
                WHERE `date` = '$date') AS CashClosingData
                ON CashClosing.id = row_id
                WHERE creation_date <= '$date' AND (deletion_date > '$date' OR deletion_date IS NULL)
                ORDER BY order_id";

        return parent::query($sql);
    }

    public static function add_details($name, $type) {
        $sql = "INSERT INTO CashClosing (name, type)
                VALUES ('$name', $type)";

        return parent::query($sql);
    }

    public static function update_name($id, $name) {
        $sql = "UPDATE CashClosing
                SET name = '$name'
                WHERE id = $id";

        return parent::query($sql);
    }

    public static function update_type($id, $type) {
        $sql = "UPDATE CashClosing
                SET type = '$type'
                WHERE id = $id";

        return parent::query($sql);
    }

    public static function delete_rows($row_id, $date) {
        $sql = "UPDATE CashClosing SET deletion_date = '$date'
                WHERE id IN ('".implode("','", $row_id)."') AND deletion_date IS NULL ";

        return parent::query($sql);
    }

    public static function update_row_order($row_id, $order_id) {
        $sql = "UPDATE CashClosing
                SET order_id = '$order_id'
                WHERE id = '$row_id'";

        return parent::query($sql);
    }
}
?>