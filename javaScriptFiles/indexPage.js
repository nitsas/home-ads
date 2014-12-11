function initializeMap() 
{
  var position = new google.maps.LatLng(38.2504650, 21.7370665);
  var mapOptions = {
    zoom: 13,
    center: position,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };

  var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);

  addTheMarkers(map); 
}




function addTheMarkers(map)
{
  // Get the latitude and longitude of each ad (top 5 ads and 5 most recent ads).
  // The lat and lng have been printed in hidden spans inside the html.
  var topLatLngs = new Array();
  for (var i=1; i<6; i++) {
    lat = document.getElementById("map_lat_top" + i).innerHTML;
    lng = document.getElementById("map_lng_top" + i).innerHTML;
    if (lat != "NULL" && lng != "NULL")
      topLatLngs.push( new google.maps.LatLng(lat, lng) );
  }
  var recentLatLngs = new Array();
  for (i=1; i<6; i++) {
    lat = document.getElementById("map_lat_recent" + i).innerHTML;
    lng = document.getElementById("map_lng_recent" + i).innerHTML;
    if (lat != 'NULL' && lng != 'NULL')
      recentLatLngs.push( new google.maps.LatLng(lat, lng) );
  }

  // Create variables holding the marker icons:
  var redIcon = new Array();
  for (i=1; i<6; i++)
    redIcon[i] = new google.maps.MarkerImage("../img/redMarker" + i + ".png",
      new google.maps.Size(25, 42),
      new google.maps.Point(0, 0),
      new google.maps.Point(13, 42));
  var blueIcon = new Array();
  for (i=1; i<6; i++)
    blueIcon[i] = new google.maps.MarkerImage("../img/blueMarker" + i + ".png",
      new google.maps.Size(25, 42),
      new google.maps.Point(0, 0),
      new google.maps.Point(13, 42));
  var shadow = new google.maps.MarkerImage("../img/markerShadow.png",
    new google.maps.Size(50, 45),
    new google.maps.Point(0, 0),
    new google.maps.Point(14, 45));    

  // Add the markers to the map (create them).
  var marker = new Array();
  for (i=0; i<topLatLngs.length; i++)
    // the top ads markers (red)
    marker.push(new google.maps.Marker({ 
      position: topLatLngs[i], map: map, icon: redIcon[i+1], title: "top " + (i+1), shadow: shadow
    }));
  for (i=0; i<recentLatLngs.length; i++)
    // the recent ads markers (blue)
    marker.push(new google.maps.Marker({ 
      position: recentLatLngs[i], map: map, icon: blueIcon[i+1], title: "recent " + (i+1), 
      shadow: shadow
    }));
}
