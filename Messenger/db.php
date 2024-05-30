<?php
$host="localhost:3306";
$user="root";
$pass= "Asy-120512";
$db="asyran";

$conn=mysqli_connect($host,$user,$pass,$db);

function formatDate($date){
    return date("g:i a",strtotime($date));
}

