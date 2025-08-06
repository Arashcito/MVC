<?php
// Minors to Majors System - Frontend Interface
// This file should be included in the main index.php

if (!defined('DB_CONNECTION_AVAILABLE') || !DB_CONNECTION_AVAILABLE) {
    echo '<div class="alert alert-danger">Database connection not available. Please check your configuration.</div>';
    return;
}

// Variables are set in index.php: $minorsToMajorsResult, $locationFilter

// Get available locations for filter
if (!isset($availableLocations)) {
    $availableLocations = getAvailableLocations($pdo);
}

// Get the main report data
if (!isset($minorsToMajorsResult)) {
    $locationFilter = $_POST['location_filter'] ?? 'all';
    if ($locationFilter && $locationFilter !== 'all') {
        $minorsToMajorsResult = getMinorsToMajorsByLocation($pdo, $locationFilter);
    } else {
        $minorsToMajorsResult = getMinorsToMajorsReport($pdo);
    }
}
?>

<style>
.minors-to-majors-container {
    max-width: 100%;
    margin: 20px auto;
    padding: 20px;
}

.minors-majors-header {
    background: linear-gradient(135deg, #fd7e14 0%, #e83e8c 100%);
    color: white;
    padding: 20px;
    border-radius: 10px 10px 0 0;
    margin-bottom: 0;
}

.filter-section {
    background-color: #f8f9fa;
    padding: 20px;
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 10px 10px;
    margin-bottom: 20px;
}

.filter-form {
    display: flex;
    gap: 15px;
    align-items: end;
    flex-wrap: wrap;
}

.form-group {
    flex: 1;
    min-width: 200px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #495057;
}

.form-group select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ced4da;
    border-radius: 5px;
    font-size: 14px;
    background-color: white;
}

