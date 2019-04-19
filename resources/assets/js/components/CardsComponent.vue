<template>
	<div class="cardComponent">
		<div class="welcome" v-if="data.cardState == 0">
			<span class="display-4">Welcome to Dashboard!</span><br>
			<span class="subheading">Please select a building for details.</span>  
		</div>
		<v-card v-if="data.cardState == 1">
			<v-card-title>
				<h1>{{ currentBuilding.name }}</h1>
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
				:headers="floorsColumns"
				:items="currentBuilding.floors"
				:search="search"
				class="elevation-1"
				hide-actions
			>
				<template slot="items" slot-scope="props">
					<td>{{ humanizeFloorName(props.item.name)}}</td>
					<td>{{ props.item.noOfRooms }}</td>
					<td>{{ humanize(props.item.created_at) }}</td>
					<td>
						<v-icon
							@click="selectFloor(props.item)"
						>
							edit
						</v-icon>
					</td>
					<td>
						<v-icon 
							@click="deleteFloor(props.item)"
						>
							delete
						</v-icon>
					</td>
				</template>
				<template slot="no-data">
					<v-alert :value="true" color="error" icon="warning">
						Sorry, no floors to display here :(
					</v-alert>
				</template>
			</v-data-table>
		</v-card>
		<div v-if="data.cardState == 2">
			<v-card>
				<v-card-title>
					<h1>
						<span class="pointer" @click="back">{{ currentBuilding.name}}</span>
						> 
						<span class="floorName">{{humanizeFloorName(currentFloor.name, false)}}</span>
					</h1>
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
					:headers="roomsColumns"
					:items="currentFloor.rooms"
					:search="search"
					class="elevation-1"
					hide-actions
				>
					<template slot="items" slot-scope="props">
						<td>{{ humanizeRoomName(props.item.name) }}</td>
						<td>{{ props.item.noOfSeats }}</td>
						<td>{{ props.item.coords }}</td>
						<td>{{ props.item.shape }}</td>
						<td 
							:color="props.item.status == 'available' ? 'teal' : 'red' ">{{ props.item.status }}</td>
						<td>{{ humanize(props.item.created_at) }}</td>
						<td>
							<v-icon
								@click="updateForm(props.item, true)"
							>
								edit
							</v-icon>
						</td>
						<td>
							<v-icon
								@click="deleteRoom(props.item)"
							>
								delete
							</v-icon>
						</td>
					</template>
					<template slot="no-data">
						<v-alert :value="true" color="error" icon="warning">
							Sorry, no rooms to display here :(
						</v-alert>
					</template>
				</v-data-table>
			</v-card>
		</div>
		<div class="buttons" v-if="data.cardState > 0">
			<v-btn
				color="grey"
				outline
				dark
				small
				@click="addForm"
			>
				<v-icon>add</v-icon>
					Add {{ (data.cardState == 1) ? 'floor' : 'room' }}
				</v-btn>
			<v-btn
				outline
				color="green"
				dark
				small
				@click="updateForm(' ', false)"
			>
				<v-icon>edit</v-icon>
					Update {{ (data.cardState == 1) ? 'building' : 'floor' }}
			</v-btn>	
		</div>
	</div>
</template>

<script>
	import Vue from 'vue';
	import axios from 'axios';
	
	export default {
		data() {
			return {
				updateRoom: false,
				currentFloor: {},
				currentRoom: {},
				menu: false,
				lastCardState: 1,
				floorsColumns: [
					{ text: 'Name', value: 'name' },
					{ text: 'Number of rooms', value: 'noOfFloors' },
					{ text: 'Created at', value: 'created_at' },
					{ text: 'Select' },
					{ text: 'Delete' },
				],
				roomsColumns: [
					{ text: 'Name', value: 'name' },
					{ text: 'Number of seats', value: 'noOfSeats' },
					{ text: 'Coordinates', value: 'coords' },
					{ text: 'Shape', value: 'shape' },
					{ text: 'Status', value: 'status' },
					{ text: 'Created at', value: 'created_at' },
					{ text: 'Update' },
					{ text: 'Delete' }
				],
				search: '',
			};
		},

		props: {
			currentBuilding: Object,
			data: Object,
		},

		mounted: function() {
		},

		methods: {
			back: function() {
				this.data.cardState--;
			},

			humanize: function(date) {
		      return moment(date).format("MMMM Do YYYY, HH:mm:ss");
		    },

			selectFloor: function(floor) {
				this.currentFloor = floor;
				this.data.roomsDropdown = false;
				this.data.cardState = 2;
				this.$emit('rooms-card', this.currentFloor);
			},

			addForm: function() {
				this.$emit('store-form', this.data.cardState);
			},

			updateForm: function(room, data = null) {
				this.$emit('update-form', this.data.cardState, data, room);
			},

			deleteFloor: function(floor) {
				this.axios
				.post('/admin/floor/delete', {
					floor: floor,
				})
				.then(response => {
					if(response.data)
					{	
						this.currentBuilding.floors.splice(this.currentBuilding.floors.indexOf(floor),1);
						this.successAlert(floor.name + ' deleted','', true);
					}
				})
			},

			deleteRoom: function(room) {
				this.axios
				.post('/admin/room/delete', {
					room: room,
				})
				.then(response => {
					if(response.data)
					{	
						this.currentFloor.rooms.splice(this.currentFloor.rooms.indexOf(room),1);
						this.successAlert(room.name + ' deleted','', true);
					}
				})
			},


			humanizeFloorName: function(floorName, upperCase = true) {
				let floor = floorName.split('_')[1].split('-')[0];

				if(upperCase) {
					floor = floor.charAt(0).toUpperCase() + floor.slice(1);
 				}

 				if(floor.toLowerCase() == 'basement') {
 					return floor;
 				} else {
 					return floor + ' floor';
 				}
			},

			humanizeRoomName: function(roomName) {
				return roomName.split('_')[2] + ' room';
			}
		}
	}
	
</script>