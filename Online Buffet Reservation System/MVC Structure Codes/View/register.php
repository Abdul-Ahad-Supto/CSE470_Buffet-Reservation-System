<!DOCTYPE html>
<!-- View/register.php -->
<?php session_start(); ?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Register - Ristorante Con Fusion</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .registration-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .registration-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 500px;
            width: 100%;
        }
        .otp-input {
            text-align: center;
            font-size: 24px;
            letter-spacing: 10px;
        }
        .countdown-timer {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="registration-card">
                        <!-- Logo -->
                        <div class="text-center mb-4">
                            <img src="img/logo.png" height="60" alt="Logo">
                            <h3 class="mt-3">Create Your Account</h3>
                            <p class="text-muted">Join us for an amazing buffet experience</p>
                        </div>

                        <!-- Registration Form -->
                        <form id="registrationForm" action="../Controller/authcontroller.php" method="POST">
                            <input type="hidden" name="action" value="register">
                            
                            <div class="form-group">
                                <label for="username">Username</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                    </div>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-phone"></i></span>
                                    </div>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           pattern="[0-9]{11}" placeholder="01XXXXXXXXX" required>
                                </div>
                                <small class="form-text text-muted">We'll send you an OTP for verification</small>
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                    </div>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           minlength="6" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password">Confirm Password</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                    </div>
                                    <input type="password" class="form-control" id="confirm_password" 
                                           name="confirm_password" required>
                                </div>
                            </div>

                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#">Terms and Conditions</a>
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Register</button>
                        </form>

                        <hr class="my-4">

                        <p class="text-center">
                            Already have an account? <a href="login.php">Login here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- OTP Verification Modal -->
    <div class="modal fade" id="otpModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Verify Your Phone Number</h5>
                </div>
                <div class="modal-body">
                    <p class="text-center mb-4">
                        We've sent a 6-digit code to your phone number<br>
                        <strong id="phoneDisplay"></strong>
                    </p>
                    
                    <form id="otpForm">
                        <input type="hidden" id="user_id" name="user_id">
                        <div class="form-group">
                            <input type="text" class="form-control otp-input" id="otp" name="otp" 
                                   maxlength="6" pattern="[0-9]{6}" placeholder="000000" required>
                        </div>
                        
                        <div class="text-center mb-3">
                            <small class="text-muted">
                                Didn't receive the code? 
                                <a href="#" id="resendOTP" class="d-none">Resend OTP</a>
                                <span id="countdown" class="countdown-timer"></span>
                            </small>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-block">Verify OTP</button>
                    </form>
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

        // Password confirmation validation
        $('#confirm_password').on('keyup', function() {
            if ($('#password').val() !== $(this).val()) {
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
            }
        });

        // Registration form submission
        $('#registrationForm').submit(function(e) {
    e.preventDefault();
    
    if ($('#password').val() !== $('#confirm_password').val()) {
        alert('Passwords do not match!');
        return false;
    }

    // Show loading state
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.text();
    submitBtn.prop('disabled', true).text('Registering...');

    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            console.log('Registration response:', response); // Debug log
            
            if (response.success) {
                // Check if user_id exists
                if (response.user_id) {
                    $('#user_id').val(response.user_id);
                    $('#phoneDisplay').text($('#phone').val());
                    $('#otpModal').modal('show');
                    startCountdown(120); // 2 minutes countdown
                } else {
                    // Fallback if no user_id but success
                    alert('Registration successful! Please check your phone for OTP and then login.');
                    window.location.href = 'login.php';
                }
            } else {
                alert(response.message || 'Registration failed. Please try again.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Registration error:', xhr.responseText); // Debug log
            alert('An error occurred during registration. Please try again.');
        },
        complete: function() {
            // Reset button state
            submitBtn.prop('disabled', false).text(originalText);
        }
    });
});

        // OTP form submission
        $('#otpForm').submit(function(e) {
            e.preventDefault();
            
            $.ajax({
                url: '../Controller/authcontroller.php',
                method: 'POST',
                data: {
                    action: 'verify_otp',
                    user_id: $('#user_id').val(),
                    otp: $('#otp').val()
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Phone verified successfully! Redirecting to login...');
                        window.location.href = 'login.php';
                    } else {
                        alert(response.message);
                        $('#otp').val('').focus();
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        });

        // Resend OTP
        $('#resendOTP').click(function(e) {
            e.preventDefault();
            
            $.ajax({
                url: '../Controller/authcontroller.php',
                method: 'POST',
                data: {
                    action: 'resend_otp',
                    user_id: $('#user_id').val()
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('OTP sent successfully!');
                        startCountdown(120);
                    } else {
                        alert(response.message);
                    }
                }
            });
        });

        // Countdown timer
        function startCountdown(seconds) {
            $('#resendOTP').addClass('d-none');
            $('#countdown').removeClass('d-none');
            
            const interval = setInterval(function() {
                const minutes = Math.floor(seconds / 60);
                const secs = seconds % 60;
                $('#countdown').text(`(${minutes}:${secs.toString().padStart(2, '0')})`);
                
                if (seconds <= 0) {
                    clearInterval(interval);
                    $('#countdown').addClass('d-none');
                    $('#resendOTP').removeClass('d-none');
                }
                seconds--;
            }, 1000);
        }
    </script>
</body>
</html>