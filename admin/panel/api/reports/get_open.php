<?php
echo json_encode(Database::execSimpleSelect("SELECT Reports.*, Ranking.name FROM Reports LEFT JOIN Ranking as Ranking on Ranking.id = Reports.ReporterId WHERE Status = 0 OR Status = 1"));
