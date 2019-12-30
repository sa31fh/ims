<?php
require_once "database_table.php";

class CateringOrderTable extends DatabaseTable {

    public static function add_order($name , $date_delivery, $date_created) {
        $sql = "INSERT INTO CateringOrder (name , date_delivery, date_created)
                VALUES ('$name', '$date_delivery', '$date_created')";

        return parent::query($sql);
    }

    public static function get_orders() {
        $sql = "SELECT * FROM CateringOrder
                ORDER BY date_delivery DESC";

        return parent::query($sql);
    }

    public static function get_tracked($order_id) {
        $sql = "SELECT * FROM CateringOrder
                WHERE  id = '$order_id'";

        return parent::query($sql);
    }

    public static function get_catering_people($order_id) {
        $sql = "SELECT people FROM CateringOrder
                WHERE id = '$order_id'";

        return parent::query($sql)->fetch_assoc()["people"];
    }

     public static function update_catering_people($people, $id) {
        $sql = "UPDATE CateringOrder
                SET people = $people
                WHERE id = '$id'";

        return parent::query($sql);
    }

    public static function get_orders_by_date($todays_date, $future_date) {
        $sql = "SELECT * FROM CateringOrder
                WHERE date_delivery > '$todays_date' AND date_delivery <= '$future_date'
                ORDER BY date_delivery ASC";

        return parent::query($sql);
    }

    public static function update_invoice_status($id, $status) {
        $sql = "UPDATE CateringOrder
                SET status = $status
                WHERE id = '$id'";

        return parent::query($sql);
    }

    public static function get_order_invoice() {
        $sql = "SELECT * FROM CateringOrder
                WHERE date_invoice IS NOT NUll
                ORDER BY date_delivery DESC";

        return parent::query($sql);
    }

    public static function delete_order($order_id) {
        $sql = "DELETE FROM CateringOrder
                WHERE id = $order_id";

        return parent::query($sql);
    }

    public static function remove_invoice($order_id) {
        $sql = "UPDATE CateringOrder
                SET date_invoice = NULL,
                    status = 1
                WHERE id = $order_id";

        return parent::query($sql);

    }

    public static function edit_order($order_id, $name, $date_delivery) {
        $sql = "UPDATE CateringOrder
                SET name = '$name',
                    date_delivery = '$date_delivery'
                WHERE id = $order_id";

        return parent::query($sql);
    }

    public static function get_order_count($todays_date, $future_date) {
        $sql = "SELECT COUNT(id) AS order_count FROM CateringOrder
                WHERE date_delivery > '$todays_date' AND date_delivery <= '$future_date'";

        if ($result = parent::query($sql)){
            return $result->fetch_assoc()['order_count'];
        } else {
            throw new Exception("get order count query failed");
        }
    }

    public static function update_order_invoice($order_id, $date) {
        $sql = "UPDATE CateringOrder
                SET date_invoice = '$date'
                WHERE id = $order_id";

        return parent::query($sql);
    }

    public static function update_order_note($note, $order_id) {
        $sql = "UPDATE CateringOrder
                SET notes = '$note'
                WHERE id = $order_id";

        return parent::query($sql);
    }
}

?>