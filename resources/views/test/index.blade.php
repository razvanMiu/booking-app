@extends ('layouts.master')

@section ('style')

	@include ('layouts.style')

@endsection


@section ('content')
	
	<v-form ref="form" v-model="valid" lazy-validation style="width: 50%; margin:0 auto; margin-top: 200px">
		<v-text-field 
			v-model="test" 
			label="Title" 
			:rules="rules"
			required>
			
		</v-text-field>
	</v-form>

@endsection


@section ('script')
	
	@include ('layouts.footer')

@endsection