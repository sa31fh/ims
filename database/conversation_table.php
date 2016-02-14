<?php 
require_once "data_base_table.php";

class ConversationTable extends DatabaseTable {

	public static function create_conversation($sender_name, $receiver_name, $title, $message, $date, $attachment, $sender_status, $receiver_status) {

		$sql = "INSERT INTO Conversation (`timestamp`, sender, receiver, title, sender_conversationStatusId, receiver_conversationStatusId)
            VALUES ('$date' , '$sender_name' , '$receiver_name' , '$title', (SELECT id FROM ConversationStatus WHERE status = '$sender_status'), 
                   (SELECT id FROM ConversationStatus WHERE status = '$receiver_status'))";

	    if ($result = parent::query($sql)) {
	        $sql = "SELECT id from Conversation ORDER BY id DESC LIMIT 1";
	            if($result = parent::query($sql)){
	                $id = (int) $result->fetch_assoc()['id'];

	                $sql = "INSERT INTO Message (`timestamp`, sender, receiver, message, attachment, conversation_id)
	                        VALUES ('$date', '$sender_name', '$receiver_name', '$message', '$attachment', '$id')";

	                if ($result = parent::query($sql)) {
	                    return $result; 
	                } else {
	                    echo "<br> set_new_conversation insert into message query failed <br>";
	                    return false;
	                }
	            }
	    } else {
	        echo "<br> set_new_conversation query failed <br>";
	        return false;
	    }
	}

	public static function get_received_conversations($user) {

		$sql = "SELECT id, `timestamp`, sender, receiver, first_name, last_name, sender_status, receiver_status, title FROM Conversation
            INNER JOIN (SELECT first_name, last_name, username FROM User) AS nameTable
            ON (nameTable.username = sender OR nameTable.username = receiver) AND (nameTable.username != '$user')
            INNER JOIN (SELECT id as sstId, status AS sender_status FROM ConversationStatus) as senderStatusTable
            ON senderStatusTable.sstId = sender_conversationStatusId
            INNER JOIN (SELECT id as rstId, `status` AS receiver_status FROM ConversationStatus) as receiverStatusTable
            ON receiverStatusTable.rstId = receiver_conversationStatusId 
            WHERE (sender = '$user' AND (sender_status != 'deleted' AND sender_status != 'destroy'))
            OR (receiver = '$user'AND (receiver_status != 'deleted' AND receiver_status != 'destroy'))
            ORDER BY `timestamp` DESC";

        $result = parent::query($sql);
        return $result;
	}

	public static function get_deleted_conversations($user) {

		$sql = "SELECT id, `timestamp`, sender, receiver, first_name, last_name, sender_status, receiver_status, title FROM Conversation
            INNER JOIN (SELECT first_name, last_name, username FROM User) AS nameTable
            ON (nameTable.username = sender OR nameTable.username = receiver) AND (nameTable.username != '$user')
            INNER JOIN (SELECT id as sstId, status AS sender_status FROM ConversationStatus) as senderStatusTable
            ON senderStatusTable.sstId = sender_conversationStatusId
            INNER JOIN (SELECT id as rstId, `status` AS receiver_status FROM ConversationStatus) as receiverStatusTable
            ON receiverStatusTable.rstId = receiver_conversationStatusId 
            WHERE (sender = '$user' AND sender_status = 'deleted' )
            OR (receiver = '$user' AND receiver_status = 'deleted')
            ORDER BY `timestamp` DESC";

        $result = parent::query($sql);
        return $result;
	}

	public static function change_conversation_status($user, $conversation_id, $status) {

		$sql = "UPDATE Conversation 
            SET sender_conversationStatusId = IF(sender = '$user', (SELECT id FROM ConversationStatus WHERE status = '$status'), sender_conversationStatusId),
                receiver_conversationStatusId = IF(receiver = '$user', (SELECT id FROM ConversationStatus WHERE status = '$status'), receiver_conversationStatusId)
            WHERE id = '$conversation_id'";

        $result = parent::query($sql);
        return $result;
	}

	public static function set_destroy_date($user, $conversation_id, $date) {

		$sql = "UPDATE Conversation 
            	SET sender_destroyDate = IF(sender = '$user', ".$date.", sender_destroyDate),
                receiver_destroyDate = IF(receiver = '$user', ".$date.", receiver_destroyDate)
            	WHERE id = '$conversation_id'";

        $result = parent::query($sql);
        return $result;
	}

	public static function set_destroy_status($user, $date) {

		$sql = "UPDATE Conversation 
            	SET sender_conversationStatusId = IF((sender = '$user' AND sender_destroyDate = '$date'), (SELECT id FROM ConversationStatus WHERE status = 'destroy') , sender_conversationStatusId),
                receiver_conversationStatusId = IF((receiver = '$user' AND receiver_destroyDate = '$date'), (SELECT id FROM ConversationStatus WHERE status = 'destroy') , receiver_conversationStatusId)";
        $result = parent::query($sql);
        return $result;
	}

	public static function count_unread_conversation($user) {

		$sql = "SELECT COUNT(id) AS unreadConversations FROM Conversation 
	            WHERE (sender = '$user' AND sender_conversationStatusId = (SELECT id FROM ConversationStatus WHERE status = 'unread'))
	            OR (receiver = '$user' AND receiver_conversationStatusId = (SELECT id FROM ConversationStatus WHERE status = 'unread'))";

        $result = parent::query($sql);
        return $result->fetch_assoc()['unreadConversations'];
	}
}

?>