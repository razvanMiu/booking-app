<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<title>BSB Booking</title>

		@include ('admin.layouts.style')
		
		@yield ('style')
	</head>
	<body>

		<div id="app" v-cloak>
			<v-app id="inspire">
					@include ('admin.layouts.nav')
					<v-content>
						<v-container fluid>
							@yield ('content')
						</v-container>
					</v-content>
			</v-app>
		</div>

		@include ('admin.layouts.footer')
		
	</body>
</html>
