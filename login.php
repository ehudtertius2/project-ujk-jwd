<?php
session_start();

// Data dummy user
$users = [
    'admin' => [
        'password' => 'admin123',
        'role' => 'admin',
        'name' => 'Administrator'
    ],
    'user' => [
        'password' => 'user123',
        'role' => 'user',
        'name' => 'Pengunjung'
    ]
];

// Proses login
if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (isset($users[$username]) && $users[$username]['password'] === $password) {
        $_SESSION['login'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $users[$username]['role'];
        $_SESSION['name'] = $users[$username]['name'];
        
        header('Location: toko.php');
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Toko Rajut</title>
    <link href="images/pngwing.com.png" type="image/png" rel="icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #462764 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;    
        }
        .login-card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(1, 247, 21, 0.62);
            overflow: hidden;
        }
        .login-header {
            background: #2d3436;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .login-body {
            padding: 30px;
            background: white;
        }
        .demo-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="login-card">
                    <div class="login-header">
                        <i class="fas fa-tshirt fa-3x mb-2"></i>
                        <h3 class="mb-0">Toko Rajut</h3>
                        <p class="mb-0 small">Silakan login untuk melanjutkan</p>
                    </div>
                    <div class="login-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user me-1"></i>Username
                                </label>
                                <input type="text" name="username" class="form-control" 
                                       placeholder="Masukkan username" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-lock me-1"></i>Password
                                </label>
                                <input type="password" name="password" class="form-control" 
                                       placeholder="Masukkan password" required>
                            </div>
                            <button type="submit" name="login" class="btn btn-dark w-100">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>
                        </form>
                        
                        <hr>
                        <div class="demo-info">
                            <i class="fas fa-info-circle text-primary me-1"></i>
                            <strong>Data Demo:</strong>
                            <div class="mt-1">
                                <span class="badge bg-success me-1">Admin</span> 
                                username: <code>admin</code> | pass: <code>admin123</code>
                                <br>
                                <span class="badge bg-info me-1">User</span> 
                                username: <code>user</code> | pass: <code>user123</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>