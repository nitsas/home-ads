function addToFavorites(username, aggeliaID)
// Adds ad with id aggeliaID to username's favorites.
{
  // the parameters we'll send to the php script via url
  var params = 'what=addToFavorites&username=' + username;
  params += '&aggeliaID=' + aggeliaID;

  // Create an XMLHttpRequest (or equivalent) object through which we'll make the query.
  if (window.XMLHttpRequest)
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp = new XMLHttpRequest();
  else
    // code for IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      if (xmlhttp.responseText.match(/ok/)) {
        document.getElementById("favoritesButtonSpan").setAttribute('class', 'hidden');
      }
    }
  }

  xmlhttp.open("GET","/phpServeFiles/serveAdPage.php?"+params,true);
  xmlhttp.send();
}




function deleteAd(aggeliaID)
// Deletes ad with id aggeliaID.
{
  // the parameters we'll send to the php script via url
  var params = 'what=deleteAd&aggeliaID=' + aggeliaID;

  // Create an XMLHttpRequest (or equivalent) object through which we'll make the query.
  if (window.XMLHttpRequest)
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp = new XMLHttpRequest();
  else
    // code for IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

  xmlhttp.onreadystatechange = function() {
    var table = document.getElementById("theTable");
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      if (xmlhttp.responseText)
        document.write(xmlhttp.responseText);
    }
  }

  var r = confirm("Προσοχή, αν πατήσετε ΟΚ η αγγελία θα διαγραφεί οριστικά!");
  if (r == true) {
    xmlhttp.open("GET","/phpServeFiles/serveAdPage.php?"+params,true);
    xmlhttp.send();
  }
}




function addParoxi(aggeliaID, newParoxi)
// Called when an admin wants to add a new paroxi to the ad.
{
  if (newParoxi == 'false')
    return;
  else {
    var params = 'what=addParoxi';
    params += '&aggeliaID=' + aggeliaID;
    params += '&newParoxi=' + encodeURI(newParoxi);
    
    // Create an XMLHttpRequest (or equivalent) object through which we'll make the query.
    if (window.XMLHttpRequest)
      // code for IE7+, Firefox, Chrome, Opera, Safari
      xmlhttp = new XMLHttpRequest();
    else
      // code for IE6, IE5
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

    xmlhttp.onreadystatechange = function() {
      var table = document.getElementById("theTable");
      if (xmlhttp.readyState==4 && xmlhttp.status==200) {
        if (xmlhttp.responseText)
          var td = xmlhttp.responseText.split('<separator>');
          var row = table.rows.length - 4;
          table.rows[row].firstChild.innerHTML = td[0];
          table.rows[row].lastChild.innerHTML = td[1];
      }
    }

  xmlhttp.open("GET","/phpServeFiles/serveAdPage.php?"+params,true);
  xmlhttp.send();

  }  
}




function removeParoxi(aggeliaID, eidosParoxis)
// Called when an admin wants to remove a paroxi from the ad.
{
  if (eidosParoxis == 'false')
    return;
  else {
    var params = 'what=removeParoxi';
    params += '&aggeliaID=' + aggeliaID;
    params += '&eidosParoxis=' + encodeURI(eidosParoxis);

    // Create an XMLHttpRequest (or equivalent) object through which we'll make the query.
    if (window.XMLHttpRequest)
      // code for IE7+, Firefox, Chrome, Opera, Safari
      xmlhttp = new XMLHttpRequest();
    else
      // code for IE6, IE5
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

    xmlhttp.onreadystatechange = function() {
      var table = document.getElementById("theTable");
      if (xmlhttp.readyState==4 && xmlhttp.status==200) {
        if (xmlhttp.responseText)
          var td = xmlhttp.responseText.split('<separator>');
          var row = table.rows.length - 4;
          table.rows[row].firstChild.innerHTML = td[0];
          table.rows[row].lastChild.innerHTML = td[1];
      }
    }

  xmlhttp.open("GET","/phpServeFiles/serveAdPage.php?"+params,true);
  xmlhttp.send();
  
  }
}




