<?php

// THIS MUST BE REPLACED WITH YOUR DATABASE NAME
$DB_USERNAME = "your-own-database-name";

// Open a connection to the database
 
$dbh = pg_connect("dbname=$DB_USERNAME");
if (!$dbh) {
    die("Error in connection: " . pg_last_error());
}

?>