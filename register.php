<?php
include 'db.php';

if(isset($_POST['register'])){

    $name=$_POST['name'];
    $email=$_POST['email'];
    $password=$_POST['password'];

    mysqli_query($conn,"INSERT INTO users(name,email,password)
    VALUES('$name','$email','$password')");

    echo "Registration Successful";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Register</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-5">

<h2>User Registration</h2>

<form method="POST">

<input type="text" name="name" placeholder="Name" class="form-control mb-3" required>

<input type="email" name="email" placeholder="Email" class="form-control mb-3" required>

<input type="password" name="password" placeholder="Password" class="form-control mb-3" required>

<button name="register" class="btn btn-success">
Register
</button>

</form>

</div>

</body>
</html>