function changeApproved(aggeliaID)
// If isApproved == true ('1') then disapprove ad aggeliaID,
// else approve it.
{
  var params = 'what=changeApproval';
  params += '&aggeliaID=' + aggeliaID;

  // Create an XMLHttpRequest (or equivalent) object through which we'll make the query.
  if (window.XMLHttpRequest)
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp = new XMLHttpRequest();
  else
    // code for IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      var approved=/approved/;
      var disapproved=/disapproved/;
      if (xmlhttp.responseText.match(disapproved)){
        document.getElementById("approvalButton").value = "Έγκριση αγγελίας!";
      }
      else if (xmlhttp.responseText.match(approved)) {
        document.getElementById("approvalButton").value = "Σήμανση ως μη εγκεκριμένη!";
      }
    }
  }

  xmlhttp.open("GET","/phpServeFiles/serveAdPage.php?"+params,true);
  xmlhttp.send();
  
}




function changeInfo(aggeliaID)
// Gives the admin the option to change the ad's main info.
{
  var table = document.getElementById("theTable");
  var allagiButton = document.getElementById("allagiButton");
  allagiButton.value = "Άκυρο";
  func = allagiButton.onclick;
  allagiButton.onclick = function () {
                                 document.getElementById("allagiButton").value = "Αλλαγή!";
                                 document.getElementById("theTable").deleteRow(2);
                                 document.getElementById("allagiButton").onclick = func;
                              }
  var row = table.insertRow(2);
  var td0 = row.insertCell(0);
  var td1 = row.insertCell(1);
  td0.style.backgroundColor = '#E0E0E0';
//  td1.style.backgroundColor = '#E0E0E0';

  td1HTML = '<div class="centerAlign">';
  td1HTML += '<input type="button" value="Αποδοχή" id="confirmChangeInfoButton" '
                  + 'class="button" onclick="confirmChangeInfo(' + aggeliaID + ')" />';
  td1HTML += '</div>';
  td1.innerHTML = td1HTML;

  var params = 'what=changeInfo&aggeliaID=' + aggeliaID;

  // Create an XMLHttpRequest (or equivalent) object through which we'll make the query.
  if (window.XMLHttpRequest)
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp = new XMLHttpRequest();
  else
    // code for IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      td0.innerHTML = xmlhttp.responseText;
    }
  }

  xmlhttp.open("GET","/phpServeFiles/serveAdPage.php?"+params,true);
  xmlhttp.send();
  
}




function confirmChangeInfo(aggeliaID)
// Sends the new info for ad aggeliaID to the database.
{
  // First get (and check) all the info.
  var aggeliaType = document.getElementById("aggeliaType").value;
  var categoryID = document.getElementById("categoryID").value;
  var yearMade = parseInt(document.getElementById("yearMade").value);
  if (isNaN(yearMade) || yearMade < 1901 || yearMade > 2155) 
    yearMade = false;
  var size = parseInt(document.getElementById("size").value);
  if (isNaN(size)) 
    size = false;
  var street = document.getElementById("street").value;
  var streetNumber = parseInt(document.getElementById("streetNumber").value);
  if (isNaN(streetNumber))
    streetNumber = false;
  var area = document.getElementById("area").value;
  if (area = '')
    area = false;
  var cost = parseInt(document.getElementById("cost").value);
  if (isNaN(cost))
    cost = false;

  // Get all the info in a parameters string which we'll send via url to a php script.
  var params = 'what=confirmChangeInfo';
  params += '&aggeliaID=' + aggeliaID;
  params += '&aggeliaType=' + aggeliaType;
  params += '&categoryID=' + categoryID;
  if (yearMade) 
    params += '&yearMade=' + yearMade;
  if (size)
    params += '&size=' + size;
  params += '&street=' + street;
  if (streetNumber)
    params += '&streetNumber=' + streetNumber;
  if (area)
    params += '&area=' + area;
  if (cost)
    params += '&cost=' + cost;

  // Create an XMLHttpRequest (or equivalent) object through which we'll make the query.
  if (window.XMLHttpRequest)
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp = new XMLHttpRequest();
  else
    // code for IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

  // Define what to do when the request has been processed.
  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      // Get the response.
      info = xmlhttp.responseText.split('<separator>');
      newLat = info[0];
      newLng = info[1];
      response = info[2];
      // Change the cell containing the info.
      document.getElementById("theTable").rows[1].firstChild.innerHTML = response;
      document.getElementById("allagiButton").value = 'Αλλαγή!';
      document.getElementById("allagiButton").onclick = function () { changeInfo(aggeliaID); };
      document.getElementById("theTable").deleteRow(2);
      // Update the map.
      document.getElementById("map_canvas").innerHTML = "";
      document.getElementById("hiddenLat").innerHTML = newLat;
      document.getElementById("hiddenLng").innerHTML = newLng;
      initializeMap();
    }
  }

  xmlhttp.open("GET","/phpServeFiles/serveAdPage.php?"+params,true);
  xmlhttp.send();
  

}




