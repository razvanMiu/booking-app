<v-navigation-drawer
  fixed
  class="white"
  app
  v-model="drawer"
>
  <v-list 
  dense 
  >
  <v-list-tile>Buildings</v-list-tile>
  <v-divider
  dark
  class="my-3"
  >
    
  </v-divider>
  <v-list-tile @click="addBuildingContainer">
    <v-list-tile-action>
        <v-icon>add</v-icon>
  </v-list-tile-action>
    <v-list-tile-content>
      Add building
    </v-list-tile-content>
  </v-list-tile>
    <v-list-tile v-for="(building, index) in buildings" @click="">
      <v-list-tile-content>
        <v-list-tile-title @click="floorsCard(building)">@{{building.name}}</v-list-tile-title>
      </v-list-tile-content>
      <v-list-tile-action @click="requestDeleteConfirmation(building)">
        <v-icon>delete</v-icon>
      </v-list-tile-action>
  </v-list>
</v-navigation-drawer>
<v-toolbar color="grey darken-2" dark fixed app>
  <v-toolbar-side-icon @click.stop="drawer = !drawer">
    <v-icon>@{{ drawer ? 'keyboard_arrow_left' : 'keyboard_arrow_right'}}</v-icon>
  </v-toolbar-side-icon>
  
  <v-toolbar-title>Dashboard</v-toolbar-title>
    <v-toolbar-items class="hidden-sm-and-down">
      <v-btn flat href="/">
        BookingApp
      </v-btn>
    </v-toolbar-items>
    <v-spacer></v-spacer>
      <v-toolbar-items class="hidden-sm-and-down">
        <v-btn flat href="/admin">
          <v-icon>home</v-icon>
        </v-btn>
        <v-btn flat href="/admin/history">
          <v-icon>history</v-icon>
        </v-btn>
        <v-btn flat href="/logout">
          <v-icon>logout</v-icon>
        </v-btn>
      </v-toolbar-items>
      <v-btn fab dark color="grey darken-2" class="hidden-md-and-up" @click.stop="drawerRight = !drawerRight">
        <v-icon>menu</v-icon>
      </v-btn>
</v-toolbar>

<v-navigation-drawer
  fixed
  class="grey lighten-4"
  right
  clipped
  app
  disable-resize-watcher
  v-model="drawerRight"
>
<v-list dense>
        <v-list-tile>
            <v-btn flat href="/admin">Home</v-btn>
        </v-list-tile>
        <v-list-tile>
            <v-btn flat href="/admin/history">History</v-btn>
        </v-list-tile>
        <v-list-tile>
            <v-btn flat href="/logout">Logout</v-btn>
        </v-list-tile>
    </v-list>
</v-navigation-drawer>
