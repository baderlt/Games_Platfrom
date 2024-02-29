<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="{{asset('index.css')}}">
    <style>

        body {
            color: white !important;
            background-color: #f4f4f4;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #131313;
        }

        .login-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 600px;
            background-color: #333;
            color: #ccc;
        }

        .login-container h2 {
            text-align: center;
            color: #333;
        }

        .login-form {
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            font-size: 14px;
            margin-bottom: 5px;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
        }

        .form-group button {
            background-color: #131313;
            color: #fff;
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 3px;
            margin-top: 10px;
            cursor: pointer;
            text-shadow: 1px 1px 1px #FF9999, 2px 2px 1px blue, 4px 4px 1px red;
        
        }

        .form-group button:hover {
            background-color: #990000 !important;
        }
        h2{
            color: white !important;
            text-shadow: 1px 1px 1px red;
        }
        .error{
            color: red;
            font-size: 13px;
        }
        /* .modal {
            display: none;
            position: relative;
            
        } */
    </style>
</head>
<body>


 
    <!-- <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Validation Error</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div> -->
    <div class="login-container">

        <h2 >Login</h2>
        <form class="login-form" method='POST' action="{{ route('admin.login') }}" >
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="name" name="name" value="administrateur1" placeholder="name " required>
                @error('name')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" value="bonjourunivers1!" name="password" placeholder="password " required>
                @error('password')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <button type="submit">Login</button>
            </div>
        </form>
    </div>
</body>
</html>
