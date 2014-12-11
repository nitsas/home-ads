function countAds(elemID, newVal)
// Counts the ads fulfilling the currently selected options (search page).
// Called every time an option is changed.
{
  // If this is called for the first time initialize all options' values.
  if ( (typeof firstTime) == 'undefined' ) {
    firstTime = true;               // global variable
    categoryID = false;
    aggeliaType = false;
    minCost = false;
    maxCost = false;
    minSize = false;
    maxSize = false;
    minYear = false;
    maxYear = false;
    street = false;
    streetNumber = false;
    paroxi = false;
    approved = 'true';
  }
  
  switch (elemID) {
    case 'aggeliaType':
      aggeliaType = newVal;
      break;
    case 'categoryID':
      categoryID = newVal;
      if (categoryID === 'false')
        categoryID = false;
      break;
    case 'minCost':
      if ( isNaN(minCost = parseInt(newVal)) )
        minCost = false;
      break;
    case 'maxCost':
      if ( isNaN(maxCost = parseInt(newVal)) )
        maxCost = false;
      break;
    case 'minSize':
      if ( isNaN(minSize = parseInt(newVal)) )
        minSize = false;
      break;
    case 'maxSize':
      if ( isNaN(maxSize = parseInt(newVal)) )
        maxSize = false;
      break;
    case 'minYear':
      if ( isNaN(minYear = parseInt(newVal)) )
        minYear = false;
      else if ( (minYear < 1901) || (minYear > 2155) )
        minYear = false;
      break;
    case 'maxYear':
      if ( isNaN(maxYear = parseInt(newVal)) )
        maxYear = false;
      else if ( (maxYear < 1901) || (maxYear > 2155) )
        maxYear = false;
      break;
    case 'street':
      street = new String(newVal);
      // Don't let the user type whatever he wants.
      street = street.replace(/[^a-zA-Zα-ωΑ-Ω0-9-',.άέίόύήώϊϋ]/g, "");
      street = encodeURI(street);        // encode it so that we can get it through the URL
    case 'streetNumber':
      if ( isNaN(streetNumber = parseInt(newVal)) )
        streetNumber = false;
      break;
    case 'approved':
      approved = newVal;
      break;
    case 'paroxi':
      // newVal is actually the options attibute of the select element
      paroxi = new String("");
      var index = 0;
      for (i=0; i<newVal.length; i++) {
        if (newVal[i].selected) {
          paroxi = paroxi + '&paroxi[' + index + ']=' + newVal[i].value;
          index++;
        }
      }
      break;
    default:
      return;
  }

  // Create the GET parameters string to pass to the php script.
  var params = new String("");
  if ( aggeliaType && !(aggeliaType === "dontCare") )
    params = params + '&aggeliaType=' + aggeliaType;
  if (categoryID)
    params = params + '&categoryID=' + categoryID;
  if (minCost)
    params = params + '&minCost=' + minCost;
  if (maxCost)
    params = params + '&maxCost=' + maxCost;
  if (minSize)
    params = params + '&minSize=' + minSize;
  if (maxSize)
    params = params + '&maxSize=' + maxSize;
  if (minYear)
    params = params + '&minYear=' + minYear;
  if (maxYear)
    params = params + '&maxYear=' + maxYear;
  if (street)
    params = params + '&street=' + street;
  if (streetNumber)
    params = params + '&streetNumber=' + streetNumber;
  if (approved)
    params = params + '&approved=' + approved;
  if (paroxi) 
    params = params + paroxi;

  if (params.charAt(0) == '&')
    params = params.slice(1);
  
  // Make an XMLHttpRequest (or equivalent) object through which we'll make the query.
  if (window.XMLHttpRequest)
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp = new XMLHttpRequest();
  else
    // code for IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      document.getElementById("numAds").innerHTML=xmlhttp.responseText;
    }
  }

  xmlhttp.open("GET","/phpServeFiles/serveAjaxOnSearchPage.php?"+params,true);
  xmlhttp.send();
}



