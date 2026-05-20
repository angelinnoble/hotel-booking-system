<?php
session_start();

if(!isset($_SESSION['user'])){
header("Location: login.php");
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-5">

<h2>Welcome User</h2>

<a href="booking.php" class="btn btn-success">
Book Room
</a>

<a href="feedback.php" class="btn btn-warning">
Feedback
</a>

<a href="logout.php" class="btn btn-danger">
Logout
</a>

</div>

</body>
</html>