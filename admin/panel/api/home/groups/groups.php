<?php
echo json_encode(Database::execSimpleSelect("SELECT * FROM Groups"));