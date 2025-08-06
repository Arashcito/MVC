<!-- Payment System Form -->
<div id="make-payment" class="report-section" style="display: block;">
    <div class="section-header">
        <h2 class="section-title">Make a Payment</h2>
        <div class="section-info">
            <p>Process membership payments with automatic installment tracking and member status management.</p>
        </div>
    </div>
    
    <div class="report-form">
        <form id="paymentForm" method="POST" action="">
            <input type="hidden" name="action" value="process_payment">
            
            <div class="payment-form-grid">
                <div class="form-group">
                    <label for="memberID">Member ID <span class="required">*</span>:</label>
                    <input type="number" id="memberID" name="memberID" placeholder="Enter Member ID (numbers only)" required min="1" step="1">
                    <small class="form-help">Enter the numeric Member ID</small>
                </div>
                
                <div class="form-group">
                    <label for="membershipYear">Membership Year <span class="required">*</span>:</label>
                    <input type="number" id="membershipYear" name="membershipYear" value="<?php echo date('Y'); ?>" required min="2020" max="2030" step="1">
                    <small class="form-help">Year for which payment is being made</small>
                </div>
                
                <div class="form-group">
                    <label for="amount">Amount <span class="required">*</span>:</label>
                    <input type="number" id="amount" name="amount" placeholder="0.00" required min="0.01" step="0.01">
                    <small class="form-help">Payment amount (decimal allowed)</small>
                </div>
                
                <div class="form-group">
                    <label for="method">Payment Method <span class="required">*</span>:</label>
                    <select id="method" name="method" required>
                        <option value="">Select Payment Method</option>
                        <option value="cash">Cash</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="debit_card">Debit Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="cheque">Cheque</option>
                        <option value="online">Online Payment</option>
                    </select>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <span id="submitText">Process Payment</span>
                    <span id="submitLoader" style="display: none;">Processing...</span>
                </button>
                <button type="reset" class="btn btn-secondary">Clear Form</button>
                <button type="button" class="btn btn-info" onclick="checkPaymentHistory()">Check Payment History</button>
            </div>
        </form>
        
        <!-- Payment Status Display -->
        <div id="paymentStatus" class="payment-status" style="display: none;">
            <div id="statusContent"></div>
        </div>
        
        <!-- Payment History Modal -->
        <div id="paymentHistoryModal" class="payment-modal" style="display: none;">
            <div class="payment-modal-content">
                <div class="payment-modal-header">
                    <h3>Payment History</h3>
                    <span class="payment-modal-close" onclick="closePaymentHistory()">&times;</span>
                </div>
                <div id="paymentHistoryContent">
                    <!-- History will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment System Styles -->
<style>
    .section-info {
        margin-bottom: 15px;
        color: #6c757d;
        font-style: italic;
    }
    
    .payment-form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .form-help {
        display: block;
        margin-top: 5px;
        font-size: 0.875em;
        color: #6c757d;
        font-style: italic;
    }
    
    .required {
        color: #dc3545;
        font-weight: bold;
    }
    
    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .btn-info {
        background-color: #17a2b8;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }
    
    .btn-info:hover {
        background-color: #138496;
    }
    
    .payment-status {
        margin-top: 20px;
        padding: 15px;
        border-radius: 5px;
        border: 1px solid #ddd;
    }
    
    .status-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }
    
    .status-error {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }
    
    .status-info {
        background-color: #d1ecf1;
        border-color: #bee5eb;
        color: #0c5460;
    }
    
    .payment-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
    }
    
    .payment-modal-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 20px;
        border-radius: 8px;
        max-width: 600px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
    }
    
    .payment-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }
    
    .payment-modal-close {
        font-size: 24px;
        cursor: pointer;
        color: #999;
    }
    
    .payment-modal-close:hover {
        color: #333;
    }
    
    .payment-history-item {
        padding: 10px;
        border: 1px solid #eee;
        margin-bottom: 10px;
        border-radius: 4px;
    }
    
    .payment-history-item h4 {
        margin: 0 0 5px 0;
        color: #333;
    }
    
    .payment-history-item p {
        margin: 2px 0;
        color: #666;
        font-size: 14px;
    }
