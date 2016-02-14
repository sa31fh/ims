<?php 
require_once "data_base_table.php";

class MessageTable extends DatabaseTable {

	public static function create_message($sender, $receiver, $message, $conversation_id, $date) {

		$sql = "INSERT INTO Message (`timestamp`, sender, receiver, message, conversation_id)
            	VALUES ('$date', '$sender', '$receiver', '$message', '$conversation_id')";

	    if ($result = parent::query($sql)) {
	        $sql = "UPDATE Conversation 
	                SET `timestamp`='$date'
	                WHERE id = '$conversation_id'";

	        if($result = parent::query($sql)){
	            return true;
	        } else {
	            echo "create_message query failed at updating conversation";
	            return false;
	        }
	    } else {
	        echo "<br> create_message query failed <br>";
	        return false;
	    }
	}

	public static function get_messages($conversation_id) {

		$sql = "SELECT * FROM Message
	            INNER JOIN (SELECT first_name, last_name, username FROM User) as nameTable
	            ON nameTable.username = Message.sender
	            WHERE conversation_id = '$conversation_id'
	            ORDER BY `timestamp` ASC";

        $result = parent::query($sql);
        return $result;
	}
}


?>