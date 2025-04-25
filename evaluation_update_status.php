<?php
require_once 'includes/config.php';
require_once 'includes/data_access.php';

if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $evaluation_id = isset($_POST['evaluation_id']) ? (int)$_POST['evaluation_id'] : 0;
    $new_status = isset($_POST['new_status']) ? $_POST['new_status'] : '';
    
    
    $valid_statuses = ['draft', 'submitted', 'reviewed', 'approved'];
    
    if ($evaluation_id > 0 && in_array($new_status, $valid_statuses)) {
        try {
            
            $stmt = $pdo->prepare("SELECT * FROM evaluations WHERE evaluation_id = ?");
            $stmt->execute([$evaluation_id]);
            $evaluation = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$evaluation) {
                $_SESSION['error_message'] = "Evaluation not found.";
                header("Location: evaluations.php");
                exit();
            }
            
           
            $can_update = false;
            if ($_SESSION['user_id'] === $evaluation['evaluator_id'] && $evaluation['status'] === 'draft' && $new_status === 'submitted') {
                $can_update = true;
            } elseif ($_SESSION['user_role'] === 'admin' && in_array($evaluation['status'], ['submitted', 'reviewed']) && in_array($new_status, ['reviewed', 'approved'])) {
                $can_update = true;
            }
            
            if (!$can_update) {
                $_SESSION['error_message'] = "You don't have permission to perform this action.";
                header("Location: evaluation_view.php?id={$evaluation_id}");
                exit();
            }
            
            $updateQuery = "UPDATE evaluations SET status = ?";
            
           
            if ($new_status === 'submitted') {
                $updateQuery .= ", submitted_at = NOW()";
            } elseif ($new_status === 'reviewed') {
                $updateQuery .= ", reviewed_at = NOW()";
            } elseif ($new_status === 'approved') {
                $updateQuery .= ", approved_at = NOW()";
            }
            
            $updateQuery .= " WHERE evaluation_id = ?";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute([$new_status, $evaluation_id]);
            
            
            recordAuditTrail(
                $_SESSION['user_id'],
                'UPDATE',
                'evaluations',
                $evaluation_id,
                ['status' => $evaluation['status']],
                ['status' => $new_status]
            );
            
            $_SESSION['success_message'] = "Evaluation status updated to " . ucfirst($new_status) . ".";
            
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Database error: " . $e->getMessage();
        }
    } else {
        $_SESSION['error_message'] = "Invalid parameters.";
    }
    
    
    header("Location: evaluation_view.php?id={$evaluation_id}");
    exit();
} else {
    
    header("Location: evaluations.php");
    exit();
}
?>