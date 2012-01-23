<?php

//require_once('voxeo-sms-lib.php');
require_once('mysql-db-lib.php');

function echo_options()
{
        $conn = get_db_connection();

        $query = "SELECT language_name_string FROM languages";
        $result = mysql_query($query) or die(mysql_error());

        $i = 1;

        while ($row = mysql_fetch_assoc($result)) {
                echo("Press " . $i . " for " . $row["language_name_string"] . ".\n");
                $i++;
        }
}

echo('<?xml version="1.0" encoding="UTF-8" ?>');
?>

<callxml version="3.0">
        <prompt choices="1,2">
                <?php echo_options(); ?>
        </prompt>
        <on event="choice:1">
                <say>one</say>
        </on>
        
        <on event="choice:2">
                <say>two</say>
        </on>
        <wait value="10s"/> 
</callxml>