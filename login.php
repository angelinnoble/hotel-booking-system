<?php
session_start();

include 'db.php';

if(isset($_POST['login'])){

$email=$_POST['email'];
$password=$_POST['password'];

$query=mysqli_query($conn,
"SELECT * FROM users
WHERE email='$email'
AND password='$password'");

if(mysqli_num_rows($query)>0){

$_SESSION['user']=$email;

header("Location: dashboard.php");

}else{
echo "Invalid Login";
}

}
?>

<!DOCTYPE html>
<html>
<head>

<title>Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-5">

<h2>User Login</h2>

<form method="POST">

<input type="email" name="email"
class="form-control mb-3"
placeholder="Email">

<input type="password" name="password"
class="form-control mb-3"
placeholder="Password">

<button name="login"
class="btn btn-primary">
Login
</button>

</form>

</div>

</body>
</html>