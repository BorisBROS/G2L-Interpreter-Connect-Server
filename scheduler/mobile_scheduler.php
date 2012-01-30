<?php 
require_once('config.php');

try {
    $db = new PDO("mysql:host=$mysql_server;dbname=$mysql_db", $mysql_user, $mysql_pass);
 
    echo "Connected to database"; // check for connection
 
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
 
    $sql = "SELECT * FROM events_rec";
    $result = $db->query($sql);
    foreach ($result as $row) {
        echo $row;
    }
 
    $db = null; // close the database connection
 
}
catch(PDOException $e) {
    echo $e->getMessage();
}

?>
