<!DOCTYPE html>
<!-- View/verify-phone.php -->
<?php 
session_start(); 
$user_id = $_GET['user_id'] ?? '';
$phone = $_GET['phone'] ?? '';

if (empty($user_id)) {
    header('Location: login.php');
    exit();
}
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Verify Phone - Ristorante Con Fusion</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .verification-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .verification-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 400px;
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
    <div class="verification-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div class="verification-card">
                        <!-- Logo -->
                        <div class="text-center mb-4">
                            <img src="img/logo.png" height="60" alt="Logo">
                            <h3 class="mt-3">Verify Your Phone</h3>
                            <p class="text-muted">We've sent a 6-digit code to<br><strong><?php echo htmlspecialchars($phone); ?></strong></p>
                        </div>

                        <!-- Alert Messages -->
                        <div id="alertContainer"></div>

                        <!-- OTP Form -->
                        <form id="otpForm">
                            <input type="hidden" id="user_id" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                            
                            <div class="form-group">
                                <label for="otp">Enter 6-Digit Code</label>
                                <input type="text" class="form-control otp-input" id="otp" name="otp" 
                                       maxlength="6" pattern="[0-9]{6}" placeholder="000000" required>
                            </div>

                            <button type="submit" class="btn btn-success btn-block">Verify Phone</button>
                        </form>

                        <div class="text-center mt-3">
                            <small class="text-muted">
                                Didn't receive the code? 
                                <a href="#" id="resendOTP" class="d-none">Resend OTP</a>
                                <span id="countdown" class="countdown-timer"></span>
                            </small>
                        </div>

                        <hr class="my-4">

                        <div class="text-center">
                            <a href="login.php" class="text-muted">
                                <i class="fa fa-arrow-left"></i> Back to Login
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
        // Start countdown immediately
        $(document).ready(function() {
            startCountdown(120); // 2 minutes
        });

        // OTP form submission
        $('#otpForm').submit(function(e) {
            e.preventDefault();
            
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.text();
            submitBtn.prop('disabled', true).text('Verifying...');
            
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
                        showAlert('Phone verified successfully! Redirecting to login...', 'success');
                        setTimeout(function() {
                            window.location.href = 'login.php';
                        }, 2000);
                    } else {
                        showAlert(response.message, 'danger');
                        $('#otp').val('').focus();
                    }
                },
                error: function() {
                    showAlert('An error occurred. Please try again.', 'danger');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).text(originalText);
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
                        showAlert('OTP sent successfully!', 'success');
                        startCountdown(120);
                    } else {
                        showAlert(response.message, 'danger');
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

        // Show alert function
        function showAlert(message, type) {
            const alert = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            `;
            $('#alertContainer').html(alert);
        }

        // Auto-focus OTP input and handle paste
        $('#otp').focus();
        
        $('#otp').on('paste', function(e) {
            setTimeout(function() {
                const pasted = $('#otp').val();
                if (pasted.length === 6 && /^\d+$/.test(pasted)) {
                    $('#otpForm').submit();
                }
            }, 100);
        });
    </script>
</body>
</html>