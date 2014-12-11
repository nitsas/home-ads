<?php
  require_once('mainHeader.php');
  session_start();

  // In case a guest tried to get here:
  if ( !isset($_SESSION['validUser']) ) {
    displayProblemPage('Λυπούμαστε πολύ, μόνο οι εγγεγραμένοι χρήστες μπορούν να '
                             .'καταχωρίσουν νέες αγγελίες.</p><p>'
                             .'<a href="/phpFiles/registrationPage.php">Θέλω να εγγραφώ!</a>');
  }

  // For the logged in members:
  $username = $_SESSION['validUser'];
  $userKind  = getUserKind();       // exception_here

  doHtmlHeader('Σπιτικές Αγγελίες: Σελίδα Καταχώρησης', $userKind, 
                                                                             'newAdvertisementPage.js');

  echo '<h1>Καταχώρηση αγγελίας</h1>';
  displayNewAdvertisementForm();
  
  doHtmlFooter();

?>






<?php
function displayNewAdvertisementForm()
// Called when a logged in user wants to create a new advertisement.
{
  // First check if there are any categories on the system.
  // If there aren't any the user can't add a new advertisement.
  $category = getCategories();
  $paroxi = getParoxes();
  if (!$category) {
    echo '<p>Συγγνώμη αλλά δεν μπορείτε να καταχωρήσετε νέα αγγελία '
          .'αυτή τη στιγμή. Ο διαχειριστής δεν έχει προσθέσει κατηγορίες '
          .'ακινήτων στο σύστημα.</p><br />';
    doHtmlFooter();
    exit;
  }
?>
  <br />

  <form id="content2" action="processNewAdvertisement.php" method="post" 
          enctype="multipart/form-data">
    <fieldset>
      <legend align="top">
        &#160;Παρακαλώ συμπληρώστε τα στοιχεία της αγγελίας:&#160;
      </legend><object class="forTable">
      <table cellspacing="10px" id="theTable">
      <tr>
        <td width="270px" align="right"><b>Τύπος αγγελίας:</b></td> 
        <td width="auto">
          <select name="aggeliaType">
            <option selected="selected" value="rent">Ενοικίαση</option>
            <option value="sale">Πώληση</option>
           </select></td>
      </tr><tr>
        <td align="right"><b>Κατηγορία σπιτιού:</b></td>
        <td><select name="categoryID">
<?php
          list( $categoryName, $categoryID ) = each($category);
          echo "<option selected=\"selected\" value=\"$categoryID\">$categoryName</option>";
          while ( list($categoryName, $categoryID) = each($category) )
            echo "<option value=\"$categoryID\">$categoryName</option>";
?>
        </select></td>
      </tr><tr>
        <td align="right"><b>Κόστος(**): &euro; </b></td>
        <td><input type="text" name="cost" size="9" maxlength="9" /></td>
      </tr><tr>
        <td align="right"><b>Οδός:</b></td>
        <td><input type="text" name="street" size="30" maxlength="40" /></td>
      </tr><tr>
        <td align="right"><b>Αριθμός:</b></td>
        <td><input type="text" name="streetNumber" size="3" maxlength="5" /></td>
      </tr><tr>
        <td align="right"><b>Περιοχή(*):</b></td>
        <td><input type="text" name="area" size="15" maxlength="30" /></td>
      </tr><tr>
        <td align="right"><b>Μέγεθος (τ.μ.):</b></td>
        <td><input type="text" name="size" size="5" maxlength="5" /></td>
      </tr><tr>
        <td align="right"><b>Χρονολογία κατασκευής:</b></td>
        <td><input type="text" name="yearMade" size="4" maxlength="4" /></td>
      </tr><tr>
        <td align="right"><b>Παροχές(*):</b></td>
        <td><select multiple="multiple" name="paroxi[]" size="5" />
<?php
          if (!$paroxi)
            echo '<option selected="selected" value=false>Δεν υπάρχουν παροχές.</option>';
          else 
            foreach ($paroxi as $key => $value)
              echo "<option value=\"$value\">$key</option>";
?>
          </select></td>
      </tr><tr>
        <td align="right"><b>Προσθήκη φωτογραφίας(*):</b></td>
        <td><input type="file" name="photo[0]" />
             <input type="button" value=" + " onclick="addPhotoField()" />
             <input type="button" value=" - " onclick="rmvPhotoField()" /></td>
      </tr><tr>
        <td colspan="2">
          <div class="centerAlign">
            <input id="submit" type="submit" name="submit" value="Καταχώρηση" />
          </div></td></tr>
      </table></object></fieldset></form>
  <br />
  <p>(*) Μη απαραίτητα πεδία.<br />
      (**) Αν πρόκειται για ενοικίαση, το κόστος είναι ανά μήνα.</p>
  <br />
<?php
}

?>
