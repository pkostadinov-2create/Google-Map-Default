<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>Google Map Exaple</title>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?v=3.8&amp;sensor=false"></script>
	<style>
		.google-map { display: block; width: 500px; height: 500px; margin-bottom: 20px; }
	</style>
	<script>
		;(function($, window, document, undefined) {
			var $doc = $(document);
			var maps_container = '.google-map';
			var map = []; // Contains all maps that will be created

			$doc.ready(function() {
				googleMap(maps_container);
			});

			// Generate Map From Address or Coordinates
			function googleMap(container){
				$(container).each(function(){
					var $map_container = $(this);
					var _lat = $map_container.data('lat');
					var _lng = $map_container.data('lng');
					var _coordinates = $map_container.data('coordinates');
					var _address = $map_container.data('location');
					var _zoom = $map_container.data('zoom');
					var _id = $map_container.attr('id');
					var _pins = $.parseJSON(urlDecode($map_container.data('pins')));

					// Set default Zoom
					if ( typeof _zoom == 'undefined' ) {
						_zoom = 16;
					}

					if ( typeof _address != 'undefined' ) {
						var geocoder = new google.maps.Geocoder();
						geocoder.geocode({ address: _address }, function(result, status) {
							if (status == 'OK') {
								var loc = result[0].geometry.location;
								drawMap( _id, loc, _zoom );
							}
						});
					} else if ( typeof _coordinates != 'undefined' ) {
						var _coordinates_array = _coordinates.split(',');
						var loc = new google.maps.LatLng(_coordinates_array[0], _coordinates_array[1]);
						drawMap( _id, loc, _zoom );
					} else if ( typeof _lat != 'undefined' && typeof _lng != 'undefined' ) {
						var loc = new google.maps.LatLng(_lat, _lng);
						drawMap( _id, loc, _zoom );
					} else if ( typeof _pins != 'undefined' ) {
						drawMap( _id, '', _zoom );
						drawMarkers( _id, _pins );
					};
				});
			};

			// Draw the map
			function drawMap(_id, loc, _zoom) {
				// Custom Pin
				/*
				// This value is passed from the wordpress, for correct image load.
				var dir = php_passed_variables['stylesheet_directory'];
				var image = new google.maps.MarkerImage( dir + '/images/pin.png',
					new google.maps.Size(48, 64),
					new google.maps.Point(0, 0),
					new google.maps.Point(20, 64)
				);
				*/

				var args = {
					zoom: _zoom,
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					center: loc,
					panControl: false,
					zoomControl: false,
					mapTypeControl: false,
					scaleControl: false,
					streetViewControl: false,
					overviewMapControl: false
				};

				if ( loc !== '' ) {
					$.extend(true, args, {
						center: loc,
					});
				}

				map[_id] = new google.maps.Map(document.getElementById(_id), args);

				if ( loc !== '' ) {
					var marker = new google.maps.Marker({ 
						map: map[_id], 
						position: loc,
						// icon: image
					});
				};
			};

			// Draw markers on the map
			function drawMarkers(_id, _pins) {
				// Custom Pin
				/*
				// This value is passed from the wordpress, for correct image load.
				var dir = php_passed_variables['stylesheet_directory'];
				var image = new google.maps.MarkerImage( dir + '/images/pin.png',
					new google.maps.Size(48, 64),
					new google.maps.Point(0, 0),
					new google.maps.Point(20, 64)
				);
				*/

				var bounds = new google.maps.LatLngBounds();
				var infoWindowses = [];
				var markers = [];

				for (var i = 0; i < _pins.length; i++) {
					var pin = _pins[i];
					var loc = new google.maps.LatLng(pin.lat, pin.lng);

					// Initialize Pin
					markers[i] = new google.maps.Marker({ 
						map: map[_id], 
						title: pin.title,
						position: loc,
						// icon: image
					});

					// Add the current pin to the collection for centering the map
					bounds.extend(markers[i].position);

					// Initialize infoWindow
					infoWindowses[i] = new google.maps.InfoWindow({
						content: pin.title
					});

					// Allow each marker to have an info window    
					google.maps.event.addListener(markers[i], 'click', (function(marker, i) {
						return function() {
							$.each(infoWindowses, function(i, val) {
								this.close(map[_id], markers[i]);
							});

							infoWindowses[i].setContent('<h4>' + _pins[i].title + '</h4>');
							infoWindowses[i].open(map[_id], marker);
						}
					})(markers[i], i));
				};

				map[_id].fitBounds(bounds);
			};

			// Regenerate the map, to be used inside accordions, or other elements, where there is an issue with reinitialization
			function reDrawMaps(container){
				$(container).each(function(){
					var _id = $(this).attr('id');
					var center = map[_id].getCenter();
					google.maps.event.trigger(map[_id],'resize')
					map[_id].setCenter(center);
				});
			};

			// Regenerate the map on window resize
			// google.maps.event.addDomListener(window, 'resize', function() {
			// 	reDrawMaps(maps_container);
			// });

			urlDecode = function(str) {
				return decodeURIComponent((str + '')
					.replace(/%(?![\da-f]{2})/gi, function() {
						// PHP tolerates poorly formed escape sequences
						return '%25';
					})
					.replace(/\+/g, '%20'));
			}
		})(jQuery, window, document);
	</script>
</head>
<body>
	<div id="map-1" class="google-map google-map-1" data-location="Varna ul Naptun 8" data-zoom="10"></div><!-- /#map.google-map-1 -->
	<div id="map-2" class="google-map google-map-2" data-coordinates="43.225773,27.8511866" data-zoom="13"></div><!-- /#map.google-map-2 -->
	<div id="map-3" class="google-map google-map-3" data-lat="43.225773" data-lng="27.8511866" data-zoom="16"></div><!-- /#map.google-map-3 -->

	<?php
	$pins = array(
		array( 'lat' => '34.2325893', 'lng' => '-86.24783179999997', 'title' => 'Albertville' ),
		array( 'lat' => '32.9440120', 'lng' => '-85.95385320000003', 'title' => 'Alexander City' ),
		array( 'lat' => '31.1051779', 'lng' => '-87.07219179999998', 'title' => 'Brewton' ),
		array( 'lat' => '33.1028965', 'lng' => '-86.75359750000001', 'title' => 'Calera' )
	);
	?>

	<div id="map-4" class="google-map google-map-4" data-pins="<?php echo urlencode(json_encode($coordinates)); ?>"></div><!-- /#map.google-map-4 -->
</body>
</html>