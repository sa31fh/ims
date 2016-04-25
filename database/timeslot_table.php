<?php
require_once "database_table.php";

class TimeslotTable extends DatabaseTable {

    public static function add_timeslot($timeslot_name) {
        $sql = "INSERT INTO TimeSlots (name)
                VALUES ('$timeslot_name')";

        return parent::query($sql);
    }

    public static function get_timeslots() {
        $sql = "SELECT name FROM TimeSlots
                ORDER BY order_id ASC";

        return parent::query($sql);
    }

    public static function delete_timeslot($timeslot_name) {
        $sql = "DELETE FROM TimeSlots
                WHERE name = '$timeslot_name'";

        return parent::query($sql);
    }

    public static function update_timeslot_order($timeslot_name, $order_id) {
        $sql = "UPDATE TimeSlots
                SET order_id = '$order_id'
                WHERE name = '$timeslot_name'";

        return parent::query($sql);
    }
}
?>