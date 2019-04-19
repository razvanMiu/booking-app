<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>BSB Booking</title>

    @include ('admin.layouts.style')
    
    @yield ('style')
  </head>
  <body>

    <div id="app" v-cloak>
      <v-app id="inspire">
        <div class="container">
          <v-toolbar color="grey darken-2" dark fixed app>
          
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
        </v-toolbar>
        <v-content>
        <v-container fluid>
          <user-table>
          </user-table>                    
          </v-container>
        </v-content>
        </div>
      </v-app>
    </div>

    @include ('admin.layouts.footer')
    
  </body>
</html>