@extends ('layouts.master')

@section ('style')

	@include ('layouts.style')

@endsection

@section ('content')
	
	@include ('layouts.nav')

	<v-content>
		<v-container fluid fill-height justify-center wrap>
			<div class="overview-map">
				<div class="overview-floor">
					<img src="/images/BSB_overview_map.png" usemap="#image-map">

					<map name="image-map">
					    <area target="" alt="Crawford House" title="C1 Building" href="/building/5b87fa779a8920090938a283" coords="163,440,161,462,173,461,174,442,187,442,188,426,201,429,203,412,197,412,199,405,202,397,202,388,208,395,206,379,199,361,189,352,177,344,164,344,163,352,152,354,144,365,140,372,141,383,141,397,141,410,141,423,147,425,148,438" shape="poly">
					    <area target="" alt="Newland House" title="C2 Building" href="/building/5b87fa849a892009f131b812" coords="266,450,284,452,282,475,292,474,294,453,320,456,324,389,320,390,324,351,310,350,301,339,294,339,290,346,286,366,282,393,277,400,273,408,274,426" shape="poly">
					</map>
				</div>
			</div>
		</v-container>
	</v-content>

@endsection

@section ('script')
	
	@include ('layouts.footer')

@endsection
