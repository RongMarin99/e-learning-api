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
        <h1>Hello, {{ $name }}</h1>
        <p>
            <b>
                You are on your way!
            </b>
        </p>
        <p>
            <b>
                Let confirm your e-mail address.
            </b>
        </p>
        <br><br>
        <p>By clicking on the following link, you are confirming your email address.</p>
    </div>
    <center>
       <a href="{{ route('user.verify', $user_id) }}">
            <button class="confirm">Confirm Email Address</button>
        </a> 
    </center>
    <div style="margin-left:150px">
        <p><b>Regards,</b></p>
        <p><b>The RDev Team</b></p>
    </div>
</body>
</html>