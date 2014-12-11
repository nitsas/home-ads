function addTelephone()
// Adds a new form field named "telephone[i]" where i is the number of telephone fields - 1.
{
  var table = document.getElementById("theTable");
  where = table.rows.length - 3;
  row = table.insertRow(where);
  td0 = row.insertCell(0);
  td1 = row.insertCell(1);
  
  td0.setAttribute('align', 'right');
  td0.innerHTML = '<b>Τηλέφωνο ' + (where - 3) + ' (*):</b>';
  td1.innerHTML = '<input type="text" name="telephone[' + (where - 4) + ']" size="25" ' + 'maxlength="40" />';
}




function rmvTelephone()
// Removes the last telephone form field (except if there is only one).
{
  var table = document.getElementById("theTable");
  if (table.rows.length > 8) {
    // There is more than one telephone form field. Delete the last one.
    table.deleteRow(table.rows.length - 4);
  }
}

