<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }
    </style>
</head>
<body>
<div style="margin: auto;background: #3B9A67;">
    <h2 style="display: inline-block;color: white;margin-left: 25px;">DHClub</h2>
    <img style="width: 55px;margin-top: 5px;margin-right: 40px;float: right;" src="{{url('/images/logo/dh_logo.png')}}"/>
</div>
<div style="clear: both;"></div>
<table style="margin-top: 20px;margin-left: 25px;font-weight: 900;font-size: 18px;">
    <thead></thead>
    <tbody>
    <tr>
        <td>Name:</td>
        <td>{{@$user->name}}</td>
    </tr>
    <tr>
        <td>Phone:</td>
        <td>{{@$user->phone}}</td>
    </tr>
    <tr>
        <td>Email:</td>
        <td>{{@$user->email}}</td>
    </tr>
    </tbody>
</table>
</body>
</html>
