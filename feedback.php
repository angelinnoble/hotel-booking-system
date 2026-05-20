<?php
include 'db.php';

if(isset($_POST['send'])){

$name=$_POST['name'];
$message=$_POST['message'];

mysqli_query($conn,
"INSERT INTO feedback(name,message)
VALUES('$name','$message')");

echo "Feedback Sent";
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Feedback</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-5">

<h2>Feedback Form</h2>

<form method="POST">

<input type="text"
name="name"
placeholder="Your Name"
class="form-control mb-3">

<textarea name="message"
class="form-control mb-3"
placeholder="Your Feedback"></textarea>

<button name="send"
class="btn btn-success">
Send Feedback
</button>

</form>

</div>

</body>
</html>