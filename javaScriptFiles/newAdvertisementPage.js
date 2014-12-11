function addPhotoField()
// Adds a new field for photo uploading to the form.
{
  var table = document.getElementById("theTable");
  where = table.rows.length - 1;
  row = table.insertRow(where);
  td0 = row.insertCell(0);
  td1 = row.insertCell(1);

  td0.setAttribute('align', 'right');
  td0.innerHTML = '<b>Φωτογραφία ' + (where - 8) + ':</b>';
  td1.innerHTML = '<input type="file" name="photo[' + (where - 9) + ']" />';
}



function rmvPhotoField()
// Removes the last field for photo uploading from the form (if there is more than one).
{
  var table = document.getElementById("theTable");
  if (table.rows.length > 11)
    table.deleteRow(table.rows.length - 2);
}
