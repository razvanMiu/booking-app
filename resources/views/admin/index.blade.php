@extends ('admin.layouts.master')
@include ('admin.layouts.style')

@section('content')
		<cards  :data="cardConfig" :current-building="currentBuilding" v-on:store-form="openAddForm" v-on:update-form="openUpdateForm" v-on:rooms-card="roomsCard" 
		>

		</cards>
		
		<forms :modal-state="modalState" :data="formConfig" :current-building="currentBuilding" :buildings="buildings" v-on:delete-confirmed="deleteConfirmed" v-on:reset-state="resetState"
		>

		</forms>

@endsection
