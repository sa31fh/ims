<?php
require_once "database_table.php";

class BulkOrderDataTable extends DatabaseTable {

    public static function save_bulk_dates($date_created, $date_start, $date_end) {
        $sql = "INSERT INTO BulkOrderData (date_created, date_start, date_end)
                VALUES ('$date_created', '$date_start', '$date_end')
                ON DUPLICATE KEY UPDATE
                date_created = VALUES(date_created), date_start = VALUES(date_start), date_end = VALUES(date_end)";

        return parent::query($sql);
    }

    public static function get_bulk_dates($date_created) {
        $sql = "SELECT * FROM BulkOrderData
                WHERE date_created = '$date_created'";

        return parent::query($sql);
    }


}
?>