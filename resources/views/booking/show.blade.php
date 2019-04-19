@extends ('layouts.master')

@section ('style')

	@include ('layouts.style')

@endsection

@section ('content')
		
	@include ('layouts.nav')

	<v-content class="vue-content">

		<transition
			enter-active-class="animated fadeInLeftBig"
			leave-active-class="animated fadeOutLeftBig">

			<div class="overview-map" v-show="!calendarTransitionSync">
				<v-menu transition="slide-y-transition" bottom v-show="imageLoaded">
					<v-btn slot="activator" color="teal" dark>
						Floor
					</v-btn>
					<v-list v-for="(floor, index) in building.floors" :key="floor._id">
						<v-list-tile @click='changeFloor(index)' :color="building.selectedFloor == index ? 'orange' : ''">
							<v-list-tile-title>@{{ stringify(index) }}</v-list-tile-title>
						</v-list-tile>
					</v-list>
				</v-menu>

				<div>
					<floor-image 
				 		:building="building"
				 		v-on:image-loaded="loadingSuccessfull"
				 		v-on:open-calendar="openCalendar">
				 	</floor-image>
				</div>
		
				<transition
					enter-active-class="animated fadeIn"
	    			{{-- leave-active-class="animated fadeOut" --}}>
					<div class="info-container" v-if="infoContainer">
						{{-- <img :src="PATH_PLACEHOLDER" class="logo"> --}}

						<h1>
							Info
						</h1>
						<p>
							You can change the floor by pressing the <span class="highlight uppercase orange-1">floor</span> menu on the left corner.
						</p>
						<p>
							You can make a booking by pressing on one of the <span class="highlight uppercase orange-1">available</span> rooms.
						</p>
						<p>
							You can see all of your bookings by going on <a class="highlight uppercase orange-1" href="/myBookings">MY BOOKINGS</a> page. 
						</p>
						<br><br>

						<p>
							Available rooms are represented with <span class="highlight green-1">green</span> color
						</p>
						
						<p>
							Unavailable rooms are represented with <span class="highlight red-1">red</span>  color
						</p>
					</div>
				</transition>
			</div>
		</transition>

		<transition
			enter-active-class="animated fadeIn"
    		leave-active-class="animated fadeOut">
			<div v-show="calendar">
				<button @click="closeCalendar" class="back-button">
					<span class="right-arrow"> > </span>
				</button>
	
				<transition
					enter-active-class="animated fadeInRightBig"
					leave-active-class="animated fadeOutRightBig">
					<div class="calendar-container" v-show="calendar">
						<full-calendar 
							:user="user" 
							:room="currentRoom" 
							:events="currentRoomBookings" 
							:bookings="eventModalBookings"
							:config="calendarConfig"
						 	:new-event="newEvent" 
						 	:updated-event="updatedEvent" 
						 	:deleted-event="deletedEvent"
						 	v-on:event-modal="openEventModal"
						 	>
					 	</full-calendar>

						<event-modal 
							v-if="eventModal" 
							@close="closeEventModal" 
							:data="eventModalConfig"
							:user="user" 
							:room="currentRoom" 
							:bookings="eventModalBookings"
							v-on:render-event="renderNewEvent" 
							v-on:update-event="renderUpdatedEvent"
							v-on:delete-event="renderDeletedEvent">
						</event-modal>
					</div>
				</transition>
			</div>
		</transition>
	</v-content>

@endsection

@section ('script')
	
	@include ('layouts.footer')

@endsection
