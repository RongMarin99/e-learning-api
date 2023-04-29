<!DOCTYPE html>
<html>
<head>
    <title>AllPHPTricks.com</title>
    <style>
        .confirm{
            background-color: rgb(0, 168, 0);
            color: white;
            padding: 13px;
            border: none;
        }
        body{
            height: 100vh;
            width: 100%;
            padding: 100px;
        }
    </style>
</head>
<body>
    <div style="margin-left:150px">
        <h1>Hello, {{ $username }}</h1>
        <p>
            <b>
                You are on your way!
            </b>
        </p>
        <p>
            <b>
                Let reset your password.
            </b>
        </p>
        <br><br>
        <p>By clicking on the following link, you are reset your password.</p>
    </div>
    <center>
       <a href="{{ route('user.reset', $user_id) }}">
            <button class="confirm">Reset New Password</button>
        </a> 
    </center>
    <div style="margin-left:150px">
        <p><b>Regards,</b></p>
        <p><b>The RDev Team</b></p>
    </div>
</body>
</html>