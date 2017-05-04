<?php
    require_once "database/conversation_table.php";
    require_once "database/inventory_table.php";
    require_once "database/variables_table.php";
    require_once "database/sales_table.php";
    require_once "database/base_quantity_table.php";
    require_once "database/notification_status_table.php";
    require_once "mpdf/vendor/autoload.php";
    include_once 'phpmailer/PHPMailerAutoload.php';

    $inventory_data = InventoryTable::get_inventory_with_deviation(date("Y-m-d", strtotime("yesterday")));
    $current_category = null;
    $row_count = 0;
    $message = "Item Deviation Report for ".date('jS F Y', strtotime("yesterday")).".";
    $attachment_title = "Deviation Report (".date('d-m-Y', strtotime("yesterday")).").pdf";
    $attachment =
        '<table class="table_view" id="print">
            <tr class="row">
                <th colspan="6" class="heading">Item Deviation Report</th>
            </tr>
            <tr id="print_date" class="row">
                <th colspan="6">
                    <div class="print_table_date">'."created on ".date('D, M d Y', strtotime("yesterday")).'</div>
                </th>
            </tr>';

    while ($row = $inventory_data->fetch_assoc()) {
        if ($row["has_deviation"] > 0) {
            if ($row["quantity"] > 0) {
                $current_deviation = (abs($row["quantity"] - $row["expected_quantity"]) * 100) / $row["quantity"];
            } else {
                $current_deviation = (abs($row["quantity"] - $row["expected_quantity"]) * 100) / 1;
            }
            $current_deviation = round($current_deviation, 2);
            if ($row["category_name"] != $current_category AND $row["category_name"] != null) {
                $current_category = $row["category_name"];
            $attachment .=
            '<tbody class="print_tbody" id="print_tbody">
                <tr id="category"><td colspan="6" class="table_heading">'.$row["category_name"].'</td></tr>
                <tr id="category_columns">
                    <th>Item</th><th>Unit</th><th>Expected Quantity</th><th>Quantity Present</th><th>Accepted Deviation</th><th>Current Deviation</th>
                </tr>';
            }
            $attachment .=
            '<tr id="column_data" class="row">
            <td>'.$row["name"].'</td>
            <td>'.$row["unit"].'</td>
            <td>'.$row["expected_quantity"].'</td>
            <td>'.$row["quantity"].'</td>
            <td>'.$row["deviation"].' %'.'</td>
            <td>'.$current_deviation.' %'.'</td>
            </tr>';

            $row_count++;
        }
    }
    $attachment .= '</table>';


    if ($row_count > 0) {
        $result = NotificationStatusTable::get_alert_info("notify by message", "daily deviation report");
        while ($row = $result->fetch_assoc()) {
            if ($row["noti_status"] == 1 AND $row["sub_noti_status"] == 1 AND $row["role"] == "admin") {
                ConversationTable::create_conversation("System", $row["user_name"], "Daily Item Deviation Report",
                        $message, gmdate("Y-m-d H:i:s"), $attachment, "Item Deviation Report", "read", "unread");
            }
        }

        $mpdf = new mPDF("", "A4", 0, 'roboto', 0, 0, 0, 0, 0, 0);
        $stylesheet = file_get_contents("css/pdf_styles.css");
        $mpdf->useSubstitutions=false;
        $mpdf->simpleTables = true;
        $mpdf->WriteHtml($stylesheet, 1);
        $mpdf->WriteHtml($attachment, 2);
        $content = $mpdf->Output('', 'S');

        $mail = new PHPMailer;
        $result = NotificationStatusTable::get_alert_info("notify by email", "daily deviation report");
        $email_count = 0;
        while ($row = $result->fetch_assoc()) {
            if ($row["noti_status"] == 1 AND $row["sub_noti_status"] == 1) {
            $mail->addAddress($row["email"]);
            $email_count++;
            }
        }
        $mail->setFrom('system@ims-test.auntyskitchen.ca', 'IMS System - Waterloo');
        $mail->Subject  = "Daily Deviation Report - ".date('d/m/y', strtotime("yesterday")) ;
        $mail->Body     = $message;
        $mail->addStringAttachment($content, $attachment_title);
        if ($content != "" AND $email_count > 0) {
            if(!$mail->send()) {
              echo 'Message was not sent.';
              echo 'Mailer error: ' . $mail->ErrorInfo;
            } else {
              echo 'Message has been sent.';
            }
        }
    }

?>