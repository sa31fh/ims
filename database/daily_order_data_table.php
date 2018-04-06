<?php
require_once "database_table.php";

class DailyOrderDataTable extends DatabaseTable {

    public static function save_dates($date_created, $qp_date) {
        $sql = "INSERT INTO DailyOrderData (date_created, qp_date)
                VALUES ('$date_created', '$qp_date')
                ON DUPLICATE KEY UPDATE
                date_created = VALUES(date_created), qp_date = VALUES(qp_date)";

        return parent::query($sql);
    }

    public static function get_dates($date_created) {
        $sql = "SELECT * FROM DailyOrderData
                WHERE date_created = '$date_created'";

        return parent::query($sql);
    }


}
?>