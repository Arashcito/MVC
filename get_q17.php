<?php
require 'bootstrap.php'; // instead of index.php

if (!isset($_GET['locationID'])) {
    echo "<p style='color:red;'>Missing location ID.</p>";
    exit;
}

$locationID = (int) $_GET['locationID'];
$results = getQualifiedFamilyMembersQ17($pdo, $locationID);

if (empty($results)) {
    echo "<p>No qualified family members found for this location.</p>";
    exit;
}

echo "<table class='data-table'>";
echo "<thead><tr><th>First Name</th><th>Last Name</th><th>Phone</th></tr></thead><tbody>";

foreach ($results as $row) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['firstName']) . "</td>";
    echo "<td>" . htmlspecialchars($row['lastName']) . "</td>";
    echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
    echo "</tr>";
}

echo "</tbody></table>";
?>
