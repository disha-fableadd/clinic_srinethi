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
        #emailLoader {
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

.account-logo img {
    max-height: 100px !important;
    width: auto;
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
            background: url('{{ asset(env('IMAGE_PATH') . 'admin/assets/img/dr.avif') }}') no-repeat center center/cover;
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

        <div class="login-image">



        </div>

        <div class="login-form">



            <form action="" method="POST" id="forgot-password-form">

                @csrf

                <div class="account-logo">


                    @php
                        use App\Models\Setting;

                        $clinicLogo = Setting::where('key', 'clinic_logo')->value('value') ?? 'admin/assets/img/cliniclogo.png';
                        $clinicName = Setting::where('key', 'clinic_name')->value('value') ?? 'Clinic';
                    @endphp

                   <img src="{{ asset(env('IMAGE_PATH') . $clinicLogo) }}" class="regular-logo"
                        alt="{{ $clinicName }}" >
                    



                </div>

                <div class="form-group">

                    <label>Email</label>

                    <input type="email" name="email" class="form-control" id="forgot-email" required>

                    @error('email')

                        <div class="text-danger">{{ $message }}</div>

                    @enderror

                </div>







                <!-- <div class="form-group text-center text-danger" id="error-message" style="display:none;">user not found</div> -->

                <div id="errorMessage" class="alert alert-danger" style="display:none;"></div>

                <!-- <div class="form-group text-center">

                    <button type="submit" class="btn btn-primary">Send Email</button>

                </div> -->

                <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary"
                        style="min-height:40px; background-color: rgb(159, 217, 193); color: #000; border: none; "
                        id="sendEmailButton">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"
                            id="emailLoader"></span>
                        <span id="sendEmailText">Send Email</span>
                    </button>
                </div>


                <div class="form-group text-center">

                    <a href="{{ url('/') }}" class="text-muted">Go To Login Page?</a>

                </div>

















                <div id="forgot-message" style="display:none;" class="alert alert-success"></div>



            </form>

        </div>





    </div>

    <script src="{{asset(env('IMAGE_PATH') . 'admin/assets/js/jquery-3.2.1.min.js')}}"></script>

    <script src="{{asset(env('IMAGE_PATH') . 'admin/assets/js/popper.min.js')}}"></script>

    <script src="{{asset(env('IMAGE_PATH') . 'admin/assets/js/bootstrap.min.js')}}"></script>

    <script src="{{asset(env('IMAGE_PATH') . 'admin/assets/js/app.js')}}"></script>

</body>

<script>
    $('#forgot-password-form').on('submit', function (e) {
        e.preventDefault();

        // Show loader and disable button
        $('#sendEmailButton').attr('disabled', true);
        $('#emailLoader').removeClass('d-none');
        // $('#sendEmailText').text('Please wait...');

        var email = $('#forgot-email').val().trim();

        $.ajax({
            url: "{{ url('api/forgot-password') }}",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify({ email: email }),

            success: function (response) {
                // Show success message
                $('#forgot-message').text(response.message).show();

                // Reset loader and button state
                $('#sendEmailButton').attr('disabled', false);
                $('#emailLoader').addClass('d-none');
                $('#sendEmailText').text('Send Email');

                // Hide message after delay
                setTimeout(function () {
                    $('#forgot-message').hide();
                }, 1500);
            },

            error: function (xhr) {
                // Handle error message
                let msg = 'Something went wrong';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                $('#forgot-message').text(msg).show();

                // Reset loader and button state
                $('#sendEmailButton').attr('disabled', false);
                $('#emailLoader').addClass('d-none');
                $('#sendEmailText').text('Send Email');

                setTimeout(function () {
                    $('#forgot-message').hide();
                }, 2000);
            }
        });
    });
</script>




</html>