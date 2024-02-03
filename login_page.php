<?php
session_start(); // Start the session

if (isset($_SESSION['user_id'])) {
    // If the user is already logged in, redirect to the schedule meeting page
    header("Location: schedule_meeting.php");
    exit();
}

// Your database connection code here
include('connection.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = $_POST['username'];
    $password = $_POST['password'];

    // Validate user credentials (you should use a secure authentication method, like password hashing)
    $loginQuery = "SELECT u.*, s.sector_name, a.admin_name
                   FROM users u
                   JOIN sectors s ON u.sector_id = s.sector_id
                   JOIN administrations a ON u.admin_id = a.admin_id
                   WHERE u.user_name = '$user_name' AND u.password = '$password'";
                   
    $loginResult = $conn->query($loginQuery);

    if ($loginResult->num_rows > 0) {
        $user = $loginResult->fetch_assoc();

        // Store user information in session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['user_name'];
        $_SESSION['sector_id'] = $user['sector_id'];
        $_SESSION['admin_id'] = $user['admin_id'];
        $_SESSION['sector_name'] = $user['sector_name'];
        $_SESSION['admin_name'] = $user['admin_name'];

        // Redirect to the schedule meeting page
        header("Location: schedule_meeting.php");
        exit();
    } else {
        $loginError = "Invalid username or password";
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <!-- Bootstrap CSS link -->
    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" href="css/log.css"/>
    
</head>
<body class="bg-light">

<div class="text-right mb-3">
 <a href="index.html" class="btn btn-danger">الرئيسية</a>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2 class="text-center">تسجيل الدخول</h2>
                </div>
                <div class="card-body">
                    <?php
                    if (isset($loginError)) {
                        echo "<div class='alert alert-danger'>$loginError</div>";
                    }
                    ?>
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="username">اسم المستخدم:</label>
                            <input type="text" id="username" name="username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="password">كلمة المرور:</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">تسجيل</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and jQuery scripts (place them at the end of the body for better performance) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>