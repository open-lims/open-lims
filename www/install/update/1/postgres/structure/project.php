<?php

$statement = array();

$statement[] = "ALTER TABLE core_project_log DROP COLUMN action_checksum";

$statement[] = "DROP FUNCTION get_subproject_samples(integer);";
$statement[] = "DROP FUNCTION get_subproject_methods(integer);";

?>