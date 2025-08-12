<!DOCTYPE html>
<!-- View/debug.php - Temporary debug page -->
<html>
<head>
    <title>Debug OTP Flow</title>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
    <h2>Debug OTP Flow</h2>
    
    <h3>Test Login with User ID 3:</h3>
    <button id="testLogin">Test Login</button>
    <div id="result"></div>
    
    <h3>Test OTP Verification:</h3>
    <input type="text" id="otpInput" placeholder="Enter OTP" maxlength="6">
    <button id="testOTP">Test OTP</button>
    
    <h3>Check Session OTP:</h3>
    <button id="checkSession">Check Session</button>
    
    <script>
        $('#testLogin').click(function() {
            $.ajax({
                url: '../Controller/authcontroller.php',
                method: 'POST',
                data: {
                    action: 'login',
                    username: 'your_test_username', // Replace with actual username
                    password: 'your_test_password'  // Replace with actual password
                },
                success: function(response) {
                    $('#result').html('<pre>' + JSON.stringify(response, null, 2) + '</pre>');
                    console.log('Response:', response);
                },
                error: function(xhr) {
                    $('#result').html('<pre>Error: ' + xhr.responseText + '</pre>');
                }
            });
        });
        
        $('#testOTP').click(function() {
            const otp = $('#otpInput').val();
            $.ajax({
                url: '../Controller/authcontroller.php',
                method: 'POST',
                data: {
                    action: 'verify_otp',
                    user_id: '3',
                    otp: otp
                },
                success: function(response) {
                    $('#result').html('<pre>OTP Result: ' + JSON.stringify(response, null, 2) + '</pre>');
                },
                error: function(xhr) {
                    $('#result').html('<pre>OTP Error: ' + xhr.responseText + '</pre>');
                }
            });
        });
        
        $('#checkSession').click(function() {
            $.ajax({
                url: 'check_session.php', // Create this simple PHP file
                success: function(response) {
                    $('#result').html('<pre>Session: ' + response + '</pre>');
                }
            });
        });
    </script>
</body>
</html>