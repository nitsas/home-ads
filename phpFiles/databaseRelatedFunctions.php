<?php

function register($username, $password, $email, $telephone, $name, $surname)
// Adds a new user to the database.
// All arguments are strings except for $telephone which can either be a string or
// an array of strings.
{
  $connection = dbConnect();

  $username = addslashes($username);
  $password = addslashes($password);
  $email      = addslashes($email);
  $name      = addslashes($name);
  $surname  = addslashes($surname);

  // check if the username is unique
  $result = $connection->query("select * from User where username='$username'");
  if (!$result)
    throw new Exception('Πρόβλημα κατά την ανάκτηση των υπαρχόντων usernames.');
  if ( $result->num_rows > 0 )
    throw new Exception('Το username που επιλέξατε χρησιμοποιείται ήδη - παρακαλώ '
                               .'πηγαίνετε πίσω και ξαναπροσπαθήστε.');

  // If the username is unique insert the new user in the database.
  // First insert the new user into table User.
  $result = $connection->query( "insert into User values
                       ('$username', sha1('$password'), '$email', FALSE)" );
  if (!$result)
    throw new Exception('Δεν μπορέσαμε να σας εγγράψουμε στην βάση δεδομένων - '
                              .'παρακαλώ προσπαθήστε ξανά αργότερα.');

  // Now insert the user's telephone(s) into table Telephone.
  if (is_array($telephone)) {
    foreach($telephone as $elem) {
      $result = $connection->query( "insert into Telephone values ('"
                                              .$username."', '".$elem."')" );
      if (!$result)
        throw new Exception('Πρόβλημα κατά την εγγραφή των τηλεφώνων στην βάση '
                                  .'δεδομένων - παρακαλώ προσπαθήστε ξανά αργότερα.');
    }
  }
  else {
    // $telephone is a simple string
    $result = $connection->query( "insert into Telephone values
                                ('$username', '$telephone')" );
    if (!$result)
      throw new Exception('Πρόβλημα κατά την εγγραφή του τηλεφώνου στην βάση '
                                .'δεδομένων - παρακαλώ προσπαθήστε ξανά αργότερα.');
  }

  // Finally, insert the user's name and surname (if there are any) into table Name.
  if ( $name && $surname )
    $result = $connection->query( "insert into Name values
                              ('$username', '$name', '$surname')" );
  elseif ( $name )
    $result = $connection->query( "insert into Name values
                              ('$username', '$name', NULL)" );
  elseif ( $surname )
    $result = $connection->query( "insert into Name values
                              ('$username', NULL, '$surname')" );

  if (!$result)
    throw new Exception('Δεν μπορέσαμε να σας εγγράψουμε στην βάση δεδομένων - '
                              .'παρακαλώ προσπαθήστε ξανά αργότερα.');

  $connection->close();

  return true;
}



function login($username, $password)
// Checks the database for the given username and password.
// Returns true if $username is a valid user (with password $password), or
// throws an exception if something wasn't right (or if we had a database problem).
{
  $connection = dbConnect();

  $username = addslashes($username);
  $password = addslashes($password);
  
  $result = $connection->query( "select * from user where username = '$username' and "
                                        ."password = sha1('$password')" );
  if (!$result)
    throw new Exception('Δεν μπορέσαμε να σας συνδέσουμε - παρακαλώ '
                               .'ξαναπροσπαθήστε.');

  $connection->close();

  if ($result->num_rows > 0)
    return true;
  else
    return false;
}



function resetPassword($username)
// Resets $username's password to a random value.
// Returns the new password or false on failure.
{
  // Get a random dictionary word between 6 and 15 (not too long) characters in length.
  $newPassword = getRandomWord(6, 15);
  if (!$newPassword) 
    throw new Exception('Αποτυχία δημιουργίας νέου κωδικού.');

  // Attackers will probably be trying dictionary words, so we'll add a random number
  // (between 0 and 999) to the password to make it slightly better.
  srand((double) microtime() * 1000000);
  $randomNumber = rand(0, 999); 
  $newPassword .= $randomNumber;

  // Set this as the user's new password in the database (or return false).
  $connection = dbConnect();
  $result = $connection->query("update user set password = sha1('$newPassword') "
                                       ."where username = '$username'");
  if (!$result)
    throw new Exception('Δεν μπορέσαμε να αλλάξουμε το password στη βάση.');
  else
    return $newPassword;     // success
}



function getEmail($username)
// Finds and returns user $username's email in the database. 
{
  $connection = dbConnect();
  $result = $connection->query("select email from user where username = '$username'");
  if (!$result)
    throw new Exception('Αδυναμία εύρεσης διεύθυνσης email.');
  else if ($result->num_rows == 0)
    throw new Exception('Δεν υπάρχει καταχωρημένη διεύθυνση email στη βάση.');
  else {
    $row = $result->fetch_row();
    $email = $row[0];

    return $email;
  }
} 



function isAdmin($username)
// Checks if the logged in user is an Administrator.    
{
  $username = addslashes($username);
  $connection = dbConnect();

  $result = $connection->query( "select * from User where username = '$username' and " 
                                          ."isAdmin = TRUE" );
  if (!$result)
    throw new Exception('Πρόβλημα με τον έλεγχο isAdmin.');

  $connection->close();
    
  if ($result->num_rows > 0)
    return true;
  else
    return false;
}



function getCategories($getAdCount=false)
// Gets the category names and ids from the database.
// If $getAdCount is true then in addition to the name and id, the number of ads for each 
// category will be returned.
{
  // Make the query.
  $connection = dbConnect();
  // Get the categoryNames and categoryIDs.
  $result = $connection->query( "select * from Category" );
  if (!$result)
    throw new Exception('Πρόβλημα κατά την ανάκτηση των κατηγοριών.');

  // Check if there were any results at all.
  if ($result->num_rows == 0)
    return false;
  // Make the results into an associative array.
  while ( $row = $result->fetch_row() )
    $category[stripslashes($row[1])] = $row[0];
  
  if ($getAdCount) {
    // Get the ad count for each category (will only return adCounts greater than 0).
    $result = $connection->query('select categoryID, count(aggeliaID) from Aggelia '
                                         .'group by categoryID');
    $connection->close();
    if (!$result)
      throw new Exception('Πρόβλημα κατά την ανάκτηση του αριθμού αγγελιών ανά '
                                 .'κατηγορία.');

    // Fill in the adCount of every category.
    $adCounts = array();
    foreach ($category as $categoryName => $categoryID)
      $adCount[$categoryID] = 0;
    while ($row = $result->fetch_row()) 
      $adCount[$row[0]] = $row[1];
    
    // Make the results into an array of associative arrays (one for each category).
    asort($category);          // sort by categoryID
    $categories = array();
    foreach ($category as $categoryName => $categoryID)
      array_push($categories, array('categoryName' => $categoryName,
                                             'categoryID' => $categoryID, 
                                             'adCount' => $adCount[$categoryID]));
    
    return $categories;
  }
  
  $connection->close();
  ksort($category);
  return $category;
}



function getUsers()
// Returns all the usernames in an array.
{
  $connection = dbConnect();
  $result = $connection->query('select username from User');
  if (!$result)
    throw new Exception('Πρόβλημα κατά την ανάκτηση των usernames.');
  $connection->close();

  // Check if there were any results at all.
  if ($result->num_rows == 0)
    return false;
  // Get the usernames in an array.
  $usernames = array();
  while ( ($row = $result->fetch_row()) )
    array_push($usernames, stripslashes($row[0]));

  return $usernames;
}  



function getParoxes($aggeliaID=false)
// If an ad's id is given, return that ad's paroxes in an array.
// If no ad's id is given, return every eidosParoxis and paroxiID from table Paroxi as 
// an associative array.
{
  if (!$aggeliaID) {
    // RETURN EVERY eidosParoxis AND paroxiID AS AN ASSOCIATIVE ARRAY.
    $connection = dbConnect();
    $result = $connection->query('select * from Paroxi');
    if (!$result)
      throw new Exception('Πρόβλημα κατά την ανάκτηση των παροχών.');
    $connection->close();
   // Check if there were any results at all.
    if ($result->num_rows < 1)
      return false;
    // Get the results into an associative array (the key is eidosParoxis).
    $paroxi = array();
    while ( ($row = $result->fetch_row()) )
      $paroxi[stripslashes($row[1])] = $row[0];
    ksort($paroxi);
  }
  else {
    // RETURN ALL eidosParoxis FOR AD $aggeliaID.
    $connection = dbConnect();
    $result = $connection->query('select eidosParoxis from Paroxi inner join HasParoxi '
                                         .'on Paroxi.paroxiID = HasParoxi.paroxiID '
                                         ."where aggeliaID = '$aggeliaID'");
    if (!$result)
      throw new Exception('Πρόβλημα κατά την ανάκτηση των παροχών.');
    $connection->close();
    // Check if there were any results at all.
    if ($result->num_rows == 0)
      return false;
    // Get the results into an array.
    $paroxi = array();
    while ($row = $result->fetch_row())
      array_push($paroxi, stripslashes($row[0]));
    sort($paroxi);
  } 

  return $paroxi;
}


function addParoxi($aggeliaID, $eidosParoxis) 
// Adds a new eidosParoxis to ad $aggeliaID.
{
  // Get all paroxes.
  $paroxes = getParoxes();

  if (array_key_exists($eidosParoxis, $paroxes)) {
    // Get all $aggeliaID's paroxes.
    $hasParoxes = getParoxes($aggeliaID);
    if (!$hasParoxes)
      $hasParoxes = array();
    if (!in_array($eidosParoxis, $hasParoxes)) {
      $paroxiID = $paroxes[$eidosParoxis];
      $connection = dbConnect();
      $result = $connection->query("insert into HasParoxi values ('$aggeliaID', '$paroxiID')");
      $connection->close();
      if (!$result)
        throw new Exception('Πρόβλημα κατά την προσθήκη νέας παροχής.');
      else {
        // The new paroxi was added successfully.
        array_push($hasParoxes, $eidosParoxis);
        sort($hasParoxes);
        return $hasParoxes;
      }
    }
    else
      // $eidosParoxis is already a paroxi of ad $aggeliaID
      return $hasParoxes;
  }
  else
    return false;
}



function removeParoxi($aggeliaID, $eidosParoxis)
// Removes paroxi $eidosParoxis from $aggeliaID.
{
  // Get all paroxes.
  $paroxes = getParoxes();

  $hasParoxes = getParoxes($aggeliaID);
  if (!$hasParoxes)
    $hasParoxes = array();
  if (in_array($eidosParoxis, $hasParoxes)) {
    $paroxiID = $paroxes[$eidosParoxis];
    $connection = dbConnect();
    $result = $connection->query("delete from HasParoxi where aggeliaID = '$aggeliaID'"
                                         ." and paroxiID = '$paroxiID'");
    $connection->close();
    if (!$result)
      throw new Exception('Πρόβλημα κατά την αφαίρεση παροχής.');
    else {
      // The paroxi was removed successfully.
      $hasParoxes = array_diff($hasParoxes, array($eidosParoxis));
      sort($hasParoxes);
      return $hasParoxes;
    }
  }
  else {
    // $eidosParoxis is already not a paroxi of $aggeliaID
    return $hasParoxes;
  }
}



function createNewParoxi($name)
// Creates a new paroxi with $name as eidosParoxis and returns its paroxiID.
{
  // Get all existing paroxes.
  $paroxes = getParoxes();

  if ( array_key_exists($name, $paroxes) ) {
    // the given paroxi already exists
    return false;
  }
  else {
    // create the new paroxi
    $connection = dbConnect();
    $name = addslashes($name);
    $result = $connection->query("insert into Paroxi values (NULL, '$name')");
    if (!$result)
      throw new Exception('Πρόβλημα κατά τη δημιουργία νέας παροχής.');

/*
    // Get the id of the newly added paroxi.
    $paroxiID = mysql_insert_id($connection);
    return $paroxiID;
*/

    // Get the id of the newly added paroxi.
    $result = $connection->query("select paroxiID from Paroxi where eidosParoxis = '$name'");
    $connection->close();
    if (!$result)
      throw new Exception('Πρόβλημα κατά την ανάκτηση του ID της νέας παροχής.');

    $row = $result->fetch_row();
    $paroxiID = $row[0];
    return $paroxiID;
  }
}



function renameParoxi($paroxiID, $name)
// Renames paroxi with id $paroxiID to $name.
{
  $paroxes = getParoxes();
  $connection = dbConnect();
  if ( !array_key_exists($name, $paroxes) ) {
    // Change the paroxi's name.
    $name = addslashes($name);
    $result = $connection->query("update Paroxi set eidosParoxis = '$name' where "
                                         ."paroxiID = '$paroxiID'");
    $returnVal = 'ok';
  }
  else {
    // $name is already the name of a paroxi so just delete paroxi $paroxiID.
    // Before doing that though, make each ad that has $paroxiID have the 
    // already existing paroxi with name $name.
    // Move $paroxiID and put paroxi with name $name in its place (for each ad).
    $destinationId = $paroxes[$name];
    $result = $connection->query("update ignore HasParoxi set paroxiID = '$destinationID' "
                                         ."where paroxiID = '$paroxiID'");
    if (!$result)
      throw new Exception('Πρόβλημα κατά την αλλαγή της παροχής στις αγγελίες.');
    // Now delete any rows that were ignored (due to the ignore keyword) in the above query.
    $result = $connection->query("delete from HasParoxi where paroxiID = '$paroxiID'");
    if (!$result)
      throw new Exception('Πρόβλημα κατά το καθάρισμα μετά την αλλαγή της παροχής.');
    // At last, delete the old paroxi.
    $result = $connection->query("delete from Paroxi where paroxiID = '$paroxiID'");
    $returnVal = 'deleted';
  }
  $connection->close();
  if (!$result)
    throw new Exception('Πρόβλημα κατά τη μετονομασία της παροχής.');
  else 
    return $returnVal;
}



function deleteParoxi($paroxiID)
// Deletes paroxi with id $paroxiID.
{
  $connection = dbConnect();
  // First delete the paroxi from all ads.
  $result = $connection->query("delete from HasParoxi where paroxiID = '$paroxiID'");
  if (!$result)
    throw new Exception('Πρόβλημα κατά την αφαίρεση της παροχής από τις αγγελίες.');

  // Now delete the paroxi.
  $result = $connection->query("delete from Paroxi where paroxiID = '$paroxiID'");
  if (!$result)
    throw new Exception('Πρόβλημα κατά την διαγραφή της παροχής από τη βάση.');

  return true;
}



function createNewCategory($name)
// Creates a new category with $name as categoryName and returns its categoryID.
{
  // Get all existing categories.
  $categories = getCategories();

  if ( array_key_exists($name, $categories) ) {
    // the given category already exists
    return false;
  }
  else {
    // create the new category
    $connection = dbConnect();
    $name = addslashes($name);
    $result = $connection->query("insert into Category values (NULL, '$name')");
    if (!$result)
      throw new Exception('Πρόβλημα κατά τη δημιουργία νέας κατηγορίας.');

    // Get the id of the newly added category.
    $result = $connection->query("select categoryID from Category "
                                         ."where categoryName = '$name'");
    $connection->close();
    if (!$result)
      throw new Exception('Πρόβλημα κατά την ανάκτηση του ID της νέας κατηγορίας.');

    $row = $result->fetch_row();
    $categoryID = $row[0];
    return $categoryID;
  }
}



function renameCategory($categoryID, $name)
// Renames category with id $categoryID to $name.
{
  $categories = getCategories();
  $connection = dbConnect();
  if ( !array_key_exists($name, $categories) ) {
    // rename the category
    $name = addslashes($name);
    $result = $connection->query("update Category set categoryName = '$name' where "
                                         ."categoryID = '$categoryID'");
    $returnVal = 'ok';
  }
  else {
    // $name is an already existing category so first move all ads to that category 
    // and then delete the category $categoryID.
    $destinationID = $categories[$name];
    $result = $connection->query("update Aggelia set categoryID = '$destinationID' where "
                                         ."categoryID = '$categoryID'");
    if (!$result)
      throw new Exception('Πρόβλημα κατά τη μετονομασία της κατηγορίας.');

    // delete the category
    $result = $connection->query("delete from Category where categoryID = '$categoryID'");
    $returnVal = 'deleted and moved to id '.$destinationID;
  }  
  $connection->close();
  if (!$result)
    throw new Exception('Πρόβλημα κατά τη μετονομασία της κατηγορίας.');
  else 
    return $returnVal;
}



function deleteCategory($categoryID)
// Deletes category with id $categoryID.
{
  $connection = dbConnect();
  // First delete all ads in that category.
  $result = $connection->query("delete from Aggelia where categoryID = '$categoryID'");
  if (!$result)
    throw new Exception('Πρόβλημα κατά την διαγραφή των αγγελιών της κατηγορίας.');

  // Now delete the category.
  $result = $connection->query("delete from Category where categoryID = '$categoryID'");
  if (!$result)
    throw new Exception('Πρόβλημα κατά την διαγραφή της κατηγορίας από τη βάση.');

  return true;
}




function changeApproval($aggeliaID)
// If ad $aggeliaID is approved make it non-approved,
// else make it approved.
{
  $searchOptions['aggeliaID'] = $aggeliaID;
  $aggeliaInfo = searchAds($searchOptions);
  if (!$aggeliaInfo)
    throw new Exception('Πρόβλημα κατά την αναζήτηση πληροφοριών για την αγγελία');

  if ($aggeliaInfo[0]['approved']) {
    $connection = dbConnect();
    $result = $connection->query('update Aggelia set approved = FALSE where aggeliaID = '
                                         .$aggeliaID);
    $connection->close();
    if (!$result)
      throw new Exception('Πρόβλημα κατά την σήμανση αγγελίας ως μη εγκεκριμένη.');
    else {
      return 'disapproved';
    }

  }
  else {
    $connection = dbConnect();
    $result = $connection->query("update Aggelia set approved = TRUE, approvalDate = default "
                                         ."where aggeliaID = '$aggeliaID'");
    $connection->close();
    if (!$result)
      throw new Exception('Πρόβλημα κατά την σήμανση αγγελίας ως εγκεκριμένη.');
    else {
      return 'approved';
    }
  }
  return false;   // will never get here, but it doesn't matter
}



function changeAdInfo($aggeliaID, $aggeliaType, $categoryID, $yearMade, $size, $street,
                             $streetNumber, $area, $cost, $latitude=false, $longitude=false)
// Changes info of ad with id $aggeliaID to the given values.
{
  $street = addslashes($street);
  if ($area)
    $area = addslashes($area);

  // Create the set string (the string saying what we want to update).
  $setString = " aggeliaType = '$aggeliaType', categoryID = '$categoryID', street = '$street' ";
  if ($yearMade)
    $setString .= ", yearMade = '$yearMade' ";
  if ($size)
    $setString .= ", size = '$size' ";
  if ($streetNumber)
    $setString .= ", streetNumber = '$streetNumber' ";
  if ($area)
    $setString .= ", area = '$area' ";
  if ($cost)
    $setString .= ", cost = '$cost' ";
  if ($latitude && $longitude)
    $setString .= ", latitude = '$latitude', longitude = '$longitude'";
  else
    $setString .= ", latitude = NULL, longitude = NULL";

  // Connect to the database and make the query.
  $connection = dbConnect();
  $result = $connection->query("update Aggelia set $setString where aggeliaID = '$aggeliaID'");
  $connection->close();
  if (!$result)
    throw new Exception('Πρόβλημα κατά την αλλαγή των στοιχείων της αγγελίας.');

  return true;
}



function getUserInfo($username)
// Finds and returns (in an associative array) all info for user $username.
{
  $connection = dbConnect();
  // First get main info.
  $result = $connection->query("select email, isAdmin from User where "
                                       ."username = '".addslashes($username)."'");
  if (!$result)
    throw new Exception('Πρόβλημα κατά την ανάκτηση των πληροφοριών');
  else if ($result->num_rows == 0)
    // Δεν υπάρχει αυτός ο χρήστης.
    return false;
  else {
    $row = $result->fetch_row();
    $userInfo = array();
    $userInfo['username'] = $username;
    $userInfo['email'] = stripslashes($row[0]);
    if ($row[1] == '0')
      $userInfo['isAdmin'] = false;
    else
      $userInfo['isAdmin'] = true;
  }
  // Now get the user's name and surname (if any).
  $result = $connection->query("select name, surname from Name where "
                                       ."username = '".addslashes($username)."'");
  if (!$result)
    throw new Exception('Πρόβλημα κατά την ανάκτηση του ονοματεπώνυμου χρήστη.');
  else if ($result->num_rows == 0) {
    // Ο χρήστης δεν έχει δηλώσει ούτε όνομα ούτε επώνυμο.
    $userInfo['name'] = '';               // used '' because it is both printable and false
    $userInfo['surname'] = '';           //
  }
  else {
    $row = $result->fetch_row();
    // Put the user's name in $userInfo.
    if (is_null($row[0]))
      $userInfo['name'] = '';
    else 
      $userInfo['name'] = stripslashes($row[0]);
    // Put the user's surname in $userInfo.
    if (is_null($row[1]))
      $userInfo['surname'] = '';
    else
      $userInfo['surname'] = stripslashes($row[1]);
  }
  // Finally, get the user's telephones (if any);
  $userInfo['telephone'] = getTelephones($username);

  return $userInfo;
}



function changeUsername($oldUsername, $newUsername)
// Changes a user's username (only admins will call this).
{
  $users = getUsers();
  if (!$users || !in_array($oldUsername, $users) || in_array($newUsername, $users) )
    // if there are no users OR $oldUsername doesn't exist OR $newUsername if already taken
    return false;

  $connection = dbConnect();
  // First update table User.
  $result = $connection->query('update User set username = \''.addslashes($newUsername).'\' '
                                       .'where username = \''.addslashes($oldUsername).'\'');
  if (!$result)
    throw new Exception('Πρόβλημα κατά την αλλαγή του username.');
  // Now update table Name.
  $result = $connection->query('update Name set username = \''.addslashes($newUsername).'\' '
                                       .'where username = \''.addslashes($oldUsername).'\'');
  if (!$result)
    throw new Exception('Πρόβλημα κατά την αλλαγή του username.');
  // Now update table Aggelia.
  $result = $connection->query('update Aggelia set username = \''.addslashes($newUsername).'\' '
                                       .'where username = \''.addslashes($oldUsername).'\'');
  if (!$result)
    throw new Exception('Πρόβλημα κατά την αλλαγή του username.');
  // Now update table Favorite.
  $result = $connection->query('update Favorite set username = \''.addslashes($newUsername).'\' '
                                       .'where username = \''.addslashes($oldUsername).'\'');
  if (!$result)
    throw new Exception('Πρόβλημα κατά την αλλαγή του username.');
  // Finally, update table Telephone.
  $result = $connection->query('update Telephone set username = \''.addslashes($newUsername).'\' '
                                       .'where username = \''.addslashes($oldUsername).'\'');
  if (!$result)
    throw new Exception('Πρόβλημα κατά την αλλαγή του username.');


  return true;
}



function changePassword($username, $oldPassword, $newPassword)
// Changes $username's password.
{
  // Call login(). If $username is an existing user and $oldPassword the user's password 
  // login will return true. Else it will return false.
  $ok = login($username, $oldPassword);
  if (!$ok)
    // $username, $oldPassword pair isn't valid.
    return false;

  // Change the password.
  $newPassword = addslashes($newPassword);
  $username = addslashes($username);
  $connection = dbConnect();
  $result = $connection->query("update User set password = sha1('$newPassword') where "
                                       ."username = '$username'");
  if (!$result)
    throw new Exception('Πρόβλημα κατά την αλλαγή του password.');
  else 
    return true;
}



function changeEmail($username, $email)
// Changes a user's email address.
{
  $connection = dbConnect();
  // Update table User.
  $result = $connection->query('update User set email = \''.addslashes($email).'\' where '
                                       .'username = \''.addslashes($username).'\'');
  $connection->close();
  if (!$result)
    throw new Exception('Πρόβλημα κατά την αλλαγή της διεύθυνσης email.');
  else 
    return true;
}



function changeName($username, $name)
// Changes a user's name.
{
  $connection = dbConnect();
  // Update table Name.
  $result = $connection->query('update Name set name = \''.addslashes($name).'\' where '
                                       .'username = \''.addslashes($username).'\'');
  $connection->close();
  if (!$result)
    throw new Exception('Πρόβλημα κατά την αλλαγή του ονόματος.');
  else 
    return true;
}



function changeSurname($username, $surname)
// Changes a user's surname.
{
  $connection = dbConnect();
  // Update table Name.
  $result = $connection->query('update Name set surname = \''.addslashes($surname).'\' where '
                                       .'username = \''.addslashes($username).'\'');
  $connection->close();
  if (!$result)
    throw new Exception('Πρόβλημα κατά την αλλαγή του επιθέτου.');
  else 
    return true;
}



function getTelephones($username)
// Gets (and returns) the list of $username's telephones (or false if none exists).
{
  $connection = dbConnect();

  $result = $connection->query("select telephone from Telephone where username='$username'");
  $connection->close();
  if (!$result)
    throw new Exception('Πρόβλημα κατά την ανάκτηση των τηλεφώνων.');
  else if ($result->num_rows == 0)
    return false;
  else {
    $telephones = array();
    while ($row = $result->fetch_row())
      array_push($telephones, $row[0]);
    sort($telephones);
    return $telephones;
  }
}



function addTelephone($username, $telephone)
// Adds $telephone to the list of $username's telephone numbers (if not already in there).
{
  $listTelephones = getTelephones($username);
  if (!is_array($listTelephones))
    $listTelephones = array();
  if ( in_array($telephone, $listTelephones) )
    return $listTelephones;

  $connection = dbConnect();
  $result = $connection->query("insert into Telephone values ('$username', '$telephone')");
  $connection->close();
  if (!$result)
    throw new Exception('Πρόβλημα κατά την εισαγωγή νέου τηλεφώνου.');
  else {
    array_push($listTelephones, $telephone);
    sort($listTelephones);
    return $listTelephones;
  }
}



function removeTelephone($username, $telephone)
// Removes $telephone from the list of $username's telephone numbers (if it exists).
{
  $listTelephones = getTelephones($username);
  if ( !in_array($telephone, $listTelephones) )
    return $listTelephones;

  $connection = dbConnect();
  $result = $connection->query("delete from Telephone where username = '$username' and "
                                       ."telephone = '$telephone'");
  $connection->close();
  if (!$result)
    throw new Exception('Πρόβλημα κατά την διαγραφή τηλεφώνου.');
  else {
    $listTelephones = array_diff($listTelephones, array($telephone));
    sort($listTelephones);
    return $listTelephones;
  }
}



function countAdvertisements($which='approved')
// If $which == 'approved' counts only aproved advertisements, 
// otherwise it counts all advertisements in the database.
{
  // Connect to the database.
  $connection = dbConnect();
  
  if ($which == 'approved')
    $result = $connection->query('select count(*) from Aggelia where approved = TRUE');
  else
    $result = $connection->query('select count(*) from Aggelia');
  if (!$result) 
    throw new Exception('Πρόβλημα με την καταμέτρηση των αγγελιών.');
  $connection->close();

  $row = $result->fetch_row();
  $numberOfAdvertisements = $row[0];

  return $numberOfAdvertisements;
}



function searchAds($options=false, $format='mini')
// $options is an associatice array specifying the options the ads must fulfill.
// Returns all ads that fulfill all options ($format specifies if mini or full descriptions will 
// be returned).
// Shortcuts:
// If options is false returns all approved ads.
// If options is 'all' returns all ads.
{
  $connection = dbConnect();

  // We'll create the parts of the query on table Aggelia one by one. 
  // There is a $select part, a $from part, and a $where part.


  // $from part (always the same)
  $from = ' Aggelia inner join Category on Aggelia.categoryID = Category.categoryID ';

  // $select part
  if ($format == 'full') 
    $select = ' aggeliaID, username, categoryName, cost, street, area, streetNumber, size, '
                .'yearMade, approved, aggeliaType, approvalDate, timesViewed '
                .', latitude, longitude ';
  else 
    $select = ' aggeliaID, username, categoryName, cost, street, area, streetNumber, size, '
                .'yearMade, approved, aggeliaType ';

  // $where part (the most complex)
  // each option in $options that is not set or is false will be disregarded
  if ( (!$options) || empty($options) )
    // shortcut 1
    $where = " approved = TRUE ";
  else if ($options === 'all')
    // shortcut 2
    $where = " true ";
  else {
    $where = " true ";         // so that an " and ..." after that won't matter
    // check all options one by one
    // -- aggeliaID --
    if ( isset($options['aggeliaID']) && ($options['aggeliaID']) ) 
      $where .= " and aggeliaID = '".$options['aggeliaID']."' ";
    // -- aggeliaType --
    if ( isset($options['aggeliaType']) && ($options['aggeliaType']) )
      $where .= " and aggeliaType = '".$options['aggeliaType']."' ";
    // -- categoryID --
    if ( isset($options['categoryID']) && ($options['categoryID']) )
      $where .= " and Aggelia.categoryID = '".$options['categoryID']."' ";
    // -- categoryName --
    if ( isset($options['categoryName']) && ($options['categoryName']) )
      $where .= " and categoryName = '".$options['categoryName']."' ";
    // -- minCost --
    if ( isset($options['minCost']) && ($options['minCost']) )
      $where .= " and cost >= '".$options['minCost']."' ";
    // -- maxCost --
    if ( isset($options['maxCost']) && ($options['maxCost']) )
      $where .= " and cost <= '".$options['maxCost']."' ";
    // -- minSize --
    if ( isset($options['minSize']) && ($options['minSize']) )
      $where .= " and size >= '".$options['minSize']."' ";
    // -- maxSize --
    if ( isset($options['maxSize']) && ($options['maxSize']) )
      $where .= " and size <= '".$options['maxSize']."' ";
    // -- username --
    if ( isset($options['username']) && ($options['username']) )
      $where .= " and username = '".$options['username']."' ";
    // -- street --
    if ( isset($options['street']) && ($options['street']) )
      $where .= " and street = '".$options['street']."' ";
    // -- streetNumber --
    if ( isset($options['streetNumber']) && ($options['streetNumber']) )
      $where .= " and streetNumber = '".$options['streetNumber']."' ";
    // -- area --
    if ( isset($options['area']) && ($options['area']) )
      $where .= " and area = '".$options['area']."' ";
    // -- minYearMade --
    if ( isset($options['minYear']) && ($options['minYear']) )
      $where .= " and yearMade >= '".$options['minYear']."' ";
    // -- maxYearMade --
    if ( isset($options['maxYear']) && ($options['maxYear']) )
      $where .= " and yearMade <= '".$options['maxYear']."' ";
    // -- approved --
    if ( isset($options['approved']) ) {
      if ($options['approved'] === true)
        $where .= " and approved = TRUE ";
      else if ($options['approved'] == false)
        $where .= " and approved = FALSE ";
    }
    // -- minApprovalDate --
    if ( isset($options['minApprovalDate']) && ($options['minApprovalDate']) )
      $where .= " and approvalDate >= '".$options['minApprovalDate']."' ";
    // -- maxApprovalDate --
    if ( isset($options['maxApprovalDate']) && ($options['maxApprovalDate']) )
      $where .= " and approvalDate <= '".$options['maxApprovalDate']."' ";
    // -- minTimesViewed --
    if ( isset($options['minTimesViewed']) && ($options['minTimesViewed']) )
      $where .= " and timesViewed >= '".$options['minTimesViewed']."' ";
    // -- maxTimesViewed --
    if ( isset($options['maxTimesViewed']) && ($options['maxTimesViewed']) )
      $where .= " and timesViewed <= '".$options['maxTimesViewed']."' ";
  }


  // Finally, make the query.
  $result = $connection->query('select '.$select.' from '.$from.' where '.$where);

  if (!$result)
    throw new Exception('Πρόβλημα κατά την αναζήτηση αγγελιών.');
  else if ($result->num_rows == 0) 
    return false;
  
  // Get the results in array $aggelia ($aggelia will be an array of associative arrays).
  $aggelia = $result->fetch_all(MYSQLI_ASSOC);


  // GET ADDITIONAL INFO (if $format == 'full')
  // If full format was specified or we need to check each ad's paroxes and photos
  // get each ad's photos and paroxes in array $aggelia.
  if ( ($format == 'full')  ||  isset($options['hasParoxi']) ) {
    // We've got to get each ad's paroxes as well.
    // -- eidosParoxis --
    foreach ($aggelia as &$current) {
      $result = $connection->query("select eidosParoxis from Paroxi inner join HasParoxi on "
                                           ."Paroxi.paroxiID = HasParoxi.paroxiID where aggeliaID = '"
                                           .$current['aggeliaID']."'");
      if (!$result)
        throw new Exception('Πρόβλημα κατά την ανάκτηση παροχών από τη βάση.');
      else if ($result->num_rows == 0)
        $current['eidosParoxis'] = false;
      else {
        $current['eidosParoxis'] = array();
        while ($row = $result->fetch_row())
          array_push($current['eidosParoxis'], $row[0]);
      }
    }
  }

  if ( ($format == 'full')  ||  isset($options['hasPhotos']) ) {
    // We've got to get each ad's photos as well.
    // -- photoPath --
    foreach ($aggelia as &$current) {
      $result = $connection->query("select photoPath from Photo where aggeliaID = '"
                                           .$current['aggeliaID']."'");
      if (!$result)
        throw new Exception('Πρόβλημα κατά την ανάκτηση φωτογραφιών από τη βάση.');
      else if ($result->num_rows == 0)
        $current['photoPath'] = false;
      else {
        $current['photoPath'] = array();
        while ($row = $result->fetch_row())
          array_push($current['photoPath'], $row[0]);
      }
    }
  }

  if ($format == 'full') {
    // We've got to get the telephone numbers of the user who registered the ad.
    // -- telephone --
    foreach ($aggelia as &$current) {
      $result = $connection->query("select telephone from Telephone where username = '"
                                           .$current['username']."'");
      if (!$result)
        throw new Exception('Πρόβλημα κατά την ανάκτηση των τηλεφώνων από τη βάση.');
      else if ($result->num_rows == 0)
        $current['telephone'] = false;
      else {
        $current['telephone'] = array();
        while ($row = $result->fetch_row())
          array_push($current['telephone'], $row[0]);
      }
    }
  }


  // CHECK ADDITIONAL INFO (if respective $options are set)
  // If the hasPhotos option was set keep only the ads that comply with that option.
  if ( isset($options['hasPhotos']) ) {
    // Make sure that all aggelies to be returned comply with the option 'hasPhotos'.
    if ($options['hasPhotos']) {
      // wanted aggelies must have at least one photo each
      $num = count($aggelia);
      for ($i=0; $i<$num; $i++)
        if (!$aggelia[$i]['photoPath'])
          unset($aggelia[$i]);
    }
    else {
      // wanted aggelies must not have any photos
      $num = count($aggelia);
      for ($i=0; $i<$num; $i++)
        if ($array[$i]['photoPath'])
          unset($array[$i]);
    }
    // Some rows might have been unset so reindex array $aggelia.
    $aggelia = array_values($aggelia);
  }

  // If the hasParoxi option was set keep only the ads that comply with that option.
  if ( isset($options['hasParoxi']) )
    // Make sure that all aggelies to be returned have all the paroxes in array 'hasParoxi'.
    if ( is_array($options['hasParoxi']) && !empty($options['hasParoxi']) ) {
      // wanted aggelies must have all paroxes in $options['hasParoxi'] (paroxiIDs)
      $num = count($aggelia);
      for ($i=0; $i<$num; $i++) {
        // make sure $aggelia[$i] has all paroxes in $options['hasParoxi'] - if not unset it
        if ($aggelia[$i]['eidosParoxis']) {
          $paroxesMissing = array_diff($options['hasParoxi'], $aggelia[$i]['eidosParoxis']);
          if ( !empty($paroxesMissing) )
            unset($aggelia[$i]);
        }
        else       // $aggelia[$i] has no paroxes, so unset it
          unset($aggelia[$i]);
      }
      // Some rows might have been unset so reindex array $aggelia.
      $aggelia = array_values($aggelia);
    }

  
  // Return the array with the results.
  return $aggelia;
}



function registerNewAdvertisement($username, $aggeliaType, $categoryID, $cost, $street, 
                                            $streetNumber, $size, $yearMade, $area, $paroxi=false,
                                            $photo=false, $latitude=false, $longitude=false)
// Adds a new advertisement to the database.
// All arguments are strings except for $paroxi (which is an array of strings) 
// and $photo (which is an array of uploaded files - only their tmp_names and types).
{
  // Open a connection to the database.
  $connection = dbConnect();

  // add slashes to strings and turn numerical strings to ints
  $username = addslashes($username);
  $cost = (int) $cost;
  $street = addslashes($street);
  $streetNumber = (int) $streetNumber;
  $size = (int) $size;
  $yearMade = (int) $yearMade;
  if ($area) 
    $area = addslashes($area);
  else
    $area = "NULL";
  if (!$latitude || !$longitude) {
    $latitude = "NULL";
    $longitude = "NULL";
  }

  // Insert all table Aggelia related info.
  $result = $connection->query("insert into Aggelia values "
                      ."(NULL, '$username', $categoryID, $cost, '$street', '$area', $streetNumber, "
                      ."$size, $yearMade, default, '$aggeliaType', NULL, default"
                      .", $latitude, $longitude"
                      .")");
  if (!$result)
    throw new Exception('Δεν μπορέσαμε να καταχωρήσουμε την αγγελία σας - '
                               .'παρακαλούμε προσπαθήστε ξανά.');

  // Get the newly inserted advertisement's ID.
/*
  $aggeliaID = mysql_insert_id($connection);
  
*/
  $result = $connection->query("select max(aggeliaID) from Aggelia");
  if (!$result)
    throw new Exception('Πρόβλημα κατά την επικοινωνία με τη βάση.');
  $row = $result->fetch_row();
  $aggeliaID = $row[0];

  if ($paroxi) {
    // Insert all table Paroxi related info.
    foreach ($paroxi as $paroxiID) {
      $result = $connection->query("insert into HasParoxi values ('$aggeliaID', '$paroxiID')");
      if (!$result)
        throw new Exception('Πρόβλημα κατά την αποθήκευση των παροχών στη βάση.');
    }
  }

  if ($photo) {
    // Insert all Photo related info.
    // First find out what the new photo's id will be (the new photo's pathname will contain 
    // that id so that it's surely unique).
    $nextPhotoID = getNextPhotoID();
    // Now add all the new photos paths to the database and save all the uploaded files.
    // We'll be saving the files into folder userPhotos with filenames like "photo_1.jpg" etc.
    foreach ($photo as $elem) {
      // Create the new photo's path name.
      $nextPhotoPath = 'photo_'.$nextPhotoID.".";
      $nextPhotoID++;
      if ($elem['type'] == 'image/gif')
        $nextPhotoPath .= 'gif';
      else 
        $nextPhotoPath .= 'jpg';

      // Save the uploaded file inside folder userPhotos with filename $nextPhotoPath.
      if ( file_exists("../userPhotos/".$nextPhotoPath) )
        throw new Exception('Πρόβλημα κατά την αποθήκευση των φωτογραφιών.');
      else
        move_uploaded_file($elem['tmp_name'], "../userPhotos/$nextPhotoPath");

      // Now save the new photo path to the database.
      $result = $connection->query("insert into Photo values (NULL, $aggeliaID, '../userPhotos/"
                                           .$nextPhotoPath."')");
      if (!$result) {
        // Delete the saved photo from the server.
        unlink("../userPhotos/".$nextPhotoPath);
        throw new Exception('Πρόβλημα κατά την αποθήκευση των paths των '
                                  .'φωτογραφιών στη βάση δεδομένων.');
      }
    }
  }

  $connection->close();

  return true;
}



function getNextPhotoID()
// Returns the id of the next element to go into table Photo.
{
  $connection = dbConnect();

  $result = $connection->query("select max(photoID) from Photo");
  if (!$result)
    throw new Exception('Πρόβλημα κατά την επικοινωνία με τη βάση.');
  if ($result->num_rows == 0)
    $nextPhotoID = 1;
  else {
    $row = $result->fetch_row();
    $nextPhotoID = $row[0] + 1;
  }
  return $nextPhotoID;
}



function addToFavorites($username, $aggeliaID)
// Adds ad with id $aggeliaID to $username's favorites.
{
  $connection = dbConnect();

  // First check if the ad is already in $username's favorites.
  $favorites = getFavorites($username, 'idsOnly');
  $username = addslashes($username);      // getFavorites will addslashes() too

  if ( !in_array($aggeliaID, $favorites) ) {
    $result = $connection->query("insert into Favorite values ('$username', '$aggeliaID')");
    if (!$result)
      throw new Exception('Πρόβλημα κατά την εισαγωγή αγγελίας στα αγαπημένα.');
  }
  $connection->close();

  return true;
}



function getFavorites($username, $return='mini')
// Gets the favorite advertisements for the currently logged-in user.
{
  $username = addslashes($username);
  $connection = dbConnect();

  // Get the IDs of the user's favorite advertisements.
  $result = $connection->query("select aggeliaID from Favorite where username='$username'");
  if (!$result)
    throw new Exception('Δεν μπορέσαμε να ανακτήσουμε τις αγαπημένες σας αγγελίες '
                              .'από τη βάση δεδομένων - παρακαλούμε ξαναπροσπαθήστε.');

  if ($result->num_rows == 0)
    return false;

  // Turn the result into an array.
  $aggeliaIDs = array();
  while ($row = $result->fetch_row()) 
    array_push($aggeliaIDs, $row[0]);
  if ($return === 'idsOnly')
    return $aggeliaIDs;

  // Get all necessary info for a mini description of each advertisement in aggeliaIDs.
  // First create the query for the first aggeliaID.
  $query = 'select aggeliaID, categoryName, cost, street, streetNumber, size, yearMade, '
             .'aggeliaType from aggelia inner join category on aggelia.categoryID = '
             .'category.categoryID where aggeliaID = '.$aggeliaIDs[0];
  // Now add the rest aggeliaIDs to the query.
  $numAggelies = count($aggeliaIDs);
  for ($i=1; $i<$numAggelies; $i++) 
    $query = $query.' or aggeliaID = '.$aggeliaIDs[$i];

  // Now make the query.
  $result = $connection->query($query);
  if (!$result)
    throw new Exception('Δεν μπορέσαμε να ανακτήσουμε πληροφορίες για τις '
                              .'αγαπημένες σας αγγελίες από τη βάση δεδομένων - '
                              .'παρακαλούμε ξαναπροσπαθήστε.');

  // Turn the results into an associative array.
  $favorites = $result->fetch_all(MYSQLI_ASSOC);

  $connection->close();

  return $favorites;
}



function getFullDescription($aggeliaID)
// Gets all info related to the advertisement with id $aggeliaID into an associative array.
{
  $connection = dbConnect();

  // We're gonna do two things with one query.
  // The first one is we'll check if $aggeliaID is a valid id for an advertisement.
  // The second one is we'll get all info about $aggeliaID from table Aggelia as well as its
  // category name from table Category.
  $result = $connection->query('select aggeliaID, username, categoryName, cost, street, '
                     .'area, streetNumber, size, yearMade, approved, aggeliaType, approvalDate, '
                     .'timesViewed, latitude, longitude from Aggelia inner join Category '
                     .'on aggelia.categoryID = category.categoryID where '
                     ."aggeliaID = $aggeliaID");
  if (!$result)
    throw new Exception('Πρόβλημα κατά την εκτέλεση ερωτήματος στη βάση δεδομένων.');
  if ($result->num_rows == 0)
    return false;

  // Get all the info about $aggeliaID gathered so far into an associative array.
  $info = $result->fetch_assoc();

  // Now get all the benefits (paroxes) related to $aggeliaID from table Paroxi.
  $result = $connection->query("select eidosParoxis from Paroxi where aggeliaID = $aggeliaID");
  if (!$result)
    throw new Exception('Πρόβλημα κατά την εκτέλεση ερωτήματος στη βάση δεδομένων.');
  if ($result->num_rows == 0)
    $info['eidosParoxis'] = false;
  else {
    $info['eidosParoxis'] = array();
    $i = 0;
    while ($row = $result->fetch_row())
      $info['eidosParoxis'][$i++] = stripslashes($row[0]);
  }
  
  // Time to get all the photo paths related to $aggeliaID from table Photo.
  $result = $connection->query("select photoPath from Photo where aggeliaID = $aggeliaID");
  if (!$result)
    throw new Exception('Πρόβλημα κατά την εκτέλεση ερωτήματος στη βάση δεδομένων.');
  if ($result->num_rows == 0)
    $info['photoPath'] = false;
  else {
    $info['photoPath'] = array();
    $i = 0;
    while ($row = $result->fetch_row())
      $info['photoPath'][$i++] = stripslashes($row[0]);
  }

  // Lastly, get all telephones of the user who registered the advertisement with id $aggeliaID.
  $result = $connection->query("select telephone from Telephone where username = '"
                                       .$info['username']."'");
  if (!$result)
    throw new Exception('Πρόβλημα κατά την εκτέλεση ερωτήματος στη βάση δεδομένων.');
  if ($result->num_rows == 0)
    $info['telephone'] = false;
  else {
    $info['telephone'] = array();
    $i = 0;
    while ($row = $result->fetch_row())
      $info['telephone'][$i++] = $row[0];
  }
  
  $connection->close();

  return $info;  
}



function deleteAd($aggeliaID)
// Deletes the ad with id $aggeliaID.
{
  $connection = dbConnect();
  // First delete the ad's info.
  $result = $connection->query("delete from Aggelia where aggeliaID = '$aggeliaID'");
  if (!$result)
    throw new Exception('Πρόβλημα κατά την διαγραφή των πληροφοριών αγγελίας');

  // Now delete related info in table HasParoxi.
  $result = $connection->query("delete from HasParoxi where aggeliaID = '$aggeliaID'");
  if (!$result)
    throw new Exception('Πρόβλημα κατά την διαγραφή των παροχών της αγγελίας');

  // Now delete $aggeliaID from all users' favorites.
  $result = $connection->query("delete from Favorite where aggeliaID = '$aggeliaID'");
  if (!$result)
    throw new Exception('Πρόβλημα κατά την διαγραφή της αγγελίας από τον '
                               .'πίνακα με τα αγαπημένα χρηστών.');

  // Get all photoPaths (for ad $aggeliaID) from table Photo.
  $result = $connection->query("select photoPath from Photo where aggeliaID = '$aggeliaID'");
  if (!$result)
    throw new Exception('Πρόβλημα κατά την ανάκτηση των φωτογραφιών της '
                              .'αγγελίας.');
  $photo = array();
  while ($row = $result->fetch_row())
    array_push($photo, $row[0]);

  // Delete all photos in array $photo from the disk.
  foreach ($photo as $elem)
    unlink($elem);

  // Now Delete all related info from table Photo.
  $result = $connection->query("delete from Photo where aggeliaID = '$aggeliaID'");
  if (!$result)
    throw new Exception('Πρόβλημα κατά την διαγραφή των φωτογραφιών της '
                              .'αγγελίας.');

  $connection->close();
  return true;
}



function viewedAd($aggeliaID)
// Increase the timesViewed counter of this ad by one.
{
  $connection = dbConnect();

  $result = $connection->query("update Aggelia set timesViewed = timesViewed + 1 "
                                       ."where aggeliaID = '$aggeliaID'");
  if (!$result)
    throw new Exception('Πρόβλημα κατά την αύξηση του timesViewed μετρητή.');

  return true;
}



function getTop5Ads()
// Gets the top 5 most viewed ads (check their timesViewed counters).
{
  $connection = dbConnect();

  // Make the query (returns all ads ordered by timesViewed).
  $result = $connection->query('select aggeliaID, aggeliaType, categoryName, yearMade, '
                   .'size, street, streetNumber, cost, timesViewed, latitude, longitude from Aggelia '
                   .'inner join Category on Aggelia.categoryID = Category.categoryID '
                   .'where approved = TRUE '
                   .'order by timesViewed desc, aggeliaID');

  // Keep the first 5 result rows as an array of associative arrays (one for each ad).
  if (!$result)
    throw new Exception('Πρόβλημα κατά την ανάκτηση των δημοφιλέστερων '
                               .'αγγελιών.');
  else if ($result->num_rows == 0)
    return false;
  else {
    $top5 = $result->fetch_all(MYSQLI_ASSOC);
    $top5 = array_slice($top5, 0, 5);
    return $top5;
  }
}



function getMostRecentAds($howMany=5)
// Gets the 5 most recently approved ads.
{
  $connection = dbConnect();

  // Make the query (returns all approved ads ordered by approvalDate).
  $result = $connection->query('select aggeliaID, aggeliaType, categoryName, yearMade, size, '
                       .'street, streetNumber, cost, approvalDate, latitude, longitude from Aggelia '
                       .'inner join Category on Aggelia.categoryID = Category.categoryID where '
                       .'approved = TRUE order by approvalDate desc, aggeliaID');

  // Keep the first $howMany result rows as an array of associative arrays (one for each ad).
  if (!$result)
    throw new Exception('Πρόβλημα κατά την ανάκτηση των νεότερων αγγελιών.');
  else if ($result->num_rows == 0)
    return false;
  else {
    $mostRecent = $result->fetch_all(MYSQLI_ASSOC);
    if ($result->num_rows > $howMany) 
      $mostRecent = array_slice($mostRecent, 0, $howMany);
    return $mostRecent;
  }
}



function dbConnect()
// Makes a connection to the database.
{
  // CHANGE_THIS_DATABASE_LOCATION can be localhost
  // CHANGE_THIS_DATABASE_NAME was originally web_aggelies_db
  $connection = new mysqli('CHANGE_THIS_DATABASE_LOCATION', 'CHANGE_THIS_DATABASE_USER_NAME', 'CHANGE_THIS_DATABASE_PASSWORD', 'CHANGE_THIS_DATABASE_NAME');
  if (!$connection)
    throw new Exception('Η σύνδεση με τη βάση δεδομένων απέτυχε.');

  $connection->query("set names 'utf8'");
  return $connection;
}
  
?>

