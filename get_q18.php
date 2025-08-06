<?php
require 'bootstrap.php';

$results = getQualifiedFamilyMembersQ18($pdo);

if (empty($results)) {
    echo "<p>No qualified members found.</p>";
    exit;
}

echo "<table class='data-table'>";
echo "<thead><tr>
        <th>Name</th>
        <th>Age</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Location</th>
      </tr></thead><tbody>";

foreach ($results as $m) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($m['firstName'] . ' ' . $m['lastName']) . "</td>";
    echo "<td>" . htmlspecialchars($m['age']) . "</td>";
    echo "<td>" . htmlspecialchars($m['phone']) . "</td>";
    echo "<td>" . htmlspecialchars($m['email']) . "</td>";
    echo "<td>" . htmlspecialchars($m['locationName']) . "</td>";
    echo "</tr>";
}

echo "</tbody></table>";
?>
