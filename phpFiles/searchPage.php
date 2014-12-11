<?php
  require_once('mainHeader.php');
  session_start();

  try {
    $userKind = getUserKind();
    doHtmlHeader('Σπιτικές Αγγελίες: Σελίδα αναζήτησης', $userKind, 'searchPage.js');
    echo '<h1>Αναζήτηση αγγελίας</h1>';
    displaySearchForm($userKind);
    doHtmlFooter();
  }
  catch (Exception $e) {
    displayProblemPage($e->getMessage(), 'noHeader');
  }

?>






<?php
function displaySearchForm($userKind='guest')
{
  $approvedAds = countAdvertisements();
  $allAds = countAdvertisements('all');
  if ($approvedAds == 0) {
    if ( ($userKind == 'admin') && ($allAds != 0) ) 
      echo '<span id="numAds"><p>Υπάρχουν μόνο <b>'.$allAds.'</b> μη-εγκεκριμένες αγγελίες.</p></span>';
    else 
      throw new Exception('Προς το παρόν δεν υπάρχουν αγγελίες στο σύστημα. '
                                .'Δοκιμάστε ξανά αργότερα.');
  } 
  else {
    echo '<span id="numAds"><p>Υπάρχουν <b>'.$approvedAds.'</b> εγκεκριμένες ';
    if ($userKind == 'admin')
      echo ' και <b>'.($allAds-$approvedAds).'</b> μη-εγκεκριμένες ';
    echo 'αγγελίες.</p></span>';
  }

  $category = getCategories();
  $paroxi = getParoxes();
?>
  <br />

  <form id="content2" action="doSearch.php" method="post">
    <fieldset>
      <legend align="top">
        &#160;Παρακαλώ συμπληρώστε τα στοιχεία που σας ενδιαφέρουν.&#160;
      </legend><div class="forTable">
      <table cellspacing="10px">
      <tr>
        <td align="right" width="auto"><b>Τύπος αγγελίας:</b></td>
        <td width="auto">
          <select name="aggeliaType" id="aggeliaType" onchange="countAds(this.id, this.value)">
            <option selected="selected" value="dontCare">Δεν με ενδιαφέρει</option>
            <option value="rent">Ενοικίαση</option>
            <option value="sale">Πώληση</option>
          </select></td>
      </tr><tr>
        <td align="right"><b>Κατηγορία σπιτιού:</b></td>
        <td>
          <select name="categoryID" id="categoryID" onChange="countAds(this.id, this.value)">
<?php
          if (!$category) 
            echo '<option selected="selected" value=false>Δεν υπάρχουν κατηγορίες.</option>';
          else {
            echo '<option selected="selected" value=false>Δεν με ενδιαφέρει</option>';
            while ( list($categoryName, $categoryID) = each($category) )
              echo "<option value=\"$categoryID\">$categoryName</option>";
          }
?>
          </select><td>
      </tr><tr>
        <td align="right"><b>Κόστος:</b></td>
        <td>από &euro; <input type="text" name="minCost" id="minCost" 
                       onChange="countAds(this.id, this.value)" size="9" maxlength="9" />
             έως &euro; <input type="text" name="maxCost" id="maxCost" 
                       onChange="countAds(this.id, this.value)" size="9" maxlength="9" /></td>
      </tr><tr>
        <td align="right"><b>Μέγεθος (τ.μ.):</b></td>
        <td>από &nbsp; &nbsp;<input type="text" name="minSize" id="minSize" 
                       onChange="countAds(this.id, this.value)" size="9" maxLength="5" />
             έως &nbsp; &nbsp;<input type="text" name="maxSize" id="maxSize" 
                       onChange="countAds(this.id, this.value)" size="9" maxLength="5" /></td>
      </tr><tr>
        <td align="right"><b>Χρονολογία κατασκευής:</b></td>
        <td>από &nbsp; &nbsp;<input type="text" name="minYear" id="minYear" 
                       onChange="countAds(this.id, this.value)" size="9" maxLength="4" />
             έως &nbsp; &nbsp;<input type="text" name="maxYear" id="maxYear" 
                       onChange="countAds(this.id, this.value)" size="9" maxLength="4" /></td>
      </tr><tr>
        <td align="right"><b>Οδός:</b></td>
        <td><input type="text" name="street" id="street" onChange="countAds(this.id, this.value)" 
                       size="30" maxlength="40" /></td>
      </tr><tr>
        <td align="right"><b>Αριθμός:</b></td>
        <td><input type="text" name="streetNumber" id="streetNumber" 
                       onChange="countAds(this.id, this.value)" size="3" maxlength="5" /></td>
      </tr><tr>
        <td align="right"><b>Παροχές (*):</b></td>
        <td>
          <select multiple="multiple" name="paroxi[]" id="paroxi" 
                       onChange="countAds(this.id, this.options)" size="5" />
<?php
          if (!$paroxi)
            echo '<option selected="selected" value=false>Δεν υπάρχουν παροχές.</option>';
          else 
            foreach ($paroxi as $key => $value)
              echo "<option value=\"$key\">$key</option>";
          
          echo "</select></td>";
          if ($userKind == 'admin') {
            echo '</tr><tr><td align="right"><b>Εγκεκριμένες ή όχι:</b></td>';
            echo '<td><select name="approved" id="approved" '
                   .'onChange="countAds(this.id, this.value)" />';
            echo '<option value="dontCare">Δεν με ενδιαφέρει</option>';
            echo '<option selected="selected" value="true">Μόνο εγκεκριμένες</option>';
            echo '<option value="false">Μόνο μη-εγκεκριμένες</option>';
            echo '</select></td>';
          }
?>
      </tr><tr><td colspan="2"><br /></td></tr><tr>
        <td colspan="2">
          <div class="centerAlign">
            <input type="submit" id="submit" name="submit" value="Αναζήτηση!" />
          </div></td></tr></table></div>
      <br /><br /><p>(*): Μπορείτε να επιλέξετε πολλές ταυτόχρονα 
                      κρατώντας πατημένο το ctrl (cmd για Mac).</p>
      </fieldset></form><br /> 

<?php
}

?>
