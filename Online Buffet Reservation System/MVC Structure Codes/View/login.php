<?php
// View/login.php
session_start();
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    header('Location: indexv2.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - Ristorante Con Fusion</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 400px;
            width: 100%;
        }
        .social-login {
            margin-top: 20px;
        }
        .social-btn {
            width: 100%;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div class="login-card">
                        <!-- Logo -->
                        <div class="text-center mb-4">
                            <img src="img/logo.png" height="60" alt="Logo">
                            <h3 class="mt-3">Welcome Back!</h3>
                            <p class="text-muted">Login to make your reservation</p>
                        </div>

                        <!-- Alert Messages -->
                        <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <?php 
                                echo htmlspecialchars($_SESSION['message']); 
                                unset($_SESSION['message']);
                            ?>
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                        <?php endif; ?>

                        <!-- Login Form -->
                        <form id="loginForm" action="../Controller/authcontroller.php" method="POST">
                            <input type="hidden" name="action" value="login">
                            
                            <div class="form-group">
                                <label for="username">Username or Email</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                    </div>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                                    <label class="custom-control-label" for="remember">Remember me</label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="forgot-password.php">Forgot Password?</a>
                        </div>

                        <hr class="my-4">

                        <p class="text-center">
                            Don't have an account? <a href="register.php">Register here</a>
                        </p>

                        <div class="text-center">
                            <a href="indexv2.php" class="text-muted">
                                <i class="fa fa-arrow-left"></i> Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        // Toggle password visibility
        $('#togglePassword').click(function() {
            const passwordField = $('#password');
            const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
            passwordField.attr('type', type);
            $(this).find('i').toggleClass('fa-eye fa-eye-slash');
        });

        // Login form submission
        $('#loginForm').submit(function(e) {
            e.preventDefault();
            
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        window.location.href = 'indexv2.php';
                    } else {
                        if (response.user_id) {
                            // Phone not verified
                            if (confirm(response.message + '\nWould you like to verify now?')) {
                                window.location.href = 'verify-phone.php?user_id=' + response.user_id;
                            }
                        } else {
                            alert(response.message);
                        }
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        });
    </script>
</body>
</html>
