import Vue from 'vue';
import swal from 'sweetalert';
import axios from 'axios';
/*
 * Global Variables
 */
Vue.mixin({
	data: function() {
		 return {
			axios: axios,
			swal:  swal,

			OVERLAP_BOOKING_MESSAGE: 		"You can't overlap a booking",
			INPAST_BOOKING_MESSAGE: 		"You can't go in the past",
			CREATE_BOOKING_MESSAGE: 		'Booking created',
			UPDATE_BOOKING_MESSAGE: 		'Booking updated',
			DELETE_BOOKING_MESSAGE: 		'Booking deleted',
			EMPTY_BOOKING_MESSAGE: 			'You have to fill all the fields',
			NOT_ALLOWED_MESSAGE:     		'You are not allowed to do that', 

			PATH_PLACEHOLDER: 	'/images/logo/logo-BSB.png',
			PATH_BUILDINGS: 	'/images/bsb/buildings/',
			PATH_ROOMS: 		'/images/bsb/rooms/',

			INVITATION_SENT: 	'Invitations have been sent',

			FLOOR_LABELS: ['Basement', 'Ground', 'First', 'Second', 'Third', 'Forth', 'Fifth', 'Sixth'],
		}
	},

	methods: {

		successAlert(title = '', text = '', button = true) {
			swal({
				title: title,
				text: text,
				button: button,
				timer: 2000,
				icon:  "success",
			});
		},

		errorAlert(title = '', text = '', button = true) {
			swal({
				title: title,
				text: text,
				button: button,
				timer: 2000,
				icon:  "error",
			})
		},
		/*
		 * Convert number into floor labels
		 */
		stringify(number) {
			return this.FLOOR_LABELS[number];
		},
		/*
		 * timeSpace()
		 * 		0 -> event in the past
		 *  	1 -> event in the future
		 *   	2 -> event in the past but in present day
		 */
		timeSpace(date, momentObj = true) {
			let then;
			let now  = moment(moment().format('YYYY-MM-DD HH:mm:ss'), 'YYYY-MM-DD HH:mm:ss');
			if(momentObj) {
				then = moment(date.format('YYYY-MM-DD HH:mm:ss'), 'YYYY-MM-DD HH:mm:ss');
			} else {
				then = moment(date, 'YYYY-MM-DD HH:mm:ss');
			}
			
			if(moment(then.format('YYYY-MM-DD')).diff(moment(now.format('YYYY-MM-DD'))) < 0) {
				return 0;
			} else if(moment(then.format('YYYY-MM-DD')).diff(moment(now.format('YYYY-MM-DD'))) == 0) {
				if(then.diff(now, 'minutes') < 0) {
					return 2;
				}
			}

			return 1;
		},

		dateToNumber(date) {
     		let newDate = new Date(date);
     		
			return newDate.getDay();
		},

		overlap(momentA, momentB, time = false) {
			if(time) {

				momentA.startTime = momentA.start.format("HH:mm").split(":");
				momentA.endTime   = momentA.end.format("HH:mm").split(":");

				momentB.startTime = momentB.start.format("HH:mm").split(":");
				momentB.endTime   = momentB.end.format("HH:mm").split(":");

				momentA.startTime = Number(momentA.startTime[0]) * 60 + Number(momentA.startTime[1]);
				momentA.endTime   = Number(momentA.endTime[0]) * 60 + Number(momentA.endTime[1]);

				momentB.startTime = Number(momentB.startTime[0]) * 60 + Number(momentB.startTime[1]);
				momentB.endTime   = Number(momentB.endTime[0]) * 60 + Number(momentB.endTime[1]);

				return momentA.startTime < momentB.endTime && momentA.endTime > momentB.startTime;
				
			} else {
				if(momentA.start.diff(momentB.end) < 0 && momentA.end.diff(momentB.start) > 0) {
					return true;
				}
			}

			return false;
		},

		iterationCopy: function(src) {
			let target = {};
			for (let prop in src) {
				if (src.hasOwnProperty(prop)) {
					target[prop] = src[prop];
				}
			}
			return target;
		}
	}
})

/*
 * Nice Colors
 */
let blue_color 	= '#255E69';
let red_color  	= '#AA3939';
let green_color = '#277554';

export default Vue;