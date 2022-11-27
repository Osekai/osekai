<?php
$payments = Database::execSimpleSelect("SELECT * FROM Payments ORDER BY PayDate DESC");
$donations = Database::execSimpleSelect("SELECT * FROM Donations WHERE Checked = 1 Order by DonoDate DESC");
$topdonators = Database::execSimpleSelect("SELECT SUM(DonoAmount) AS TotalDonation, Username FROM Donations WHERE Checked = 1 GROUP BY Username ORDER BY SUM(DonoAmount) DESC");

$costs = Database::execSimpleSelect("SELECT SUM(Amount) AS Costs FROM Payments")[0]['Costs'];
$donationTotal = Database::execSimpleSelect("SELECT SUM(DonoAmount) AS DonationTotal FROM Donations WHERE Checked = 1")[0]['DonationTotal'];
$percentage = round(intval($donationTotal) * 100 / intval($costs), 2);
?>