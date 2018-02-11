<?php
require_once "database_table.php";

class SalesTable extends DatabaseTable {

    public static function add_actual_sale($value, $date) {
        $sql = "INSERT INTO Sales (actual_sales, `date`)
                VALUES ($value, '$date')
                ON DUPLICATE KEY UPDATE
                `date`= VALUES(`date`), actual_sales = VALUES(actual_sales)";

        return parent::query($sql);
    }

    public static function add_expected_sale($value, $date) {
        $sql = "INSERT INTO Sales (expected_sales, `date`)
                VALUES ($value, '$date')
                ON DUPLICATE KEY UPDATE
                expected_sales = VALUES(expected_sales), `date`= VALUES(`date`)";

        return parent::query($sql);
    }

    public static function get_actual_sale($date) {
        $sql = "SELECT actual_sales FROM Sales
                WHERE `date` = '$date'";

        if ($result = parent::query($sql)) {
            return $result->fetch_assoc()['actual_sales'];
        }
    }

    public static function get_expected_sale($date) {
        $sql = "SELECT expected_sales FROM Sales
                WHERE `date` = '$date'";

        if ($result = parent::query($sql)) {
            return $result->fetch_assoc()['expected_sales'];
        }
    }
}
?>