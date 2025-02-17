<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/INKAICON.png') }}">
    <!-- Sweetalert2 (include theme bootstrap) -->
    <link rel="stylesheet" type="text/css" href="{{ asset('adminlte3/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.css') }}">

    <style>
        body {
            background-image: url('{{ asset('images/trainpicture2.jpeg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        .container {
            max-width: 400px;
            margin: 100px auto;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
        }
        .logo img {
            max-width: 150px;
        }
        .lead {
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }
        .fancy-checkbox input[type="checkbox"] {
            display: none;
        }
        .fancy-checkbox input[type="checkbox"] + span:before {
            content: "\f0c8";
            font-family: FontAwesome;
            display: inline-block;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo text-center">
            <img src="{{ asset('images/INKAICON.png') }}" alt="Klorofil Logo">
        </div>
        <p class="lead">Login to your account</p>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email or Username:</label>
                <input type="text" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" class="form-control" id="password" required>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="showPassword">
                <label class="form-check-label" for="showPassword">Show Password</label>
            </div>
            <button type="submit" class="btn btn-primary btn-lg btn-block">LOGIN</button>
            <div class="bottom">
                <span class="helper-text"><i class="fa fa-lock"></i> <a href="https://drive.google.com/drive/folders/1upK-LtnY5IMinWSpvQZG7Iiv7NYV9XBi?usp=sharing">User Manual Login/Register/Forgot password</a></span>
            </div>
            <!-- <div class="bottom">
                <span class="helper-text"><i class="fa fa-lock"></i> <a href="{{ url('register') }}">Register</a></span>
            </div> -->
            <div class="bottom">
                <span class="helper-text"><i class="fa fa-lock"></i> <a href="">Register? WA 081515814752</a></span>
            </div>
            <div class="bottom">
                <span class="helper-text"><i class="fa fa-lock"></i> 
                    <a href="https://wa.me/6281515814752?text=Saya%20ingin%20mengganti%20kata%20sandi" target="_blank">
                        Hubungi WhatsApp untuk Reset Kata Sandi
                    </a>
                </span>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#showPassword').on('change', function() {
                var passwordField = $('#password');
                var passwordFieldType = passwordField.attr('type');
                if (passwordFieldType === 'password') {
                    passwordField.attr('type', 'text');
                } else {
                    passwordField.attr('type', 'password');
                }
            });
        });
    </script>
</body>
</html>
