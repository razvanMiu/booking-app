<template>
	<div>
		<v-dialog v-model="dialog" max-width="500px">
		     <v-card>
				<v-card-title id="editBookingModal">
					<span class="headline">Edit Booking</span>
				</v-card-title>

				<v-card-text>
					<v-container grid-list-md>
							<v-form ref="form" v-model="valid" lazy-validation>
								<v-text-field 
									v-model="editedItem.title" 
									label="Title" 
									:rules="rules"
									:readonly="status == 'finished'"
									required>
									
								</v-text-field>
								<v-textarea 
									v-model="editedItem.description" 
									label="Description" 
									:rules="rules"	
									:readonly="status == 'finished'"
									required>
								</v-textarea>
							</v-form>
					</v-container>
				</v-card-text>

				<v-card-actions>
					<v-spacer></v-spacer>
					<v-btn color="blue darken-1" flat @click.native="close">Cancel</v-btn>
					<v-btn color="blue darken-1" flat @click.native="save" :disabled="!valid || status == 'finished'">Save</v-btn>
				</v-card-actions>
		     </v-card>
		</v-dialog>

		<v-card>
			<v-card-title>
				Booking
				<v-spacer></v-spacer>
				<v-text-field
					v-model="search"
					append-icon="search"
					label="Search"
					single-line
					hide-details>

				</v-text-field>
			</v-card-title>

			<v-data-table
				:headers="headers"
				:items="filteredItems"
				:search="search"
				:pagination.sync="pagination"
				class="elevation-1"
	    			>
				<template slot="items" slot-scope="props">
					<td>{{ props.item.title }}</td>
					<v-tooltip bottom>
						<td slot="activator" class="overflow-hidden">{{ props.item.description }}</td>
						<span>{{ props.item.description }}</span>
					</v-tooltip>
					<td>{{ humanize(props.item.start, props.item.dow) }}</td>
					<td>{{ humanize(props.item.end, props.item.dow) }}</td>
					<td>
						<v-btn 
							:color="
								props.item.status == 'available' ? 'teal' : 
								props.item.status == 'finished' ? 'grey darken-1' : 'red darken-3'"
							@click="$emit('change-status', props.item)"
							dark>
							{{ props.item.status }}
						</v-btn>
					</td>
					 <td class="justify-center layout px-0">
						<v-icon
							small
							class="mr-2"
							@click="editItem(props.item)"
							>
							edit
						</v-icon>
						<v-icon
							small
							@click="deleteItem(props.item)"
							>
							delete
						</v-icon>
					</td>
				</template>
				<template slot="no-data">
			        <v-alert :value="true" color="error" icon="warning">
		          		Sorry, nothing to display here :(
			        </v-alert>
		 	 	</template>

		 	 	<template slot="no-results">
		 	 		<v-alert :value="true" color="error" icon="warning">
		          		Your search for "{{ search }}" found no results.
		        	</v-alert>
		 	 	</template>
		    </v-data-table>	
		</v-card>
		<div class="text-xs-center pt-2">
			<v-menu transition="slide-y-transition" bottom>
				<v-btn slot="activator" color="teal" dark>
					Status filter
				</v-btn>
				<v-list>
					<v-list-tile 
						@click="setStatus('available')"
						:color="status == 'available' ? 'orange' : ''">
						<v-list-tile-title>Available</v-list-tile-title>
					</v-list-tile>

					<v-list-tile 
						@click="setStatus('inactive')"
						:color="status == 'inactive' ? 'orange' : ''">
						<v-list-tile-title>Inactive</v-list-tile-title>
					</v-list-tile>

					<v-list-tile 
						@click="setStatus('finished')"
						:color="status == 'finished' ? 'orange' : ''">
						<v-list-tile-title>Finished</v-list-tile-title>
					</v-list-tile>
				</v-list>
			</v-menu>
		</div>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				/* Vue data table settings */
				valid: true,
				search: '',
				pagination: {
					sortBy: 'startDate.date',
		    	},
			 	headers: [
					{ text: 'Title', value: 'title' },
					{ text: 'Description', value: 'description' },
					{ text: 'Start date', value: 'start'},
					{ text: 'End date', value: 'end'},
					{ text: 'Status', value: 'status'},
	         		{ text: 'Actions', value: 'name', sortable: false }
	        	],
	        	/* Edit modal */
		    	dialog: false,
		    	/* Edited booking */
	        	editedIndex: -1,
				editedItem: {},
				/* Rules for input */
				rules: [
					v => !!v || 'Field is required',
				],
				/* Days for recurring events */
				days: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
				/* Status filter value */
				status: 'available',
			}
		},

		props: {
			user: Object,
		},

		computed: {
			/*
			 * Show only needed events
			 */
			filteredItems() {
				if(this.user.bookings != null) {
					return this.user.bookings.filter( (booking) => {
						return booking.status == this.status;
					});
				}

				return [];
			}
		},

		methods: {
			/*
			 * Make the time human readable
			 */
			humanize: function(time, dow) {
				if(time.split('T').length > 1) 
		      		return moment(time).format("MMMM Do YYYY, HH:mm");
				else 
			 	{
			 		return time + ' ' + this.numToDay(dow);
			  	}
			},
			/*
			 * Assign the booking that we want to edit to the editedItem
			 */
			editItem: function(item) {
				this.editedIndex 	= this.user.bookings.indexOf(item)
				this.editedItem 	= Object.assign({}, item)
				this.dialog 		= true
			},
			/*
			 * Delete booking
			 */
			deleteItem: function(item) {
				const index = this.user.bookings.indexOf(item)
				if(confirm('Are you sure you want to delete this item?')) {
					this.$emit('delete-booking', item);
					this.user.bookings.splice(index, 1)
				}
			},
			/*
			 * Close edit booking modal
			 */
			close: function() {
				this.dialog = false
				setTimeout(() => {
					this.editedItem = Object.assign({}, this.defaultItem)
					this.editedIndex = -1
				}, 300)
			},
			/*
			 * Commit editedItem to DB
			 */
			save: function() {
				if (this.editedIndex > -1) {
					this.$emit('edit-booking', this.editedItem);
					Object.assign(this.user.bookings[this.editedIndex], this.editedItem)
				} else {
					this.user.bookings.push(this.editedItem)
				}
				this.close()
			},
			/*
			 * Set status for filtering the items
			 */
			setStatus: function(status) {
				this.status = status;
			},
			/*
			 * Convert day number to string
			 */
			numToDay: function(dow) {
        		let s = '';
        		dow.forEach((day) => {
        			s = s + this.days[day] + ' ';
        		})

        		return s;
        	}
		},

		watch: {
			dialog (val) {
				val || this.close()
			}
  		},
	}
</script>