function addTelephone(username)
// Adds the newly given telephone to user "username" and changes the page content
// to reflect the change.
{
  // Get the given telephone from the input field (with id="newTelephone").
  var telephone = document.getElementById("newTelephone").value;
  if ( (telephone.length != 10) || (!telephone.match(/^\d{10}$/) ) ) {
    // if the given telephone is not a valid phone number
    document.getElementById("newTelephone").value = "";
    return;
  }

  // The parameters we'll send to the php script via url.
  var params = 'what=addTelephone';
  params += '&username=' + encodeURI(username);
  params += '&telephone=' + telephone;
  
  // Create an XMLHttpRequest (or equivalent) object through which we'll make the query.
  if (window.XMLHttpRequest)
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp = new XMLHttpRequest();
  else
    // code for IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      var table = document.getElementById("theTable");
      var row = table.rows.length - 3;
      table.rows[row].firstChild.innerHTML = xmlhttp.responseText;
      document.getElementById("newTelephone").value = "";
    }
  }

  xmlhttp.open("GET","/phpServeFiles/serveAdPage.php?"+params,true);
  xmlhttp.send();

}




function removeTelephone(username)
// Removes the given telephone number from a user (if it exists) and changes the
// page content to reflect the change.
{
  // Get the given telephone from the input field (with id="newTelephone").
  var telephone = document.getElementById("newTelephone").value;
  if ( (telephone.length != 10) || (!telephone.match(/^\d{10}$/) ) ) {
    // if the given telephone is not a valid phone number
    document.getElementById("newTelephone").value = "";
    return;
  }

  // The parameters we'll send to the php script via url.
  var params = 'what=removeTelephone';
  params += '&username=' + encodeURI(username);
  params += '&telephone=' + telephone;
  
  // Create an XMLHttpRequest (or equivalent) object through which we'll make the query.
  if (window.XMLHttpRequest)
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp = new XMLHttpRequest();
  else
    // code for IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      var table = document.getElementById("theTable");
      var row = table.rows.length - 3;
      table.rows[row].firstChild.innerHTML = xmlhttp.responseText;
      document.getElementById("newTelephone").value = "";
    }
  }

  xmlhttp.open("GET","/phpServeFiles/serveAdPage.php?"+params,true);
  xmlhttp.send();

}



function initializeMap()
// Initializes the map (google maps API).
{
  var thisLat = document.getElementById("hiddenLat").innerHTML;
  var thisLng = document.getElementById("hiddenLng").innerHTML;
  
  var position = new google.maps.LatLng(thisLat, thisLng);
  var mapOptions = {
    zoom: 18,
    center: position,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };

  var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);

  if (thisLat != "38.254465" &&
     thisLng != "21.7370665"      ) {
    var redIcon = new google.maps.MarkerImage("../img/redMarker.png",
      new google.maps.Size(25, 42),
      new google.maps.Point(0, 0),
      new google.maps.Point(13, 42));
    var shadow = new google.maps.MarkerImage("../img/markerShadow.png",
      new google.maps.Size(50, 45),
      new google.maps.Point(0, 0),
      new google.maps.Point(14, 45));    
    var marker = new google.maps.Marker({
      icon: redIcon,
      position: position,
      map: map,
      shadow: shadow
    });
  }
  else {
    // the address could not be geocoded (address not found)
    map.setZoom(12);
  }
}


