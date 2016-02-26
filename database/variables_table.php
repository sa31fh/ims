<?php 
require_once "database_table.php";

class VariablesTable extends DatabaseTable {

    public static function get_expected_sales() {
        $sql = "SELECT value FROM Variables WHERE name='ExpectedSales'";

        if ($result = parent::query($sql)) {
            return (int) $result->fetch_assoc()['value'];
        } else {
            throw new Exception("get_expected_sales query failed");
        }
    }

    public static function update_expected_sales($expected_sales) {
        $sql = "INSERT INTO Variables (name, value)  
                VALUES ('ExpectedSales', '$expected_sales') 
                ON DUPLICATE KEY UPDATE name = VALUES(name), value = VALUES(value)";

        return parent::query($sql);
    }

    public static function get_base_sales() {
        $sql = "SELECT value FROM Variables WHERE name='BaseSales'";

        if ($result = parent::query($sql)) {
            return (int) $result->fetch_assoc()['value'];
        } else {
            throw new Exception("get_base_sales query failed");
        }
    }

    public static function update_base_sales($base_sales) {
        $sql = "INSERT INTO Variables (name, value)  
                VALUES ('BaseSales', '$base_sales') 
                ON DUPLICATE KEY UPDATE name = VALUES(name), value = VALUES(value)";

        return parent::query($sql);
    }
}
?>