<?php
// Goalkeepers Only System - Frontend Interface
// This file should be included in the main index.php

if (!defined('DB_CONNECTION_AVAILABLE') || !DB_CONNECTION_AVAILABLE) {
    echo '<div class="alert alert-danger">Database connection not available. Please check your configuration.</div>';
    return;
}

// Variables are set in index.php: $goalkeepersOnlyResult, $locationFilter

// Get available locations for filter
if (!isset($availableGoalkeeperLocations)) {
    $availableGoalkeeperLocations = getAvailableGoalkeeperLocations($pdo);
}

// Get the main report data
if (!isset($goalkeepersOnlyResult)) {
    $locationFilter = $_POST['location_filter'] ?? 'all';
    if ($locationFilter && $locationFilter !== 'all') {
        $goalkeepersOnlyResult = getGoalkeepersByLocation($pdo, $locationFilter);
    } else {
        $goalkeepersOnlyResult = getGoalkeepersOnlyReport($pdo);
    }
}
?>

<style>
.goalkeepers-container {
    max-width: 100%;
    padding: 20px;
}

.header {
    background: #28a745;
    color: white;
    padding: 20px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.header h2 {
    margin: 0;
}

.filter-section {
    background: #f8f9fa;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 20px;
}

.filter-form {
    display: flex;
    gap: 15px;
    align-items: end;
}

.form-group {
    flex: 1;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-group select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 3px;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn:hover {
    opacity: 0.9;
}

.stats-section {
    margin-bottom: 20px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.stat-card {
    background: white;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 15px;
    text-align: center;
}

.stat-number {
    font-size: 2em;
    font-weight: bold;
    color: #28a745;
}

.stat-label {
    color: #666;
    font-size: 0.9em;
}

.export-section {
    text-align: right;
    margin-bottom: 15px;
}

.table-container {
    background: white;
    border: 1px solid #ddd;
    border-radius: 5px;
    overflow: hidden;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    background: #28a745;
    color: white;
    padding: 12px;
    text-align: left;
    font-weight: bold;
}

.data-table td {
    padding: 10px 12px;
    border-bottom: 1px solid #eee;
}

.data-table tr:hover {
    background: #f8f9fa;
}

.membership-badge {
    background: #007bff;
    color: white;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 0.9em;
    font-weight: bold;
}

.age-badge {
    background: #6c757d;
    color: white;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.8em;
}

.location-badge {
    background: #17a2b8;
    color: white;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.8em;
}

.contact-info {
    font-size: 0.9em;
}

.contact-info a {
    color: #007bff;
    text-decoration: none;
}

.no-data {
    text-align: center;
    padding: 40px;
    color: #666;
    font-style: italic;
}

.criteria-info {
    background: #e9ecef;
    padding: 15px;
    border-radius: 5px;
    margin-top: 20px;
    font-size: 0.9em;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 5px;
}

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

@media (max-width: 768px) {
    .filter-form {
        flex-direction: column;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .data-table {
        font-size: 0.9em;
    }
    
    .data-table th,
    .data-table td {
        padding: 8px;
    }
}
</style>

<div class="goalkeepers-container">
    <div class="header">
        <h2>🥅 Goalkeepers Only Report</h2>
        <p>Active members who have ONLY been assigned as goalkeepers in team formations</p>
    </div>
    
    <div class="filter-section">
        <h3>Filter by Location</h3>
        <form method="POST" class="filter-form">
            <input type="hidden" name="action" value="filter_goalkeepers">
            <div class="form-group">
                <label>Location:</label>
                <select name="location_filter">
                    <option value="all" <?php echo (!isset($_POST['location_filter']) || $_POST['location_filter'] === 'all') ? 'selected' : ''; ?>>
                        All Locations
                    </option>
                    <?php if ($availableGoalkeeperLocations['success']): ?>
                        <?php foreach ($availableGoalkeeperLocations['data'] as $location): ?>
                            <option value="<?php echo htmlspecialchars($location); ?>" 
                                    <?php echo (isset($_POST['location_filter']) && $_POST['location_filter'] === $location) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($location); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>
    
    <?php if ($goalkeepersOnlyResult['success']): ?>
        <div class="stats-section">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo isset($goalkeepersOnlyResult['totalGoalkeepers']) ? $goalkeepersOnlyResult['totalGoalkeepers'] : count($goalkeepersOnlyResult['data']); ?></div>
                    <div class="stat-label">Total Exclusive Goalkeepers</div>
                </div>
                
                <?php if (isset($goalkeepersOnlyResult['locationStats'])): ?>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo count($goalkeepersOnlyResult['locationStats']); ?></div>
                        <div class="stat-label">Locations with Goalkeepers</div>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($goalkeepersOnlyResult['ageStats'])): ?>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo count($goalkeepersOnlyResult['ageStats']); ?></div>
                        <div class="stat-label">Age Groups Represented</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="export-section">
            <a href="?action=export_goalkeepers_csv<?php echo isset($_POST['location_filter']) && $_POST['location_filter'] !== 'all' ? '&location=' . urlencode($_POST['location_filter']) : ''; ?>" class="btn btn-success">
                📥 Export to CSV
            </a>
        </div>
        
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Membership #</th>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Contact Info</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($goalkeepersOnlyResult['data'])): ?>
                        <?php foreach ($goalkeepersOnlyResult['data'] as $goalkeeper): ?>
                            <tr>
                                <td>
                                    <span class="membership-badge">
                                        #<?php echo htmlspecialchars($goalkeeper['membershipNumber']); ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($goalkeeper['firstName'] . ' ' . $goalkeeper['lastName']); ?></strong>
                                </td>
                                <td>
                                    <span class="age-badge"><?php echo htmlspecialchars($goalkeeper['age']); ?> years</span>
                                </td>
                                <td class="contact-info">
                                    <?php if ($goalkeeper['phone']): ?>
                                        <div>📞 <?php echo htmlspecialchars($goalkeeper['phone']); ?></div>
                                    <?php endif; ?>
                                    <?php if ($goalkeeper['email']): ?>
                                        <div>📧 
                                            <a href="mailto:<?php echo htmlspecialchars($goalkeeper['email']); ?>">
                                                <?php echo htmlspecialchars($goalkeeper['email']); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!$goalkeeper['phone'] && !$goalkeeper['email']): ?>
                                        <div style="color: #666; font-style: italic;">No contact info</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="location-badge">
                                        <?php echo htmlspecialchars($goalkeeper['currentLocationName']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="no-data">
                                🥅 No exclusive goalkeepers found matching the criteria.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="criteria-info">
            <strong>Report Criteria:</strong> This report shows active club members who:
            <ul>
                <li>Have been assigned to at least one team formation session as a goalkeeper</li>
                <li>Have NEVER been assigned to any formation session with a role other than goalkeeper</li>
                <li>Are currently active members</li>
            </ul>
            Results are sorted by location name (ascending), then by membership number (ascending).
            
            <?php if (isset($goalkeepersOnlyResult['locationStats']) && !empty($goalkeepersOnlyResult['locationStats'])): ?>
                <br><br><strong>Distribution by Location:</strong>
                <?php foreach ($goalkeepersOnlyResult['locationStats'] as $location => $count): ?>
                    <br>• <?php echo htmlspecialchars($location); ?>: <?php echo $count; ?> goalkeeper(s)
                <?php endforeach; ?>
            <?php endif; ?>
            
            <?php if (isset($goalkeepersOnlyResult['ageStats']) && !empty($goalkeepersOnlyResult['ageStats'])): ?>
                <br><br><strong>Distribution by Age Group:</strong>
                <?php foreach ($goalkeepersOnlyResult['ageStats'] as $ageGroup => $count): ?>
                    <br>• <?php echo htmlspecialchars($ageGroup); ?>: <?php echo $count; ?> goalkeeper(s)
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
    <?php else: ?>
        <div class="alert alert-danger">
            <h4>Error Loading Report</h4>
            <p><?php echo htmlspecialchars($goalkeepersOnlyResult['message']); ?></p>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple loading state for buttons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            if (this.type === 'submit') {
                this.innerHTML = '⏳ Loading...';
                this.disabled = true;
            }
        });
    });
});
</script>
