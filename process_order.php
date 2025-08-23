<?php
include 'db_connect.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['items']) || !isset($input['tableId']) || !isset($input['total'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

try {
    // Generate order ID
    $orderId = 'ORD' . time() . rand(100, 999);
    
    // Prepare order data
    $itemsJson = json_encode($input['items']);
    $totalPrice = $input['total'];
    $tableId = $input['tableId'];
    $paymentMethod = isset($input['paymentMethod']) ? $input['paymentMethod'] : 'unknown';
    $orderTime = date('Y-m-d H:i:s');
    
    // Check if order_details table has the Table_Id column
    $checkColumn = $pdo->query("SHOW COLUMNS FROM order_details LIKE 'Table_Id'")->fetch();
    
    if ($checkColumn) {
        // Insert into database with Table_Id
        $stmt = $pdo->prepare("INSERT INTO order_details (Order_Id, Order_Items, Total_Price, Order_Time, Status, Table_Id) 
                              VALUES (?, ?, ?, ?, 'pending', ?)");
        $stmt->execute([$orderId, $itemsJson, $totalPrice, $orderTime, $tableId]);
    } else {
        // Insert into database without Table_Id (fallback)
        $stmt = $pdo->prepare("INSERT INTO order_details (Order_Id, Order_Items, Total_Price, Order_Time, Status) 
                              VALUES (?, ?, ?, ?, 'pending')");
        $stmt->execute([$orderId, $itemsJson, $totalPrice, $orderTime]);
    }
    
    // Update table status to occupied
    $updateTable = $pdo->prepare("UPDATE tables SET Status = 'Occupied' WHERE Table_Id = ?");
    $updateTable->execute([$tableId]);
    
    // Return success
    echo json_encode([
        'success' => true,
        'orderId' => $orderId,
        'message' => 'Order placed successfully'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to process order: ' . $e->getMessage()]);
}
?>