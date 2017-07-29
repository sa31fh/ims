<?php
require_once "database_table.php";

class ItemRequiredDaysTable extends DatabaseTable {

    public static function add_item_day($day_id, $item_id) {
        $sql = "INSERT INTO ItemRequiredDays (day_id, item_id)
                VALUES ($day_id, $item_id)";

        return parent::query($sql);
    }


    public static function get_item_days($item_id) {
        $sql = "SELECT day_id FROM ItemRequiredDays
                WHERE item_id = $item_id";

        return parent::query($sql);
    }

    public static function get_by_day($day_id) {
        $sql = "SELECT item_id FROM ItemRequiredDays
                WHERE day_id = $day_id";

        return parent::query($sql);
    }

    public static function remove_item_day($day_id, $item_id) {
        $sql = "DELETE FROM ItemRequiredDays
                WHERE item_id = $item_id
                AND day_id = $day_id";

        return parent::query($sql);
    }
}
?>
