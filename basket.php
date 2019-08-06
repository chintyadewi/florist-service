<?php
require_once('connection.php');

$url=$_SERVER['REQUEST_URI'];
$id_user = substr($url, strrpos($url, '/') + 1);

// update content
$sql = "select * from basket where id_user='$id_user'";
$query = mysqli_query($con, $sql );

if($query) {
    $result=array();
    while($row=mysqli_fetch_assoc($query)){
        $result[]=$row;
    }
    echo json_encode($result);
} else {
    echo "Failed to get data";
}
?>