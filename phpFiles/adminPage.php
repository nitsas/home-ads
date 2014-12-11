<?php
  require_once('mainHeader.php');
  session_start();

  if (isset($_GET['whatToDo']))
    $whatToDo = $_GET['whatToDo'];
  else
    $whatToDo = false;

  try {
    $userKind = getUserKind();
  }
  catch (Exception $e) {
    displayProblemPage($e->getMessage());
  }
  if ($userKind != 'admin')
    displayProblemPage('Λυπούμαστε, μόνο οι διαχειριστές μπορούν να δουν αυτή τη '
                             .'σελίδα.');

  try {
    if ($whatToDo == 'adminUsers') {
      displayAdminUsers();
    }
    else if ($whatToDo == 'adminCategories') {
      displayAdminCategories();
    }
    else if ($whatToDo == 'adminParoxes') {
      displayAdminParoxes();
    }
    else {
      displayAdminMainPage();
    }
  }
  catch (Exception $e) {
    displayProblemPage($e->getMessage());
  }
?>








<?php



function displayAdminMainPage()
// Displays content of the administrator page.
{
  doHtmlHeader('Σπιτικές Αγγελίες: Κεντρική σελίδα διαχείρισης', 'admin', 'adminPage.js');
  echo '<h1>Διαχείριση ιστοτόπου</h1>';
  echo '<p><ul id="list">';
  echo '<li><a href="/phpFiles/adminPage?whatToDo=adminUsers">Διαχείριση εγγεγραμμένων χρηστών</a></li>';
  echo '<li><a href="/phpFiles/searchPage">Διαχείριση αγγελιών</a></li>';
  echo '<li><a href="/phpFiles/adminPage?whatToDo=adminCategories">Διαχείριση λίστας κατηγοριών</a></li>';
  echo '<li><a href="/phpFiles/adminPage?whatToDo=adminParoxes">Διαχείριση λίστας παροχών</a></li>';
  echo '</ul></p>';
  doHtmlFooter();
}




function displayAdminUsers()
// Displays the Administer Users page (only admin uses this).
{
  doHtmlHeader('Σπιτικές Αγγελίες: Διαχείριση εγγεγραμμένων χρηστών', 'admin', 'adminPage.js');
  echo '<h1>Διαχείριση εγγεγραμμένων χρηστών</h1><br />';

  $listOfUsernames = getUsers();
  if ($listOfUsernames) {
    echo '<p>Παρακαλώ επιλέξτε τον χρήστη που θέλετε να διαχειριστείτε:';
    echo '<ol id="list">';
    foreach ($listOfUsernames as $elem)
      echo "<li><a href='/phpFiles/profilePage.php?username=$elem'>$elem</a></li>";
    echo '</ol>';
    echo '</p>';
  }
  else     // getUsers() returned false (this will never happen -admin is registered- but anyway)
    echo '<p>Δεν υπάρχει κανείς εγγεγραμμένος χρήστης προς το παρόν.</p>';

  doHtmlFooter();
}




