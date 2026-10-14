<!-- <!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <h1>Reset Your Password</h1>
    <p>Click the link below to reset your password:</p>
    <a href="{{ url('/reset-password?token=' . $token) }}">Reset Password</a>
</body>
</html> -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('admin/assets/img/cliniclogohalf.png')}}">
    <title>Reset Password</title>

    <link rel="stylesheet" type="text/css" href="{{asset('admin/assets/css/bootstrap.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/assets/css/style.css')}}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: "Montserrat", sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f6f5;
        }

        .login-container {
            display: flex;
            width: 900px;
            height: 500px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

      

        .login-form {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px;
        }

        .account-logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: rgb(159 217 193);
            border-color: rgb(159 217 193);
            transition: all 0.3s;
            color: black;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: rgb(139 197 173);
        }

        .form-group label {
            font-weight: bold;
        }

        .form-control {
            border-radius: 50px;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                width: 90%;
                height: auto;
            }

            .login-image {
                height: 250px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">

        <div class="login-form">
            <h1>Reset Your Password</h1>
            <p>Click the link below to reset your password:</p>
            <a href="{{ url('/reset-password?token=' . $token) }}">Reset Password</a>
        </div>
    </div>
    <script src="{{asset('admin/assets/js/jquery-3.2.1.min.js')}}"></script>
    <script src="{{asset('admin/assets/js/popper.min.js')}}"></script>
    <script src="{{asset('admin/assets/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('admin/assets/js/app.js')}}"></script>
</body>



</html>