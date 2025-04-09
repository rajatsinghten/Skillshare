<?php
session_start();
require_once('db.php');

// Handle Registration
if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Check if email already exists
    $check_query = "SELECT id FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if (mysqli_stmt_num_rows($stmt) > 0) {
        $_SESSION['error'] = "Email already exists!";
        header("Location: ../pages/register.php");
        exit();
    }
    
    // Insert new user
    $query = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sss", $name, $email, $password);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Registration successful! Please login.";
        header("Location: ../pages/login.php");
        exit();
    } else {
        $_SESSION['error'] = "Registration failed. Please try again.";
        header("Location: ../pages/register.php");
        exit();
    }
}

// Handle Login
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    $query = "SELECT id, name, password FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            header("Location: ../pages/index.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid password!";
            header("Location: ../pages/login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Email not found!";
        header("Location: ../pages/login.php");
        exit();
    }
}

// Only redirect to login if not logged in and not trying to login/register
if (!isset($_SESSION['user_id']) && !isset($_POST['login']) && !isset($_POST['register'])) {
    header("Location: ../pages/login.php");
    exit();
}
?>
