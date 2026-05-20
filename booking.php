<?php
include 'db.php';

if(isset($_POST['book'])){

$name=$_POST['name'];
$room=$_POST['room'];
$checkin=$_POST['checkin'];
$checkout=$_POST['checkout'];

mysqli_query($conn,
"INSERT INTO bookings(name,room_type,checkin,checkout)
VALUES('$name','$room','$checkin','$checkout')");

echo "Room Booked Successfully";
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Booking</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-5">

<h2>Room Booking</h2>

<form method="POST">

<input type="text" name="name"
placeholder="Your Name"
class="form-control mb-3">

<select name="room"
class="form-control mb-3">

<option>Luxury Room</option>
<option>Deluxe Room</option>
<option>Premium Suite</option>

</select>

<input type="date"
name="checkin"
class="form-control mb-3">

<input type="date"
name="checkout"
class="form-control mb-3">

<button name="book"
class="btn btn-primary">
Book Now
</button>

</form>

</div>

</body>
</html>