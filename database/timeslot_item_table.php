<?php
require_once "database_table.php";

class TimeslotItemTable extends DatabaseTable {

    /**
     * Add new item to a timeslot.
     *
     * @param string $item_name         Name of new item.
     * @param string $timeslot_name     Name of timeslot to assign item to.
     * @return boolean                  Returns true on query success and false if item already exists.
     */
    public static function add_timeslot_item($item_name, $timeslot_name) {
        $sql = "INSERT INTO TimeSlotItem (item_id, timeslot_id)
                VALUES ((SELECT id FROM Item WHERE name ='$item_name'),
                        (SELECT id FROM TimeSlots WHERE name ='$timeslot_name'))";

        return parent::query($sql);
    }

    /**
     * Remove item from timeslot.
     *
     * @param  string $item_name     Name of item to remove.
     * @param  string $timeslot_name Name of timeslot to remove from.
     * @return boolean               Returns true on query success and false if item already exists.
     */
    public static function remove_timeslot_item($item_name, $timeslot_name) {
        $sql = "DELETE FROM TimeSlotItem
                WHERE item_id = (SELECT id FROM Item WHERE name = '$item_name')
                AND timeslot_id = (SELECT id FROM TimeSlots WHERE name = '$timeslot_name')";

        return parent::query($sql);
    }

    /**
     * Update factor for given table id.
     *
     * @param  int $timeslot_inventory_id    Id of table row to update.
     * @param  int $factor                   New factor value.
     * @return boolean               Returns true on query success and false if item already exists.
     */
    public static function update_timeslot_factor($timeslot_inventory_id, $factor) {
        $sql = "UPDATE TimeSlotItem
                SET factor = '$factor'
                WHERE id = '$timeslot_inventory_id'";

        return parent::query($sql);
    }
}
?>