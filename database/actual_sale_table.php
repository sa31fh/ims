<?php 
require_once "database_table.php";

class ActualSale extends DatabaseTable {

    public static function add_actual_sale($value, $date) {
        $sql = "INSERT INTO ActualSale (value, `date`)
                VALUES ($value, '$date')
                ON DUPLICATE KEY UPDATE
                value = VALUES(value), `date`= VALUES(`date`)";

        return parent::query($sql);
    }

    public static function get_actual_sale($date) {
        $sql = "SELECT value FROM ActualSale
                WHERE `date` = '$date'";

        if ($result = parent::query($sql)) {
            return (int) $result->fetch_assoc()['value'];
        }
        return parent::query($sql);
    }
}
?>