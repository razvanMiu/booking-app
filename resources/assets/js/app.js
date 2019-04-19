window['moment-range'].extendMoment(moment);

import Vue from './config';
import Vuetify from 'vuetify';
import colors from 'vuetify/es5/util/colors';

Vue.use(Vuetify);

Vue.component('floor-image', require('./components/FloorComponent.vue'));
Vue.component('full-calendar', require('./components/FullCalendar.vue'));
Vue.component('event-modal', require('./components/EventModalComponent.vue'));
Vue.component('my-bookings', require('./components/MyBookingsComponent.vue'));

const app = new Vue({
	el: '#root',

	data: {
		'loader': false,
		/*
		 * User data
		 */
		'user': {},
		/*
		 * Event modal data
		 */
		'eventModalBookings': [],
		'eventModalConfig': {},
		/*
		 * Watchers for fullcalendar (rerender events)
		 */
		'updatedEvent': null,
		'deletedEvent': null,
		'newEvent': 	null,
		/*
		 * Calendar data
		 */
		'calendarConfig': {
			'slotEventOverlap': 	false,		
			'eventLimit': 			true,			
			'allDaySlot': 			false,
			'droppable': 			false,	
			'eventTextColor': 		'#ffffff',
			'eventColor': 			'#009688',
			'firstDay': 			1,
			'slotLabelFormat': 		'h(:mm) a',
			'defaultView': 			'month',		 
			
			views: {
				week: {
					columnFormat: 'ddd D/M'
				},

				agenda: {
					eventLimit: 3,
				}
     		},
		},		
		/*
		 * Building data
		 */
		'building': {
			'selectedFloor' 	: 0,
			'floors'			: [],
			'name'				: null,
		},

		'currentRoomBookings'	: [],
		'currentRoom'			: null,
		/*
		 * Components togglers
		 */
		'calendarTransitionSync': 	false,
		'eventModal': 				false,
		'calendar': 				false,
		/*
		 * Navigation containers togglers
		 */
		'drawerRight'  	: false,
		'drawerLeft'	: true,
		/* True when floor image finished loading */
		'imageLoaded'  	: false,
		/* Shows info container after floor image finished loading */
		'infoContainer'	: false,
	},
	
	mounted: function() {
		/*
		 * Fetch building data 
		 */
		if(window.location.pathname.split('/')[1] == 'building') {
			this.getFloors();
		}

		/* Fetch user data */
		this.getUser();
	},

	methods: {
		/*
		 * Get details about every floor, rooms and bookings for current building
		 */
		getFloors: function() {
			axios
			.get('/api/building/floors/' + window.location.pathname.split('/')[2])
			.then(response => {
				this.building.name 		= null;
				this.building.floors 	= [];

				this.building.name 		= response.data.building.name;
				this.building.floors 	= response.data.building.floors;
			})
			.catch(e => {
				console.log(e);
			});
		},
		/*
		 * Get user data
		 */
		getUser: function() {
			axios
			.get('/user')
			.then(response => {
				this.user = {};

				this.user = response.data;
			});
		},
		/*
	     * Change the floor which will compute imageSrc for it
		 */
		changeFloor: function(floor) {
			this.building.selectedFloor = floor
		},
		/*
		 * Open the calendar with all events for the current room
		 */
		openCalendar: function(room) {
			this.currentRoom = room;
			axios
			.get('/api/bookings/' + room._id)
			.then(response => {
				this.currentRoomBookings = [];
				response.data.bookings.forEach(booking => {
					if(booking.status != 'inactive') {
						let parsedBooking;
						booking.id 			= booking._id;
						booking._id 		= null;
						booking.textColor 	= '#FFFFFF';
						booking.rendering   = false;
						booking.overlap 	= false;
						/* Set recurring if event repeats */
						if(booking.dow.length > 0) {
							booking.isRecurring = true;
							booking.editable 	= false;

							booking.ranges.start = moment(booking.ranges.start, "YYYY-MM-DD");
							booking.ranges.end 	 = moment(booking.ranges.end, "YYYY-MM-DD");

						} else {
							booking.isRecurring = false;
						}
						/* If event is in the past */
						if(!booking.isRecurring && this.timeSpace(booking.end, false) != 1) {
							if(booking.status == "available") {
								booking.status = "finished";
								this.updateEvent(booking, false);
							}
							booking.backgroundColor 	= '#5B5B5B';
							booking.borderColor			= '#5B5B5B';
							booking.editable  			= false;
						} else  {
							if(this.user._id == booking.user_id) {
								booking.backgroundColor		= '#009688';
								booking.borderColor			= '#009688';
								booking.editable 			= true;
							} else {
								booking.backgroundColor 	= '#576FA3';
								booking.borderColor			= '#576FA3';
								booking.editable 			= false;
							}

							if(booking.dow.length > 0) {
								booking.editable = false;
							} 
						}

						this.currentRoomBookings.push(booking);
						parsedBooking = this.iterationCopy(booking);

						if(parsedBooking.dow.length == 0) {
							parsedBooking.start = moment(parsedBooking.start, "YYYY-MM-DDTHH:mm:ss");
							parsedBooking.end = moment(parsedBooking.end, "YYYY-MM-DDTHH:mm:ss");
						} else {
							parsedBooking.start = moment(parsedBooking.start, "HH:mm");
							parsedBooking.end = moment(parsedBooking.end, "HH:mm");
						}
						this.eventModalBookings.push(parsedBooking);
					}
				});
				/*
				 * To make transition smooth
				 */
				setTimeout(() => {
					this.calendar = true;
				}, 500);
				this.calendarTransitionSync = true;
			})
			.catch(e => {
				console.log(e);
			});
		},
		/*
		 * Close the calendar and go back to the floor overview
		 */
		closeCalendar: function() {
			this.eventModalBookings 	= [];
			this.currentRoomBookings	= [];
			this.currentRoom 			= null;
			/*
			 * To make animation smooth
			 */
			this.calendar = false;
			setTimeout(() => {
  				this.calendarTransitionSync = false;

  				$(function () {
					ImageMap('img[usemap]');
				});
			}, 200)
		},
		/*
		 * Triggered on 'event-modal' event emitted by full-calendar component
		 */
		openEventModal: function(event, selector) {
			if(selector == "select") {
				this.eventModalConfig.eventClick 	= false;
				this.eventModalConfig.sameUser 		= true;
				this.eventModalConfig.username 		= this.user.name;
				this.eventModalConfig.select 		= true;
			} else {
				this.eventModalConfig.eventClick 	= true;
				this.eventModalConfig.username 		= event.username;
				this.eventModalConfig.select 		= false;
				if(this.user._id == event.user_id) {
					this.eventModalConfig.sameUser = true;
				} else {
					this.eventModalConfig.sameUser = false;
				} 
			}
			if(event.end.format("DD") != event.start.format("DD")) {
				this.eventModalConfig.repeatAllowed = false;
			} else {
				this.eventModalConfig.repeatAllowed = true;
			}

			this.eventModalConfig.participants 	= event.participants;
			this.eventModalConfig.description 	= event.description;
			this.eventModalConfig.finished 		= event.finished;
			this.eventModalConfig.allDay 		= event.allDay;
			this.eventModalConfig.title 		= event.title;	
			this.eventModalConfig.start 	 	= event.start;
			this.eventModalConfig.color 		= event.backgroundColor;
			this.eventModalConfig.end 	 		= event.end;
			this.eventModalConfig.dow 			= event.dow;
			this.eventModalConfig.id 	 		= event.id;
			this.eventModal 					= true;
		},
		/*
		 * Triggered when event-modal component is closed
		 */
		closeEventModal: function() {
			this.eventModal = false
			this.eventModalConfig.start 	= null;
			this.eventModalConfig.end 	 	= null;
			this.eventModalConfig.id 	 	= null;
		},
		/*
		 * Triggered by event-modal component
		 * Used to commuicate between event-modal and full-calendar components
		 */
		renderNewEvent: function(eventData) {
			this.newEvent = eventData;
		},
		renderUpdatedEvent: function(eventData) {
			this.updatedEvent = eventData;
		},
		renderDeletedEvent: function(eventData) {
			this.deletedEvent = eventData;
		},
		/*
		 * Open my-bookings component
		 */
		openMyBookings: function() {
			this.myBookingsContainer = true;
		},
		/*
		 * Change status of the booking
		 */
		changeStatus: function(eventData) {
			if(eventData.status == 'available') {
				eventData.status = 'inactive';
			} else if(eventData.status == 'inactive') {
				eventData.status = 'available';
			} else {
				this.errorAlert(this.NOT_ALLOWED_MESSAGE);

				return false;
			}
			this.updateEvent(eventData);
			
			return true;
		},

		updateEvent: function(eventData, alert = true) {
			axios
			.patch('/book/' + eventData.id, {
				event: eventData,
			})
			.then(response => {
				if(alert) {
					this.successAlert(this.UPDATE_BOOKING_MESSAGE);
				}
				eventData = {};
			})
			.catch(e => {
				console.log(e);
			});
		},

		deleteEvent: function(eventData) {
			axios
    		.delete('/book/' + eventData._id)
			.then(response => {
				this.successAlert(this.DELETE_BOOKING_MESSAGE);
				eventData = {};
			})
			.catch(e => {
				console.log(e);
			});
		},
		/* Show info container after the first load of a floor image  */
		loadingSuccessfull: function() {
			this.imageLoaded = true;
			setTimeout(() => {
				this.infoContainer = true;
			}, 500);
		},
	},
});