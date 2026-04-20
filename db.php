<?php
$conn = new mysqli("localhost","root","","operation_redlight");
if($conn->connect_error){
    die("Connection Failed");
}
?>