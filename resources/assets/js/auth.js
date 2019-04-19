import Vue from './config';
import Vuetify from 'vuetify';

Vue.use(Vuetify);

Vue.component('login-form', require('./components/LoginComponent.vue'));
Vue.component('register-form', require('./components/RegisterComponent.vue'));

const app = new Vue({
	el: '#root',

	data: {
		/*
		 * Authentication data
		 */
		'loginActive': true,
		'registerActive': false,
	},

	methods: {
		/*
		 * Toggle between login and register forms
		 */
		toggleAuthForm: function() {
			this.loginActive = ! this.loginActive;
			this.registerActive = ! this.registerActive;
		},
	}
});