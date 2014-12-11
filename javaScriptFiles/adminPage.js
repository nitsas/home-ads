function deletE(what, id, tableRow)
// Deletes the paroxi (if what=='paroxi') or category (if what=='category')
// with the given id.
{
  // Create the parameters to send to the php script via url.
  if (what === 'paroxi') {
    var params = 'what=deleteParoxi&id=' + id;
  }
  else if (what === 'category') {
    var params = 'what=deleteCategory&id=' + id;
  }
  else {
    return false;
  }

  // Create an XMLHttpRequest (or equivalent) object through which we'll make the query.
  if (window.XMLHttpRequest)
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp = new XMLHttpRequest();
  else
    // code for IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

  // Define what will be done when the php script responds back.
  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      if (xmlhttp.responseText.match(/ok/)) {
        document.getElementById("theTable").deleteRow(tableRow);
      }
    }
  }

  xmlhttp.open("GET","/phpServeFiles/serveAdminPage.php?"+params,true);
  xmlhttp.send();
}




function rename(what, id, row)
// Renames paroxi (if what=='paroxi') or category (if what=='category') with the given id.
{
  if (what === 'paroxi') {
    var name = document.getElementById("name"+id).value;
    var params = 'what=renameParoxi&id=' + id + '&name=' + encodeURI(name);
  }
  else if (what === 'category') {
    var name = document.getElementById("name"+id).value;
    var params = 'what=renameCategory&id=' + id + '&name=' + encodeURI(name);
  }
  else
    return false;

  // Create an XMLHttpRequest (or equivalent) object through which we'll make the query.
  if (window.XMLHttpRequest)
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp = new XMLHttpRequest();
  else
    // code for IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

  // Define what will be done when the php script responds back.
  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      if (xmlhttp.responseText.match(/ok/)) {
        if (what === 'paroxi') 
          document.getElementById("paroxi"+id).innerHTML = name;
        else if (what === 'category') 
          document.getElementById("category"+id).innerHTML = name;
        else 
          return false;
        document.getElementById("name"+id).value = "";
      }
      else if (xmlhttp.responseText.match(/deleted/)) {
        var destinationID = xmlhttp.responseText.match(/\d+/);
        var sourceAdCount = parseInt(document.getElementById("adCount"+id).innerHTML);
        var destinationSpan = document.getElementById("adCount"+destinationID); 
        var destinationAdCount = parseInt(destinationSpan.innerHTML);
        destinationSpan.innerHTML = sourceAdCount + destinationAdCount;
        document.getElementById("theTable").deleteRow(row);
      }
    }
  }

  xmlhttp.open("GET","/phpServeFiles/serveAdminPage.php?"+params,true);
  xmlhttp.send();
}




function create(what)
// Adds a new paroxi (if what='paroxi') or category (if what='category') with the given name.
{
  if (what === 'paroxi') {
    var name = document.getElementById("newParoxi").value;
    var params = 'what=newParoxi&name=' + encodeURI(name);
  }
  else if (what === 'category') {
    var name = document.getElementById("newCategory").value;
    var params = 'what=newCategory&name=' + encodeURI(name);
  }
  else
    return false;

  // Create an XMLHttpRequest (or equivalent) object through which we'll make the query.
  if (window.XMLHttpRequest)
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp = new XMLHttpRequest();
  else
    // code for IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

  // Define what will be done when the php script responds back.
  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      if (xmlhttp.responseText.match(/ok/)) {
        var id = xmlhttp.responseText.match(/\d+/);
        var table = document.getElementById("theTable");
        var row = table.insertRow(table.rows.length);
        var td0 = row.insertCell(0);
        var td1 = row.insertCell(1);
        var td2 = row.insertCell(2);
        if (what === 'paroxi') {
          td0.innerHTML = '<span id="paroxi' + id + '">' + name + '</span>';
          td1.innerHTML = '<input type="button" value=" - " onclick="deletE(\'paroxi\', ' + 
                               id + ', ' + (table.rows.length-1) + ')" />';
          td2.innerHTML = '<input type="text" id="name' + id + '" size="20" ' + 
                               'maxlength="25" /> &nbsp; ' + 
                               '<input type="button" class="button" value="ok" ' + 
                               'onclick="rename(\'paroxi\', ' + id + ', ' + (table.rows.length-1) + ')" />';
          document.getElementById("newParoxi").value = "";
        }
        else if (what === 'category') {
          var td3 = row.insertCell(3);
          td0.innerHTML = '<span id="category' + id + '">' + name + '</span>';
          td1.innerHTML = '<span id="adCount' + id + '">0</span>';
          td2.innerHTML = '<input type="button" value=" - " onclick="deletE(\'category\', ' +
                               id + ', ' + (table.rows.length-1) + ')" />';
          td3.innerHTML = '<input type="text" id="name' + id + '" size="35" ' + 
                               'maxlength="50" /> &nbsp; ' + 
                               '<input type="button" class="button" value="ok" ' + 
                               'onclick="rename(\'category\', ' + id + ', ' + (table.rows.length-1) + ')" />';
          document.getElementById("newCategory").value = "";
        }
        else 
          return false;
      }
    }
  }

  xmlhttp.open("GET","/phpServeFiles/serveAdminPage.php?"+params,true);
  xmlhttp.send();
}


