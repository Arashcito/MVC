<?php
require 'bootstrap.php';

$results = getQualifiedFamilyMembersQ19($pdo);

if (empty($results)) {
    echo "<p>No qualified volunteer personnel found.</p>";
    exit;
}

echo "<table class='data-table'>";
echo "<thead><tr>
        <th>Name</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Minor Count</th>
        <th>Location</th>
        <th>Role</th>
      </tr></thead><tbody>";

foreach ($results as $p) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($p['firstName'] . ' ' . $p['lastName']) . "</td>";
    echo "<td>" . htmlspecialchars($p['phone']) . "</td>";
    echo "<td>" . htmlspecialchars($p['email']) . "</td>";
    echo "<td>" . htmlspecialchars($p['minorCount']) . "</td>";
    echo "<td>" . htmlspecialchars($p['locationName']) . "</td>";
    echo "<td>" . htmlspecialchars($p['role']) . "</td>";
    echo "</tr>";
}

echo "</tbody></table>";
?>
