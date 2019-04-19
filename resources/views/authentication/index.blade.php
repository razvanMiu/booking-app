@extends ('layouts.master')

@section ('style')
	
	@include ('layouts.style')

@endsection

@section ('content')
	<div id="authContainer">
		<login-form v-if="loginActive" @close="toggleAuthForm">
		</login-form>

		<register-form v-if="registerActive" @close="toggleAuthForm">
		</register-form>
	</div>
@endsection

@section ('script')
	<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
	<script src="/js/auth.js"></script>
@endsection
	