.btn-filter {
    background: linear-gradient(135deg, #fd7e14 0%, #e83e8c 100%);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-weight: 600;
    height: fit-content;
}

.btn-filter:hover {
    background: linear-gradient(135deg, #e8680b 0%, #d91a72 100%);
}

.stats-summary {
    background-color: #f8f9fa;
    padding: 20px;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    margin-bottom: 20px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.stat-card {
    background: white;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #fd7e14;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.stat-number {
    font-size: 2em;
    font-weight: bold;
    color: #fd7e14;
    margin-bottom: 5px;
}

.stat-label {
    color: #6c757d;
    font-size: 0.9em;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.members-table-container {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 20px;
}

.members-table-wrapper {
    max-height: 70vh;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 10px;
}

.members-table {
    width: 100%;
    margin: 0;
    border-collapse: collapse;
}

.members-table th {
    background: linear-gradient(135deg, #fd7e14 0%, #e83e8c 100%);
    color: white;
    padding: 15px 10px;
    text-align: left;
    font-weight: 600;
    border: none;
    position: sticky;
    top: 0;
    z-index: 10;
}

.members-table td {
    padding: 12px 10px;
    border-bottom: 1px solid #e9ecef;
    vertical-align: top;
}

.members-table tr:hover {
    background-color: #f8f9fa;
}

.members-table tr:nth-child(even) {
    background-color: #fbfbfb;
}

.members-table tr:nth-child(even):hover {
    background-color: #f0f0f0;
}

.membership-badge {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.85em;
    font-weight: 600;
}

.age-badge {
    background-color: #17a2b8;
    color: white;
    padding: 3px 6px;
    border-radius: 10px;
    font-size: 0.8em;
    font-weight: 500;
}

.location-badge {
    background-color: #6f42c1;
    color: white;
    padding: 3px 6px;
    border-radius: 10px;
    font-size: 0.8em;
    font-weight: 500;
}

.contact-info {
    font-size: 0.9em;
    color: #6c757d;
}

.contact-info a {
    color: #007bff;
    text-decoration: none;
}

.contact-info a:hover {
    text-decoration: underline;
}

.export-buttons {
    margin-bottom: 20px;
    text-align: right;
}

.btn-export {
    background: linear-gradient(135deg, #17a2b8 0%, #007bff 100%);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    margin-left: 10px;
}

.btn-export:hover {
    background: linear-gradient(135deg, #138496 0%, #0056b3 100%);
    color: white;
    text-decoration: none;
}

.no-data {
    text-align: center;
    padding: 40px;
    color: #6c757d;
    font-style: italic;
}

.location-stats, .age-stats {
    margin-top: 10px;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
    border-bottom: 1px solid #e9ecef;
}

.stat-item:last-child {
    border-bottom: none;
}

.stat-item-label {
    font-weight: 500;
    color: #495057;
}

.stat-item-value {
    color: #fd7e14;
    font-weight: 600;
}

@media (max-width: 768px) {
    .filter-form {
        flex-direction: column;
    }
    
    .form-group {
        min-width: 100%;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .members-table {
        font-size: 0.85em;
    }
    
    .members-table th,
    .members-table td {
        padding: 8px 5px;
    }
    
    .export-buttons {
        text-align: center;
    }
    
    .btn-export {
        display: block;
        margin: 5px auto;
        width: 200px;
    }
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 5px;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

.alert-info {
    color: #0c5460;
    background-color: #d1ecf1;
    border-color: #bee5eb;
}
</style>

<div class="minors-to-majors-container">
    <div class="minors-majors-header">
        <h2 style="margin: 0;"><i class="fas fa-user-graduate"></i> Minors to Majors Report</h2>
        <p style="margin: 5px 0 0 0; opacity: 0.9;">Active major members who started as minors - sorted by location and age</p>
    </div>
    
    <div class="filter-section">
        <h3 style="margin-bottom: 15px; color: #495057;">Filter by Location</h3>
        <form method="POST" class="filter-form">
            <input type="hidden" name="action" value="filter_minors_majors">
            <div class="form-group">
                <label>Location:</label>
                <select name="location_filter">
                    <option value="all" <?php echo (!isset($_POST['location_filter']) || $_POST['location_filter'] === 'all') ? 'selected' : ''; ?>>
                        All Locations
                    </option>
                    <?php if ($availableLocations['success']): ?>
                        <?php foreach ($availableLocations['data'] as $location): ?>
                            <option value="<?php echo htmlspecialchars($location); ?>" 
                                    <?php echo (isset($_POST['location_filter']) && $_POST['location_filter'] === $location) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($location); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <button type="submit" class="btn-filter">
                <i class="fas fa-filter"></i> Filter
            </button>
        </form>
    </div>
    
    <?php if ($minorsToMajorsResult['success']): ?>
        <div class="stats-summary">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo isset($minorsToMajorsResult['totalMembers']) ? $minorsToMajorsResult['totalMembers'] : count($minorsToMajorsResult['data']); ?></div>
                    <div class="stat-label">Total Members</div>
                </div>
                
                <?php if (isset($minorsToMajorsResult['locationStats'])): ?>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo count($minorsToMajorsResult['locationStats']); ?></div>
                        <div class="stat-label">Locations</div>
                        <div class="location-stats">
                            <?php foreach ($minorsToMajorsResult['locationStats'] as $location => $count): ?>
                                <div class="stat-item">
                                    <span class="stat-item-label"><?php echo htmlspecialchars($location); ?></span>
                                    <span class="stat-item-value"><?php echo $count; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($minorsToMajorsResult['ageStats'])): ?>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo count($minorsToMajorsResult['ageStats']); ?></div>
                        <div class="stat-label">Age Groups</div>
                        <div class="age-stats">
                            <?php foreach ($minorsToMajorsResult['ageStats'] as $ageGroup => $count): ?>
                                <div class="stat-item">
                                    <span class="stat-item-label"><?php echo htmlspecialchars($ageGroup); ?> years</span>
                                    <span class="stat-item-value"><?php echo $count; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="export-buttons">
            <a href="?action=export_minors_majors_csv<?php echo isset($_POST['location_filter']) && $_POST['location_filter'] !== 'all' ? '&location=' . urlencode($_POST['location_filter']) : ''; ?>" class="btn-export">
                <i class="fas fa-download"></i> Export to CSV
            </a>
        </div>
        
        <div class="members-table-container">
            <div class="members-table-wrapper">
                <table class="members-table">
                    <thead>
                        <tr>
                            <th>Membership #</th>
                            <th>Name</th>
                            <th>Date Joined</th>
                            <th>Current Age</th>
                            <th>Contact Info</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($minorsToMajorsResult['data'])): ?>
                            <?php foreach ($minorsToMajorsResult['data'] as $member): ?>
                                <tr>
                                    <td>
                                        <span class="membership-badge">
                                            #<?php echo htmlspecialchars($member['membershipNumber']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($member['firstName'] . ' ' . $member['lastName']); ?></strong>
                                        <?php if (isset($member['ageWhenJoined'])): ?>
                                            <br><small style="color: #6c757d;">Joined at age <?php echo $member['ageWhenJoined']; ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars(date('M j, Y', strtotime($member['dateOfJoining']))); ?>
                                        <?php if (isset($member['yearsAsMember'])): ?>
                                            <br><small style="color: #6c757d;"><?php echo $member['yearsAsMember']; ?> years as member</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="age-badge"><?php echo htmlspecialchars($member['currentAge']); ?> years</span>
                                    </td>
                                    <td class="contact-info">
                                        <?php if ($member['phone']): ?>
                                            <div><i class="fas fa-phone"></i> <?php echo htmlspecialchars($member['phone']); ?></div>
                                        <?php endif; ?>
                                        <?php if ($member['email']): ?>
                                            <div><i class="fas fa-envelope"></i> 
                                                <a href="mailto:<?php echo htmlspecialchars($member['email']); ?>">
                                                    <?php echo htmlspecialchars($member['email']); ?>
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <div style="color: #6c757d; font-style: italic;">No contact info</div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="location-badge">
                                            <?php echo htmlspecialchars($member['currentLocationName']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="no-data">
                                    <i class="fas fa-users"></i><br>
                                    No members found matching the criteria.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div style="margin-top: 20px; padding: 15px; background-color: #e9ecef; border-radius: 5px; font-size: 0.9em; color: #495057;">
            <strong>Report Criteria:</strong> Shows active major club members who:
            <ul style="margin: 10px 0 0 20px;">
                <li>Currently have "Major" membership status</li>
                <li>Are currently "Active" members</li>
                <li>Have a family member ID (indicating they were minors when they joined)</li>
                <li>Were under 18 years old when they joined the club</li>
            </ul>
            Results are sorted by location name (ascending), then by current age (ascending).
        </div>
        
    <?php else: ?>
        <div class="alert alert-danger">
            <h4>Error Loading Report</h4>
            <p><?php echo htmlspecialchars($minorsToMajorsResult['message']); ?></p>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add loading states to buttons
    const buttons = document.querySelectorAll('.btn-filter, .btn-export');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            if (this.type === 'submit') {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                this.disabled = true;
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }, 100);
            }
        });
    });
    
    // Add hover effects for badges
    const badges = document.querySelectorAll('.membership-badge, .age-badge, .location-badge');
    badges.forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.style.transition = 'transform 0.2s ease';
        });
        
        badge.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
</script>