function displayAdminCategories()
// Displays the Administer Categories page (only admin uses this).
{
  doHtmlHeader('Σπιτικές Αγγελίες: Διαχείριση κατηγοριών', 'admin', 'adminPage.js');
  echo '<h1>Διαχείριση κατηγοριών</h1><br />';
  $categories = getCategories('withAdCount');
  
  echo '<br /><h2>Διαχείριση ήδη υπαρχουσών κατηγοριών</h2>';
  echo '<p>Χρησιμοποιείστε τα πεδία παρακάτω για να διαγράψετε ή να μετονομάσετε '
         .'κάποια κατηγορία. Προσοχή, αν διαγράψετε κάποια κατηγορία αυτόματα θα '
         .'διαγραφούν και όλες οι αγγελίες της! Αν μετονομάσετε κάποια κατηγορία σε '
         .'κάποια ήδη υπάρχουσα αυτόματα θα μεταφερθούν εκεί όλες οι αγγελίες της.</p><br />';
  if (!$categories) { 
    echo '<p>Δεν υπάρχουν κατηγορίες στο σύστημα. Δημιουργήστε μερικές!</p>';
    $categories = array();
  }
  echo '<div id="content2" class="forTable" style="text-align:center;">';
  echo '<table cellspacing="2px" id="theTable"><tr>';
  echo '<th width="150px">Όνομα κατηγορίας</th><th width="90px">Αρ.αγγελιών</th>'
         .'<th width="72px">Διαγραφή</th>';
  echo '<th width="180px">Μετονομασία / Μετακίνηση</th></tr>';
  $row = 1;
  foreach ($categories as $elem) {
    echo '<tr><td><span id="category'.$elem['categoryID'].'">'.$elem['categoryName'].'</span></td>';
    echo '<td><span id="adCount'.$elem['categoryID'].'">'.$elem['adCount'].'</span></td>';
    echo '<td><input type="button" value=" - " '
           ."onclick=\"deletE('category', ".$elem['categoryID'].", $row)\" />";
    echo '</td><td>';
    echo '<input type="text" id="name'.$elem['categoryID'].'" size="35" maxlength="50" /> &nbsp; '
          .'<input type="button" class="button" value="ok" '
          ."onclick=\"rename('category', ".$elem['categoryID'].", $row)\" />";
    echo '</td></tr>';
    $row++;
  }
  echo '</table></div>';
  echo '<br /><br /><br /><br /><br /><h2>Προσθήκη νέας κατηγορίας</h2>'; 
  echo '<p>Γράψτε παρακάτω το όνομα της νέας κατηγορίας και πατήστε το ok.</p>';
  echo '<div class="forTable"><input type="text" id="newCategory" size="35" maxlength="50" /> '
        .'&nbsp; <input type="button" class="button" value="ok" '
        ."onclick=\"create('category')\" />";
  echo '</div><br /><br />';

  doHtmlFooter();
}




function displayAdminParoxes()
// Displays the Administer Paroxes page (only admin uses this).
{
  doHtmlHeader('Σπιτικές Αγγελίες: Διαχείριση λίστας παροχών', 'admin', 'adminPage.js');
  echo '<h1>Διαχείριση παροχών</h1><br />';
  $paroxes = getParoxes();
  if (is_array($paroxes))
    asort($paroxes);       // sort by paroxiID
  echo '<br /><h2>Διαχείριση ήδη υπαρχουσών παροχών</h2>';
  echo '<p>Χρησιμοποιείστε τα πεδία παρακάτω για να διαγράψετε ή να μετονομάσετε '
         .'κάποια παροχή. Αν διαγράψετε κάποια παροχή, αυτόματα αφαιρείται από '
         .'όλες τις αγγελίες.</p><br />';
  if (!$paroxes) { 
    echo '<p>Δεν υπάρχουν διαθέσιμες παροχές στο σύστημα. Δημιουργήστε μερικές!</p>';
    $paroxes = array();
  }
  echo '<div id="content2" class="forTable" style="text-align:center;">';
  echo '<table cellspacing="2px" id="theTable"><tr>';
  echo '<th width="150px">Όνομα παροχής</th><th width="72px">Διαγραφή</th>';
  echo '<th width="180px">Μετονομασία</th></tr>';
  $row = 1;
  foreach ($paroxes as $eidosParoxis => $paroxiID) {
    echo '<tr><td><span id="paroxi'.$paroxiID.'">'.$eidosParoxis.'</span></td>';
    echo '<td><input type="button" value=" - " '
           ."onclick=\"deletE('paroxi', $paroxiID, $row)\" /></td>";
    echo '<td><input type="text" id="name'.$paroxiID.'" size="20" maxlength="25" /> &nbsp; '
          .'<input type="button" class="button" value="ok" '
          ."onclick=\"rename('paroxi', $paroxiID, $row)\" />";
    echo '</td></tr>';
    $row++;
  }
  echo '</table></div>';
  echo '<br /><br /><br /><br /><br /><h2>Προσθήκη νέας παροχής</h2>'; 
  echo '<p>Γράψτε παρακάτω το όνομα της νέας παροχής και πατήστε το ok.</p>';
  echo '<div class="forTable"><input type="text" id="newParoxi" size="25" maxlength="25" /> '
        .'&nbsp; <input type="button" class="button" value="ok" '
        ."onclick=\"create('paroxi')\" />";
  echo '</div><br /><br />';
  
  doHtmlFooter();
}



?>
