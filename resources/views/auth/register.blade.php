@php

    $categoryprojectbaru= json_decode($category, true)[0];
    $categoryproject = trim($categoryprojectbaru, '"'); // Remove the extra double quotes
    $listpic = json_decode($categoryproject, true);

@endphp

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
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 400px;
            margin: 100px auto;
            background-color: #fff;
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
        <form method="POST" action="{{ route('registerklik') }}">
                                @csrf
                                <div class="form-group">
                                    <input type="text" name="name" class="form-control" placeholder="Name" required autofocus>
                                </div>
                                <div class="form-group">
                                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                                </div>
                                <div class="form-group">
                                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                                </div>
                                <div class="form-group">
                                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                                </div>
                                <div class="form-group">
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
                                </div>
                                <div class="form-group">
                                    <select name="rule" class="form-control" required>
                                        @foreach($listpic as $pic)
                                            <option value="{{ $pic }}">{{ $pic }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg btn-block">Register</button>
                            </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

