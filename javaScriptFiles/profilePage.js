function changeUsername(oldUsername)
// Changes a user's username.
{
  // get the new username
  var newUsername = document.getElementById("usernameField").value;
  document.getElementById("usernameField").value = "";

/*
  if (!validUsername(newUsername)) {
    alert('Μη δεκτό username.');
    return;
  }
*/
  
  // Create the parameters to pass to the php script (via url).
  var params = 'what=changeUsername&oldUsername=' + encodeURI(oldUsername) +
                    '&newUsername=' + encodeURI(newUsername);

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
        window.location = "/phpFiles/profilePage.php?username=" + newUsername;
      }
      else {
        alert('Το username που επιλέξατε χρησιμοποιείται ήδη.');
        return;
      }
    }
  }

  xmlhttp.open("GET","/phpServeFiles/serveProfilePage.php?"+params,true);
  xmlhttp.send();
}




function changeEmail(username)
// Changes the user's email address.
{
  // Get the new email address.
  var email = document.getElementById("emailField").value;
  document.getElementById("emailField").value = "";

  if (!email.match(/^[a-zA-Z0-9 \._\-]+@([a-zA-Z0-9][a-zA-Z0-9\-]*\.)+[a-zA-Z]+$/)) {
    alert('Μη έγκυρη διεύθυνση email.');
    return;
  }

  // Create the parameters to pass to the php script (via url).
  var params = 'what=changeEmail&username=' + encodeURI(username) + '&email=' + 
                    encodeURI(email);

  // Create an XMLHttpRequest (or equivalent) object through which we'll make the query.
  if (window.XMLHttpRequest)
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp = new XMLHttpRequest();
  else
    // code for IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      if (xmlhttp.responseText.match(/ok/))
        document.getElementById("theTable").rows[2].cells[1].innerHTML = email;
      else
        document.write('alla');
    }
  }

  xmlhttp.open("GET","/phpServeFiles/serveProfilePage.php?"+params,true);
  xmlhttp.send();
}




function addTelephone(username)
// Adds the newly given telephone to user "username" and changes the page content
// to reflect the change.
{
  // Get the given telephone from the input field (with id="telephoneField").
  var telephone = document.getElementById("telephoneField").value;
  document.getElementById("telephoneField").value = "";
  if ( (telephone.length != 10) || (!telephone.match(/^\d{10}$/) ) ) {
    // if the given telephone is not a valid phone number
    alert('Μη έγκυρος αριθμός τηλεφώνου.');
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
      document.getElementById("theTable").rows[3].cells[1].innerHTML = xmlhttp.responseText;
    }
  }

  xmlhttp.open("GET","/phpServeFiles/serveProfilePage.php?"+params,true);
  xmlhttp.send();

}




function removeTelephone(username)
// Removes the given telephone number from a user (if it exists) and changes the
// page content to reflect the change.
{
  // Get the given telephone from the input field (with id="telephoneField").
  var telephone = document.getElementById("telephoneField").value;
  document.getElementById("telephoneField").value = "";

  if ( (telephone.length != 10) || (!telephone.match(/^\d{10}$/) ) ) {
    // if the given telephone is not a valid phone number
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
      document.getElementById("theTable").rows[3].cells[1].innerHTML = xmlhttp.responseText;
    }
  }

  xmlhttp.open("GET","/phpServeFiles/serveProfilePage.php?"+params,true);
  xmlhttp.send();

}




function changeName(username)
// Changes username's name.
{
  // Get the given name.
  var name = document.getElementById("nameField").value;
  document.getElementById("nameField").value = "";

  // Create the parameters to pass to the php script (via url).
  var params = 'what=changeName&username=' + encodeURI(username) + '&name=' + 
                    encodeURI(name);

  // Create an XMLHttpRequest (or equivalent) object through which we'll make the query.
  if (window.XMLHttpRequest)
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp = new XMLHttpRequest();
  else
    // code for IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      if (xmlhttp.responseText.match(/ok/))
        document.getElementById("theTable").rows[4].cells[1].innerHTML = name;
    }
  }

  xmlhttp.open("GET","/phpServeFiles/serveProfilePage.php?"+params,true);
  xmlhttp.send();
}




function changeSurname(username)
// Changes username's surname.
{
  // Get the given surname.
  var surname = document.getElementById("surnameField").value;
  document.getElementById("surnameField").value = "";

  // Create the parameters to pass to the php script (via url).
  var params = 'what=changeSurname&username=' + encodeURI(username) + '&surname=' + 
                    encodeURI(surname);

  // Create an XMLHttpRequest (or equivalent) object through which we'll make the query.
  if (window.XMLHttpRequest)
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp = new XMLHttpRequest();
  else
    // code for IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      if (xmlhttp.responseText.match(/ok/))
        document.getElementById("theTable").rows[5].cells[1].innerHTML = surname;
    }
  }

  xmlhttp.open("GET","/phpServeFiles/serveProfilePage.php?"+params,true);
  xmlhttp.send();
}




function changePassword(username)
// Changes username's password.
{
  // Get a hold of the input password fields (old, new and verification).
  var oldPasswordField = document.getElementById("oldPasswordField");
  var newPasswordField = document.getElementById("newPasswordField");
  var newPassword2Field = document.getElementById("newPassword2Field");
  
  if ( oldPasswordField.value.length < 5    || 
       newPasswordField.value.length < 5   || 
       newPassword2Field.value.length < 5     ) {
    alert('Μη δεκτός κωδικός. Κάθε κωδικός πρόσβασης πρέπει να αποτελείται ' +
              'από τουλάχιστον 5 χαρακτήρες!');
    oldPasswordField.value = "";
    newPasswordField.value = "";
    newPassword2Field.value = "";
    return false;
  }
  else if (newPasswordField.value != newPassword2Field.value) {
    alert('Οι νέοι κωδικοί δεν συμφωνούν μεταξύ τους!');
    oldPasswordField.value = "";
    newPasswordField.value = "";
    newPassword2Field.value = "";
    return false;
  }

  // Create the parameters to pass to the php script (via url).
  var params = "what=changePassword&username=" + encodeURI(username) +
                    "&oldPassword=" + encodeURI(oldPasswordField.value) +
                    "&newPassword=" + encodeURI(newPasswordField.value);

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
        alert('Ο κωδικός πρόσβασης άλλαξε επιτυχώς!');
        oldPasswordField.value = "";
        newPasswordField.value = "";
        newPassword2Field.value = "";
        return true;
      }
      else {
        alert('Λάθος κωδικός πρόσβασης.');
        oldPasswordField.value = "";
        newPasswordField.value = "";
        newPassword2Field.value = "";
        return true;
      }
    }
  }

  xmlhttp.open("GET","/phpServeFiles/serveProfilePage.php?"+params,true);
  xmlhttp.send();
}





