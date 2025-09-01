<?php
require_once __DIR__ . '/../../config/Db_Connect.php';

class UserController
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
        session_start();
    }

    // User Registration
    public function register($data)
    {
        global $pdo;

        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            return false; // email already exists
        }

        // Insert new user
        // $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
        $success = $stmt->execute([
            $data['name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT)
        ]);

        if ($success) {
            // fetch the user back
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // auto login
            $_SESSION['user'] = $user;
            return true;
        }

        return false;
    }


    // User Login
    public function login($data)
    {

        // echo password_hash("yourpassword", PASSWORD_DEFAULT);

        $email = trim($data['email']);
        $password = $data['password'];

        // $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        // $stmt->execute([$email]);
        // $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // if ($user && password_verify($password, $user['password'])) {
        //     $_SESSION['user_id'] = $user['id'];
        //     $_SESSION['user_name'] = $user['name'];
        //     $_SESSION['user_email'] = $user['email'];
        //     $_SESSION['role'] = $user['role'];  // ✅ store role
        //     $_SESSION['user'] = $user;
        //     return true;
        // }
        // return false;

        // Check user by email
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if ($user['role'] === 'admin') {
                if ($user['role'] === 'admin') {
                    // Plain text check for admin
                    if ($password === $user['password']) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['name'];
                        $_SESSION['user_email'] = $user['email'];
                        $_SESSION['role'] = $user['role'];  // ✅ store role
                        $_SESSION['user'] = $user;
                        header("Location: ../../public/index.php");
                        exit;
                    } else {
                        $error = "Invalid admin password.";
                    }
                } else {
                    // User login (hashed password check)
                    if (password_verify($password, $user['password'])) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['name'];
                        $_SESSION['user_email'] = $user['email'];
                        $_SESSION['role'] = $user['role'];  // ✅ store role
                        $_SESSION['user'] = $user;
                        header("Location: ../../public/index.php");
                        exit;
                    } else {
                        $error = "Invalid user password.";
                    }
                }
            } else {
                // ✅ Normal user: hashed password check
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];  // ✅ store role
                    $_SESSION['user'] = $user;
                    header("Location: ../../public/index.php");
                    exit;
                } else {
                    $error = "Invalid password for user.";
                }
            }
        } else {
            $error = "No account found with this email.";
        }
    }

    // Logout
    public function logout()
    {
        session_destroy();
        header("Location: ../../public/index.php");
        exit;
    }

    // Check Login
    public function isLoggedIn()
    {
        return isset($_SESSION['user']);
    }
}
