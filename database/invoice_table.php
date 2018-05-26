<?php
require_once "database_table.php";


class InvoiceTable extends DataBaseTable {

    public static function get_tracked_invoices() {
    $sql = "SELECT * FROM Invoice
            ORDER BY `date` DESC";

        return parent::query($sql);
    }

    public static function track_invoice($date) {

        $sql = "INSERT INTO Invoice (`date`)
                VALUES ('$date')
                ON DUPLICATE KEY UPDATE
                `date` = VALUES(`date`)";

        return parent::query($sql);
    }

    public static function get_tracked($date) {
        $sql = "SELECT * FROM Invoice
                WHERE  `date` = '$date'";

        return parent::query($sql);
    }

    public static function update_invoice_status($date, $status) {
        $sql = "UPDATE Invoice
                SET status = $status
                WHERE `date` = '$date'";

        return parent::query($sql);
    }

    public static function remove_invoice($date) {
        $sql = "DELETE FROM Invoice
                WHERE `date` = '$date'";

        return parent::query($sql);
    }
}
?>