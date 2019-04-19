<template>
  <div>
    <v-dialog persistent v-model="data.dialog"  max-width="500px">
      <v-card>
        <v-card-title class="eventTitle">
          <p v-model="data.modalTitle" v-show="data.deleteBuildingConfirmation == false" class="headline">{{data.modalTitle}}</p>
          <p v-show="data.deleteBuildingConfirmation" class="headline">Are you sure you want to delete "{{data.deleteBuilding.name}}" building?</p>
        </v-card-title>
        <v-card-text>
          <v-container grid-list-md>
            <v-form ref="form" v-model="valid" v-if="modalState == 1 || modalState == 2" lazy-validation>
              <v-text-field 
                v-model="buildingName" 
                label="Building name" 
                :rules="rules"
                required>

              </v-text-field>
              <v-text-field 
                v-model="noOfFloors" 
                label="Number of floors" 
                :rules="rules"  
                required>
              </v-text-field>             
              </v-form>
              <v-form ref="form" v-model="valid" v-if="modalState == 3 || modalState == 4" lazy-validation>
              <v-text-field 
                v-model="floorName" 
                label="Floor name" 
                :rules="rules"
                required>

              </v-text-field>
              <v-text-field 
                v-model="noOfRooms" 
                label="Number of rooms" 
                :rules="rules"  
                required>
              </v-text-field>             
              </v-form>
              <v-form ref="form" v-model="valid" v-if="modalState == 5 || modalState == 6" lazy-validation>
              <v-text-field 
                v-model="RoomName" 
                label="Room name" 
                :rules="rules"
                required>

              </v-text-field>
              <v-text-field 
                v-model="noOfSeats" 
                label="Number of seats" 
                :rules="rules"  
                required>
              </v-text-field>
              <v-text-field 
                v-model="coords" 
                label="Coordinates" 
                :rules="rules"  
                required>
              </v-text-field>
              <v-select
                :items="['available', 'unavailable']"
                v-model="status"
                label="Status"
                :rules="rules"
                required
              ></v-select>
              <v-select
                :items="['rect', 'poly', 'circle']"
                v-model="shape"
                label="Shape"
                :rules="rules"
                required
              ></v-select>             
            </v-form>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-btn color="blue darken-1" flat @click.native="closeDialog">Cancel</v-btn>
          <v-btn color="blue darken-1" flat @click.native="stateVerify" v-show="data.deleteBuildingConfirmation == false" :disabled="!valid">Save</v-btn>
          <v-btn color="blue darken-1" flat @click.native="deleteBuilding()" v-show="data.deleteBuildingConfirmation == true">Delete</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
	import Vue from 'vue';
	import axios from 'axios';
	
	export default {
	data() {
		return {
        RoomName: "",
        noOfSeats: "",
        coords: "",
        shape: "",
        status: "",
        buildingName: '',
        noOfFloors: '',
        floorName: '',
        noOfRooms: '',
		    updateContainer: false,
		    addContainer: false,
        valid: false,
        rules: [
          v => {
            return !!v || 'Field is required'
          },
        ],

		};
	},



	props: {
      currentBuilding: Object,
      buildings: Array,
    	modalState: Number,
      data: Object,
    },

    watch: {
      modalState(val) {
        if(this.modalState == 2)
        {
          this.buildingName = this.currentBuilding.name;
          this.noOfFloors = this.currentBuilding.noOfFloors;
        }
        if(this.modalState == 1)
        {
          this.buildingName = '';
          this.noOfFloors = '';
        }
        if(this.modalState == 4)
        {
          this.floorName = this.data.currentFloor.name;
          this.noOfRooms = this.data.currentFloor.noOfRooms;
        }
        if(this.modalState == 3)
        {
          this.floorName = '';
          this.noOfRooms = '';
        }
        if(this.modalState == 6)
        {
          this.RoomName = this.data.currentRoom.name;
          this.noOfSeats = this.data.currentRoom.noOfSeats;
          this.coords = this.data.currentRoom.coords;
          this.shape = this.data.currentRoom.shape;
          this.status = this.data.currentRoom.status;
        }
        if(this.modalState == 5)
        {
          this.RoomName = '';
          this.noOfSeats = '';
          this.coords = '';
          this.shape = '';
          this.status = '';
        }
      },
    },

	methods: {
    closeDialog: function() {
      this.data.dialog = false;
      this.data.deleteBuildingConfirmation = false;
      this.$emit('reset-state'); // for watch
    },

    deleteBuilding: function() {
      this.data.deleteBuildingConfirmation = false;
      this.data.dialog = false;
      this.$emit('delete-confirmed');
    },

    stateVerify: function() {
      if(this.modalState == 1)
      {
        this.addBuilding();
      }
      if(this.modalState == 2)
      {
        this.updateBuilding();
      }
      if(this.modalState == 3)
      {
        this.addFloor();
      }
      if(this.modalState == 4)
      {
        this.updateFloor();
      }
      if(this.modalState == 5)
      {
        this.addRoom();
      }
      if(this.modalState == 6)
      {
        this.updateRoom();
      }
    },

  	wrongInput: function(e) {
  	    let errorStatus = '';
  	    if(e.response.data.errors != undefined)
  	    {
  	    	for(let error in e.response.data.errors)
  			    {
  			      errorStatus += e.response.data.errors[error][0] + '\n';
  			    }
  	     	this.errorAlert('', errorStatus, false);
  	    }
      },

  	addBuilding: function() {
      if(this.buildingName && this.noOfFloors)
      {
            this.data.dialog = false;
            this.axios
            .post('/admin/store', {
              buildingName: this.buildingName,
              noOfFloors: this.noOfFloors,
            })
            .then(response => {
              response.data.building.floors = [];
              if(this.buildings.push(response.data.building))
                this.successAlert('Building added','', true);
              this.buildingName = '';
              this.noOfFloors = '';
            })
            .catch(e => {
              this.wrongInput(e);
            })
            this.$emit('reset-state');
      }
      else
      {
        this.valid = false;
      }

         
      },

    addFloor: function() {
        if(this.floorName && this.noOfRooms)
        {
          this.data.dialog = false;
          this.axios
          .post('/admin/floors/store', {
            floorName: this.floorName,
            noOfRooms: this.noOfRooms,
            currentBuilding: this.currentBuilding,
          })
          .then(response => {
              response.data.floor.rooms = [];
              if(this.currentBuilding.floors.push(response.data.floor))
              {
                  this.successAlert('Floor added','', true);
              }
              this.floorName = '';
              this.noOfRooms = '';
          })
          .catch(e => {
            this.wrongInput(e);

          })
          this.$emit('reset-state');
        }
        else
        {
          this.valid = false;
        }
        
      },

    addRoom: function() {
      if(this.RoomName && this.noOfSeats && this.coords && this.status && this.shape)
      {
        this.data.dialog = false;
        this.axios
        .post('/admin/rooms/store', {
          RoomName: this.RoomName,
          noOfSeats: this.noOfSeats,
          coords: this.coords,
          status: this.status,
          shape: this.shape,
          currentFloor: this.data.currentFloor,
        })
        .then(response => {
          if(this.data.currentFloor.rooms.push(response.data.room))
              this.successAlert('Room added','', true);

          this.RoomName = '';
          this.noOfSeats = '';
          this.coords = '';
        })
        .catch(e => {
          this.wrongInput(e);
        })
        this.$emit('reset-state');
      }
      else
      {
        this.valid = false;
      }
      
      },

    updateBuilding: function() {
      if(this.buildingName && this.noOfFloors)
      {
        this.data.dialog = false;
        this.axios
        .post('/admin/building/update', {
          noOfFloors: this.noOfFloors,
          buildingName: this.buildingName,
          currentBuilding: this.currentBuilding,
        })
        .then(response => {
          if(response.data.history)
          {
            this.currentBuilding.name = this.buildingName;
            this.currentBuilding.noOfFloors = this.noOfFloors;
            this.successAlert('Building updated','', true);
          }
            
        })
        .catch(e => {
          this.wrongInput(e);
        })
        this.$emit('reset-state');
      }
      else
      {
        this.valid = false;
      }
    },

    updateFloor: function() {
      if(this.floorName && this.noOfRooms)
      {
        this.data.dialog = false;
        this.axios
        .post('/admin/floor/update', {
          floorName: this.floorName,
          noOfRooms: this.noOfRooms,
          currentFloor: this.data.currentFloor,
        })
        .then(response => {
          console.log(response)
          if(response.data.history)
            {
              this.data.currentFloor.name = this.floorName;
              this.data.currentFloor.noOfRooms = this.noOfRooms;
              this.successAlert('Floor updated','', true);
            }
        })
        .catch(e => {
          console.log('response')
          this.wrongInput(e);
        })
        this.$emit('reset-state');
      }
      else
      {
        this.valid = false;
      }
      
    },

    updateRoom: function() {
      if(this.RoomName && this.noOfSeats && this.coords && this.status && this.shape)
      {
        this.data.dialog = false;
        this.axios
        .post('/admin/room/update', {
          RoomName: this.RoomName,
          noOfSeats: this.noOfSeats,
          coords: this.coords,
          status: this.status,
          shape: this.shape,
          currentRoom: this.data.currentRoom,
        })
        .then(response => {
          if(response.data.history)
            {
              this.data.currentRoom.name = this.RoomName;
              this.data.currentRoom.noOfSeats = this.noOfSeats;
              this.data.currentRoom.coords = this.coords;
              this.data.currentRoom.status = this.status;
              this.data.currentRoom.shape = this.shape;
              this.data.currentRoom = response.data.history;
            }
            this.successAlert('Room updated','', true);
        })
        .catch(e => {
          this.wrongInput(e);
        })
        this.$emit('reset-state');
      }
      else
      {
        this.valid = false;
      } 
    }
  },

}
	
	
</script>