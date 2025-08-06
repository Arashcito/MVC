<div class="section-header">
    <h2 class="section-title">All-Rounder Players Report</h2>
    <p style="color: #666; font-size: 14px; margin: 5px 0;">Members who have played in all four positions (Goalkeeper, Defender, Midfielder, Forward) in game sessions</p>
</div>

<?php if (isset($_SESSION['message'])): ?>
    <div class="alert <?php echo $_SESSION['success'] ? 'alert-success' : 'alert-error'; ?>">
        <?php echo htmlspecialchars($_SESSION['message']); ?>
    </div>
    <?php 
    unset($_SESSION['message']); 
    unset($_SESSION['success']); 
    ?>
<?php endif; ?>

<!-- Filter Form -->
<div class="filter-section">
    <form method="POST" action="index.php">
        <input type="hidden" name="action" value="filter_all_rounder_players">
        
        <div class="form-group">
            <label for="location_filter">Filter by Location:</label>
            <select name="location_filter" id="location_filter">
                <option value="all">All Locations</option>
                <?php foreach ($allRounderLocations as $location): ?>
                    <option value="<?php echo htmlspecialchars($location); ?>" 
                            <?php echo (isset($_POST['location_filter']) && $_POST['location_filter'] === $location) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($location); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button type="submit" class="btn">Filter Results</button>
        
        <!-- Export CSV Button -->
        <?php 
        $exportLocation = isset($_POST['location_filter']) && $_POST['location_filter'] !== 'all' ? $_POST['location_filter'] : null;
        $exportUrl = 'index.php?action=export_all_rounder_players_csv';
        if ($exportLocation) {
            $exportUrl .= '&location=' . urlencode($exportLocation);
        }
        ?>
        <a href="<?php echo $exportUrl; ?>" class="btn btn-secondary">Export to CSV</a>
    </form>
</div>

<!-- Summary Statistics -->
<?php if (isset($allRounderPlayersResult) && $allRounderPlayersResult['success']): ?>
    <div class="stats-summary">
        <h3>Summary</h3>
        <p><strong>Total All-Rounder Players:</strong> <?php echo $allRounderPlayersResult['totalMembers']; ?></p>
        
        <?php if (!empty($allRounderPlayersResult['locationStats'])): ?>
            <div class="stats-grid">
                <div class="stat-box">
                    <h4>By Location</h4>
                    <ul>
                        <?php foreach ($allRounderPlayersResult['locationStats'] as $location => $count): ?>
                            <li><?php echo htmlspecialchars($location) . ': ' . $count; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="stat-box">
                    <h4>By Age Group</h4>
                    <ul>
                        <?php foreach ($allRounderPlayersResult['ageStats'] as $ageGroup => $count): ?>
                            <li><?php echo $ageGroup . ': ' . $count; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Results Table -->
<?php if (isset($allRounderPlayersResult) && $allRounderPlayersResult['success'] && !empty($allRounderPlayersResult['data'])): ?>
    <div class="table-container">
        <table class="results-table">
            <thead>
                <tr>
                    <th>Membership #</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Age</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Current Location</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allRounderPlayersResult['data'] as $member): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($member['membershipNumber']); ?></td>
                        <td><?php echo htmlspecialchars($member['firstName']); ?></td>
                        <td><?php echo htmlspecialchars($member['lastName']); ?></td>
                        <td><?php echo htmlspecialchars($member['age']); ?></td>
                        <td><?php echo htmlspecialchars($member['phone'] ?: 'No phone'); ?></td>
                        <td><?php echo htmlspecialchars($member['email'] ?: 'No email'); ?></td>
                        <td><?php echo htmlspecialchars($member['currentLocationName']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php elseif (isset($allRounderPlayersResult) && $allRounderPlayersResult['success']): ?>
    <div class="no-results">
        <p>No all-rounder players found with the current filter criteria.</p>
    </div>
<?php elseif (isset($allRounderPlayersResult)): ?>
    <div class="error-message">
        <p>Error loading all-rounder players: <?php echo htmlspecialchars($allRounderPlayersResult['message']); ?></p>
    </div>
<?php endif; ?>

<style>
.filter-section {
    background: #f8f9fa;
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid #ddd;
}

.form-group {
    margin-bottom: 10px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-group select {
    width: 200px;
    padding: 8px;
    border: 1px solid #ddd;
}

.btn {
    background: #007cba;
    color: white;
    border: none;
    padding: 8px 16px;
    margin-right: 10px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
}

.btn:hover {
    background: #005a87;
}

.btn-secondary {
    background: #6c757d;
}

.btn-secondary:hover {
    background: #545b62;
}

.stats-summary {
    background: #f8f9fa;
    padding: 15px;
    margin-bottom: 20px;
    border-left: 4px solid #007cba;
}

.stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-top: 15px;
}

.stat-box {
    background: white;
    padding: 15px;
    border: 1px solid #ddd;
}

.stat-box h4 {
    margin-top: 0;
    color: #007cba;
}

.stat-box ul {
    list-style: none;
    padding: 0;
    margin: 10px 0 0 0;
}

.stat-box li {
    padding: 3px 0;
    border-bottom: 1px solid #eee;
}

.table-container {
    overflow-x: auto;
}

.results-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.results-table th,
.results-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

.results-table th {
    background: #f8f9fa;
    font-weight: bold;
}

.results-table tr:nth-child(even) {
    background: #f9f9f9;
}

.alert {
    padding: 12px;
    margin-bottom: 20px;
    border: 1px solid transparent;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.alert-error {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

.no-results {
    text-align: center;
    padding: 40px;
    color: #666;
    font-style: italic;
}

.error-message {
    color: #721c24;
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    padding: 15px;
    margin: 20px 0;
}
</style>
