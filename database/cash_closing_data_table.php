<?php

require_once "database_table.php";


class CashClosingDataTable extends DataBaseTable {

    public static function update_row($row_id, $date, $quantity, $note) {
        $sql = "INSERT INTO CashClosingData (row_id, `date`, quantity, notes)
                VALUES ('$row_id', '$date', $quantity, '$note')
                ON DUPLICATE KEY UPDATE
                row_id = VALUES(row_id), `date` = VALUES(`date`),
                quantity = VALUES(quantity), notes = VALUES(notes)";

        return parent::query($sql);
    }
}
?>