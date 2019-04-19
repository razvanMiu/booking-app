@extends ('layouts.master')

@section ('style')

	@include ('layouts.style')

@endsection

@section ('content')

	<div class="container">
		@include ('layouts.nav')

		<v-content>
    		<v-container fluid>
    			<my-bookings 
    				:user="user" 
    				v-on:change-status="changeStatus"
    				v-on:edit-booking="updateEvent"
    				v-on:delete-booking="deleteEvent">
    					
    			</my-bookings>
    		</v-container>
    	</v-content>
		

	</div>

@endsection

@section ('script')
	
	@include ('layouts.footer')

@endsection
