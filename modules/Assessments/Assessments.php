<?php
include 'modules/Assessments/functions.inc.php';

$student_id = UserStudentID();
if (!$student_id) {
    echo ErrorMessage(array(_('You must select a student first.')));
    return;
}

$types = GetAssessmentTypes();

/* Add score */
if ($_REQUEST['action'] == 'add') {
    DBQuery("INSERT INTO assessments_scores
        (STUDENT_ID, ASSESSMENT_TYPE_ID, SCORE, DATE_TAKEN, COMMENTS)
        VALUES (
            $student_id,
            " . (int)$_REQUEST['ASSESSMENT_TYPE_ID'] . ",
            " . (float)$_REQUEST['SCORE'] . ",
            '" . $_REQUEST['DATE_TAKEN'] . "',
            '" . $_REQUEST['COMMENTS'] . "'
        )");
}

/* Delete score */
if ($_REQUEST['action'] == 'delete') {
    DBQuery("DELETE FROM assessments_scores
        WHERE ID=" . (int)$_REQUEST['id'] . " AND STUDENT_ID=" . (int)$student_id);
}

$scores = DBGet("
    SELECT s.ID, t.TITLE AS ASSESSMENT, s.SCORE, s.DATE_TAKEN, s.COMMENTS
    FROM assessments_scores s
    JOIN assessments_types t ON t.ID=s.ASSESSMENT_TYPE_ID
    WHERE s.STUDENT_ID=$student_id
    ORDER BY s.DATE_TAKEN DESC
");

DrawHeader(_('Assessments for Student'));

echo '<h3>Add Assessment Score</h3>';

echo '<form method="POST">';
echo '<table>';

echo '<tr><td>Assessment Type</td><td><select name="ASSESSMENT_TYPE_ID">';
foreach ($types as $t) {
    echo "<option value=\"{$t['ID']}\">{$t['TITLE']}</option>";
}
echo '</select></td></tr>';

echo '<tr><td>Score</td><td><input name="SCORE" required></td></tr>';
echo '<tr><td>Date Taken</td><td><input type="date" name="DATE_TAKEN"></td></tr>';
echo '<tr><td>Comments</td><td><textarea name="COMMENTS"></textarea></td></tr>';

echo '</table>';
echo '<input type="hidden" name="action" value="add">';
echo '<button type="submit">Add Score</button>';
echo '</form>';

echo '<hr><h3>Existing Scores</h3>';

foreach ($scores as $s) {
    echo "<p>
        <strong>{$s['ASSESSMENT']}</strong> – Score: {$s['SCORE']} – Date: {$s['DATE_TAKEN']}<br>
        {$s['COMMENTS']}<br>
        <a href=\"?action=delete&id={$s['ID']}\">[Delete]</a>
    </p>";
}
