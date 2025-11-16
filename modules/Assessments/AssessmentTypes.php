<?php
include 'modules/Assessments/functions.inc.php';

if ($_REQUEST['action'] == 'add') {
    DBQuery("INSERT INTO assessments_types (TITLE, SHORT_NAME, SORT_ORDER)
        VALUES ('" . $_REQUEST['TITLE'] . "', '" . $_REQUEST['SHORT_NAME'] . "', " . (int)$_REQUEST['SORT_ORDER'] . ")");
}

if ($_REQUEST['action'] == 'delete') {
    DBQuery("DELETE FROM assessments_types WHERE ID='" . (int)$_REQUEST['id'] . "'");
}

$types = DBGet("SELECT * FROM assessments_types ORDER BY SORT_ORDER");

DrawHeader(_('Assessment Types'));

echo '<form method="POST">';
echo '<table><tr><td>Title</td><td><input name="TITLE"></td></tr>
      <tr><td>Short Name</td><td><input name="SHORT_NAME"></td></tr>
      <tr><td>Sort Order</td><td><input name="SORT_ORDER"></td></tr></table>';
echo '<input type="hidden" name="action" value="add">';
echo '<input type="submit" value="Add Assessment Type"></form>';

echo '<hr><h3>Existing Assessment Types</h3>';

foreach ($types as $t) {
    echo "<p>{$t['TITLE']} ({$t['SHORT_NAME']}) 
        <a href=\"?action=delete&id={$t['ID']}\">[Delete]</a></p>";
}