</style>

<!-- Payment System JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentForm = document.getElementById('paymentForm');
        const statusDiv = document.getElementById('paymentStatus');
        const statusContent = document.getElementById('statusContent');
        
        if (paymentForm) {
            paymentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Show loading state
                showLoadingState(true);
                showPaymentStatus('Processing payment...', 'info');
                
                // Get form data
                const formData = new FormData(paymentForm);
                
                // Validate inputs
                const memberID = parseInt(formData.get('memberID'));
                const membershipYear = parseInt(formData.get('membershipYear'));
                const amount = parseFloat(formData.get('amount'));
                const method = formData.get('method');
                
                if (!memberID || memberID < 1) {
                    showPaymentStatus('❌ Error: Please enter a valid Member ID (positive number)', 'error');
                    showLoadingState(false);
                    return;
                }
                
                if (!membershipYear || membershipYear < 2020 || membershipYear > 2030) {
                    showPaymentStatus('❌ Error: Please enter a valid Membership Year (2020-2030)', 'error');
                    showLoadingState(false);
                    return;
                }
                
                if (!amount || amount <= 0) {
                    showPaymentStatus('❌ Error: Please enter a valid amount (greater than 0)', 'error');
                    showLoadingState(false);
                    return;
                }
                
                if (!method) {
                    showPaymentStatus('❌ Error: Please select a payment method', 'error');
                    showLoadingState(false);
                    return;
                }
                
                // Submit via AJAX
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    // Reload page to show result
                    window.location.reload();
                })
                .catch(error => {
                    showPaymentStatus('❌ Error: Network error occurred', 'error');
                    showLoadingState(false);
                });
            });
        }
        
        function showPaymentStatus(message, type) {
            statusContent.innerHTML = message;
            statusDiv.className = 'payment-status status-' + type;
            statusDiv.style.display = 'block';
            
            // Auto-hide info messages after 3 seconds
            if (type === 'info') {
                setTimeout(() => {
                    statusDiv.style.display = 'none';
                }, 3000);
            }
        }
        
        function showLoadingState(loading) {
            const submitText = document.getElementById('submitText');
            const submitLoader = document.getElementById('submitLoader');
            const submitBtn = paymentForm.querySelector('button[type="submit"]');
            
            if (loading) {
                submitText.style.display = 'none';
                submitLoader.style.display = 'inline';
                submitBtn.disabled = true;
            } else {
                submitText.style.display = 'inline';
                submitLoader.style.display = 'none';
                submitBtn.disabled = false;
            }
        }
        
        // Make functions globally available
        window.showPaymentStatus = showPaymentStatus;
        window.showLoadingState = showLoadingState;
    });
    
    function checkPaymentHistory() {
        const memberID = document.getElementById('memberID').value;
        
        if (!memberID || memberID < 1) {
            showPaymentStatus('❌ Please enter a valid Member ID first', 'error');
            return;
        }
        
        // Show modal
        document.getElementById('paymentHistoryModal').style.display = 'block';
        document.getElementById('paymentHistoryContent').innerHTML = '<p>Loading payment history...</p>';
        
        // Fetch payment history via AJAX (placeholder for now)
        setTimeout(() => {
            document.getElementById('paymentHistoryContent').innerHTML = `
                <div class="payment-history-item">
                    <h4>Payment History for Member ${memberID}</h4>
                    <p>This feature will show detailed payment history when integrated with the backend.</p>
                    <p><strong>Note:</strong> Payment history lookup will be implemented in the next phase.</p>
                </div>
            `;
        }, 500);
    }
    
    function closePaymentHistory() {
        document.getElementById('paymentHistoryModal').style.display = 'none';
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('paymentHistoryModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }
</script>
