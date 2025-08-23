<?php
include 'db_connect.php';

// Fetch all items from the database at page load
$stmt = $pdo->query("SELECT * FROM Item_Details ORDER BY Item_Category, Item_Id DESC");
$allItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$specialItemsCount = 0;
foreach ($allItems as $item) {
    if ($item['Item_Category'] === 'Special Offers') {
        $specialItemsCount++;
    }
}



// Group items by category for the tabs and for filtering
$categoryItems = [];
$categories = [];
foreach ($allItems as $item) {
    $category = $item['Item_Category'];
    if (!isset($categoryItems[$category])) {
        $categoryItems[$category] = [];
    }
    $categoryItems[$category][] = $item;
    if (!in_array($category, $categories)) {
        $categories[] = $category;
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_item') {
        // Get form data
        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? '';
        $category = $_POST['category'] ?? '';
        $status = $_POST['status'] ?? 'Active';

        // Handle image upload
        $image = 'default.jpg'; // Default image if none uploaded

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $image = $targetPath;
            }
        }

        // Add to database
        if (addMenuItem($pdo, $name, $price, $category, $image, $status)) {
            echo json_encode(['success' => true, 'message' => 'Item added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add item']);
        }
        exit;
    }
    elseif ($_POST['action'] === 'update_item') {
        // Get form data
        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? '';
        $category = $_POST['category'] ?? '';
        $status = $_POST['status'] ?? 'Active';
        $image = null; // Default to null (no change)

        // Handle image upload if new image was provided
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $image = $targetPath;
            }
        }

        // Update in database
        if (updateMenuItem($pdo, $id, $name, $price, $category, $image, $status)) {
            echo json_encode(['success' => true, 'message' => 'Item updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update item']);
        }
        exit;
    }
}

