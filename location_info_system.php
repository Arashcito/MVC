<?php
// Location Info System - Frontend Interface
// This file should be included in the main index.php

if (!defined('DB_CONNECTION_AVAILABLE') || !DB_CONNECTION_AVAILABLE) {
    echo '<div class="alert alert-danger">Database connection not available. Please check your configuration.</div>';
    return;
}

// Get location data
$locationResult = getLocationInfo($pdo);
?>

<style>
.location-info-container {
    max-width: 100%;
    margin: 20px auto;
    padding: 20px;
}

.location-stats-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 10px 10px 0 0;
    margin-bottom: 0;
}

.location-stats-summary {
    background-color: #f8f9fa;
    padding: 15px;
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 10px 10px;
    margin-bottom: 20px;
}

.location-table-container {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.location-table {
    width: 100%;
    margin: 0;
    border-collapse: collapse;
}

.location-table th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 10px;
    text-align: left;
    font-weight: 600;
    border: none;
    position: sticky;
    top: 0;
    z-index: 10;
}

.location-table td {
    padding: 12px 10px;
    border-bottom: 1px solid #e9ecef;
    vertical-align: top;
}

.location-table tr:hover {
    background-color: #f8f9fa;
}

.location-table tr:nth-child(even) {
    background-color: #fbfbfb;
}

.location-table tr:nth-child(even):hover {
    background-color: #f0f0f0;
}

.location-type-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.85em;
    font-weight: 500;
}

.type-head {
    background-color: #dc3545;
    color: white;
}

.type-branch {
    background-color: #28a745;
    color: white;
}

.member-count {
    text-align: center;
    font-weight: 600;
}

.minor-count {
    color: #17a2b8;
}

.major-count {
    color: #fd7e14;
}

.team-count {
    color: #6f42c1;
}

.export-buttons {
    margin-bottom: 20px;
    text-align: right;
}

.btn-export {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
    background: linear-gradient(135deg, #218838 0%, #1ca085 100%);
    color: white;
    text-decoration: none;
}

.no-data {
    text-align: center;
    padding: 40px;
    color: #6c757d;
    font-style: italic;
}

.phone-numbers {
    max-width: 150px;
    word-wrap: break-word;
}

.website-link {
    max-width: 120px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.website-link a {
    color: #007bff;
    text-decoration: none;
}

.website-link a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .location-table {
        font-size: 0.85em;
    }
    
    .location-table th,
    .location-table td {
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

.location-table-wrapper {
    max-height: 70vh;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 10px;
}

.stats-row {
    display: flex;
    justify-content: space-around;
    text-align: center;
}

.stat-item {
    flex: 1;
}

.stat-number {
    font-size: 1.5em;
    font-weight: bold;
    color: #495057;
}

.stat-label {
    font-size: 0.9em;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
</style>

<div class="location-info-container">
    <div class="location-stats-header">
        <h2 style="margin: 0;"><i class="fas fa-map-marker-alt"></i> Location Information System</h2>
        <p style="margin: 5px 0 0 0; opacity: 0.9;">Complete details for all volleyball club locations</p>
    </div>
    
    <?php if ($locationResult['success']): ?>
        <div class="location-stats-summary">
            <div class="stats-row">
                <div class="stat-item">
                    <div class="stat-number"><?php echo $locationResult['count']; ?></div>
                    <div class="stat-label">Total Locations</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">
                        <?php 
                        $totalMinor = array_sum(array_column($locationResult['data'], 'minorMemberCount'));
                        echo $totalMinor; 
                        ?>
                    </div>
                    <div class="stat-label">Minor Members</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">
                        <?php 
                        $totalMajor = array_sum(array_column($locationResult['data'], 'majorMemberCount'));
                        echo $totalMajor; 
                        ?>
                    </div>
                    <div class="stat-label">Major Members</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">
                        <?php 
                        $totalTeams = array_sum(array_column($locationResult['data'], 'teamCount'));
                        echo $totalTeams; 
                        ?>
                    </div>
                    <div class="stat-label">Total Teams</div>
                </div>
            </div>
        </div>
        
        <div class="export-buttons">
            <a href="?action=export_location_csv" class="btn-export">
                <i class="fas fa-download"></i> Export to CSV
            </a>
        </div>
        
        <div class="location-table-container">
            <div class="location-table-wrapper">
                <table class="location-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Address</th>
                            <th>City</th>
                            <th>Province</th>
                            <th>Postal Code</th>
                            <th>Phone(s)</th>
                            <th>Website</th>
                            <th>Type</th>
                            <th>Capacity</th>
                            <th>Manager</th>
                            <th>Minor Members</th>
                            <th>Major Members</th>
                            <th>Teams</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($locationResult['data'] as $location): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($location['locationID']); ?></td>
                                <td><?php echo htmlspecialchars($location['address']); ?></td>
                                <td><?php echo htmlspecialchars($location['city']); ?></td>
                                <td><?php echo htmlspecialchars($location['province']); ?></td>
                                <td><?php echo htmlspecialchars($location['postalCode']); ?></td>
                                <td class="phone-numbers"><?php echo htmlspecialchars($location['phones']); ?></td>
                                <td class="website-link">
                                    <?php if ($location['webAddress'] !== 'No website'): ?>
                                        <a href="<?php echo htmlspecialchars($location['webAddress']); ?>" target="_blank">
                                            <?php echo htmlspecialchars($location['webAddress']); ?>
                                        </a>
                                    <?php else: ?>
                                        <span style="color: #6c757d; font-style: italic;">No website</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="location-type-badge type-<?php echo strtolower($location['type']); ?>">
                                        <?php echo htmlspecialchars($location['type']); ?>
                                    </span>
                                </td>
                                <td class="member-count"><?php echo htmlspecialchars($location['maxCapacity']); ?></td>
                                <td><?php echo htmlspecialchars($location['managerName']); ?></td>
                                <td class="member-count minor-count"><?php echo htmlspecialchars($location['minorMemberCount']); ?></td>
                                <td class="member-count major-count"><?php echo htmlspecialchars($location['majorMemberCount']); ?></td>
                                <td class="member-count team-count"><?php echo htmlspecialchars($location['teamCount']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div style="margin-top: 20px; padding: 15px; background-color: #e9ecef; border-radius: 5px; font-size: 0.9em; color: #495057;">
            <strong>Note:</strong> Locations are sorted by Province (ascending), then by City (ascending). 
            Data includes complete address information, contact details, capacity, management, and membership statistics.
        </div>
        
    <?php else: ?>
        <div class="alert alert-danger">
            <h4>Error Loading Location Information</h4>
            <p><?php echo htmlspecialchars($locationResult['message']); ?></p>
        </div>
    <?php endif; ?>
</div>

<script>
// Add some interactive features
document.addEventListener('DOMContentLoaded', function() {
    // Add click handlers for export functionality
    const exportButton = document.querySelector('.btn-export');
    if (exportButton) {
        exportButton.addEventListener('click', function(e) {
            // Show loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exporting...';
            this.style.pointerEvents = 'none';
            
            // Reset after a delay to show the export is happening
            setTimeout(() => {
                this.innerHTML = originalText;
                this.style.pointerEvents = 'auto';
            }, 2000);
        });
    }
    
    // Add hover effects for location type badges
    const typeBadges = document.querySelectorAll('.location-type-badge');
    typeBadges.forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
            this.style.transition = 'transform 0.2s ease';
        });
        
        badge.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
</script>
