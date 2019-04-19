<template>
	<div>
		<v-dialog v-model="dialog" max-width="505px" persistent transition="fade-transition" class="scrollbar-style">
		     <v-card>
				<v-card-title class="event-modal-card" :style="{'background-color': data.color}">
					<div class="card-menu">
						<v-tooltip bottom v-if="!data.select && data.sameUser && !data.finished">
							<v-btn slot="activator" fab small dark color="transparent" @click="email = true; edit = false" depressed>
								<v-icon>email</v-icon>
							</v-btn>
							<span>Send invitations</span>
						</v-tooltip>

						<v-tooltip bottom v-if="data.eventClick && data.sameUser && !data.finished">
							<v-menu transition="scale-transition" bottom slot="activator" auto> 
								<v-btn slot="activator" fab small dark color="transparent" depressed>
									<v-icon>list</v-icon>
								</v-btn>
								<v-list>
									<v-list-tile 
										@click="edit = true; email = false">
										<v-list-tile-title>Edit</v-list-tile-title>
									</v-list-tile>

									<v-list-tile 
										@click="deleteBooking">
										<v-list-tile-title>Delete</v-list-tile-title>
									</v-list-tile>
								</v-list>
							</v-menu>
							<span>Options</span>
						</v-tooltip>
			
						<v-tooltip bottom>
							<v-btn slot="activator" fab small dark color="transparent" depressed @click.native="$emit('close')" :disabled="disabled.closeButton">
								<v-icon>close</v-icon>
							</v-btn>
							<span>Close</span>
						</v-tooltip>
					</div>
					
					<span class="headline pointer" v-if="!data.select" @click="reset">{{ title }}</span>
					<span class="headline" v-if="data.select && title == ''">New Event</span>
					<span class="headline" v-if="data.select && title != ''">{{ title }}</span>
				</v-card-title>

				<v-card-text>
					<v-container grid-list-md>

						<div v-if="!data.select && !edit && !email" class="card-body">
							<span class="body-2 font-weight-medium font-italic description">
								{{ description }} 
							</span>

							<v-expansion-panel>
								<v-expansion-panel-content>
									<div slot="header">
										Participants ({{ data.participants.length }})
									</div>
									<v-card>
										<v-card-text>
											<v-chip  
												v-for="(participant, index) in data.participants" 
												small
												:key=index>
												{{ participant.name }}
											</v-chip>
										</v-card-text>
									</v-card>
								</v-expansion-panel-content>
							</v-expansion-panel>
						</div>

						<div v-if="email" class="card-email">
							<span class="body-2 font-weight-medium">
								Send invitations
							</span>

							<emailsTyper 
								:emails="emails"
								:participants="data.participants">
									
							</emailsTyper>
						</div>

						<div v-if="edit || data.select" class="card-edit">
							<v-form ref="form" v-model="valid" lazy-validation>
								<v-text-field 
									v-model="title" 
									label="Title" 
									:rules="[rules.required, rules.title.min, rules.title.max]"
									:readonly="! data.sameUser || data.finished"
									required>
									
								</v-text-field>

								<v-textarea 
									v-model="description" 
									label="Description" 
									:rules="[rules.required, rules.description.min, rules.description.max]"	
									:readonly="! data.sameUser || data.finished"
									required>
								</v-textarea>
							</v-form>
							
							<div class="repeat" v-if="this.data.repeatAllowed">
								<div class="repeat-header">
									<v-checkbox label="Repeat" v-model="repeat" color="teal"></v-checkbox>
								</div>

								<div class="repeat-days" v-if="repeat">
									<v-btn fab color='teal' dark small :outline="!Boolean(dow.indexOf(1) > -1)" @click="addDayOfWeek(1)">
										M
									</v-btn>
									<v-btn fab color='teal' dark small :outline="!Boolean(dow.indexOf(2) > -1)" @click="addDayOfWeek(2)">
										T
									</v-btn>
									<v-btn fab color='teal' dark small :outline="!Boolean(dow.indexOf(3) > -1)" @click="addDayOfWeek(3)">
										W
									</v-btn>
									<v-btn fab color='teal' dark small :outline="!Boolean(dow.indexOf(4) > -1)" @click="addDayOfWeek(4)">
										T
									</v-btn>
									<v-btn fab color='teal' dark small :outline="!Boolean(dow.indexOf(5) > -1)" @click="addDayOfWeek(5)">
										F
									</v-btn>
									<v-btn fab color='teal' dark small :outline="!Boolean(dow.indexOf(6) > -1)" @click="addDayOfWeek(6)">
										S
									</v-btn>
									<v-btn fab color='teal' dark small :outline="!Boolean(dow.indexOf(0) > -1)" @click="addDayOfWeek(0)">
										S
									</v-btn>
								</div>
							</div>
						</div>



						<v-layout row wrap v-if="!email && repeat">
					        <v-flex xs12 lg6>
								<v-menu
									ref="menu1"
									:close-on-content-click="false"
									v-model="startRangeMenu"
									:nudge-right="40"
									lazy
									transition="scale-transition"
									offset-y
									full-width
									max-width="290px"
									min-width="290px">

						            <v-text-field
										slot="activator"
										v-model="startRangeFormatted"
										label="Start range"
										hint="MM/DD/YYYY format"
										persistent-hint
										prepend-icon="event"
										@blur="startRange = parseDate(startRangeFormatted)">
											
									</v-text-field>

					            	<v-date-picker v-model="startRange" no-title @input="startRangeTest()">
					            	</v-date-picker>
					          	</v-menu>
					        </v-flex>

					  
					        <v-flex xs12 lg6>
								<v-menu
									:close-on-content-click="false"
									v-model="endRangeMenu"
									:nudge-right="40"
									lazy
									transition="scale-transition"
									offset-y
									full-width
									max-width="290px"
									min-width="290px">

									<v-text-field
										slot="activator"
										v-model="endRangeFormatted"
										label="End range"
										hint="MM/DD/YYYY format"
										persistent-hint
										prepend-icon="event"
										@blur="endRange = parseDate(endRangeFormatted)">
											
									</v-text-field>			

					            	<v-date-picker v-model="endRange" no-title @input="endRangeTest()">
					            	</v-date-picker>
				         	 	</v-menu>
				        	</v-flex>
			      		</v-layout>

						<div class="card-footer" v-if="!email && !edit && !repeat">
							<div class="card-footer-item">
								<p>
									<v-icon>
										schedule
									</v-icon>
									<span class="body-1">
										{{ duration }}
									</span>
								</p>
							</div>
							
							<div class="card-footer-item">
								<p>
									<v-icon>
										event
									</v-icon>
									<span class="body-1">
										{{ dateRange }}
									</span>
								</p>
							</div>

							<div class="card-footer-item">
								<p>
									<v-icon>
										assignment_ind
									</v-icon>
									<span class="body-1">
										{{ data.username }}
									</span>
								</p>
							</div>

						</div>
					</v-container>
				</v-card-text>

				<v-card-actions>
					<v-spacer></v-spacer>
					<v-btn color="gray" flat v-if="(edit || email) && data.sameUser" @click="reset">Cancel</v-btn>
					<v-btn color="gray" flat v-if="data.select && data.sameUser" @click="submitBooking" :disabled="disabled.submitButton || !valid">Submit</v-btn>
					<v-btn color="gray" flat v-if="email && data.sameUser" @click="sendInvitations">Send</v-btn>
					<v-btn color="gray" flat v-if="edit && data.sameUser" @click="updateBooking" :disabled="disabled.updateButton || !valid">Update</v-btn>
				</v-card-actions>
		     </v-card>
		</v-dialog>

		<v-dialog
		    v-model="loader"
		    hide-overlay
		    persistent
		    width="300">

			<v-card
				color="primary"
				dark>
				<v-card-text>
					Sending emails
					<v-progress-linear
						indeterminate
						color="white"
						class="mb-0">
							
					</v-progress-linear>
				</v-card-text>
		    </v-card>
		</v-dialog>

		<v-snackbar
			v-model="snackbar.toggle"
			bottom
			right
			:timeout="snackbar.timeout">
				{{ snackbar.message }}
			<v-btn
				:disabled="snackbar.closeButton"
				color="pink"
				flat
				@click="snackbar.toggle = false">
					Close
			</v-btn>
		</v-snackbar>
	</div>
