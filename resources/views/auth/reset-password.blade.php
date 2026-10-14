<!DOCTYPE html>

<html lang="en">



<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">

    <link rel="shortcut icon" type="image/x-icon"
        href="{{asset(env('IMAGE_PATH') . 'admin/assets/img/cliniclogohalf.png')}}">

     <title>Fablead-HMS</title>



    <link rel="stylesheet" type="text/css" href="{{asset(env('IMAGE_PATH') . 'admin/assets/css/bootstrap.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset(env('IMAGE_PATH') . 'admin/assets/css/style.css')}}">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        #resetLoader {
            margin-right: 6px;
        }

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



        .login-image {

            flex: 1;

            background: url('{{asset("admin/assets/img/dr.avif")}}') no-repeat center center/cover;

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

        <div class="login-image"></div>

        <div class="login-form">

            <form id="reset-password-form">

                @csrf

                <div class="account-logo">


                    @php
                        use App\Models\Setting;

                        $clinicLogo = Setting::where('key', 'clinic_logo')->value('value') ?? env('IMAGE_PATH') . 'admin/assets/img/cliniclogo.png';
                        $clinicName = Setting::where('key', 'clinic_name')->value('value') ?? 'Clinic';
                    @endphp

                    <img src="{{ asset($clinicLogo) }}" class="regular-logo" alt="{{ $clinicName }}"
                        style="height:auto;width:50px;height:50px">
                    <span style="font-size:20px;color:black">{{ $clinicName }}</span>

                </div>



                <!-- Bootstrap Alert for Error Messages -->

                <div class="alert alert-danger d-none" id="error-alert"></div>

                <div class="alert alert-success d-none" id="success-alert"></div>



                <input type="hidden" id="reset-token" value="{{ request()->get('token') }}">



                <div class="form-group">

                    <label>New Password</label>

                    <input type="password" name="password" class="form-control" id="new-password">

                </div>



                <div class="form-group">

                    <label>Confirm Password</label>

                    <input type="password" class="form-control" name="password_confirmation" id="confirm-password">

                </div>



                <!-- <button type="submit" class="btn btn-primary">Reset Password</button> -->


                <button type="submit"
                    style="min-height:40px; background-color: rgb(159, 217, 193); color: #000; border: none; "
                    class="btn btn-primary" id="resetPasswordButton">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"
                        id="resetLoader"></span>
                    <span id="resetText">Reset Password</span>
                </button>


            </form>

        </div>

    </div>



    <script src="{{asset(env('IMAGE_PATH') . 'admin/assets/js/jquery-3.2.1.min.js')}}"></script>

    <script src="{{asset(env('IMAGE_PATH') . 'admin/assets/js/popper.min.js')}}"></script>

    <script src="{{asset(env('IMAGE_PATH') . 'admin/assets/js/bootstrap.min.js')}}"></script>

    <script src="{{asset(env('IMAGE_PATH') . 'admin/assets/js/app.js')}}"></script>

</body>



<!-- <script>





    $('#reset-password-form').on('submit', function (e) {

        e.preventDefault();



        const password = $('#new-password').val();

        const passwordConfirmation = $('#confirm-password').val();

        const resetToken = $('#reset-token').val();



        $.ajax({

            url: "{{ url('api/reset-password') }}",

            type: "POST",

            data: {

                token: resetToken,

                password: password,

                password_confirmation: passwordConfirmation

            },

            success: function (response) {

                $('#error-alert').addClass('d-none');

                $('#success-alert').removeClass('d-none').text(response.message);

            },

            error: function (xhr) {

                const errors = xhr.responseJSON;



                if (errors.errors) {

                    let errorMessages = '';

                    $.each(errors.errors, function (key, value) {

                        errorMessages += `<li>${value[0]}</li>`;

                    });

                    $('#error-alert').removeClass('d-none').html(`<ul>${errorMessages}</ul>`);

                } else {

                    $('#error-alert').removeClass('d-none').text(errors.message || 'An error occurred');

                }

            }

        });

    });



</script> -->



<script>
    $('#reset-password-form').on('submit', function () {
        $('#resetPasswordButton').attr('disabled', true);
        $('#resetLoader').removeClass('d-none');
        // $('#resetText').text('Please wait...');
    });
</script>

<script>
    $('#reset-password-form').on('submit', function (e) {
        e.preventDefault();

        // Show loader
        $('#resetPasswordButton').attr('disabled', true);
        $('#resetLoader').removeClass('d-none');

        const password = $('#new-password').val();
        const passwordConfirmation = $('#confirm-password').val();
        const resetToken = $('#reset-token').val();

        $.ajax({
            url: "{{ url('api/reset-password') }}",
            type: "POST",
            data: {
                token: resetToken,
                password: password,
                password_confirmation: passwordConfirmation
            },
            success: function (response) {
                $('#error-alert').addClass('d-none');
                $('#success-alert').removeClass('d-none').text(response.message);

                // Redirect to login page after a short delay
                setTimeout(function () {
                    window.location.href = "{{ url('/') }}";
                }, 1000);
            },
            error: function (xhr) {
                const errors = xhr.responseJSON;

                if (errors.errors) {
                    let errorMessages = '';
                    $.each(errors.errors, function (key, value) {
                        errorMessages += `<li>${value[0]}</li>`;
                    });
                    $('#error-alert').removeClass('d-none').html(`<ul>${errorMessages}</ul>`);
                } else {
                    $('#error-alert').removeClass('d-none').text(errors.message || 'An error occurred');
                }
            },
            complete: function () {
                // Always remove loader and re-enable button
                $('#resetPasswordButton').attr('disabled', false);
                $('#resetLoader').addClass('d-none');
            }
        });
    });
</script>


</html>