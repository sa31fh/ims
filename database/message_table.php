<?php 
require_once "database_table.php";

class MessageTable extends DatabaseTable {

    public static function create_message($sender, $receiver, $message, $conversation_id, $date) {
        $sql = "INSERT INTO Message (`timestamp`, sender, receiver, message, conversation_id)
                VALUES ('$date', '$sender', '$receiver', '$message', '$conversation_id')";

        if ($result = parent::query($sql)) {
            $sql = "UPDATE Conversation 
                    SET `timestamp`='$date'
                    WHERE id = '$conversation_id'";

            return parent::query($sql);
        }
    }

    public static function get_messages($conversation_id) {
        $sql = "SELECT * FROM Message
                INNER JOIN (SELECT first_name, last_name, username FROM User) as nameTable
                ON nameTable.username = Message.sender
                WHERE conversation_id = '$conversation_id'
                ORDER BY `timestamp` ASC";

        return parent::query($sql);
    }
}
?>