<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
		<title>BSB Booking</title>
		
		@yield ('style')
	</head>
	<body>
		
		<div id="root">
			<v-app id="inspire" v-cloak>
				@yield ('content')		
			</v-app>
		</div>
		
		@yield ('script')
		
	</body>
</html>