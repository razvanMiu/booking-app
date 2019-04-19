<template>
	<div>
		<div class="email-container">
			<div style="max-height: 120px; overflow: auto; margin-bottom: 40px;">
				<v-chip 
					v-for="(email, index) in emails" 
					:key="email"
					@input="removeEmail(index)"
					small
					close>
					{{ email }}
				</v-chip>
			</div>

	          <v-text-field
	          	v-model="currentEmail" 
				label="Add emails..." 
				:rules="[rules.email]"
				@keyup="key">

	          </v-text-field>
		</div>
	</div>	
</template>

<script>
	export default {
		data() {
			return {
				currentEmail: '',
				emailRegExp: /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
				rules: {
					email: value => {

						return this.emailRegExp.test(value) || 'Invalid email';
					}
				}
			}
		},

		props: {
			participants: Array,
			emails: Array,
		},

		methods: {
			key(event) {
				if(event.keyCode == 13) {	
					this.addEmail();
				}
			},

			addEmail: function() {
				if(!this.checkDuplicateEmail()) {
					if(this.validEmail(this.currentEmail) && this.currentEmail) {
						this.emails.push(this.currentEmail);
						this.resetEmailInput();
					}
				} else {
					this.resetEmailInput();
				}
			},

			checkDuplicateEmail: function() {
				let mailSent = false;
				for(let i = 0; i < this.participants.length; i++) {
					if(this.currentEmail == this.participants[i].email) {
						mailSent = true;
						break;
					}
				}

				if(mailSent) {
					this.errorAlert('You already sent an email to this person');

					return true;
				} else if(this.emails.indexOf(this.currentEmail) > -1) {
					this.errorAlert('Email already in the list');

					return true;
				}

				return false;
			},

			validEmail: function (email) {

				return this.emailRegExp.test(email);
			},

			removeEmail: function(index) {
				this.emails.splice(index, 1);
			},

			resetEmailInput: function() {
				this.rules.email = value => {
					return true;
				}

				setTimeout(() => {
					this.rules.email = value => {

						return this.emailRegExp.test(value) || 'Invalid email';
					}
				}, 100);

				this.currentEmail = '';
			}
		}, 
	}
</script>