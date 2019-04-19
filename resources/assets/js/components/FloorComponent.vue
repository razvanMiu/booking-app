 <template>
	<div class="overview-floor" :style="{width: computedWidth + '%', 'padding-bottom': computedHeight + 'px', float: 'left'}">

		<v-container v-if="building.floors.length == 0" justify-center>
			<v-btn fab loading></v-btn>
		</v-container>
		
		<img id="overview-image" v-else usemap="#image-map"
			:src="imageSrc"
			:style="{width: computedWidth + '%'}"
			v-resize="onResize"
			@load="imgLoaded">	
			

		<div class="roomsContainer" v-if="building.floors.length > 0 && loaded"> 
			<transition
				enter-active-class="animated fadeIn"
    			leave-active-class="animated fadeOut">
				<div v-show="roomsLoaded"> 
					<img class="room-image" v-for="room in building.floors[building.selectedFloor].rooms" 
						:src="availableRoomSrc + room.name + '.png'" 
						:style="{width: computedWidth + '%'}"
						@load="roomLoaded">
				</div>
			</transition>
		</div>

		<map v-if="building.floors.length > 0 && roomsLoaded" name="image-map">
			<area v-for="room in building.floors[building.selectedFloor].rooms" 
				:coords="room.coords" 
				:shape="room.shape" 
				:title="room.name" 
				:alt="room.name" 
				@click="$emit('open-calendar', room)">
		</map>
	</div>
</template>

<script>
	export default {
		data: function() {
			return {
				loaded: 		false,
				roomsLoaded: 	false,
				roomsContor: 	0,
				/*
				 * Floor and rooms images have absolute position
				 * So for making them responsive I've changed the width
				 * With this formula: (1 - window.innerWidth / 3000) * 100;
				 */
				computedWidth: null,
				/*
				 * The container of the floor and rooms images needed to have 
				 * The same width and height as the images.
				 * For height I added padding-bottom equal with floor image height
				 */
				computedHeight: 100,
			}
		},

		props: {
			building: Object,
		},

		computed: {
			/*
			 * Get the path that contains .png files for every floor
			 */
			floorPath: function () {

				return this.PATH_BUILDINGS + this.building.name + '/';
			},
			/*
			 * Get .png file for selected floor
			 */
			imageSrc: function () {
				this.loaded = false;

				return this.floorPath + this.building.floors[this.building.selectedFloor].name + '.png';
			},
			/*
			 * Get the path that contains .png files with every available room
			 */
			availableRoomSrc: function () {

				return this.PATH_ROOMS + this.building.name + '/available/'
			},
		},

		methods: {
			/*
			 * Called after floor image has been loaded
			 */
			imgLoaded: function () {
				this.computedHeight = $('#overview-image').height();
				this.roomsContor 	= 0;
				this.roomsLoaded 	= false;
				this.loaded 		= true;	

				this.$emit('image-loaded');
			},

			roomLoaded: function() {
				this.roomsContor++;

				if(this.roomsContor == this.building.floors[this.building.selectedFloor].rooms.length) {
					this.roomsLoaded = true;
					setTimeout(() => {
						$(function () {
							ImageMap('img[usemap]');
						});
					}, 100);
				}
			},
			/*
			 * Makes absolute image responsive
			 */
			onResize: function() {
				if($('#overview-image').height() > 0) {
					this.computedHeight = $('#overview-image').height();
				} else {
					this.computedHeight = window.innerHeight;
				}

				this.computedWidth = (1 - window.innerWidth / 3000) * 100;
			}
		}
	}
</script>