<?php
session_start();
require_once 'db.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['UserID']) || $_SESSION['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Check if claim ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "Invalid claim ID.";
    header("Location: admin_dashboard.php");
    exit();
}

$claim_id = intval($_GET['id']);

// Handle rejection reason if provided via POST
$rejection_reason = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rejection_reason'])) {
    $rejection_reason = trim($_POST['rejection_reason']);
}

// If no rejection reason provided and it's a GET request, show form
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get claim details for the form
    $get_claim_query = "
        SELECT mc.*, u.Name as StudentName, u.MatricID 
        FROM merit_claim mc 
        JOIN user u ON mc.UserID = u.UserID 
        WHERE mc.ClaimID = ? AND mc.Claim_Status IN ('Pending', 'Submitted')
    ";
    
    $stmt = $conn->prepare($get_claim_query);
    $stmt->bind_param("i", $claim_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['error_message'] = "Claim not found or already processed.";
        header("Location: admin_dashboard.php");
        exit();
    }
    
    $claim = $result->fetch_assoc();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reject Claim - MyPetakom</title>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
            
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: 'Poppins', sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1rem;
            }
            
            .container {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 1rem;
                padding: 2rem;
                max-width: 600px;
                width: 100%;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            }
            
            .header {
                text-align: center;
                margin-bottom: 2rem;
            }
            
            .header h1 {
                color: #ef4444;
                font-size: 1.75rem;
                margin-bottom: 0.5rem;
            }
            
            .claim-info {
                background: #f9fafb;
                border-radius: 0.75rem;
                padding: 1.5rem;
                margin-bottom: 1.5rem;
                border-left: 4px solid #ef4444;
            }
            
            .claim-info h3 {
                color: #111827;
                margin-bottom: 1rem;
            }
            
            .claim-details {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 0.75rem;
            }
            
            .claim-details span {
                color: #6b7280;
                font-size: 0.9rem;
                padding: 0.5rem;
                background: white;
                border-radius: 0.5rem;
            }
            
            .form-group {
                margin-bottom: 1.5rem;
            }
            
            .form-group label {
                display: block;
                margin-bottom: 0.5rem;
                font-weight: 600;
                color: #374151;
            }
            
            .form-group textarea {
                width: 100%;
                padding: 0.75rem;
                border: 2px solid #e5e7eb;
                border-radius: 0.5rem;
                font-family: inherit;
                font-size: 0.9rem;
                resize: vertical;
                min-height: 120px;
                transition: border-color 0.2s;
            }
            
            .form-group textarea:focus {
                outline: none;
                border-color: #ef4444;
            }
            
            .buttons {
                display: flex;
                gap: 1rem;
                justify-content: center;
            }
            
            .btn {
                padding: 0.75rem 1.5rem;
                border: none;
                border-radius: 0.5rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
                text-decoration: none;
                display: inline-block;
                text-align: center;
            }
            
            .btn-reject {
                background: linear-gradient(135deg, #ef4444, #dc2626);
                color: white;
            }
            
            .btn-reject:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
            }
            
            .btn-cancel {
                background: #6b7280;
                color: white;
            }
            
            .btn-cancel:hover {
                background: #4b5563;
            }
            
            @media (max-width: 768px) {
                .claim-details {
                    grid-template-columns: 1fr;
                }
                
                .buttons {
                    flex-direction: column;
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>⚠️ Reject Merit Claim</h1>
                <p>Please provide a reason for rejecting this claim</p>
            </div>
            
            <div class="claim-info">
                <h3>Claim Details</h3>
                <div class="claim-details">
                    <span><strong>Claim ID:</strong> #<?= htmlspecialchars($claim['ClaimID']) ?></span>
                    <span><strong>Student:</strong> <?= htmlspecialchars($claim['StudentName']) ?></span>
                    <span><strong>Matric ID:</strong> <?= htmlspecialchars($claim['MatricID']) ?></span>
                    <span><strong>Event:</strong> <?= htmlspecialchars($claim['EventName']) ?></span>
                    <span><strong>Event Date:</strong> <?= date('M j, Y', strtotime($claim['EventDate'])) ?></span>
                    <span><strong>Organizer:</strong> <?= htmlspecialchars($claim['Organizer']) ?></span>
                    <span><strong>Position:</strong> <?= htmlspecialchars($claim['Position']) ?></span>
                    <span><strong>Level:</strong> <?= htmlspecialchars($claim['Level']) ?></span>
                    <span><strong>Submitted:</strong> <?= date('M j, Y g:i A', strtotime($claim['Submitted_Date'])) ?></span>
                    <?php if ($claim['Supporting_Doc']): ?>
                        <span><strong>Document:</strong> <a href="uploads/<?= htmlspecialchars($claim['Supporting_Doc']) ?>" target="_blank" style="color: #2563eb;">View Document</a></span>
                    <?php endif; ?>
                </div>
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label for="rejection_reason">Reason for Rejection *</label>
                    <textarea 
                        id="rejection_reason" 
                        name="rejection_reason" 
                        placeholder="Please explain why this claim is being rejected (e.g., insufficient documentation, invalid event details, etc.)..."
                        required
                    ></textarea>
                </div>
                
                <div class="buttons">
                    <button type="submit" class="btn btn-reject">Reject Claim</button>
                    <a href="admin_dashboard.php" class="btn btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Process the rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($rejection_reason)) {
        $_SESSION['error_message'] = "Rejection reason is required.";
        header("Location: reject_claim.php?id=$claim_id");
        exit();
    }
    
    try {
        // First, get the claim details
        $get_claim_query = "
            SELECT mc.*, u.Name as StudentName, u.MatricID 
            FROM merit_claim mc 
            JOIN user u ON mc.UserID = u.UserID 
            WHERE mc.ClaimID = ? AND mc.Claim_Status IN ('Pending', 'Submitted')
        ";
        
        $stmt = $conn->prepare($get_claim_query);
        $stmt->bind_param("i", $claim_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Claim not found or already processed.");
        }
        
        $claim = $result->fetch_assoc();
        
        // Update the claim status to 'Rejected'
        // Note: Since your table doesn't have rejection_reason column, we'll just update the status
        $reject_query = "
            UPDATE merit_claim 
            SET Claim_Status = 'Rejected' 
            WHERE ClaimID = ?
        ";
        
        $stmt = $conn->prepare($reject_query);
        $stmt->bind_param("i", $claim_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to reject claim.");
        }
        
        $_SESSION['success_message'] = "Merit claim #$claim_id has been rejected. Reason: " . htmlspecialchars($rejection_reason);
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error rejecting claim: " . $e->getMessage();
    }
    
    // Redirect back to admin dashboard
    header("Location: admin_dashboard.php");
    exit();
}
?>