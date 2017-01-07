<?php

require_once "database/inventory_table.php";
require_once "database/conversation_table.php";
require_once "database/notification_status_table.php";
include_once 'phpmailer/PHPMailerAutoload.php';

$quantity = InventoryTable::get_inventory_quantity(date("Y-m-d", strtotime("yesterday")))->fetch_assoc();
$message = "Inventory for \"<strong>".date("l, jS F Y", strtotime("yesterday"))."</strong>\" has not been completed by 12:30AM.";

if ($quantity == "") {

    $result = NotificationStatusTable::get_alert_info("notify by message", "incomplete inventory alert");
        while ($row = $result->fetch_assoc()) {
            if ($row["noti_status"] == 1 AND $row["sub_noti_status"] == 1 AND $row["role"] == "admin") {
                ConversationTable::create_conversation("System", $row["user_name"], "Incomplete Inventory Alert",
                        $message, gmdate("Y-m-d H:i:s"), null, null, "read", "unread");
            }
        }

    $mail = new PHPMailer;
        $result = NotificationStatusTable::get_alert_info("notify by email", "incomplete inventory alert");
        $email_count = 0;
        while ($row = $result->fetch_assoc()) {
            if ($row["noti_status"] == 1 AND $row["sub_noti_status"] == 1) {
            $mail->addAddress($row["email"]);
            $email_count++;
            }
        }
        $mail->setFrom('system@ims-test.auntyskitchen.ca', 'IMS System - Waterloo');
        $mail->Subject  = "Incomplete Inventory Alert - ".date('d/m/y', strtotime("yesterday"));
        $mail->Body     = "Inventory for \"".date("l, jS F Y", strtotime("yesterday"))."\" has not been completed by 12:30AM.";
        if ($email_count > 0) {
            if(!$mail->send()) {
              echo 'Message was not sent.';
              echo 'Mailer error: ' . $mail->ErrorInfo;
            } else {
              echo 'Message has been sent.';
            }
        }
}
?>
