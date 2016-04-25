<?php
require_once "database_table.php";

Class TimeslotItemTable extends DatabaseTable {

    public static function add_timeslot_item($item_name, $timeslot_name) {
        $sql = "INSERT INTO TimeSlotItem (item_id, timeslot_id)
                VALUES ((SELECT id FROM Item WHERE name ='$item_name'),
                        (SELECT id FROM TimeSlots WHERE name ='$timeslot_name'))";

        return parent::query($sql);
    }

    public static function remove_timeslot_item($item_name, $timeslot_name) {
        $sql = "DELETE FROM TimeSlotItem
                WHERE item_id = (SELECT id FROM Item WHERE name = '$item_name')
                AND timeslot_id = (SELECT id FROM TimeSlots WHERE name = '$timeslot_name')";

        return parent::query($sql);
    }

    public static function update_timeslot_factor($tsi_id, $factor) {
        $sql = "UPDATE TimeSlotItem
                SET factor = '$factor'
                WHERE id = '$tsi_id'";

        return parent::query($sql);
    }
}
?>