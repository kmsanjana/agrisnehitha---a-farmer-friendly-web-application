<?php
error_reporting(0);
// Create connection
$con=mysqli_connect("localhost","root","","agrisnehitha");

// Check connection
if (mysqli_connect_errno($con2))
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
?>