// Handle delete requests
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete_item' && isset($_GET['id'])) {
    $id = $_GET['id'];
    if (deleteMenuItem($pdo, $id)) {
        echo json_encode(['success' => true, 'message' => 'Item deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete item']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Tasty Legs - Admin Dashboard</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap JS (bundle already includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<style>
    :root {
      --primary-red: #E4002B;
      --dark-red: #C10024;
      --dark: #231F20;
      --light: #FFFFFF;
      --yellow: #FFC72C;
      --light-gray: #F5F5F5;
      --medium-gray: #E0E0E0;
      --dark-gray: #757575;
      --success: #4CAF50;
      --warning: #FF9800;
      --info: #2196F3;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      -webkit-tap-highlight-color: transparent;
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: #f9f9f9;
      color: var(--dark);
      line-height: 1.6;
      overflow-x: hidden;
    }

    /* Admin Header */
    .admin-header {
      background-color: var(--primary-red);
      color: white;
      padding: 12px 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .admin-header .logo {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 18px;
      font-weight: 700;
    }

    .admin-header .logo i {
      font-size: 20px;
    }

    .admin-header .user-controls {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .admin-header .user-controls .notification {
      position: relative;
      cursor: pointer;
    }

    .admin-header .user-controls .notification .badge {
      position: absolute;
      top: -5px;
      right: -5px;
      background-color: var(--yellow);
      color: var(--dark);
      border-radius: 50%;
      width: 16px;
      height: 16px;
      font-size: 9px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
    }

    .admin-header .user-controls .user-profile {
      display: flex;
      align-items: center;
      gap: 6px;
      cursor: pointer;
    }

    .admin-header .user-controls .user-profile img {
      width: 28px;
      height: 28px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid white;
    }

    .admin-header .user-controls .user-profile span {
      font-size: 14px;
    }

    /* Admin Layout */
    .admin-container {
      display: flex;
      min-height: calc(100vh - 56px);
      position: relative;
    }

    /* Sidebar */
    .admin-sidebar {
      width: 250px;
      background-color: white;
      box-shadow: 2px 0 10px rgba(0,0,0,0.05);
      padding: 15px 0;
      transition: all 0.3s ease;
      transform: translateX(-100%);
      overflow-y: auto;
      position: fixed;
      top: 56px;
      left: 0;
      bottom: 0;
      z-index: 900;
    }

    .admin-sidebar.active {
      transform: translateX(0);
    }

    .admin-sidebar.collapsed {
      width: 70px;
    }

    .admin-sidebar.collapsed .menu-item span {
      display: none;
    }

    .admin-sidebar.collapsed .menu-item {
      justify-content: center;
    }

    .admin-sidebar.collapsed .sidebar-header {
      justify-content: center;
    }

    .sidebar-header {
      display: flex;
      align-items: center;
      padding: 0 15px 15px;
      border-bottom: 1px solid var(--medium-gray);
      margin-bottom: 15px;
    }

    .sidebar-header h3 {
      font-size: 14px;
      color: var(--dark-gray);
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-left: 8px;
    }

    .sidebar-header i {
      color: var(--dark-gray);
      font-size: 16px;
    }

    .menu {
      list-style: none;
      padding: 0;
    }

    .menu-item {
      padding: 10px 15px;
      display: flex;
      align-items: center;
      gap: 10px;
      cursor: pointer;
      transition: all 0.2s;
      border-left: 3px solid transparent;
    }

    .menu-item i {
      color: var(--dark-gray);
      font-size: 16px;
      width: 20px;
      text-align: center;
    }

    .menu-item span {
      font-size: 13px;
      font-weight: 500;
      color: var(--dark);
    }

    .menu-item:hover {
      background-color: rgba(228, 0, 43, 0.1);
      border-left-color: var(--primary-red);
    }

    .menu-item.active {
      background-color: rgba(228, 0, 43, 0.1);
      border-left-color: var(--primary-red);
    }

    .menu-item.active i,
    .menu-item.active span {
      color: var(--primary-red);
    }

    /* Main Content */
    .admin-content {
      flex: 1;
      padding: 15px;
      background-color: #f9f9f9;
      transition: all 0.3s;
      width: 100%;
      position: relative;
      z-index: 1;
      overflow: visible !important;
    }

    .admin-content.expanded {
      margin-left: -180px;
    }

    /* Dashboard Cards */
    .stats-cards {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 15px;
      margin-bottom: 20px;
      position: relative;
      z-index: 1;
    }

    .stats-cards {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 15px;
      margin-bottom: 20px;
      position: relative;
    }

    .stat-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .stat-card .card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
    }

    .stat-card .card-header h3 {
      font-size: 13px;
      color: var(--dark-gray);
      font-weight: 500;
    }

    .stat-card .card-header i {
      font-size: 18px;
      color: var(--dark-gray);
      opacity: 0.7;
    }

    .stat-card .card-body {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
    }

    .stat-card .card-body .value {
      font-size: 22px;
      font-weight: 700;
      color: var(--dark);
    }

    .stat-card .card-body .change {
      font-size: 11px;
      padding: 3px 6px;
      border-radius: 10px;
      font-weight: 600;
    }

    .stat-card .card-body .change.positive {
      background-color: rgba(76, 175, 80, 0.1);
      color: var(--success);
    }

    .stat-card .card-body .change.negative {
      background-color: rgba(244, 67, 54, 0.1);
      color: #F44336;
    }

    /* Menu Management Section */
    .section-header {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-bottom: 15px;
    }

    .section-header h2 {
      font-size: 18px;
      font-weight: 700;
      color: var(--dark);
      position: relative;
      padding-bottom: 6px;
    }

    .section-header h2::after {
      content: "";
      position: absolute;
      left: 0;
      bottom: 0;
      width: 35px;
      height: 2px;
      background-color: var(--primary-red);
    }

    .section-header .actions {
      display: flex;
      gap: 8px;
      overflow : visible;
      padding-bottom: 5px;
    }

    .section-header .actions::-webkit-scrollbar {
      display: none;
    }

    .btn {
      padding: 7px 12px;
      border-radius: 5px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      border: none;
      transition: all 0.2s;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      white-space: nowrap;
    }

    .btn-primary {
      background-color: var(--primary-red);
      color: white;
    }

    .btn-primary:hover {
      background-color: var(--dark-red);
    }

    .btn-outline {
      background-color: transparent;
      border: 1px solid var(--medium-gray);
      color: var(--dark);
    }

    .btn-outline:hover {
      background-color: var(--light-gray);
    }

    .btn-success {
      background-color: var(--success);
      color: white;
    }

    .btn-success:hover {
      background-color: #3d8b40;
    }

    .btn-danger {
      background-color: #F44336;
      color: white;
    }

    .btn-danger:hover {
      background-color: #d32f2f;
    }

    /* Confirmation Animation Modal - Improved */
    /* Updated Confirmation Animation Modal */
    #confirmationModal {
      position: fixed;
      top: 0; 
      left: 0; 
      width: 100vw; 
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 2000;
      background: rgba(0,0,0,0.7); /* Darker background for better contrast */
      backdrop-filter: blur(8px); /* More blur */
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.3s;
    }

    #confirmationModal.active {
      opacity: 1;
      visibility: visible;
    }

    #confirmationModal .modal-content {
      background: white;
      border-radius: 20px;
      padding: 30px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      width: auto; /* Changed from fixed width to auto */
      max-width: 90%;
      min-width: 300px; /* Minimum width to prevent too narrow */
      box-shadow: 0 10px 30px rgba(0,0,0,0.3);
      position: relative;
      overflow: hidden;
      text-align: center;
      transition: all 0.3s ease;
    }

    /* Add a subtle gradient background to the modal content */
    #confirmationModal .modal-content::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 8px;
      background: linear-gradient(90deg, var(--primary-red), var(--yellow));
    }

    .confirm-checkmark {
      width: 120px;
      height: 120px;
      position: relative;
      margin-bottom: 25px;
    }

    .confirm-checkmark svg {
      width: 120px;
      height: 120px;
      display: block;
    }

    .confirm-checkmark .circle {
      stroke: #4CAF50;
      stroke-width: 8;
      fill: none;
      stroke-dasharray: 314;
      stroke-dashoffset: 314;
      animation: drawCircle 0.5s ease-out forwards;
    }

    .confirm-checkmark .check {
      stroke: #4CAF50;
      stroke-width: 8;
      fill: none;
      stroke-dasharray: 70;
      stroke-dashoffset: 70;
      animation: drawCheck 0.4s 0.5s cubic-bezier(.77,0,.18,1) forwards;
    }

    .confirm-success-text {
      font-weight: 700;
      color: #333;
      text-align: center;
      margin: 15px 0 0;
      line-height: 1.4;
      max-width: 400px; /* Maximum width before text wraps */
      word-wrap: break-word;
    }

    @media (max-width: 480px) {
      #confirmationModal .modal-content {
        padding: 20px;
        min-width: 250px;
      }
      
      .confirm-success-text {
        font-size: 1rem;
        padding: 0 10px;
      }
    }

    /* Add a subtle pulse animation to the whole modal */
    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.02); }
      100% { transform: scale(1); }
    }

    #confirmationModal.active .modal-content {
      animation: pulse 2s infinite;
    }

    /* For delete confirmation, change the colors */
    #confirmationModal.delete-confirmation .confirm-checkmark .circle,
    #confirmationModal.delete-confirmation .confirm-checkmark .check {
      stroke: var(--primary-red);
    }

    /* For update confirmation, change the colors */
    #confirmationModal.update-confirmation .confirm-checkmark .circle,
    #confirmationModal.update-confirmation .confirm-checkmark .check {
      stroke: var(--info);
    }
    @keyframes drawCircle {
      to { stroke-dashoffset: 0; }
    }
    @keyframes drawCheck {
      to { stroke-dashoffset: 0; }
    }

    /* Categories Navigation */
    .categories-nav {
      display: flex;
      gap: 8px;
      padding: 12px 0;
      overflow-x: auto;
      scrollbar-width: none;
      margin-bottom: 15px;
      position: sticky;
      top: 56px;
      background-color: #f9f9f9;
      z-index: 100;
      border-bottom: 1px solid var(--medium-gray);
    }

    .categories-nav::-webkit-scrollbar {
      display: none;
    }

    .category-tab {
      padding: 6px 12px;
      border-radius: 18px;
      font-size: 13px;
      font-weight: 600;
      white-space: nowrap;
      background: var(--light-gray);
      color: var(--dark);
      cursor: pointer;
      transition: all 0.2s;
      border: 1px solid var(--medium-gray);
    }

    .category-tab.active {
      background: var(--primary-red);
      color: white;
      border-color: var(--primary-red);
    }

    /* Menu Items Grid */
    .menu-items-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
      gap: 15px;
      position: relative;
    }

    .menu-card {
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 3px 8px rgba(0,0,0,0.06);
      transition: all 0.3s;
      position: relative;
      border: 1px solid var(--medium-gray);
    }
    .menu-card .card-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        background-color: var(--yellow);
        color: var(--dark);
        padding: 3px 6px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 700;
        z-index: 2;
    }

    .menu-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 12px rgba(228, 0, 43, 0.15);
      border-color: var(--primary-red);
    }

    .menu-card .card-img-container {
      width: 100%;
      height: 120px;
      overflow: hidden;
      position: relative;
    }

    .menu-card .card-img-container img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.3s;
    }

    .menu-card:hover .card-img-container img {
      transform: scale(1.05);
    }

    /* .menu-card .card-badge {
      position: absolute;
      top: 8px;
      right: 8px;
      background-color: var(--yellow);
      color: var(--dark);
      padding: 3px 6px;
      border-radius: 3px;
      font-size: 11px;
      font-weight: 700;
    } */

    .menu-card .card-content {
      padding: 12px;
    }

    .menu-card .card-content h4 {
      margin: 0 0 6px;
      font-size: 14px;
      font-weight: 700;
      color: var(--dark);
    }

    .menu-card .card-content .price {
      color: var(--primary-red);
      font-size: 14px;
      font-weight: 800;
      margin-bottom: 8px;
    }

    .menu-card .card-content .description {
      font-size: 12px;
      color: var(--dark-gray);
      margin-bottom: 12px;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .menu-card .card-actions {
      display: flex;
      justify-content: space-between;
      gap: 8px;
    }

    .menu-card .card-actions .btn {
      flex: 1;
      padding: 6px;
      font-size: 12px;
      justify-content: center;
    }

    .menu-card .card-actions .btn i {
      font-size: 12px;
    }

    /* Add New Item Card */
    .add-card {
      background: white;
      border-radius: 10px;
      padding: 15px;
      box-shadow: 0 3px 8px rgba(0,0,0,0.06);
      cursor: pointer;
      transition: all 0.3s;
      border: 2px dashed var(--medium-gray);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      min-height: 100%;
    }

    .add-card:hover {
      border-color: var(--primary-red);
      background-color: rgba(228, 0, 43, 0.05);
    }

    .add-card i {
      font-size: 24px;
      margin-bottom: 8px;
      color: var(--primary-red);
    }

    .add-card span {
      font-weight: 600;
      font-size: 13px;
      color: var(--dark);
    }

    /* Modals */
    .modal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1000;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s;
    }

    .modal.active {
      opacity: 1;
      visibility: visible;
    }

    .modal-content {
      background-color: white;
      border-radius: 10px;
      width: 95%;
      max-width: 500px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.15);
      transform: translateY(-20px);
      transition: all 0.3s;
      max-height: 90vh;
      overflow-y: auto;
    }

    .modal.active .modal-content {
      transform: translateY(0);
    }

    .modal-header {
      padding: 15px;
      border-bottom: 1px solid var(--medium-gray);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .modal-header h3 {
      font-size: 16px;
      color: var(--dark);
    }

    .modal-header .close-btn {
      background: none;
      border: none;
      font-size: 18px;
      cursor: pointer;
      color: var(--dark-gray);
    }

    .modal-body {
      padding: 15px;
    }

    .form-group {
      margin-bottom: 12px;
    }

    .form-group label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
      font-size: 13px;
    }

    .form-control {
      width: 100%;
      padding: 8px 10px;
      border: 1px solid var(--medium-gray);
      border-radius: 5px;
      font-size: 13px;
      transition: all 0.2s;
    }

    .form-control:focus {
      outline: none;
      border-color: var(--primary-red);
      box-shadow: 0 0 0 2px rgba(228, 0, 43, 0.2);
    }

    .form-control.textarea {
      min-height: 80px;
      resize: vertical;
    }

    /* Dropdown Styles with Hover Activation */
    .dropdown {
        position: relative;
        display: inline-block;
        z-index: 110;
    }

    .dropdown-menu {
      background-color: white;
      min-width: 180px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.1), 
                  0 2px 4px rgba(0,0,0,0.05);
      z-index: 1100;
      position: absolute;
      border-radius: 6px;
      padding: 6px 0;
      margin-top: 5px;
      border: 1px solid var(--medium-gray);
      opacity: 0;
      visibility: hidden;
      transform: translateY(-10px);
      transition: all 0.2s ease-out;
      }

      /* Hover activation */
      .dropdown:hover .dropdown-menu,
      .dropdown:focus-within .dropdown-menu {
          opacity: 1;
          visibility: visible;
          transform: translateY(0);
      }

      .dropdown-item {
          color: var(--dark);
          padding: 10px 16px;
          text-decoration: none;
          display: block;
          font-size: 14px;
          transition: all 0.15s ease;
          cursor: pointer;
          position: relative;
      }

      .dropdown-item:hover {
          background-color: rgba(228, 0, 43, 0.08);
          color: var(--primary-red);
          padding-left: 20px;
      }

      .dropdown-item.active {
          background-color: rgba(228, 0, 43, 0.05);
          color: var(--primary-red);
          font-weight: 500;
      }

      .dropdown-item.active:after {
          content: "✓";
          position: absolute;
          right: 16px;
          color: var(--primary-red);
      }

      .dropdown-divider {
          height: 1px;
          background-color: var(--medium-gray);
          margin: 6px 0;
          opacity: 0.7;
      }

      /* Dropdown toggle button styles */
      .btn-outline.dropdown-toggle {
          background-color: white;
          border: 1px solid var(--medium-gray);
          color: var(--dark);
          padding: 7px 12px;
          display: inline-flex;
          align-items: center;
          gap: 6px;
          transition: all 0.2s ease;
      }

      .btn-outline.dropdown-toggle:hover {
          background-color: var(--light-gray);
          border-color: var(--dark-gray);
      }

      /* Filter dropdown specific styles */
      .filter-dropdown .dropdown-toggle {
          border-color: var(--primary-red);
          color: var(--primary-red);
      }

      .filter-dropdown .dropdown-toggle:hover {
          background-color: rgba(228, 0, 43, 0.1);
      }

      .filter-dropdown .dropdown-menu {
          border-color: var(--primary-red);
      }

    .img-preview {
      width: 100%;
      height: 150px;
      object-fit: cover;
      border-radius: 6px;
      margin-bottom: 12px;
      border: 1px solid var(--medium-gray);
      background-color: var(--light-gray);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--dark-gray);
      overflow: hidden;
    }

    .img-preview img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .modal-footer {
      padding: 12px 15px;
      border-top: 1px solid var(--medium-gray);
      display: flex;
      justify-content: flex-end;
      gap: 8px;
    }

    /* Status Toggle */
    .status-toggle {
      position: relative;
      display: inline-block;
      width: 45px;
      height: 22px;
    }

    .status-toggle input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .status-slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      transition: .4s;
      border-radius: 22px;
    }

    .status-slider:before {
      position: absolute;
      content: "";
      height: 14px;
      width: 14px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }

    input:checked + .status-slider {
      background-color: var(--success);
    }

    input:checked + .status-slider:before {
      transform: translateX(23px);
    }

    /* Overlay for mobile sidebar */
    .sidebar-overlay {
      position: fixed;
      top: 56px;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0,0,0,0.5);
      z-index: 800;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s;
    }

    .sidebar-overlay.active {
      opacity: 1;
      visibility: visible;
    }

    /* Utility Classes */
    .text-success {
      color: var(--success);
    }

    .text-warning {
      color: var(--warning);
    }

    .text-danger {
      color: #F44336;
    }

    .text-muted {
      color: var(--dark-gray);
    }

    .mb-2 {
      margin-bottom: 10px;
    }

    .mb-3 {
      margin-bottom: 15px;
    }

    .mt-2 {
      margin-top: 10px;
    }

    .mt-3 {
      margin-top: 15px;
    }

    .ml-2 {
      margin-left: 10px;
    }

    .d-flex {
      display: flex;
    }

    .align-items-center {
      align-items: center;
    }

    .justify-content-between {
      justify-content: space-between;
    }

    .w-100 {
      width: 100%;
    }

    .mb-0 {
      margin-bottom: 0;
    }

    /* Confirmation Overlay Animation */
    .confirmation-overlay {
      position: fixed;
      top: 0; left: 0; width: 100vw; height: 100vh;
      background: rgba(0,0,0,0.9);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 2000;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
    }
    .confirmation-overlay.active {
      opacity: 1;
      visibility: visible;
    }
    .order-placed-animation {
      text-align: center;
      color: #4CAF50;
    }
    .animation-content {
      animation: fadeInOut 2.5s forwards;
    }
    .checkmark {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      display: block;
      stroke-width: 4;
      stroke: #4CAF50;
      stroke-miterlimit: 10;
      margin: 0 auto 10px auto;
      box-shadow: inset 0px 0px 0px #4CAF50;
      animation: fill-checkmark 0.4s ease-in-out 0.4s forwards, scale-checkmark 0.3s ease-in-out 0.9s both;
    }
    .checkmark__circle {
      stroke-dasharray: 166;
      stroke-dashoffset: 166;
      stroke-width: 4;
      stroke-miterlimit: 10;
      stroke: #4CAF50;
      fill: none;
      animation: stroke-checkmark-circle 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
    }
    .checkmark__check {
      transform-origin: 50% 50%;
      stroke-dasharray: 48;
      stroke-dashoffset: 48;
      animation: stroke-checkmark 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
    }
    @keyframes fadeInOut {
      0% { opacity: 0; transform: scale(0.8); }
      20% { opacity: 1; transform: scale(1.1); }
      40% { opacity: 1; transform: scale(1); }
      80% { opacity: 1; transform: scale(1); }
      100% { opacity: 0; transform: scale(0.9); }
    }
    @keyframes stroke-checkmark-circle {
      0% { stroke-dashoffset: 166; }
      100% { stroke-dashoffset: 0; }
    }
    @keyframes stroke-checkmark {
      0% { stroke-dashoffset: 48; }
      100% { stroke-dashoffset: 0; }
    }
    @keyframes fill-checkmark {
      0% { box-shadow: inset 0px 0px 0px #4CAF50; }
      100% { box-shadow: inset 0px 0px 0px 50px rgba(76, 175, 80, 0); }
    }
    @keyframes scale-checkmark {
      0%, 100% { transform: none; }
      50% { transform: scale3d(1.1, 1.1, 1); }
    }

    /* Stats Cards Styles */
    .stats-cards {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 20px;
      margin-bottom: 25px;
    }

    .stat-card {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .card-header h3 {
        font-size: 15px;
        color: var(--dark-gray);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .card-header i {
        font-size: 20px;
        color: var(--dark-gray);
        opacity: 0.8;
    }

    .card-body {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }

    .value {
        font-size: 28px;
        font-weight: 700;
        color: var(--dark);
    }

    .change {
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .change.positive {
        background-color: rgba(76, 175, 80, 0.1);
        color: var(--success);
    }

    .change.negative {
        background-color: rgba(244, 67, 54, 0.1);
        color: #F44336;
    }

    .change.neutral {
        background-color: rgba(33, 150, 243, 0.1);
        color: var(--info);
    }

    .stat-card:nth-child(4) {  /* Targets the 4th stat card */
    display: none !important;
    }

    .stat-card:nth-child(1) {
    border-left-color: var(--primary-red); /* Total Items - Blue */
    }

    .stat-card:nth-child(2) {
    border-left-color: var(--primary-red); /* Active Items - Green */
    }

    .stat-card:nth-child(3) {
    border-left-color: var(--primary-red); /* Inactive Items - Orange */
    }

    /* Enhanced Delete Confirmation Modal */
    #deleteModal .modal-content {
      background: white;
      border-radius: 20px;
      padding: 30px;
      width: 90%;
      max-width: 400px;
      text-align: center;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
      border-top: 6px solid var(--primary-red);
      animation: modalAppear 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }

    @keyframes modalAppear {
      0% { transform: scale(0.8); opacity: 0; }
      100% { transform: scale(1); opacity: 1; }
    }

    .delete-icon {
      font-size: 60px;
      color: var(--primary-red);
      margin-bottom: 20px;
      animation: pulse 1s infinite alternate;
    }

    @keyframes pulse {
      0% { transform: scale(1); }
      100% { transform: scale(1.1); }
    }

    .delete-modal-title {
      font-size: 22px;
      font-weight: 700;
      margin-bottom: 10px;
      color: var(--dark);
    }

    .delete-modal-text {
      font-size: 15px;
      color: var(--dark-gray);
      margin-bottom: 25px;
      line-height: 1.5;
    }

    .delete-modal-footer {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-top: 20px;
    }

    .delete-modal-btn {
      padding: 10px 25px;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      border: none;
      min-width: 100px;
    }

    .delete-modal-btn-cancel {
      background-color: var(--light-gray);
      color: var(--dark);
      border: 1px solid var(--medium-gray);
    }

    .delete-modal-btn-cancel:hover {
      background-color: var(--medium-gray);
    }

    .delete-modal-btn-confirm {
      background-color: var(--primary-red);
      color: white;
      box-shadow: 0 4px 12px rgba(228, 0, 43, 0.3);
    }

    .delete-modal-btn-confirm:hover {
      background-color: var(--dark-red);
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(228, 0, 43, 0.4);
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
      20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
</style>
</head>
<body>
  <!-- Admin Header -->
  <header class="admin-header">
    <div class="d-flex align-items-center">
      <button class="menu-toggle btn btn-outline" style="margin-right: 10px;">
        <i class="fas fa-bars"></i>
      </button>
      <div class="logo">
        <i class="fas fa-utensils"></i>
        <span>Tasty Legs Admin</span>
      </div>
    </div>
    <div class="user-controls">
      <div class="notification">
        <i class="fas fa-bell"></i>
        <span class="badge">3</span>
      </div>
      <div class="user-profile">
        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Admin User">
        <span>Admin</span>
      </div>
    </div>
  </header>
  
  <!-- Admin Container -->
  <div class="admin-container">
    <!-- Sidebar Overlay (Mobile Only) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Sidebar -->
    <aside class="admin-sidebar">
      <div class="sidebar-header">
        <i class="fas fa-store"></i>
        <h3>Restaurant</h3>
      </div>
      <ul class="menu">
        <li class="menu-item">
          <i class="fas fa-tachometer-alt"></i>
          <span>Dashboard</span>
        </li>
        <li class="menu-item active">
          <i class="fas fa-utensils"></i>
          <span>Menu Management</span>
        </li>
        <li class="menu-item">
          <i class="fas fa-users"></i>
          <span>Staff Management</span>
        </li>
        <li class="menu-item">
          <i class="fas fa-shopping-bag"></i>
          <span>Orders</span>
        </li>
        <li class="menu-item">
          <i class="fas fa-chart-line"></i>
          <span>Analytics</span>
        </li>
        <li class="menu-item">
          <i class="fas fa-cog"></i>
          <span>Settings</span>
        </li>
      </ul>
    </aside>
    
    <!-- Main Content -->
    <main class="admin-content">
      <!-- Stats Cards -->
      <div class="stats-cards">
    <div class="stat-card">
        <div class="card-header">
            <h3>Total Items</h3>
            <i class="fas fa-boxes"></i>
        </div>
        <div class="card-body">
            <div class="value">0</div>
            <div class="change neutral">
                <i class="fas fa-equals"></i> 0%
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="card-header">
            <h3>Active Items</h3>
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="card-body">
            <div class="value">0</div>
            <div class="change positive">
                <i class="fas fa-arrow-up"></i> 0%
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="card-header">
            <h3>Inactive Items</h3>
            <i class="fas fa-ban"></i>
        </div>
        <div class="card-body">
            <div class="value">0</div>
            <div class="change negative">
                <i class="fas fa-arrow-down"></i> 0%
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="card-header">
            <h3>Special Items</h3>
            <i class="fas fa-star"></i>
        </div>
        <div class="card-body">
            <div class="value"><?= $specialItemsCount ?></div>
            <div class="change positive">
                <i class="fas fa-arrow-up"></i> <?= count($allItems) > 0 ? round($specialItemsCount / count($allItems) * 100) : 0 ?>%
            </div>
        </div>
    </div>
</div>
      
 <!-- Menu Management Section -->
<div class="section-header">
    <h2>Menu Management</h2>
    <div class="actions">
        <!-- Hoverable Dropdown -->
        <div class="dropdown hover-dropdown">
            <button class="btn btn-outline dropdown-toggle" type="button" id="filterDropdown">
                <i class="fas fa-filter"></i> Filter Status
            </button>
            <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                <li><a class="dropdown-item filter-option active" href="#" data-filter="all">All Items</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item filter-option" href="#" data-filter="active">Active Only</a></li>
                <li><a class="dropdown-item filter-option" href="#" data-filter="inactive">Inactive Only</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Category Navigation -->
<div class="categories-nav">
    <div class="category-tab active" data-category="all">All Items</div>
    <div class="category-tab" data-category="special">Special Offers</div>
    <div class="category-tab" data-category="buckets">Chicken Buckets</div>
    <div class="category-tab" data-category="burgers">Burgers & Wraps</div>
    <div class="category-tab" data-category="rice">Rice & Meals</div>
    <div class="category-tab" data-category="sides">Sides & Snacks</div>
    <div class="category-tab" data-category="desserts">Desserts</div>
    <?php foreach ($categories as $category): 
        $static = ['special', 'buckets', 'burgers', 'rice', 'sides', 'desserts'];
        $cat_key = strtolower(str_replace(' ', '_', $category));
        if (in_array($cat_key, $static)) continue;
    ?>
        <div class="category-tab" data-category="<?= htmlspecialchars($cat_key) ?>">
            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $category))) ?>
        </div>
    <?php endforeach; ?>
</div>

  <!-- Cards Container -->
  <div id="cardsContainer" class="row g-3"></div> 
      <!-- Menu Items Grid -->
      <!-- Menu Items Grid -->
<div class="menu-items-grid" id="menuItemsContainer">
    <!-- Add New Item Card -->
    <div class="add-card" id="addNewItemCard">
        <i class="fas fa-plus"></i>
        <span>Add New Item</span>
    </div>
    
    <!-- PHP-generated items -->
    <?php foreach ($allItems as $item): 
        $categorySlug = strtolower(str_replace(' ', '_', $item['Item_Category']));
    ?>
        <!-- In your menu-items-grid section, update the menu-card div to include data-status -->
        <div class="menu-card" data-id="<?= htmlspecialchars($item['Item_Id']) ?>" 
          data-category="<?= htmlspecialchars($categorySlug) ?>"
          data-status="<?= strtolower(htmlspecialchars($item['Item_Status'])) ?>"
          data-special="<?= $item['Item_Category'] === 'Special Offers' ? 'true' : 'false' ?>">
            <div class="card-img-container">
                <img src="<?= htmlspecialchars($item['Item_Image']) ?>" alt="<?= htmlspecialchars($item['Item_Name']) ?>">
                  <?php 
                      $isSpecial = strtolower($item['Item_Category']) === 'special offers' || 
                                  strtolower($item['Item_Category']) === 'special';
                      if ($isSpecial): ?>
                          <div class="card-badge">Special</div>
                  <?php endif; ?>
            </div>
            <div class="card-content">
                <h4><?= htmlspecialchars($item['Item_Name']) ?></h4>
                <div class="price">Rs. <?= htmlspecialchars($item['Item_Price']) ?></div>
                
                <div class="card-actions">
                    <button class="btn btn-outline edit-item-btn">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-outline delete-item-btn">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <small class="<?= $item['Item_Status'] === 'Active' ? 'text-success' : 'text-muted' ?>">
                        <i class="fas fa-circle"></i> <?= htmlspecialchars($item['Item_Status']) ?>
                    </small>
                    <small class="text-muted">
                        ID: <?= htmlspecialchars($item['Item_Id']) ?>
                    </small>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
    </main>
  </div>
  
  <!-- Add/Edit Item Modal -->
  <div class="modal" id="itemModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="modalTitle">Add New Menu Item</h3>
        <button class="close-btn" id="closeModalBtn">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="itemName">Item Name</label>
          <input type="text" id="itemName" class="form-control" placeholder="Enter item name">
        </div>
        <div class="form-group">
          <label for="itemCategory">Category</label>
          <select id="itemCategory" class="form-control">
            <option value="special">Special Offers</option>
            <option value="buckets">Chicken Buckets</option>
            <option value="burgers">Burgers & Wraps</option>
            <option value="rice">Rice & Meals</option>
            <option value="sides">Sides & Snacks</option>
            <option value="desserts">Desserts</option>
          </select>
        </div>
        <div class="form-group">
          <label for="itemPrice">Price (Rs.)</label>
          <input type="text" id="itemPrice" class="form-control" placeholder="Enter price">
        </div>
        <!-- <div class="form-group">
          <label for="itemDescription">Description</label>
          <textarea id="itemDescription" class="form-control textarea" placeholder="Enter item description"></textarea>
        </div> -->
        <div class="form-group">
          <label>Status</label>
          <div class="d-flex align-items-center">
            <label class="status-toggle mb-0">
              <input type="checkbox" id="itemStatus" checked>
              <span class="status-slider"></span>
            </label>
            <span class="ml-2" id="statusText">Active</span>
          </div>
        </div>
        <div class="form-group">
          <label>Item Image</label>
          <div class="img-preview" id="imagePreview">
            <span>No image selected</span>
          </div>
          <input type="file" id="itemImage" accept="image/*" style="display: none;">
          <button class="btn btn-outline w-100" id="uploadImageBtn">
            <i class="fas fa-upload"></i> Upload Image
          </button>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline" id="cancelBtn">Cancel</button>
        <button class="btn btn-primary" id="saveItemBtn">Save Item</button>
      </div>
    </div>
  </div>
  
  <!-- Delete Confirmation Modal -->
<div class="modal" id="deleteModal">
  <div class="modal-content">
    <div class="delete-icon">
      <i class="fas fa-exclamation-triangle"></i>
    </div>
    <div class="modal-header">
      <h3 class="delete-modal-title">Confirm Deletion</h3>
      <button class="close-btn" id="closeDeleteModalBtn">&times;</button>
    </div>
    <div class="modal-body">
      <p class="delete-modal-text">Are you sure you want to delete this menu item? This action cannot be undone.</p>
    </div>
    <div class="modal-footer delete-modal-footer">
      <button class="btn btn-outline delete-modal-btn-cancel" id="cancelDeleteBtn">Cancel</button>
      <button class="btn btn-danger delete-modal-btn-confirm" id="confirmDeleteBtn">Delete</button>
    </div>
  </div>
</div>
  
  <!-- Confirmation Animation Modal - Improved -->
  <div class="modal" id="confirmationModal">
    <div class="modal-content">
      <div class="confirm-checkmark">
        <svg viewBox="0 0 100 100">
          <circle class="circle" cx="50" cy="50" r="45"/>
          <polyline class="check" points="30,55 45,70 70,40"/>
        </svg>
      </div>
      <div class="confirm-success-text">Item Added Successfully!</div>
    </div>
  </div>
  
  <!-- Image Added Confirmation Overlay -->
  <div class="confirmation-overlay" id="imageAddedOverlay">
    <div class="order-placed-animation">
      <div class="animation-content">
        <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
          <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
          <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
        </svg>
        <h2>Image Added!</h2>
        <p>Your image has been uploaded successfully.</p>
      </div>
    </div>
  </div>
  
<script>
    document.addEventListener('DOMContentLoaded', function() {
      // DOM Elements
      const menuItemsContainer = document.getElementById('menuItemsContainer');
      const itemModal = document.getElementById('itemModal');
      const deleteModal = document.getElementById('deleteModal');
      const categoryTabs = document.querySelectorAll('.category-tab');
      const menuToggle = document.querySelector('.menu-toggle');
      const adminSidebar = document.querySelector('.admin-sidebar');
      const sidebarOverlay = document.getElementById('sidebarOverlay');

      // State variables
      let currentCategory = 'all';
      let currentStatusFilter = 'all';
      let isEditing = false;
      let currentItemId = null;
      let itemToDelete = null;

      // Initialize the page
      function init() {
          setupEventListeners();
          renderMenuItems(); // Initial render
          initializeDropdownHover();
          updateStatsCards(); // Update stats cards with data
      }

      // Initialize dropdown hover behavior
      function initializeDropdownHover() {
          const dropdowns = document.querySelectorAll('.dropdown');
          
          dropdowns.forEach(dropdown => {
              const menu = dropdown.querySelector('.dropdown-menu');
              let hoverTimeout;
              
              // Show on hover
              dropdown.addEventListener('mouseenter', function() {
                  clearTimeout(hoverTimeout);
                  menu.style.opacity = '0';
                  menu.style.visibility = 'hidden';
                  menu.style.transform = 'translateY(-10px)';
                  menu.style.display = 'block';
                  
                  // Trigger reflow to enable transition
                  void menu.offsetWidth;
                  
                  menu.style.opacity = '1';
                  menu.style.visibility = 'visible';
                  menu.style.transform = 'translateY(0)';
              });
              
              // Hide with delay when mouse leaves
              dropdown.addEventListener('mouseleave', function() {
                  hoverTimeout = setTimeout(() => {
                      menu.style.opacity = '0';
                      menu.style.transform = 'translateY(-10px)';
                      setTimeout(() => {
                          menu.style.display = 'none';
                      }, 200);
                  }, 300);
              });
              
              // Keep open if mouse enters menu
              menu.addEventListener('mouseenter', function() {
                  clearTimeout(hoverTimeout);
              });
          });
      }

      // Update stats cards with data
      function updateStatsCards() {
          // Get all visible cards after filtering
          const visibleCards = document.querySelectorAll('.menu-card[style="display: block;"]');
          
          // Count different types of items
          let activeCount = 0;
          let inactiveCount = 0;
          let specialCount = 0;
          
          visibleCards.forEach(card => {
              const status = card.dataset.status ? card.dataset.status.toLowerCase() : 'active';
              if (status === 'active') {
                  activeCount++;
              } else {
                  inactiveCount++;
              }
              
              if (card.querySelector('.card-badge')) {
                  specialCount++;
              }
          });
          
          const totalCount = visibleCards.length;
          
          // Update the cards
          document.querySelector('.stats-cards .stat-card:nth-child(1) .value').textContent = totalCount;
          document.querySelector('.stats-cards .stat-card:nth-child(2) .value').textContent = activeCount;
          document.querySelector('.stats-cards .stat-card:nth-child(3) .value').textContent = inactiveCount;
          document.querySelector('.stats-cards .stat-card:nth-child(4) .value').textContent = specialCount;

          // Calculate and update percentages
          document.querySelector('.stats-cards .stat-card:nth-child(1) .change').innerHTML = 
              `<i class="fas fa-equals"></i> 100%`;
          
          document.querySelector('.stats-cards .stat-card:nth-child(2) .change').innerHTML = 
              `<i class="fas fa-arrow-up"></i> ${totalCount > 0 ? Math.round((activeCount / totalCount) * 100) : 0}%`;
          
          document.querySelector('.stats-cards .stat-card:nth-child(3) .change').innerHTML = 
              `<i class="fas fa-arrow-down"></i> ${totalCount > 0 ? Math.round((inactiveCount / totalCount) * 100) : 0}%`;
          
          document.querySelector('.stats-cards .stat-card:nth-child(4) .change').innerHTML = 
              `<i class="fas fa-arrow-up"></i> ${totalCount > 0 ? Math.round((specialCount / totalCount) * 100) : 0}%`;
      }

      // Setup all event listeners
      function setupEventListeners() {
          // Category tabs
          categoryTabs.forEach(tab => {
              tab.addEventListener('click', () => {
                  categoryTabs.forEach(t => t.classList.remove('active'));
                  tab.classList.add('active');
                  currentCategory = tab.dataset.category;
                  renderMenuItems();
              });
          });
          
          // Menu toggle for mobile
          menuToggle.addEventListener('click', () => {
              adminSidebar.classList.toggle('active');
              sidebarOverlay.classList.toggle('active');
          });
          
          // Close sidebar when clicking on overlay
          sidebarOverlay.addEventListener('click', () => {
              adminSidebar.classList.remove('active');
              sidebarOverlay.classList.remove('active');
          });
          
          // Modal controls
          document.getElementById('addNewItemCard').addEventListener('click', () => {
              openItemModal(false);
          });
          
          document.getElementById('closeModalBtn').addEventListener('click', () => {
              closeItemModal();
          });
          
          document.getElementById('cancelBtn').addEventListener('click', () => {
              closeItemModal();
          });
          
          document.getElementById('saveItemBtn').addEventListener('click', () => {
              saveItem();
          });
          
          document.getElementById('uploadImageBtn').addEventListener('click', () => {
              document.getElementById('itemImage').click();
          });
          
          document.getElementById('itemImage').addEventListener('change', (e) => {
              const file = e.target.files[0];
              if (file) {
                  const reader = new FileReader();
                  reader.onload = (event) => {
                      document.getElementById('imagePreview').innerHTML = `
                      <img src="${event.target.result}" alt="Preview">
                      `;
                  };
                  reader.readAsDataURL(file);
              }
          });
          
          document.getElementById('itemStatus').addEventListener('change', (e) => {
              document.getElementById('statusText').textContent = 
                  e.target.checked ? 'Active' : 'Inactive';
          });
          
          // Delete modal controls
          document.getElementById('closeDeleteModalBtn').addEventListener('click', () => {
              closeDeleteModal();
          });
          
          document.getElementById('cancelDeleteBtn').addEventListener('click', () => {
              closeDeleteModal();
          });
          
          document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
              deleteItem();
          });

          // Add hover effects for delete button
          document.getElementById('confirmDeleteBtn').addEventListener('mouseenter', function() {
              this.innerHTML = '<i class="fas fa-trash"></i> Delete';
          });

          document.getElementById('confirmDeleteBtn').addEventListener('mouseleave', function() {
              this.textContent = 'Delete';
          });

          // Filter functionality
          const filterOptions = document.querySelectorAll('.filter-option');
          filterOptions.forEach(option => {
              option.addEventListener('click', function(e) {
                  e.preventDefault();
                  const filterValue = this.getAttribute('data-filter');
                  
                  // Update active state
                  filterOptions.forEach(opt => opt.classList.remove('active'));
                  this.classList.add('active');
                  currentStatusFilter = filterValue;
                  
                  // Filter items
                  renderMenuItems();
              });
          });

          // Bind event listeners to dynamically created items
          bindItemEventListeners();
      }

      // Render menu items based on current filters
      function renderMenuItems() {
          const allCards = document.querySelectorAll('.menu-card');
          
          allCards.forEach(card => {
              const cardCategory = card.dataset.category ? card.dataset.category.toLowerCase() : 'all';
              const cardStatus = card.dataset.status ? card.dataset.status.toLowerCase() : 'active';
              
              const categoryMatch = currentCategory === 'all' || cardCategory === currentCategory;
              const statusMatch = currentStatusFilter === 'all' || 
                                (currentStatusFilter === 'active' && cardStatus === 'active') ||
                                (currentStatusFilter === 'inactive' && cardStatus === 'inactive');
              
              if (categoryMatch && statusMatch) {
                  card.style.display = 'block';
              } else {
                  card.style.display = 'none';
              }
          });
          
          // Update stats after filtering
          updateStatsCards();
      }

      // Bind event listeners to dynamically created items
      function bindItemEventListeners() {
          // Add New Item button event
          const addNewItemCard = document.getElementById('addNewItemCard');
          if (addNewItemCard) {
              addNewItemCard.addEventListener('click', () => {
                  openItemModal(false);
              });
          }

          document.querySelectorAll('.edit-item-btn').forEach(btn => {
              btn.addEventListener('click', (e) => {
                  const card = e.target.closest('.menu-card');
                  const itemId = parseInt(card.dataset.id);
                  openItemModal(true, itemId);
              });
          });

          document.querySelectorAll('.delete-item-btn').forEach(btn => {
              btn.addEventListener('click', (e) => {
                  const card = e.target.closest('.menu-card');
                  itemToDelete = parseInt(card.dataset.id);
                  openDeleteModal();
              });
          });
      }

      // Open item modal in add or edit mode
      function openItemModal(editMode, itemId = null) {
          isEditing = editMode;
          currentItemId = itemId;

          const modalTitle = document.getElementById('modalTitle');
          const saveBtn = document.getElementById('saveItemBtn');

          if (editMode) {
              modalTitle.textContent = 'Edit Menu Item';
              saveBtn.textContent = 'Update Item';

              // Get the item data from the card
              const card = document.querySelector(`.menu-card[data-id="${itemId}"]`);
              if (card) {
                  const name = card.querySelector('h4').textContent;
                  const price = card.querySelector('.price').textContent.replace('Rs. ', '');
                  const statusElement = card.querySelector('small.text-success, small.text-muted');
                  const status = statusElement ? statusElement.textContent.trim() === 'Active' : true;
                  const category = card.dataset.category;

                  document.getElementById('itemName').value = name;
                  document.getElementById('itemPrice').value = price;
                  document.getElementById('itemStatus').checked = status;
                  document.getElementById('statusText').textContent = status ? 'Active' : 'Inactive';
                  document.getElementById('itemCategory').value = category;
                  
                  const imgSrc = card.querySelector('.card-img-container img').src;
                  document.getElementById('imagePreview').innerHTML = `
                      <img src="${imgSrc}" alt="Preview">
                  `;
                  document.getElementById('itemImage').value = '';
              }
          } else {
              modalTitle.textContent = 'Add New Menu Item';
              saveBtn.textContent = 'Add Item';

              // Reset ALL form fields
              document.getElementById('itemName').value = '';
              document.getElementById('itemPrice').value = '';
              document.getElementById('itemStatus').checked = true;
              document.getElementById('statusText').textContent = 'Active';
              document.getElementById('itemCategory').value = 'special';
              document.getElementById('imagePreview').innerHTML = '<span>No image selected</span>';
              document.getElementById('itemImage').value = '';
          }

          itemModal.classList.add('active');
      }
      
      // Close item modal
      function closeItemModal() {
          itemModal.classList.remove('active');
      }
      
      // Open delete confirmation modal
      function openDeleteModal() {
          deleteModal.classList.add('active');
          
          // Add shake animation to modal content
          const modalContent = deleteModal.querySelector('.modal-content');
          modalContent.style.animation = 'none';
          setTimeout(() => {
              modalContent.style.animation = 'shake 0.5s cubic-bezier(.36,.07,.19,.97) both';
          }, 10);
      }
      
      // Close delete confirmation modal
      function closeDeleteModal() {
          deleteModal.classList.remove('active');
          itemToDelete = null;
      }
      
      // Save item (add or edit)
      function saveItem() {
          const name = document.getElementById('itemName').value.trim();
          const price = document.getElementById('itemPrice').value.trim();
          const status = document.getElementById('itemStatus').checked ? 'Active' : 'Inactive';
          const category = document.getElementById('itemCategory').value;
          const imageInput = document.getElementById('itemImage');
          
          // Simple validation
          if (!name || !price) {
              alert('Please fill in all required fields');
              return;
          }

          // Create FormData object
          const formData = new FormData();
          formData.append('action', isEditing ? 'update_item' : 'add_item');
          formData.append('name', name);
          formData.append('price', price);
          formData.append('category', category);
          formData.append('status', status);
          
          if (isEditing) {
              formData.append('id', currentItemId);
          }
          
          // Add image file if selected
          if (imageInput.files.length > 0) {
              formData.append('image', imageInput.files[0]);
          }

          // Send data to server
          fetch(window.location.href, {
              method: 'POST',
              body: formData
          })
          .then(response => response.json())
          .then(data => {
              if (data.success) {
                  closeItemModal();
                  showConfirmationAnimation(isEditing ? 'Item Updated Successfully!' : 'Item Added Successfully!');
                  // Refresh the page to show the new/updated item
                  setTimeout(() => {
                      location.reload();
                  }, 1500);
              } else {
                  alert('Error: ' + data.message);
              }
          })
          .catch(error => {
              console.error('Error:', error);
              alert('An error occurred while saving the item. Please try again.');
          });
      }
      
      // Delete item
      function deleteItem() {
          if (!itemToDelete) return;
          
          // Send delete request to server
          fetch(`?action=delete_item&id=${itemToDelete}`, {
              method: 'GET'
          })
          .then(response => response.json())
          .then(data => {
              if (data.success) {
                  // Remove the item from the DOM immediately
                  const card = document.querySelector(`.menu-card[data-id="${itemToDelete}"]`);
                  if (card) {
                      card.remove();
                  }
                  closeDeleteModal();
                  showConfirmationAnimation('Item Deleted Successfully!');
                  updateStatsCards();
              } else {
                  alert('Error: ' + data.message);
              }
          })
          .catch(error => {
              console.error('Error:', error);
              alert('An error occurred while deleting the item. Please try again.');
          });
      }

      
      
      // Show confirmation animation with custom message
       function showConfirmationAnimation(message = 'Operation Successful!') {
        const confirmationModal = document.getElementById('confirmationModal');
        const messageElement = confirmationModal.querySelector('.confirm-success-text');
        const modalContent = confirmationModal.querySelector('.modal-content');
        
        // Set the message
        messageElement.textContent = message;
        
        // Calculate appropriate width based on message length
        const messageLength = message.length;
        let modalWidth;
        
        if (messageLength < 30) {
            modalWidth = '300px';
        } else if (messageLength < 60) {
            modalWidth = '350px';
        } else {
            modalWidth = '400px';
        }
        
        // Apply dynamic width
        modalContent.style.width = modalWidth;
        
        // Show the modal
        confirmationModal.classList.add('active');
        
        // Hide after delay
        setTimeout(() => {
            confirmationModal.classList.remove('active');
            // Reset width for next use
            modalContent.style.width = '';
        }, 1700);
    }
    
    // Show image added animation - Original version preserved
    function showImageAddedAnimation() {
        const overlay = document.getElementById('imageAddedOverlay');
        overlay.classList.add('active');
        setTimeout(() => {
            overlay.classList.remove('active');
        }, 2500);
    }

    // Initialize the app
    init();
    
});

// Example: If you have a sorting function, modify it to sort by ID in descending order
function sortItems() {
    const container = document.getElementById('menuItemsContainer');
    const items = Array.from(container.querySelectorAll('.menu-card'));
    
    items.sort((a, b) => {
        const idA = parseInt(a.dataset.id);
        const idB = parseInt(b.dataset.id);
        return idB - idA; // Descending order (newest first)
    });
    
    // Re-append sorted items
    items.forEach(item => container.appendChild(item));
}
</script>
</body>
</html>
