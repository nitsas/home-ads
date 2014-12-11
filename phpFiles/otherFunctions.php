<?php 

function getUserKind()
// Checks if the user is a guest, a logged in member or an administrator.
{
  if ( !isset($_SESSION['validUser']) )
      $userKind = 'guest';
  else if (isAdmin($_SESSION['validUser'])) 
      $userKind = 'admin';
  else 
    $userKind = 'member';

  return $userKind;
}



function getRandomWord($minLength, $maxLength)
// Returns a random dictionary word between $minLength and $maxLength in length.
{
  // For now return a standard word (don't know much about the dictionary).
  return 'pineapples';
}



function validTelephone($telephone)
// Can either take a string or an array of strings as an argument.
// Checks if each string is a valid telephone number.
{
  if (is_string($telephone)) {
    // Check both the length of the telephone number and whether or not
    // $telephone contains only decimal digits.
    if ( (strlen($telephone) != 10) || (!ctype_digit($telephone)) )
      return false;
  }
  else if (is_array($telephone)) {
    foreach ($telephone as $elem) {
      if ( (!is_string($elem)) || (strlen($elem) != 10) || (!ctype_digit($elem)) )
        return false;
    }
  }
  else     // if $telephone is neither a string nor an array
    return false;

  // if $telephone passed all the checks return true
  return true;
}



function isValidPhotoArray($photo)
// Checks the array of files $photo. They must all be either jpg or gif files and at most
// 150Kb each in size.
{
  // If it was called with false as a parameter just exit true.
  if (!$photo)
    return true;

  // for every file check its size, if there was an error during the upload and its
  // file type (only jpg and gif allowed)
  foreach ($photo as $elem) {
    if ( ($photo['size'] > 150000)  ||  ($photo['error'] > 0)  ||  
         (  ($photo['type'] != 'image/gif')   && 
            ($photo['type'] != 'image/jpeg') &&
            ($photo['type'] != 'image/pjpeg')    )                 ) {
      // if some file failed the tests return false
      return false;
    }
  }

  // if every file passed the tests return true;
  return true;
}



function checkAndGetUploadedPhotosArray()
// Checks all entries in $_FILES['photo'] and returns only necessary info (tmp_names and type).
// Also checks if the uploads were ok, throws an exception if there was an error.
{
  // first make some short names
  $error = $_FILES['photo']['error'];                // array
  $type = $_FILES['photo']['type'];                  // array
  $size = $_FILES['photo']['size'];                    // array
  $tmp_name = $_FILES['photo']['tmp_name'];   // array
  $numRows = count($error);

  // Check if every file was uploaded correctly.
  foreach ($error as $test) 
    if ( ($test != UPLOAD_ERR_OK) && ($test != UPLOAD_ERR_NO_FILE) )
      throw new Exception('Πρόβλημα κατά την μεταφόρτωση των φωτογραφιών.');
  
  // No error occured (but maybe no file was sent).
  $photo = array();
  for ($i=0; $i<$numRows; $i++) {
    if ($error[$i] != UPLOAD_ERR_NO_FILE) {
      // make sure the file types and file sizes are acceptable
      if ( ($size[$i] > 150000)                 ||  
          (  ($type[$i] != 'image/gif') &&
             ($type[$i] != 'image/jpeg') &&
             ($type[$i] != 'image/pjpeg')    )    ) 
      {
        throw new Exception('Προσοχή! - οι φωτογραφίες πρέπει να είναι μορφής '
                                  .'jpg ή gif και μεγέθους το πολύ 150Kb.');
      }
      else {
        array_push($photo, array('type' => $size[$i], 'tmp_name' => $tmp_name[$i]) );
      }
    }
  }

  if (empty($photo))
    return false;
  else
    return $photo;

}




function createMainAdInfo($aggeliaInfo, $showArea=true, $bold=true)
// Creates (and returns) the main info for the ad array $aggeliaInfo refers to as a string.
{
  $str = "";
  if ($bold)
    $str = '<b>';
  if ($aggeliaInfo['aggeliaType'] == 'sale')
    $str .= "Πωλείται ";
  else
    $str .= "Ενοικιάζεται ";
  if ($bold)
    $str .= '</b>';

  $str .= strtolower($aggeliaInfo['categoryName']);
  $str .= " του ".$aggeliaInfo['yearMade'].", ";
  $str .= $aggeliaInfo['size']." τ.μ., ";
  $str .= "οδός ".ucfirst(strtolower($aggeliaInfo['street']))." ".$aggeliaInfo['streetNumber'];
  if ($showArea && !is_null($aggeliaInfo['area']))
    $str .= ", περιοχή ".ucfirst(strtolower($aggeliaInfo['area']));
  $str .= ", τιμή &#x20AC;".$aggeliaInfo['cost'];
  if ($aggeliaInfo['aggeliaType'] == 'rent')
    $str .= "/μήνα";
  $str .= ".";

  return $str;
}




function validEmail($email) 
// Checks if the given email is valid.
{
  if ( preg_match('/^[a-zA-Z0-9 \._\-]+@([a-zA-Z0-9][a-zA-Z0-9\-]*\.)+[a-zA-Z]+$/',
                               $email) )
    return true;
  else
    return false;
}

?>