<?php

$statement = array();

$statement[] = "ALTER TABLE core_item_concretion DROP CONSTRAINT core_item_concretion_include_id_fkey;";
$statement[] = "ALTER TABLE core_item_concretion ADD CONSTRAINT core_item_concretion_include_id_fkey FOREIGN KEY (include_id) REFERENCES core_base_includes(id) ON DELETE CASCADE DEFERRABLE;";

$statement[] = "ALTER TABLE core_item_holders DROP CONSTRAINT core_item_holders_include_id_fkey;";
$statement[] = "ALTER TABLE core_item_holders ADD CONSTRAINT core_item_holders_include_id_fkey FOREIGN KEY (include_id) REFERENCES core_base_includes(id) ON DELETE CASCADE DEFERRABLE;";

?>