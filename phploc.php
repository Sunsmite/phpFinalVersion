<!DOCTYPE html>
<html lang="en">
<head>
  
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP geocode</title>
</head>
<body>
<?php 
/* https://www.codeofaninja.com/2014/06/google-maps-geocoding-example-php.html - code reference */
/* https://developers.google.com/maps/documentation/javascript/examples/icon-simple - another reference from Google */
function geocode($address){
    $address = urlencode($address);
    $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=AIzaSyC225NdrdbKkoa3EBaPRC5Hk1Upv0c3Gwk";
    $resp_json = file_get_contents($url);
    $resp = json_decode($resp_json, true);
    if($resp['status']=='OK'){
        $lati = isset($resp['results'][0]['geometry']['location']['lat']) ? $resp['results'][0]['geometry']['location']['lat'] : "";
        $longi = isset($resp['results'][0]['geometry']['location']['lng']) ? $resp['results'][0]['geometry']['location']['lng'] : "";
        $formatted_address = isset($resp['results'][0]['formatted_address']) ? $resp['results'][0]['formatted_address'] : "";
        if($lati && $longi && $formatted_address){
            $data_arr = array();            
            array_push(
                $data_arr, 
                    $lati, 
                    $longi, 
                    $formatted_address
                );
            return $data_arr;
        }else{
            return false;
        }
    }
    else{
        echo "<strong>ERROR: {$resp['status']}</strong>";
        return false;
    }
}
?>
  <form action="" method="post">
    <input type='text' name='address' placeholder='Enter your address here'/>
    <input type='submit' value='Get a value' />
</form>
<button id="info" onclick="info()">Info</button>
<button id="parking" onclick="parking()">Parking</button>
<button id="blue" onclick="blue()">Blue</button>
<button id="green" onclick="green()">Green</button>
</body>
<?php
if($_POST){
    $data_arr = geocode($_POST['address']);
    if($data_arr){
        $latitude = $data_arr[0];
        $longitude = $data_arr[1];
        $formatted_address = $data_arr[2];
                     
    ?>
    <div id="gmap_canvas" style="width:500px;height:500px;">mapField</div>
    <script type="text/javascript" src="https://maps.google.com/maps/api/js?key=AIzaSyC225NdrdbKkoa3EBaPRC5Hk1Upv0c3Gwk"></script>   
    <script type="text/javascript">
        function init_map() {
            var myOptions = {
                zoom: 14,
                center: new google.maps.LatLng(<?php echo $latitude; ?>, <?php echo $longitude; ?>),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            map = new google.maps.Map(document.getElementById("gmap_canvas"), myOptions);
            marker = new google.maps.Marker({
                map: map,
                position: new google.maps.LatLng(<?php echo $latitude; ?>, <?php echo $longitude; ?>)
            });
            infowindow = new google.maps.InfoWindow({
                content: "<?php echo $formatted_address; ?>"
            });
            google.maps.event.addListener(marker, "click", function () {
                infowindow.open(map, marker);
            });
            infowindow.open(map, marker);
        }
        google.maps.event.addDomListener(window, 'load', init_map);
		google.maps.event.addDomListener(window, 'load', initMap); 

	function parking(){ 
		var image = { 
		url: 'parking_lot.png', 
		scaledSize: new google.maps.Size(30, 30) 
	}; 
	marker.setIcon(image); 
	} 

	function info(){ 
		var image = { 
		url: 'info-i.png', 
		scaledSize: new google.maps.Size(30, 30) 
	}; 
	marker.setIcon(image); 
	} 
	function green(){ 
		marker.setIcon('http://maps.google.com/mapfiles/ms/icons/green-dot.png'); 
	} 
	function blue(){ 
		marker.setIcon('http://maps.google.com/mapfiles/ms/icons/blue-dot.png'); 
	} 

    </script>
 
    <?php
    }else{
        echo "No map found.";
    }
}
?>
</html>