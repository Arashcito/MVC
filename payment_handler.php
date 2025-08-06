<?php
/**
 * Payment System Handler
 * Handles all payment processing logic for the Volleyball Club Management System
 */

// Ensure this file is included from the main application
if (!defined('DB_CONNECTION_AVAILABLE')) {
    die('Direct access not allowed');
}

/**
 * Process payment with comprehensive validation and business logic
 */
function processPayment($pdo, $data) {
    try {
        $pdo->beginTransaction();
        
        // Extract variables
        $mID = (int)$data['memberID'];
        $mYear = (int)$data['membershipYear'];
        $mAmount = (float)$data['amount'];
        $mTypeMethod = $data['method'];
        
        // 1. Check if memberID exists in ClubMember table
        $stmt = $pdo->prepare("SELECT memberID, memberType FROM ClubMember WHERE memberID = ?");
        $stmt->execute([$mID]);
        $member = $stmt->fetch();
        
        if (!$member) {
            $pdo->rollback();
            return ['success' => false, 'message' => "❌ Error: Member ID $mID not found in the system."];
        }
        
        $memberType = $member['memberType'];
        
        // 2. Count existing installments for this member and year
        $stmt = $pdo->prepare("SELECT COUNT(*) as totalInstallments FROM Payment WHERE memberID = ? AND membershipYear = ?");
        $stmt->execute([$mID, $mYear]);
        $result = $stmt->fetch();
        $totalInstallments = (int)$result['totalInstallments'];
        
        // 3. Check if already made 4 installments
        if ($totalInstallments >= 4) {
            $pdo->rollback();
            return ['success' => false, 'message' => "❌ Payment Not Allowed: Member ID $mID has already completed all 4 installments for year $mYear. No additional payments are allowed for this membership year."];
        }
        
        // 4. If this is the 4th installment (totalInstallments = 3)
        if ($totalInstallments == 3) {
            // Calculate total paid so far
            $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as totalPaidAmount FROM Payment WHERE memberID = ? AND membershipYear = ?");
            $stmt->execute([$mID, $mYear]);
            $result = $stmt->fetch();
            $totalPaidAmount = (float)$result['totalPaidAmount'];
            
            // Calculate remaining amount based on member type
            $fullAmount = ($memberType == 'Major') ? 200.0 : 100.0;
            $remainingAmount = $fullAmount - $totalPaidAmount;
            
            // Check if payment amount is sufficient for final installment
            if ($mAmount < $remainingAmount) {
                $pdo->rollback();
                return ['success' => false, 'message' => "❌ Insufficient Amount: The 4th installment should have $" . number_format($remainingAmount, 2) . ". You entered $" . number_format($mAmount, 2) . ". Please enter at least $" . number_format($remainingAmount, 2) . " to complete the final payment."];
            }
        }
        
        // 5. Insert the payment record
        $nextInstallment = $totalInstallments + 1;
        $stmt = $pdo->prepare("INSERT INTO Payment (memberID, amount, method, paymentDate, membershipYear, installmentNo) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $mID,
            $mAmount,
            $mTypeMethod,
            date('Y-m-d'),
            $mYear,
            $nextInstallment
        ]);
        
        // 6. Check if member is inactive and should be activated
        $stmt = $pdo->prepare("SELECT status FROM ClubMember WHERE memberID = ?");
        $stmt->execute([$mID]);
        $memberStatus = $stmt->fetch();
        
        $responseMessage = "✅ Payment processed successfully! Installment #$nextInstallment recorded for Member ID $mID (Amount: $" . number_format($mAmount, 2) . ").";
        
        if ($memberStatus && $memberStatus['status'] == 'Inactive') {
            // Check if they've now paid in full for current year
            $currentYear = date('Y');
            if ($mYear == $currentYear) {
                $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as totalPaid FROM Payment WHERE memberID = ? AND membershipYear = ?");
                $stmt->execute([$mID, $currentYear]);
                $result = $stmt->fetch();
                $totalPaid = (float)$result['totalPaid'];
                
                $requiredAmount = ($memberType == 'Major') ? 200.0 : 100.0;
                
                if ($totalPaid >= $requiredAmount) {
                    // Update member status to Active
                    $stmt = $pdo->prepare("UPDATE ClubMember SET status = 'Active' WHERE memberID = ?");
                    $stmt->execute([$mID]);
                    
                    // Get member name
                    $stmt = $pdo->prepare("SELECT firstName FROM Person WHERE pID = ?");
                    $stmt->execute([$mID]);
                    $personResult = $stmt->fetch();
                    $memberName = $personResult ? $personResult['firstName'] : "Member $mID";
                    
                    $responseMessage .= " 🎉 Member '$memberName' has paid in full and status changed to Active!";
                }
            }
        }
        
        $pdo->commit();
        return ['success' => true, 'message' => $responseMessage];
        
    } catch (PDOException $e) {
        $pdo->rollback();
        error_log("Payment processing error: " . $e->getMessage());
        return ['success' => false, 'message' => "❌ Database error occurred while processing payment."];
    }
}

/**
 * Get payment summary for a member
 */
function getPaymentSummary($pdo, $memberID, $year = null) {
    try {
        if ($year === null) {
            $year = date('Y');
        }
        
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as installmentCount,
                COALESCE(SUM(amount), 0) as totalPaid,
                MAX(paymentDate) as lastPaymentDate
            FROM Payment 
            WHERE memberID = ? AND membershipYear = ?
        ");
        $stmt->execute([$memberID, $year]);
        
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Payment summary error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get member payment history
 */
function getMemberPaymentHistory($pdo, $memberID, $limit = 10) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                paymentID,
                amount,
                method,
                paymentDate,
                membershipYear,
                installmentNo
            FROM Payment 
            WHERE memberID = ? 
            ORDER BY paymentDate DESC, installmentNo DESC
            LIMIT ?
        ");
        $stmt->execute([$memberID, $limit]);
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Payment history error: " . $e->getMessage());
        return [];
    }
}
?>
