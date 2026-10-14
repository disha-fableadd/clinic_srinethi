<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon"
        href="{{asset(env('IMAGE_PATH') . 'admin/assets/img/cliniclogohalf.png')}}">
    <title>Fablead HMS</title>

    <link rel="stylesheet" type="text/css" href="{{asset(env('IMAGE_PATH') . 'admin/assets/css/bootstrap.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset(env('IMAGE_PATH') . 'admin/assets/css/style.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
    #loginLoader {
        margin-right: 6px;
    }



    footer {
        margin-top: auto;
    }

    body {
        font-family: "Montserrat", sans-serif;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f6f5;


        display: flex;
        flex-direction: column;
        min-height: 100vh;
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

    .login-image {
        flex: 1;
        background: url('{{asset(env('IMAGE_PATH') . "admin/assets/img/dr.avif")}}') no-repeat center center/cover;
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

        .login-form {
            padding: 25px 15px 20px 15px;
        }
    }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-image">

        </div>
        <div class="login-form">
            <form action="" method="POST" id="login-form">
                @csrf
                <div class="account-logo">

                    <!-- <img src="{{ asset(Auth::user()->creator->clinic_logo ?? 'admin/assets/img/cliniclogo.png') }}"
                            class="regular-logo" alt="{{ Auth::user()->creator->clinic_name ?? 'Clinic' }}"
                            style="height:auto;width:50px;height:50px">
                            <span style="font-size:20px;color:black">{{ Auth::user()->creator->clinic_name ?? 'Clinic' }}</span> -->

                    @php
                    use App\Models\Setting;

                    $clinicLogo = Setting::where('key', 'clinic_logo')->value('value') ?? env('IMAGE_PATH') .
                    'admin/assets/img/cliniclogo.png';
                    $clinicName = Setting::where('key', 'clinic_name')->value('value') ?? 'Clinic';
                    @endphp

                    <img src="{{ asset(env('IMAGE_PATH') . $clinicLogo) }}" class="regular-logo" alt="{{ $clinicName }}"
                        style="height:auto !important;width:200px;"> 


                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" id="email" >
                    @error('email')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
               <div class="form-group position-relative">
    <label>Password</label>

    <input type="password" name="password" class="form-control" id="password" >

    <i class="fa fa-eye" id="togglePassword"
        style="position:absolute; right:20px; top:42px; cursor:pointer; color:#777;"></i>

    @error('password')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
                <div class="form-group text-center">
                    <a href="{{ url('/forgot-password') }}" class="text-muted">Forgot Password?</a>
                </div>



                <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary "
                        style="min-height:40px; background-color: rgb(159, 217, 193); color: #000; border: none; "
                        id="loginButton">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"
                            id="loginLoader"></span>
                        <span id="loginText">Login</span>
                    </button>
                </div>

                <div id="loginerrorMessage" class="alert alert-danger" style="display:none;"></div>
                <div id="loginsuccessMessage" class="alert alert-success" style="display:none;"></div>
            </form>
        </div>

    </div>
    <script src="{{asset(env('IMAGE_PATH') . 'admin/assets/js/jquery-3.2.1.min.js')}}"></script>
    <script src="{{asset(env('IMAGE_PATH') . 'admin/assets/js/popper.min.js')}}"></script>
    <script src="{{asset(env('IMAGE_PATH') . 'admin/assets/js/bootstrap.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src="{{asset(env('IMAGE_PATH') . 'admin/assets/js/app.js')}}"></script>
    <style>
        label.error {
            color: #e63c3c;
            font-size: 13px;
            margin-top: 5px;
            display: block;
        }
        .form-control.error {
            border-color: #e63c3c;
        }

        @media (max-width: 767px) {
            .regular-logo {
                max-width: 110px !important;
                max-height: 40px !important;
                width: auto !important;
            }
        }
    </style>
</body>

<script>
$(document).ready(function() {

    $('#login-form').validate({
        rules: {
            email: {
                required: true,
                email: true
            },
            password: {
                required: true
            }
        },
        messages: {
            email: {
                required: "Please enter your email address.",
                email: "Please enter a valid email address."
            },
            password: {
                required: "Please enter your password."
            }
        },
        submitHandler: function(form) {
            $('#loginButton').attr('disabled', true);
            $('#loginLoader').removeClass('d-none');

            var email = $('#email').val().trim();
            var password = $('#password').val().trim();

            $.ajax({
                url: "{{ url('api/loginweb') }}",
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify({
                    email: email,
                    password: password
                }),
                success: function(response) {
                    $('#loginerrorMessage').hide();
                    $('#loginsuccessMessage').text(response.message || 'Login successfully').show();
                    setTimeout(function() {
                        window.location.href = "/dashboard";
                    }, 1000);
                },
                error: function(xhr) {
                    var response = xhr.responseJSON;
                    $('#loginsuccessMessage').hide();

                    if (response && response.lock_time) {
                        $('#loginerrorMessage').text(response.message).show();
                        $('#loginButton').attr('disabled', true);
                        startTimer(response.lock_time);
                    } else {
                        $('#loginerrorMessage').text(response ? response.message : 'An error occurred').show();
                        $('#loginButton').attr('disabled', false);
                    }
                    $('#loginLoader').addClass('d-none');
                }
            });
        }
    });
});


function startTimer(seconds) {

    var timer = seconds;

    var interval = setInterval(function() {

        var minutes = Math.floor(timer / 60);
        var secs = timer % 60;

        $('#loginerrorMessage').text(
            "Too many attempts. Try again in " + minutes + ":" + (secs < 10 ? "0" : "") + secs
        );

        timer--;

        if (timer < 0) {

            clearInterval(interval);

            $('#loginButton').attr('disabled', false);

            $('#loginerrorMessage').text("You can login again now.");

        }

    }, 1000);

}
$('#togglePassword').click(function () {

    const password = $('#password');

    if (password.attr('type') === 'password') {
        password.attr('type', 'text');
        $(this).removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        password.attr('type', 'password');
        $(this).removeClass('fa-eye-slash').addClass('fa-eye');
    }

});
</script>
<!-- Copyright -->
<footer class="text-center my-3">
    <p>
        Copyright &copy; <span id="year"></span> -
        <a href="https://fableadtechnolabs.com/" target="_blank" style="text-decoration: none; color: black;">
            <b>Fablead Developers Technolab</b>
        </a>
    </p>
</footer>

<script>
    document.getElementById("year").textContent = new Date().getFullYear();
</script>


</html>