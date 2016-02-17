<?php 
function convert_date_timezone($date) {
    $newDate = date_create($date, timezone_open('GMT'));
    $tz_date = date_timezone_set($newDate, timezone_open($_SESSION["timezone"]));
    return date_format($tz_date, "h:ia d/m/Y");
}
?>
