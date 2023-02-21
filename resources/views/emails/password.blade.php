<!DOCTYPE html>
<html lang="pl">

<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>@lang('Welcome') {{ $user->name }}</title>

	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;500;600;700;800&display=swap" rel="stylesheet">

	<style>
		.email-body,
		.email-bg {
			margin: 0px;
			padding: 0px;
			background-color: rgb(243, 246, 248);
			font-size: 16px;
			font-family: 'Baloo 2', cursive, 'Open Sans', Helvetica, Arial,
				monospace, sans-serif;
			box-sizing: border-box;
			height: auto;
		}

		.email-bg {
			margin: 50px auto;
			width: 90%;
			max-width: 600px;
		}

		.email-box {
			position: relative;
			float: left;
			width: 100%;
			min-height: 200px;
			margin-bottom: 100px;
			background-color: #fff;
			box-shadow: 1px 5px 20px rgb(104, 150, 181, 0.05);
		}

		.email-box-top {
			padding: 50px 0px;
			overflow: hidden;
			text-align: center;
			background: #ffc900;
		}

		.email-box-mid {
			padding: 30px 0px;
			text-align: center;
		}

		.email-box-bot {
			overflow: hidden;
			background: #fefefe;
		}

		.email-text {
			padding: 10px 25px;
			text-align: center;
			font-size: 17px;
			font-weight: 400;
		}

		.email-regards {
			padding: 30px;
			text-align: center;
			font-size: 20px;
			font-weight: 900;
			background-color: #efefef;
		}

		.email-button,
		password {
			padding: 15px 25px;
			margin: 20px auto;
			min-width: 200px;
			color: #ffc900 !important;
			background: #000 !important;
			display: inline-block;
			text-decoration: none;
			text-align: center;
			font-weight: 900;
			box-shadow: 0px 5px 10px rgb(0, 0, 0, 0.1);
			transition: all .6s ease-in-out;
		}

		.email-button:hover {
			color: #000 !important;
			background: #ffcc00 !important;
			box-shadow: 0px 5px 20px #ffcc0088 !important;
		}

		.email-logo {
			margin: 10px auto;
		}
	</style>
</head>

<body class="email-body">
	<div class="email-bg">
		<div class="email-box">
			<div class="email-box-top">
				@if (file_exists(public_path() . '/vendor/webi/logo/logo.png'))
				<img class="email-logo" src="{{ $message->embed(public_path() . '/vendor/webi/logo/logo.png') }}" />
				@endif
				<h1>@lang(config('webi.email.message.welcome', 'Welcome'))!</h1>
				<h3>{{ $user->name }}</h3>
			</div>

			<div class="email-box-mid">
				<p class="email-text">
					@lang(config('webi.email.message.reset_password', 'This is your new password.'))
				</p>
				<password>{{ $password }}</password>
			</div>

			<div class="email-box-bot">
				<div class="email-regards">@lang(config('webi.email.message.regards', 'Have a nice day!'))</div>
			</div>
		</div>
	</div>
</body>

</html>