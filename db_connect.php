<?php
/**
 * Database Configuration File
 * 
 * Establishes a PDO connection to MySQL database
 * Contains sensitive credentials - keep secure
 */

// Database connection parameters
$host = 'localhost';      // Database server
$db   = 'qr_oms';         // Database name
$user = 'root';         // Database username
$pass = '';           // Database password
$charset = 'utf8mb4';     // Character encoding

// Data Source Name (connection string)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// PDO connection options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Function to update a menu item
function updateMenuItem($pdo, $id, $name, $price, $category, $image, $status) {
    // If new image was uploaded
    if ($image !== null) {
        $stmt = $pdo->prepare("UPDATE Item_Details 
                             SET Item_Name = ?, Item_Price = ?, Item_Category = ?, 
                             Item_Image = ?, Item_Status = ? 
                             WHERE Item_Id = ?");
        return $stmt->execute([$name, $price, $category, $image, $status, $id]);
    } else {
        // Keep existing image
        $stmt = $pdo->prepare("UPDATE Item_Details 
                             SET Item_Name = ?, Item_Price = ?, Item_Category = ?, 
                             Item_Status = ? 
                             WHERE Item_Id = ?");
        return $stmt->execute([$name, $price, $category, $status, $id]);
    }
}

// Function to delete a menu item
function deleteMenuItem($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM Item_Details WHERE Item_Id = ?");
    return $stmt->execute([$id]);
}

// Function to add a new menu item
function addMenuItem($pdo, $name, $price, $category, $image, $status) {
    // First get the next available ID
    $stmt = $pdo->query("SELECT MAX(CAST(SUBSTRING(Item_Id, 2) AS UNSIGNED)) as max_id FROM Item_Details");
    $result = $stmt->fetch();
    $nextNum = ($result['max_id'] ?? 0) + 1;
    $newId = 'I' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    
    $stmt = $pdo->prepare("INSERT INTO Item_Details 
                          (Item_Id, Item_Name, Item_Price, Item_Category, Item_Image, Item_Status) 
                          VALUES (?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$newId, $name, $price, $category, $image, $status]);
}

// Fetch menu items by category
function getMenuItems($pdo, $category) {
    $stmt = $pdo->prepare("SELECT * FROM Item_Details WHERE Item_Category = ? AND Item_Status = 'Active' ORDER BY Item_Id DESC");
    $stmt->execute([$category]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllMenuItems($pdo) {
    $stmt = $pdo->query("SELECT * FROM Item_Details ORDER BY Item_Category, Item_Id DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$allItems = getAllMenuItems($pdo);
?>