</template>

<script>
	import EmailsTyper from './EmailsTyperComponent.vue';

	export default {
		data() {
			return {
				/* Event modal card header background color */
				color: '',
				/*
				 * Togglers
				 */
				dialog: 	true,
				repeat: 	false,
				email: 		false,
				edit: 		false,
				/* Emails for invitations */
				emails: [],
				/* Rules for inputs */
				rules: {
					required: value => !!value || "Required.",
					title: {
						min: value => value.length >= 6 || "Min 6 characters",
						max: value => value.length <= 15 || "Max 15 characters",
					},
					description: {
						min: value => value.length >= 6 || "Min 6 characters",
						max: value => value.length <= 255 || "Max 255 characters",
					}
				},
				/* Loader for sending invitations */
				loader: false,
				/* Array with days of the week for recurring events */
				dow: [],
				/* Date picker variable */
			    startRange: 			null,
			    endRange: 				null,
			    startRangeFormatted: 	null,
			    endRangeFormatted: 		null,
			    startRangeMenu: 		false,
			    endRangeMenu: 			false,
			    prevStartRange: 		'',
			    prevEndRange: 			'',
			    /* Disable buttons after submiting & updating an event */
			    disabled: {
			    	updateButton: 	false,
			    	submitButton: 	false,
			    	closeButton: 	false,
			    },

			    title: '',
			    description: '',

			    snackbar: {
			    	toggle: false,
			    	message: '',
			    	timeout: 2000,
			    	closeButton: false,
			    },
			   
			    valid: true,
			}
		},

		components: {
			EmailsTyper,
		},

		props: {
			bookings: Array,
			data: Object,
			user: Object,
			room: Object,
		},

		mounted: function() {
			/* Set startRange as the start date of the event, 
				make the range for recurring events one week 
			*/
			this.startRange = moment(this.data.start.format("YYYY-MM-DD"), "YYYY-MM-DD").format("YYYY-MM-DD");
			this.endRange = moment(this.data.start.format("YYYY-MM-DD"), "YYYY-MM-DD").add(7, 'days').format("YYYY-MM-DD");
			/* Make title and description reactive */
			this.title = this.data.title;
			this.description = this.data.description;
		},

		computed: {
			/*
			 * Get date range as 'start - end'
			 */
			dateRange: function() {
				if(moment.isMoment(this.data.end)) 
				{
					if(! this.data.allDay) {
						if(moment.duration(this.data.end.diff(this.data.start)).asMinutes() > 1440) {
							return this.data.start.format('Do MMM YY, h:mm a') + ' - ' + this.data.end.format('Do MMM YY, h:mm a');
						} else {
							return this.data.start.format('Do MMM YY, h:mm a') + ' - ' + this.data.end.format('h:mm a');
						}
					}

					return this.data.start.format('Do MMM YY');
				}

				return null;
			},
			/*
			 * Get the duration of the event: duration = endDate - startDate
			 */
			duration: function() {
				if(moment.isMoment(this.data.end)) {
					let timeFormat;
					let time = String(moment.duration(this.data.end.diff(this.data.start)).asMinutes() / 60);

					time = time.split('.');

					timeFormat = time[0] + ' hours ';
					if(time[1]) {
						timeFormat += time[1] * 6 + ' minutes';
					}

					return timeFormat;
				}
				
				return null;
			},
		},

		watch: {
			/*
			 * Format startRange for displaying it 
			 */
		    startRange (val) {
				this.startRangeFormatted = this.formatDate(this.startRange);
				/* Keep the startRange for 100ms */
				setTimeout(() => {
					this.prevStartRange = this.startRange;
				}, 100)
		    },
			/*
			 * Format endRange for displaying it 
			 */
		    endRange (val) {
				this.endRangeFormatted = this.formatDate(this.endRange);
				/* Keep the endRange for 100ms */
				setTimeout(() => {
					this.prevEndRange = this.endRange;
				}, 100);
		    },
		},

		methods: {
			/*
			 * Store Booking
			 */
			submitBooking: function() {
				let eventData;
				if(this.title && this.description) {
					/* Not allow client to press button twice */
 					this.disableButtons();
					/* Get data */
					eventData = this.getEventData();
					/* Store */
					axios
					.post('/book', {
						event: eventData,
						user:  this.user,
						room:  this.room,
					})
					.then(response => {
						/* Validate data */
						if(!this.validate(response)) {
							return false
						}
						/* Update event id and username */
						eventData.id = response.data.bookingId;
						this.data.id = response.data.bookingId;
						this.disabled.closeButton = false;
						eventData.username = this.data.username;
						/* Toggle send invitation form */
						this.openSendInvitationForm();
						/* Update event with new informations */
						this.patchEvent(eventData);
						/* Render event in fullcalendar */
						setTimeout(() => {
							this.$emit('render-event', eventData);
						}, 200);
						this.successAlert(this.CREATE_BOOKING_MESSAGE);
					})
					.catch(e => {
						this.snackbar.message = "";
						this.snackbar.toggle = true;
						console.log(e.response.data.errors);
					});
				} else {
					this.errorAlert(this.EMPTY_BOOKING_MESSAGE);
				}
			},
			/*
			 * Update booking
			 */
			updateBooking: function() {
				let eventData;
				if(this.title && this.description) {
					/* Not allow client to press button twice */
 					this.disableButtons();
					/* Get data */
					eventData = this.getEventData();
					/* Update */
					axios
					.patch('/book/' + eventData.id, {
						event: eventData,
					})
					.then(response => {
						/* Validate data */
						if(!this.validate(response)) {
							return false
						}
						/* Update event with new informations */
						this.patchEvent(eventData);
						/* Close event modal and update the event from fullcalendar */
						this.$emit('close');
						this.$emit('update-event', eventData);
						this.successAlert(this.UPDATE_BOOKING_MESSAGE);
					})
					.catch(e => {
						console.log(e);
					});
				}
			},
			/*
			 * Delete Booking
			 */
			deleteBooking: function() {
				let eventData = this.getEventData();
				this.disableButtons();
				/* Delete */
				axios
		    	.delete('/book/' + eventData.id)
				.then(response => {
					/* Close event modal and delete the event from fullcalendar */
					this.$emit('close');
					this.$emit('delete-event', eventData);
					this.successAlert(this.DELETE_BOOKING_MESSAGE);
				})
				.catch(e => {
					console.log(e);
				});
			},
			/*
			 * Send Invitations
			 */
			sendInvitations() {
				if(this.emails.length > 0) {
					this.dialog = false;
					this.loader = true;

					axios
					.post('/invitation/' + this.data.id, {
						emails: 		this.emails,
						dateRange: 		this.dateRange,
						duration: 		this.duration,
						eventOP: 		this.data.username,
						room: 			this.room,
					})
					.then(() => {
						this.successAlert(this.INVITATION_SENT);
						this.$emit('close')
						this.emails = [];
					})
				}
			},
			/*
			 * Add a day to dow array if there are not events overlaping
			 */
			addDayOfWeek: function(day) {
				let index = this.dow.indexOf(day);
				if(index > -1) {
					this.dow.splice(index, 1);
				} else {
					if(! this.dayIsOcuppied(day)) {
						this.dow.push(day);
					} else {
						this.errorAlert("Day is occupied!");
					}
				}
			},
			
			dayIsOcuppied: function(day) {
				let ocuppied = false;
				let event = this.getEventData(true);

				this.bookings.forEach(booking => {
					if(!booking.dow.length > 0) {
						ocuppied = this.noRecurringEventOverlap (
							day,
							event,
							this.parseBooking(booking)
						);
					} else {
						ocuppied = this.recurringEventOverlap(
							day,
							event,
							this.parseBooking(booking)
						);
					}
				});

				return ocuppied;
			},

			rangeIsOccupied: function() {
				let ocuppied = false;
				let event = this.getEventData(true);

				this.dow.forEach(day => {
					this.bookings.forEach(booking => {
						if(!booking.dow.length > 0) {
							ocuppied = this.noRecurringEventOverlap (
								day,
								event,
								this.parseBooking(booking)
							);
						} else {
							ocuppied = this.recurringEventOverlap(
								day,
								event,
								this.parseBooking(booking)
							);
						}
					})
				});

				return ocuppied;
			},
			
			getEventData: function(onlyMoment = false) {
				if(this.data.title != this.title) {
					this.data.title = this.title;
				}

				if(this.data.description != this.description) {
					this.data.description = this.description;
				}

				if(this.dow.length > 0 && !onlyMoment) {
					this.data.start = this.data.start.format('HH:mm');
					this.data.end = this.data.end.format('HH:mm');
				}

			    return {
			    	backgroundColor: 	this.data.color,
			    	participants: 		this.data.participants,
					description: 		this.data.description,
					username: 			this.data.username,
					user_id:   			this.user._id,
					title: 				this.data.title,
					dow: 				this.dow.sort((a,b) => a - b),
					id: 				this.data.id,
					overlap: 			false,
					textColor: 			'#FFFFFF',
					start: 				this.data.start,
					end: 				this.data.end,
					status: 			'available',

					ranges: {
						start: 	this.startRange, 
						end: 	this.endRange,
					},

					range: moment.range(moment(this.startRange, "YYYY-MM-DD"), moment(this.endRange, "YYYY-MM-DD")),
			    }
			},

			parseBooking: function(booking) {
				let event = this.iterationCopy(booking);
				
				event.days 	= [];

				if(!event.isRecurring) {
					event.start.dayCoppy = event.start.day();
					event.end.dayCoppy = event.end.day();

					if(event.start.dayCoppy != event.end.dayCoppy) {
						while(event.end.dayCoppy >= event.start.dayCoppy) {
							event.days.push(event.start.dayCoppy++);
						}
					} else {
						event.days.push(event.start.day());
					}
				} else {
					event.days = event.dow;
				}
				

				return event;
			},

			noRecurringEventOverlap(day, event, noRecurringEvent) {
				if( event.id != noRecurringEvent.id && noRecurringEvent.days.indexOf(day) > -1 ) {
					if (event.range.contains(noRecurringEvent.start) || event.range.contains(noRecurringEvent.end)) {

						return this.overlap(event, noRecurringEvent, true);
					}
				}  

				return false;
			},

			recurringEventOverlap(day, event, recurringEvent) {
				if(event.id != recurringEvent.id && recurringEvent.days.indexOf(day) > -1) {
					if(recurringEvent.ranges.end.diff(event.range.start) > 0 && recurringEvent.ranges.start.diff(event.range.end) < 0) {	
						if((recurringEvent.ranges.end.day() > day || (recurringEvent.ranges.end.day() == 0 && day != 0)) && (recurringEvent.ranges.end.diff(event.range.start, 'days') >= 7 && event.range.end.diff(event.range.start, 'days') >= 7)) {

							return this.overlap(event, recurringEvent, true)
						}
					}  
				} 

				return false;
			},


			startRangeTest: function() {
				if(this.rangeIsOccupied()) {
					this.startRange = this.prevStartRange;
					this.errorAlert("Day is occupied!");
				}

				this.startRangeMenu = false
			},

			endRangeTest: function() {
				if(this.rangeIsOccupied()) {
					this.endRange = this.prevEndRange;
					this.errorAlert("Day is occupied!");
				}

				this.endRangeMenu = false
			},


			formatDate: function(date) {
				if (!date) {
					return null
				}
				const [year, month, day] = date.split('-');

				return `${month}/${day}/${year}`;
		    },

		    parseDate: function(date) {
				if (!date) {
					return null
				}
				const [month, day, year] = date.split('/');

				return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
		    },

		    disableButtons: function() {
		    	this.disabled.updateButton = true;
		    	this.disabled.submitButton = true;
		    	this.disabled.closeButton = true;
		    },

		    openSendInvitationForm: function() {
		    	this.email = true;
				this.edit = false;
				this.data.select = false;

				return true;
		    },

		    patchEvent: function(event) {
		    	if(this.dow.length > 0) {
					event.isRecurring = true;
					event.editable = false;

					event.ranges.start   = moment(this.startRange, "YYYY-MM-DD");
					event.ranges.end 	 = moment(this.endRange, "YYYY-MM-DD");
				} else {
					event.isRecurring 	= false;
					event.editable 		= true;
				}

				return true;
		    },

		    validate(response) {
		    	if(response.data.error != null) {
					this.snackbar.message = response.data.error;
					this.snackbar.toggle = true;
					this.snackbar.timeout = 60000;
					this.snackbar.closeButton = true;

					return false;
				}

				return true;
		    },

		    reset: function() {
				this.description 	= this.data.description;
				this.title 			= this.data.title;
				this.repeat 		= false;
				this.email 			= false;
				this.edit 			= false;
			},
		}
	}
</script>