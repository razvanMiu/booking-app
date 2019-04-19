<v-toolbar app v-if="user.name">
	<v-btn fab dark color="teal" href="/" class="home-button">
		<v-icon>home</v-icon>
	</v-btn>

	<v-toolbar-title>@{{user.name}}</v-toolbar-title>
	<v-spacer></v-spacer>
	<v-toolbar-items class="hidden-sm-and-down">	
		<v-btn flat href="/admin" v-if="user.type == 'admin'">Dashboard</v-btn>
		<v-btn flat href="/myBookings">My bookings</v-btn>
		<v-btn flat href="/logout" @click="loader = true">Logout</v-btn>
	</v-toolbar-items>
	<v-btn fab dark color="teal" class="hidden-md-and-up" @click.stop="drawerRight = !drawerRight">
		<v-icon>menu</v-icon>
	</v-btn>
</v-toolbar>


<v-navigation-drawer
	fixed
	v-model="drawerRight"
	right
	clipped
	disable-resize-watcher
	app
	v-show="!calendar"
	>
	<v-list dense>
		<v-list-tile>
			<v-btn flat href="/admin" v-if="user.type == 'admin'">Dashboard</v-btn>
		</v-list-tile>

		<v-list-tile>
			<v-btn flat href="/myBookings">My bookings</v-btn>
		</v-list-tile>

		<v-list-tile>
			<v-btn flat href="/logout">Logout</v-btn>
		</v-list-tile>
	</v-list>
</v-navigation-drawer>


{{-- <v-dialog
    v-model="loader"
    hide-overlay
    persistent
    width="300">

	<v-card
		color="primary"
		dark>
		<v-card-text>
			Please stand by
			<v-progress-linear
				indeterminate
				color="white"
				class="mb-0">
					
			</v-progress-linear>
		</v-card-text>
    </v-card>
		
</v-dialog> --}}
