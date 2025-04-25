<?php
session_start();
require_once 'includes/config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reg = trim($_POST['email']);
    $reg=stripslashes($reg);
    $reg=htmlspecialchars($reg);
    $password = $_POST['password'];
    $password = trim($password);
    $password=stripslashes($password);
    $password=htmlspecialchars($password);
    if(empty($reg)){
        $error="Enter Registration Number";
    }
    if(empty($password)){
        $error="Enter Your Password";
    }
    if (!empty($reg) && isset($password)) {
        try {
            $stmt = $pdo->prepare("SELECT Registrationno as student_id,Name, Pass as password FROM students WHERE Registrationno = ?");
            $stmt->execute([$reg]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($student && $student['password'] == $password) {
                $_SESSION['student_id'] = $student['student_id'];
                header("Location: student_ratings.php");
                exit;
            } else {
                $error = "Invalid Registration Number or password.";
            }
        } catch (PDOException $e) {
            $error="Login error: " . $e->getMessage();
            
        }
    } 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .butn:hover {
            background: linear-gradient(to right, #7e22ce, #a855f7, #7e22ce);
        }
        .butn {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-gray-900 to-purple-900">

    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md">
        <h2 class="text-3xl font-bold text-center text-purple-700 mb-6">Student Login</h2>

        <?php if (!empty($error)): ?>
        <div class="bg-red-100 text-red-700 border border-red-300 p-3 rounded mb-4 text-sm">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label for="email" class="block text-gray-700 font-medium">Registration No</label>
                <input type="text" id="email" name="email"
                       class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-600">
            </div>

            <div>
                <label for="password" class="block text-gray-700 font-medium">Password</label>
                <input type="password" id="password" name="password"
                       class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-600">
            </div>

            <button type="submit"
                    class="w-full mt-4 bg-purple-600 text-white font-semibold py-2 px-4 rounded-md butn">
                Login
            </button>

            <div class="flex justify-end mt-2 text-sm">
                <a href="intro.html" class="text-purple-500 hover:underline">Home Page</a>
            </div>
        </form>
    </div>

</body>
</html>
