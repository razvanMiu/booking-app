import Vue from './config';
import 'popper.js';
import 'bootstrap/dist/js/bootstrap.min.js';
import Vuetify from 'vuetify';
 
Vue.use(Vuetify)

Vue.component('user-table', require('./components/TableComponent.vue'));
Vue.component('forms', require('./components/FormsComponent.vue'));
Vue.component('cards', require('./components/CardsComponent.vue'));

const app = new Vue({
  el: '#app',

  data: {
    dropdown: false,
    drawer: null,
    buildings: [],
    buttons: false,
    currentBuilding: {id: "razvan e prost"},
    drawerRight: false,
    formConfig: {
      currentFloor: {},
      currentRoom: {},
      dialog: false,
      modalTitle: '',
      deleteBuildingConfirmation: false,
      deleteBuilding: {},
    },
    cardConfig: {
      cardState: 0,
    },
    formOpened: true,
    modalState: 0,
  },

  mounted: function() {
    if(window.location.pathname.split('/')[1] == 'admin')
        this.fetchBuildings();
        this.drawer = !this.drawer;
  },
  
  methods: {
    resetState: function() {
      this.modalState = 0;
    },

    roomsCard: function(current) {
      if(this.cardConfig.cardState == 2)
        this.formConfig.currentFloor = current;
    },

    openAddForm: function(cardState) {
      this.formOpened = true;
      if(cardState == 1)
      {
        this.modalState = 3;
        this.formConfig.dialog = true;
        this.formConfig.modalTitle = 'Add floor';
      } 
      else
      if(cardState == 2) 
      {
        this.modalState = 5;
        this.formConfig.dialog = true;
        this.formConfig.modalTitle = 'Add room';
      }
    },

    openUpdateForm: function(cardState, roomUpdate, roomData) {
      this.formOpened = true;
      if(cardState == 1)
      {
        this.modalState = 2;
        this.formConfig.dialog = true;
        this.formConfig.modalTitle = 'Update building';
      } 
      else
        if(cardState == 2)
          if(roomUpdate == false) 
          {
            this.modalState = 4;
            this.formConfig.dialog = true;
            this.formConfig.modalTitle = 'Update floor';
          }
          else
            {
              this.formConfig.currentRoom = roomData;
              this.modalState = 6;
              this.formConfig.dialog = true;
              this.formConfig.modalTitle = 'Update room';
            }
    },
    
    floorsCard: function(building) {
      this.currentBuilding = building;
      this.cardConfig.cardState = 1;
      this.cardConfig.floorsDropdown = false;
      this.cardConfig.addBuilding = false;
    },

    addBuildingContainer: function() {
      this.modalState = 1;
      this.formConfig.dialog = true;
      this.formConfig.modalTitle = 'Add building';
    },

    fetchBuildings: function() {
      this.axios
        .get('/admin/fetch')
        .then(response => {
            this.buildings = response.data.buildings;
        })
        .catch(e => {
        })
    },

    requestDeleteConfirmation: function(building) {
      this.formOpened = true;
      this.formConfig.dialog = true;
      this.modalState = 0
      this.formConfig.deleteBuildingConfirmation = true;
      this.formConfig.deleteBuilding = building;
    },

    deleteConfirmed: function() {
      this.deleteBuilding(this.formConfig.deleteBuilding);
    },

    deleteBuilding: function(building) {
        this.axios
        .post('/admin/building/delete', {
          building: building,
        })
        .then(response => {
          if(response.data)
          { 
            this.buildings.splice(this.buildings.indexOf(building),1);
            this.successAlert(building.name + ' deleted','', true);
            this.cardConfig.cardState = 0;
          }
        })
      },
  },

 });
