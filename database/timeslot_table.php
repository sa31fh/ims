<?php
require_once "database_table.php";

class TimeslotTable extends DatabaseTable {

    /**
     * Add new timeslot
     *
     * @param  string $timeslot_name Name of new timeslot
     * @return boolean               Returns true on query success and false if item already exists.
     */
    public static function add_timeslot($timeslot_name) {
        $sql = "INSERT INTO TimeSlots (name)
                VALUES ('$timeslot_name')";

        return parent::query($sql);
    }

    /**
     * Get all timeslots.
     *
     * @return object|false     Returns mysqli_result object on query success or false if query fails.
     */
    public static function get_timeslots() {
        $sql = "SELECT name FROM TimeSlots
                ORDER BY order_id ASC";

        return parent::query($sql);
    }

    /**
     * Delete a timeslot from the database.
     *
     * @param  string $timeslot_name Name of timeslot to delete.
     * @return boolean               Returns true on query success and false if item already exists.
     */
    public static function delete_timeslot($timeslot_name) {
        $sql = "DELETE FROM TimeSlots
                WHERE name = '$timeslot_name'";

        return parent::query($sql);
    }

    /**
     * Update order value for given timeslot.
     *
     * @param  string   $timeslot_name  Name of timeslot to update.
     * @param  int      $order_id       New order value.
     * @return boolean                  Returns true on query success and false if item already exists.
     */
    public static function update_timeslot_order($timeslot_name, $order_id) {
        $sql = "UPDATE TimeSlots
                SET order_id = '$order_id'
                WHERE name = '$timeslot_name'";

        return parent::query($sql);
    }
}
?>