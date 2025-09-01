<?php
require_once __DIR__ . '/../controllers/UserController.php';
$userController = new UserController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($userController->login($_POST)) {
        header("Location: ../../public/index.php?login_success=1");
        exit;
    } else {
        echo "<p style='color:red;'>Invalid login credentials.</p>";
    }
}

// If already logged in, redirect based on role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: ../../public/index.php");
        exit;
    } elseif ($_SESSION['role'] === 'user') {
        header("Location: ../../public/index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7f9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 400px;
            margin: 80px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #04AA6D;
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
            color: #333;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            outline: none;
            transition: 0.3s;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #04AA6D;
            box-shadow: 0 0 5px rgba(4, 170, 109, 0.3);
        }

        button {
            width: 100%;
            padding: 10px;
            background: #04AA6D;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #028a56;
        }

        p {
            text-align: center;
            margin-top: 15px;
        }

        a {
            color: #04AA6D;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="POST">
            <label>Email:</label>
            <input type="email" name="email" required>
            
            <label>Password:</label>
            <input type="password" name="password" required>
            
            <button type="submit">Login</button>
        </form>

        <p><a href="../../public/index.php">Back to Home</a></p>
    </div>
</body>
</html>
