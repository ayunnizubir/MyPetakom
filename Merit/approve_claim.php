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

// Start transaction
$conn->begin_transaction();

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
    
    // Calculate marks based on position and level
    $marks = 0;
    switch ($claim['Level']) {
        case 'International':
            $marks = ($claim['Position'] === 'Main Committee') ? 100 : 
                    (($claim['Position'] === 'Committee') ? 70 : 50);
            break;
        case 'National':
            $marks = ($claim['Position'] === 'Main Committee') ? 80 : 
                    (($claim['Position'] === 'Committee') ? 50 : 40);
            break;
        case 'State':
            $marks = ($claim['Position'] === 'Main Committee') ? 60 : 
                    (($claim['Position'] === 'Committee') ? 40 : 30);
            break;
        case 'District':
            $marks = ($claim['Position'] === 'Main Committee') ? 40 : 
                    (($claim['Position'] === 'Committee') ? 30 : 15);
            break;
        case 'UMPSA':
            $marks = ($claim['Position'] === 'Main Committee') ? 30 : 
                    (($claim['Position'] === 'Committee') ? 20 : 5);
            break;
        default:
            $marks = 5; // Default marks
    }
    
    // Update the claim status to 'Approved'
    $approve_query = "
        UPDATE merit_claim 
        SET Claim_Status = 'Approved' 
        WHERE ClaimID = ?
    ";
    
    $stmt = $conn->prepare($approve_query);
    $stmt->bind_param("i", $claim_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to approve claim.");
    }
    
    // Add the merit to the merit table
    $insert_merit_query = "
        INSERT INTO merit (UserID, EventName, Date, Organizer, Position, Level, Marks) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ";
    
    $stmt = $conn->prepare($insert_merit_query);
    $stmt->bind_param("isssssi", 
        $claim['UserID'], 
        $claim['EventName'], 
        $claim['EventDate'], 
        $claim['Organizer'], 
        $claim['Position'], 
        $claim['Level'], 
        $marks
    );
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to add merit to student record.");
    }
    
    // Commit transaction
    $conn->commit();
    
    $_SESSION['success_message'] = "Merit claim #$claim_id has been successfully approved for student {$claim['StudentName']}. Merit points awarded: $marks";
    
} catch (Exception $e) {
    // Rollback transaction
    $conn->rollback();
    $_SESSION['error_message'] = "Error approving claim: " . $e->getMessage();
}

// Redirect back to admin dashboard
header("Location: admin_dashboard.php");
exit();
?>