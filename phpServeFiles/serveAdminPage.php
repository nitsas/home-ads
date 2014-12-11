<?php
  require_once('../phpFiles/mainHeader.php');

  if (!isset($_GET['what']))
    exit;
  else
    $what = $_GET['what'];

  try {
    if ($what === 'newParoxi') {
      whatNew('paroxi');       // the code follows below
    }
    else if ($what === 'renameParoxi') {
      whatRename('paroxi');    // the code follows below
    }
    else if ($what === 'deleteParoxi') {
      whatDelete('paroxi');     // the code follows below
    }
    else if ($what === 'newCategory') {
      whatNew('category');    // the code follows below
    }
    else if ($what === 'renameCategory') {
      whatRename('category'); // the code follows below
    }
    else if ($what === 'deleteCategory') {
      whatDelete('category');   // the code follows below
    }
  }
  catch (Exception $e) {
    echo $e->getMessage();
    exit;
  }
?>




<?php
// ---------- FUNCTIONS USED ABOVE -------------


function whatNew($what)
// Adds a new paroxi (if $what=='paroxi') or category (if $what=='category').
// Prints the new id so that the javaScript can know.
{
  if (!isset($_GET['name']))
    return false;

  if ($what === 'paroxi')
    $id = createNewParoxi(urldecode($_GET['name']));
  else if ($what === 'category')
    $id = createNewCategory(urldecode($_GET['name']));
  if (!$id)
    // the name already existed
    return false;
  else {
    echo 'ok '.$id;
    return true;
  }
}



function whatDelete($what)
// Deletes a paroxi (if $what=='paroxi') or category (if $what=='category').
{
  if (!isset($_GET['id']))
    return false;

  if ($what == 'paroxi')
    $ok = deleteParoxi($_GET['id']);
  else if ($what == 'category')
    $ok = deleteCategory($_GET['id']);
  if (!$ok)
    return false;
  else {
    echo 'ok';
    return true;
  }
}



function whatRename($what)
// Renames a paroxi (if $what=='paroxi') or a category (if $what=='category').
{
  if (!isset($_GET['name']) || !isset($_GET['id']))
    return false;

  if ($what === 'paroxi')
    $ok = renameParoxi($_GET['id'], urldecode($_GET['name']));
  else if ($what === 'category')
    $ok = renameCategory($_GET['id'], urldecode($_GET['name']));
  if (!$ok)
    return false;
  else {
    echo $ok;
    return true;
  }
}


?>