<?php

function GetAssessmentTypes()
{
    return DBGet("SELECT ID, TITLE, SHORT_NAME FROM assessments_types ORDER BY SORT_ORDER");
}
