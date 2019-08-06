<?php
$con=mysqli_connect("localhost","root","","florist");
if(mysqli_errno($con)){
    echo "Connection failed";
    die($con);
}
?>