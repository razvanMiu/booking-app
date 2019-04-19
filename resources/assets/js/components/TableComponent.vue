<template>
	<div id="people">
		<v-card>
			<v-card-title>
				<h1>History</h1>
				<v-spacer></v-spacer>
				<v-text-field
					v-model="search"
					append-icon="search"
					label="Search"
					single-line
					hide-details
				></v-text-field>
			</v-card-title>
			<v-data-table
				:headers="columns"
				:items="filterBookings(status)"
				:search="search"
				:custom-filter="customFilter"
				class="elevation-1"
			>
				<template slot="items" slot-scope="props">
					<td>{{ props.item.username }}</td>
					<td class="text-xs-center" v-if="props.item.room.name">{{ props.item.room.name }}</td>
					<td class="text-xs-center" v-if="props.item.eventName">{{ props.item.eventName }}</td>
					<td class="text-xs-center" v-if="props.item.startDate">{{ humanize(props.item.startDate, props.item.dow) }}</td>
					<td class="text-xs-center" v-if="props.item.endDate">{{ humanize(props.item.endDate, props.item.dow) }}</td>
					<td class="text-xs-center">
						<v-btn
							:color = "props.item.status == 'available' ? 'teal' :
							props.item.status == 'inactive' ? 'red darken-3' : 'grey darken-1'"
							@click = "changeStatus(props.item)"
							dark
						>
							{{ props.item.status }}
						</v-btn>
					</td>
				</template>
			</v-data-table>
		</v-card>
		<div class="text-xs-center pt-2">
			<v-menu transition="slide-y-transition" bottom>
				<v-btn slot="activator" color="grey darken-2" dark>
					Status filter
				</v-btn>
				<v-list>
					<v-list-tile 
						@click="setStatus('available')"
						:color="status == 'available' ? 'orange' : ''"
					>
						<v-list-tile-title>Available</v-list-tile-title>
					</v-list-tile>

					<v-list-tile 
						@click="setStatus('inactive')"
						:color="status == 'inactive' ? 'orange' : ''"
					>
						<v-list-tile-title>Inactive</v-list-tile-title>
					</v-list-tile>

					<v-list-tile 
						@click="setStatus('finished')"
						:color="status == 'finished' ? 'orange' : ''"
					>
						<v-list-tile-title>Finished</v-list-tile-title>
					</v-list-tile>
				</v-list>
			</v-menu>
		</div>
	</div>
</template>

<script>
	import Vue from 'vue';
	import axios from 'axios';
	import {ServerTable, ClientTable, Event} from 'vue-tables-2';
	Vue.use(ClientTable);


	export default {
		data() {
			return {
				columns: [
				      { text: 'Owner', value: 'username' },
				      { text: 'Room name', value: 'room.name'  },
				      { text: 'Event name', value: 'eventName'  },
				      { text: 'Start date', value: 'startDate'  },
				      { text: 'End date', value: 'endDate'  },
				      { text: 'Status', value: 'status' },
    			],
    			search: '',
				bookings: [],
				status: 'available',
				days: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
	   	 	};
		},

		props: {
		},

		mounted: function() {
			this.fetchBookings();

		},

		methods: {
			setStatus: function(status) {
				this.status = status;
			},

		    customFilter(items, search, filter) {
		        return items.filter(row => filter(row["username"], search));
		    },

			humanize: function(date, dow) {
				if(date.split('T').length > 1) 
		      		return moment(date).format("MMMM Do YYYY, HH:mm:ss");
				else 
			 	{
			 		return date + ' ' + this.numToDay(dow);
			  	}
		    },

			changeStatus: function(item) {
		      if(item.status == 'available')
		          item.status = 'inactive';
		      else
		        if(item.status == 'inactive')
		          item.status = 'available';
		      else
		        {
		          this.errorAlert( this.NOT_ALLOWED_MESSAGE, '', true);
		          return 0;
		        }
		      this.axios
		        .post('/admin/history/update', {
		          status: item.status,
		          booking: item,
		        })
		        .then(response => {

		        })
		        .catch(e =>{

		        })
		    },

		    filterBookings: function(status) {
		    	return this.bookings.filter(booking => {
		    		return booking.status == status;
		    	});
		    },

		    fetchBookings: function() {
            this.axios
                .get('/admin/history/fetch')
                .then(response => {
                    this.bookings = [];
                    this.bookings = response.data.history; 
                })
                .catch(e => {

      			})
        	},

        	numToDay: function(dow) {
        		let s = '';
        		dow.forEach((day) => {
        			s = s + this.days[day] + ' ';
        		})

        		return s;
        	}

  		},
	}

</script>