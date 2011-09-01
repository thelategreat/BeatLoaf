<?php

//This is the most important coding.
header("Content-Type: text/Calendar");
header("Content-Disposition: inline; filename=event.ics");
echo"BEGIN:VCALENDAR
VERSION:1.0
BEGIN:VEVENT
DTSTART:".($_GET['date']+1)."
DTEND:".($_GET['date']+2)."
SUMMARY:".$_GET['artist']."
LOCATION".$_GET['venue']."
PRIORITY:3
END:VEVENT
END:VCALENDAR";

?>