<?php
// Secondary Family Info System - Frontend Interface
// This file should be included in the main index.php

if (!defined('DB_CONNECTION_AVAILABLE') || !DB_CONNECTION_AVAILABLE) {
    echo '<div class="alert alert-danger">Database connection not available. Please check your configuration.</div>';
    return;
}

// Variables are now set in index.php:
// $searchResults, $familyInfo, $searchPerformed

// Get all families for overview (limited to prevent performance issues)
if (!isset($allFamiliesResult)) {
    $allFamiliesResult = getAllSecondaryFamilyInfo($pdo);
}
?>

<style>
.secondary-family-container {
    max-width: 100%;
    margin: 20px auto;
    padding: 20px;
}

.secondary-family-header {
    background: linear-gradient(135deg, #e83e8c 0%, #6f42c1 100%);
    color: white;
    padding: 20px;
    border-radius: 10px 10px 0 0;
    margin-bottom: 0;
}

.search-section {
    background-color: #f8f9fa;
    padding: 20px;
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 10px 10px;
    margin-bottom: 20px;
}

.search-form {
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

.form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ced4da;
    border-radius: 5px;
    font-size: 14px;
}

.btn-search {
    background: linear-gradient(135deg, #e83e8c 0%, #6f42c1 100%);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-weight: 600;
    height: fit-content;
}

.btn-search:hover {
    background: linear-gradient(135deg, #d91a72 0%, #5a2d91 100%);
}

.search-results {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    overflow: hidden;
}

.results-header {
    background: linear-gradient(135deg, #17a2b8 0%, #007bff 100%);
    color: white;
    padding: 15px;
    font-weight: 600;
}

.family-member-card {
    padding: 15px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.family-member-card:last-child {
    border-bottom: none;
}

.family-member-card:hover {
    background-color: #f8f9fa;
}

.member-info {
    flex: 1;
}

.member-name {
    font-weight: 600;
    color: #495057;
    margin-bottom: 5px;
}

.member-id {
    color: #6c757d;
    font-size: 0.9em;
}

.btn-view-details {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 0.9em;
}

.btn-view-details:hover {
    background: linear-gradient(135deg, #218838 0%, #1ca085 100%);
}

.family-details {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    overflow: hidden;
}

.details-header {
    background: linear-gradient(135deg, #fd7e14 0%, #dc3545 100%);
    color: white;
    padding: 15px;
}

.secondary-member-info {
    background-color: #e7f3ff;
    padding: 20px;
    border-bottom: 2px solid #007bff;
}

.secondary-member-card {
    background: white;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.club-members-section {
    padding: 20px;
}

.club-member-card {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    border-left: 4px solid #28a745;
}

.club-member-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.membership-number {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.85em;
    font-weight: 600;
}

.member-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 10px;
    margin-top: 10px;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
    border-bottom: 1px solid #e9ecef;
}

.detail-label {
    font-weight: 600;
    color: #495057;
}

.detail-value {
    color: #6c757d;
    text-align: right;
}

.overview-section {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.overview-table {
    width: 100%;
    border-collapse: collapse;
}

.overview-table th {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
    padding: 12px;
    text-align: left;
    font-weight: 600;
}

.overview-table td {
    padding: 10px 12px;
    border-bottom: 1px solid #e9ecef;
}

.overview-table tr:hover {
    background-color: #f8f9fa;
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

.no-results {
    text-align: center;
    padding: 40px;
    color: #6c757d;
    font-style: italic;
}

@media (max-width: 768px) {
    .search-form {
        flex-direction: column;
    }
    
    .form-group {
        min-width: 100%;
    }
    
    .family-member-card {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .member-details-grid {
        grid-template-columns: 1fr;
    }
    
    .club-member-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
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

<div class="secondary-family-container">
    <div class="secondary-family-header">
        <h2 style="margin: 0;"><i class="fas fa-users"></i> Secondary Family Information System</h2>
        <p style="margin: 5px 0 0 0; opacity: 0.9;">Search and view secondary family member details and associated club members</p>
    </div>
    
    <div class="search-section">
        <h3 style="margin-bottom: 15px; color: #495057;">Search Family Member</h3>
        <form method="POST" class="search-form">
            <input type="hidden" name="action" value="search_family">
            <div class="form-group">
                <label>First Name:</label>
                <input type="text" name="first_name" value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" placeholder="Enter first name..." required>
            </div>
            <div class="form-group">
                <label>Last Name:</label>
                <input type="text" name="last_name" value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" placeholder="Enter last name..." required>
            </div>
            <button type="submit" class="btn-search">
                <i class="fas fa-search"></i> Search
            </button>
        </form>
    </div>
    
    <?php if ($searchPerformed): ?>
        <div class="search-results">
            <div class="results-header">
                <i class="fas fa-search"></i> Search Results (<?php echo $searchResults['count']; ?> found)
            </div>
            
            <?php if ($searchResults['success'] && $searchResults['count'] > 0): ?>
                <?php foreach ($searchResults['data'] as $member): ?>
                    <div class="family-member-card">
                        <div class="member-info">
                            <div class="member-name">
                                <?php echo htmlspecialchars($member['firstName'] . ' ' . $member['lastName']); ?>
                            </div>
                            <div class="member-id">Family ID: <?php echo htmlspecialchars($member['familyMemID']); ?></div>
                        </div>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="get_family_info">
                            <input type="hidden" name="family_mem_id" value="<?php echo htmlspecialchars($member['familyMemID']); ?>">
                            <button type="submit" class="btn-view-details">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-search"></i><br>
                    No family members found with the specified name.
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($familyInfo): ?>
        <?php if ($familyInfo['success']): ?>
            <div class="export-buttons">
                <a href="?action=export_secondary_family_csv&family_id=<?php echo htmlspecialchars($_POST['family_mem_id']); ?>" class="btn-export">
                    <i class="fas fa-download"></i> Export This Family
                </a>
            </div>
            
            <div class="family-details">
                <div class="details-header">
                    <h3 style="margin: 0;"><i class="fas fa-user-friends"></i> Family Details</h3>
                </div>
                
                <div class="secondary-member-info">
                    <h4 style="margin-bottom: 15px; color: #007bff;">
                        <i class="fas fa-user"></i> Secondary Family Member
                    </h4>
                    <div class="secondary-member-card">
                        <div class="member-details-grid">
                            <div class="detail-item">
                                <span class="detail-label">Name:</span>
                                <span class="detail-value">
                                    <?php echo htmlspecialchars($familyInfo['data']['secondaryMember']['firstName'] . ' ' . $familyInfo['data']['secondaryMember']['lastName']); ?>
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Phone:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($familyInfo['data']['secondaryMember']['phone'] ?: 'No phone number'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="club-members-section">
                    <h4 style="margin-bottom: 15px; color: #28a745;">
                        <i class="fas fa-users"></i> Associated Club Members (<?php echo $familyInfo['clubMemberCount']; ?>)
                    </h4>
                    
                    <?php foreach ($familyInfo['data']['clubMembers'] as $clubMember): ?>
                        <div class="club-member-card">
                            <div class="club-member-header">
                                <h5 style="margin: 0; color: #495057;">
                                    <?php echo htmlspecialchars($clubMember['firstName'] . ' ' . $clubMember['lastName']); ?>
                                </h5>
                                <span class="membership-number">
                                    #<?php echo htmlspecialchars($clubMember['clubMembershipNumber']); ?>
                                </span>
                            </div>
                            
                            <div class="member-details-grid">
                                <div class="detail-item">
                                    <span class="detail-label">Date of Birth:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($clubMember['dateOfBirth']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">SSN:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($clubMember['ssn']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Medicare:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($clubMember['medicare']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Phone:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($clubMember['phone']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Address:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($clubMember['address']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">City:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($clubMember['city']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Province:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($clubMember['province']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Postal Code:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($clubMember['postalCode']); ?></span>
                                </div>
                                <div class="detail-item" style="grid-column: 1 / -1;">
                                    <span class="detail-label">Relationship:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($clubMember['relationshipWithSecondary']); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                <h4>Error</h4>
                <p><?php echo htmlspecialchars($familyInfo['message']); ?></p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <div class="export-buttons">
        <a href="?action=export_secondary_family_csv" class="btn-export">
            <i class="fas fa-download"></i> Export All Families
        </a>
    </div>
    
    <div class="overview-section">
        <div class="results-header">
            <i class="fas fa-list"></i> All Families with Secondary Members (<?php echo $allFamiliesResult['count']; ?> families)
        </div>
        
        <?php if ($allFamiliesResult['success'] && $allFamiliesResult['count'] > 0): ?>
            <table class="overview-table">
                <thead>
                    <tr>
                        <th>Primary Member</th>
                        <th>Secondary Member</th>
                        <th>Secondary Phone</th>
                        <th>Club Members</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allFamiliesResult['data'] as $family): ?>
                        <tr>
                            <td>
                                <?php echo htmlspecialchars($family['primaryMember']['firstName'] . ' ' . $family['primaryMember']['lastName']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($family['secondaryMember']['firstName'] . ' ' . $family['secondaryMember']['lastName']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($family['secondaryMember']['phone'] ?: 'No phone'); ?></td>
                            <td><?php echo count($family['clubMembers']); ?> member(s)</td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="get_family_info">
                                    <input type="hidden" name="family_mem_id" value="<?php echo htmlspecialchars($family['primaryMember']['familyMemID']); ?>">
                                    <button type="submit" class="btn-view-details" style="font-size: 0.8em; padding: 5px 10px;">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-results">
                <i class="fas fa-users"></i><br>
                No families with secondary members found in the system.
            </div>
        <?php endif; ?>
    </div>
    
    <div style="margin-top: 20px; padding: 15px; background-color: #e9ecef; border-radius: 5px; font-size: 0.9em; color: #495057;">
        <strong>How to use:</strong> Enter the first and last name of a family member to search. 
        Select a family member from the results to view their secondary family member details and all associated club members.
        The system shows comprehensive information including contact details, membership numbers, and relationships.
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add loading states to buttons
    const buttons = document.querySelectorAll('.btn-search, .btn-view-details, .btn-export');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            if (this.type === 'submit') {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                this.disabled = true;
                
                // Re-enable after form submission
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }, 100);
            }
        });
    });
    
    // Add smooth scrolling to results
    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function() {
            setTimeout(() => {
                const results = document.querySelector('.search-results');
                if (results) {
                    results.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 100);
        });
    }
});
</script>
