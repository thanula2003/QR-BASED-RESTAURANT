<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Activity Management - Tasty Legs</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    :root {
      --kfc-red: #E4002B;
      --kfc-dark: #231F20;
      --kfc-light: #FFFFFF;
      --kfc-yellow: #FFC72C;
      --success-green: #4CAF50;
      --sidebar-width: 250px;
      --header-height: 70px;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      -webkit-tap-highlight-color: transparent;
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: #f5f7fa;
      color: var(--kfc-dark);
      overflow-x: hidden;
    }

    /* Sidebar Styles */
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: var(--sidebar-width);
      height: 100vh;
      background-color: var(--kfc-dark);
      color: white;
      padding: 20px 0;
      transition: all 0.3s ease;
      z-index: 100;
    }

    .sidebar-header {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0 20px 20px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-logo {
      font-size: 22px;
      font-weight: 700;
      color: white;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .sidebar-logo::before {
      content: "🍗";
      font-size: 28px;
    }

    .sidebar-menu {
      padding: 20px 0;
    }

    .menu-title {
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
      color: rgba(255, 255, 255, 0.5);
      padding: 0 20px 10px;
      margin-top: 10px;
    }

    .menu-item {
      display: flex;
      align-items: center;
      padding: 12px 20px;
      color: rgba(255, 255, 255, 0.8);
      text-decoration: none;
      transition: all 0.2s;
      border-left: 3px solid transparent;
    }

    .menu-item:hover {
      background-color: rgba(255, 255, 255, 0.05);
      color: white;
    }

    .menu-item.active {
      background-color: rgba(228, 0, 43, 0.2);
      color: white;
      border-left-color: var(--kfc-red);
    }

    .menu-item i {
      margin-right: 12px;
      font-size: 18px;
      width: 24px;
      text-align: center;
    }

    /* Main Content Styles */
    .main-content {
      margin-left: var(--sidebar-width);
      padding-top: var(--header-height);
      transition: all 0.3s ease;
    }

    /* Header Styles */
    .header {
      position: fixed;
      top: 0;
      left: var(--sidebar-width);
      right: 0;
      height: var(--header-height);
      background-color: white;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 30px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      z-index: 90;
      transition: all 0.3s ease;
    }

    .header-left {
      display: flex;
      align-items: center;
    }

    .toggle-sidebar {
      font-size: 20px;
      color: var(--kfc-dark);
      margin-right: 20px;
      cursor: pointer;
    }

    .header-title {
      font-size: 20px;
      font-weight: 600;
      color: var(--kfc-dark);
    }

    .header-right {
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .user-profile {
      display: flex;
      align-items: center;
      cursor: pointer;
    }

    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: var(--kfc-red);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      margin-right: 10px;
    }

    .user-name {
      font-weight: 500;
    }

    .notification-icon {
      position: relative;
      font-size: 20px;
      color: var(--kfc-dark);
      cursor: pointer;
    }

    .notification-badge {
      position: absolute;
      top: -5px;
      right: -5px;
      background-color: var(--kfc-red);
      color: white;
      border-radius: 50%;
      width: 18px;
      height: 18px;
      font-size: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* Content Wrapper */
    .content-wrapper {
      padding: 30px;
    }

    /* Page Header */
    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }

    .page-title {
      font-size: 24px;
      font-weight: 700;
      color: var(--kfc-dark);
      position: relative;
      padding-bottom: 10px;
    }

    .page-title::after {
      content: "";
      position: absolute;
      left: 0;
      bottom: 0;
      width: 50px;
      height: 3px;
      background-color: var(--kfc-red);
    }

    /* Filters and Search */
    .activity-controls {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      flex-wrap: wrap;
      gap: 15px;
    }

    .search-box {
      position: relative;
      flex: 1;
      min-width: 250px;
    }

    .search-box input {
      width: 100%;
      padding: 10px 15px 10px 40px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 14px;
      transition: all 0.3s;
    }

    .search-box input:focus {
      border-color: var(--kfc-red);
      outline: none;
      box-shadow: 0 0 0 2px rgba(228, 0, 43, 0.1);
    }

    .search-box i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #777;
    }

    .filter-group {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .filter-btn {
      padding: 8px 15px;
      background-color: white;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 14px;
      cursor: pointer;
      transition: all 0.2s;
    }

    .filter-btn:hover {
      background-color: #f5f5f5;
    }

    .filter-btn.active {
      background-color: var(--kfc-red);
      color: white;
      border-color: var(--kfc-red);
    }

    .date-picker {
      display: flex;
      align-items: center;
      gap: 10px;
      background-color: white;
      padding: 8px 15px;
      border-radius: 8px;
      border: 1px solid #ddd;
    }

    .date-picker input {
      border: none;
      font-size: 14px;
      padding: 5px;
      max-width: 120px;
    }

    .date-picker input:focus {
      outline: none;
    }

    /* Activity Cards */
    .activity-cards {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
      gap: 20px;
    }

    .activity-card {
      background-color: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
      transition: all 0.3s;
    }

    .activity-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .activity-card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 20px;
      background-color: #f9f9f9;
      border-bottom: 1px solid #eee;
    }

    .activity-id {
      font-weight: 600;
      color: var(--kfc-dark);
    }

    .activity-time {
      font-size: 13px;
      color: #777;
    }

    .activity-status {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 500;
    }

    .status-completed {
      background-color: rgba(76, 175, 80, 0.1);
      color: #4CAF50;
    }

    .activity-card-body {
      padding: 20px;
    }

    .activity-customer {
      display: flex;
      align-items: center;
      margin-bottom: 15px;
    }

    .customer-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: var(--kfc-red);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      margin-right: 12px;
    }

    .customer-info h4 {
      font-size: 16px;
      font-weight: 600;
      margin-bottom: 3px;
    }

    .customer-info p {
      font-size: 13px;
      color: #777;
    }

    .activity-items {
      margin-top: 15px;
    }

    .activity-item {
      display: flex;
      justify-content: space-between;
      padding: 8px 0;
      border-bottom: 1px dashed #eee;
    }

    .activity-item:last-child {
      border-bottom: none;
    }

    .item-name {
      font-size: 14px;
    }

    .item-quantity {
      font-size: 13px;
      color: #777;
    }

    .item-price {
      font-weight: 600;
    }

    .activity-card-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 20px;
      background-color: #f9f9f9;
      border-top: 1px solid #eee;
    }

    .activity-total {
      font-weight: 700;
      color: var(--kfc-dark);
    }

    .activity-actions {
      display: flex;
      gap: 10px;
    }

    .action-btn {
      padding: 6px 12px;
      border-radius: 6px;
      font-size: 13px;
      cursor: pointer;
      transition: all 0.2s;
      border: none;
    }

    .action-btn.view {
      background-color: var(--kfc-red);
      color: white;
    }

    .action-btn.view:hover {
      background-color: #c10024;
    }

    .action-btn.print {
      background-color: white;
      border: 1px solid #ddd;
    }

    .action-btn.print:hover {
      background-color: #f5f5f5;
    }

    /* Empty State */
    .empty-state {
      text-align: center;
      padding: 50px 20px;
      grid-column: 1 / -1;
    }

    .empty-state i {
      font-size: 60px;
      color: #ddd;
      margin-bottom: 20px;
    }

    .empty-state h3 {
      font-size: 18px;
      color: #777;
      margin-bottom: 10px;
    }

    .empty-state p {
      font-size: 14px;
      color: #999;
      max-width: 400px;
      margin: 0 auto 20px;
    }

    /* Modal Styles */
    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(3px);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }

    .modal-content {
      background: white;
      width: 90%;
      max-width: 800px;
      border-radius: 12px;
      overflow: hidden;
      animation: modalFadeIn 0.3s ease;
      max-height: 90vh;
      display: flex;
      flex-direction: column;
    }

    @keyframes modalFadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .modal-header {
      padding: 15px 20px;
      border-bottom: 1px solid #eee;
      position: relative;
    }

    .modal-title {
      font-size: 18px;
      font-weight: 600;
    }

    .modal-close {
      position: absolute;
      right: 15px;
      top: 15px;
      font-size: 20px;
      cursor: pointer;
      color: var(--kfc-dark);
    }

    .modal-body {
      padding: 20px;
      overflow-y: auto;
      flex: 1;
    }

    .order-details-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }

    .order-section {
      margin-bottom: 20px;
    }

    .order-section-title {
      font-size: 16px;
      font-weight: 600;
      margin-bottom: 15px;
      padding-bottom: 8px;
      border-bottom: 2px solid var(--kfc-red);
    }

    .order-info {
      display: grid;
      grid-template-columns: 100px 1fr;
      gap: 10px;
      margin-bottom: 10px;
    }

    .order-info-label {
      font-weight: 500;
      color: #666;
    }

    .order-info-value {
      font-weight: 500;
    }

    .order-items-table {
      width: 100%;
      border-collapse: collapse;
    }

    .order-items-table th, 
    .order-items-table td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    .order-items-table th {
      font-weight: 600;
      color: var(--kfc-dark);
      font-size: 14px;
    }

    .order-items-table td {
      font-size: 14px;
    }

    .order-total-row {
      font-weight: 700;
    }

    .modal-footer {
      padding: 15px 20px;
      border-top: 1px solid #eee;
      display: flex;
      justify-content: flex-end;
      gap: 10px;
    }

    /* Responsive Styles */
    @media (max-width: 992px) {
      .sidebar {
        transform: translateX(-100%);
      }

      .sidebar.active {
        transform: translateX(0);
      }

      .main-content {
        margin-left: 0;
      }

      .header {
        left: 0;
      }
    }

    @media (max-width: 768px) {
      .order-details-grid {
        grid-template-columns: 1fr;
      }

      .activity-cards {
        grid-template-columns: 1fr;
      }

      .activity-controls {
        flex-direction: column;
        align-items: flex-start;
      }

      .search-box {
        width: 100%;
      }
    }

    @media (max-width: 576px) {
      .content-wrapper {
        padding: 20px 15px;
      }

      .header-title {
        font-size: 18px;
      }

      .user-name {
        display: none;
      }

      .activity-card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
      }

      .activity-card-footer {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
      }

      .activity-actions {
        width: 100%;
        justify-content: flex-end;
      }
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <a href="#" class="sidebar-logo">TASTY LEGS</a>
    </div>
    <div class="sidebar-menu">
      <div class="menu-title">Main</div>
      <a href="#" class="menu-item">
        <i class="fas fa-tachometer-alt"></i>
        <span>Dashboard</span>
      </a>
      <a href="#" class="menu-item">
        <i class="fas fa-utensils"></i>
        <span>Menu Items</span>
      </a>
      <a href="#" class="menu-item">
        <i class="fas fa-receipt"></i>
        <span>Orders</span>
      </a>
      <a href="#" class="menu-item active">
        <i class="fas fa-history"></i>
        <span>Activity</span>
      </a>
      <a href="#" class="menu-item">
        <i class="fas fa-users"></i>
        <span>Customers</span>
      </a>

      <div class="menu-title">Settings</div>
      <a href="#" class="menu-item">
        <i class="fas fa-cog"></i>
        <span>Settings</span>
      </a>
      <a href="#" class="menu-item">
        <i class="fas fa-qrcode"></i>
        <span>QR Codes</span>
      </a>
      <a href="#" class="menu-item">
        <i class="fas fa-users-cog"></i>
        <span>Staff</span>
      </a>
    </div>
  </div>

  <!-- Main Content -->
  <div class="main-content" id="mainContent">
    <!-- Header -->
    <header class="header">
      <div class="header-left">
        <div class="toggle-sidebar" id="toggleSidebar">
          <i class="fas fa-bars"></i>
        </div>
        <h1 class="header-title">Activity Management</h1>
      </div>
      <div class="header-right">
        <div class="notification-icon">
          <i class="fas fa-bell"></i>
          <span class="notification-badge">3</span>
        </div>
        <div class="user-profile">
          <div class="user-avatar">SM</div>
          <span class="user-name">Staff Member</span>
        </div>
      </div>
    </header>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
      <!-- Page Header -->
      <div class="page-header">
        <h2 class="page-title">Completed Orders</h2>
      </div>

      <!-- Filters and Search -->
      <div class="activity-controls">
        <div class="search-box">
          <i class="fas fa-search"></i>
          <input type="text" id="searchInput" placeholder="Search orders...">
        </div>
        <div class="filter-group">
          <button class="filter-btn active" data-filter="all">All</button>
          <button class="filter-btn" data-filter="today">Today</button>
          <button class="filter-btn" data-filter="week">This Week</button>
          <button class="filter-btn" data-filter="month">This Month</button>
          <div class="date-picker">
            <i class="fas fa-calendar-alt"></i>
            <input type="date" id="customDate">
          </div>
        </div>
      </div>

      <!-- Activity Cards -->
      <div class="activity-cards" id="activityCards">
        <!-- Activity cards will be dynamically added here -->
      </div>

      <!-- Empty State (hidden by default) -->
      <div class="empty-state" id="emptyState" style="display: none;">
        <i class="fas fa-clipboard-list"></i>
        <h3>No Completed Orders Found</h3>
        <p>There are no completed orders matching your search criteria. Try adjusting your filters.</p>
      </div>
    </div>
  </div>

  <!-- Order Details Modal -->
  <div class="modal-overlay" id="orderModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Order Details</h3>
        <span class="modal-close" id="closeModal">&times;</span>
      </div>
      <div class="modal-body">
        <div class="order-details-grid">
          <div class="order-section">
            <h4 class="order-section-title">Order Information</h4>
            <div class="order-info">
              <div class="order-info-label">Order ID:</div>
              <div class="order-info-value" id="modalOrderId">#ORD-1254</div>
            </div>
            <div class="order-info">
              <div class="order-info-label">Date:</div>
              <div class="order-info-value" id="modalOrderDate">May 15, 2023 12:30 PM</div>
            </div>
            <div class="order-info">
              <div class="order-info-label">Status:</div>
              <div class="order-info-value">
                <span class="activity-status status-completed" id="modalOrderStatus">Completed</span>
              </div>
            </div>
            <div class="order-info">
              <div class="order-info-label">Payment:</div>
              <div class="order-info-value" id="modalOrderPayment">Credit Card</div>
            </div>
          </div>

          <div class="order-section">
            <h4 class="order-section-title">Customer Information</h4>
            <div class="order-info">
              <div class="order-info-label">Name:</div>
              <div class="order-info-value" id="modalCustomerName">John Smith</div>
            </div>
            <div class="order-info">
              <div class="order-info-label">Table:</div>
              <div class="order-info-value" id="modalCustomerTable">T5</div>
            </div>
            <div class="order-info">
              <div class="order-info-label">Contact:</div>
              <div class="order-info-value" id="modalCustomerContact">john@example.com</div>
            </div>
          </div>
        </div>

        <div class="order-section">
          <h4 class="order-section-title">Order Items</h4>
          <table class="order-items-table">
            <thead>
              <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody id="modalOrderItems">
              <!-- Order items will be added dynamically -->
            </tbody>
            <tfoot>
              <tr>
                <td colspan="3" style="text-align: right; font-weight: 600;">Subtotal:</td>
                <td id="modalSubtotal">Rs. 2,550</td>
              </tr>
              <tr>
                <td colspan="3" style="text-align: right; font-weight: 600;">Tax (10%):</td>
                <td id="modalTax">Rs. 255</td>
              </tr>
              <tr class="order-total-row">
                <td colspan="3" style="text-align: right;">Total:</td>
                <td id="modalTotal">Rs. 2,805</td>
              </tr>
            </tfoot>
          </table>
        </div>

        <div class="order-section">
          <h4 class="order-section-title">Order Notes</h4>
          <div id="modalOrderNotes">
            No special instructions provided.
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="action-btn print" id="printReceipt">
          <i class="fas fa-print"></i> Print Receipt
        </button>
        <button class="action-btn view" id="closeModalBtn">
          Close
        </button>
      </div>
    </div>
  </div>

  <script>
    // Sample order data
    const orders = [
      {
        id: 'ORD-1254',
        date: '2023-05-15T12:30:00',
        customer: {
          name: 'John Smith',
          table: 'T5',
          contact: 'john@example.com'
        },
        items: [
          { name: 'Zinger Burger', quantity: 2, price: 850 },
          { name: 'French Fries', quantity: 1, price: 400 },
          { name: 'Cola', quantity: 2, price: 225 }
        ],
        payment: 'Credit Card',
        status: 'completed',
        notes: 'No onions in the burger please.'
      },
      {
        id: 'ORD-1253',
        date: '2023-05-15T11:45:00',
        customer: {
          name: 'Sarah Johnson',
          table: 'T12',
          contact: 'sarah@example.com'
        },
        items: [
          { name: '5pc Chicken Bucket', quantity: 1, price: 2000 },
          { name: 'Rice Meal', quantity: 1, price: 800 },
          { name: 'Mashed Potato', quantity: 2, price: 350 }
        ],
        payment: 'Cash',
        status: 'completed',
        notes: 'Extra gravy on the side.'
      },
      {
        id: 'ORD-1252',
        date: '2023-05-14T19:15:00',
        customer: {
          name: 'Michael Brown',
          table: 'T8',
          contact: 'michael@example.com'
        },
        items: [
          { name: 'Family Bucket', quantity: 1, price: 4000 },
          { name: 'Potato Wedges', quantity: 2, price: 450 }
        ],
        payment: 'Credit Card',
        status: 'completed',
        notes: ''
      },
      {
        id: 'ORD-1251',
        date: '2023-05-14T18:30:00',
        customer: {
          name: 'Emily Davis',
          table: 'T3',
          contact: 'emily@example.com'
        },
        items: [
          { name: '3pc Chicken Bucket', quantity: 2, price: 1500 },
          { name: 'Coleslaw', quantity: 1, price: 300 },
          { name: 'Garlic Bread', quantity: 2, price: 250 }
        ],
        payment: 'Cash',
        status: 'completed',
        notes: 'One bucket extra crispy.'
      },
      {
        id: 'ORD-1250',
        date: '2023-05-13T13:45:00',
        customer: {
          name: 'Robert Wilson',
          table: 'T7',
          contact: 'robert@example.com'
        },
        items: [
          { name: 'Cheese Burger', quantity: 1, price: 750 },
          { name: 'Onion Rings', quantity: 1, price: 400 }
        ],
        payment: 'Credit Card',
        status: 'completed',
        notes: 'Add extra cheese to the burger.'
      }
    ];

    // DOM elements
    const activityCards = document.getElementById('activityCards');
    const emptyState = document.getElementById('emptyState');
    const searchInput = document.getElementById('searchInput');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const customDateInput = document.getElementById('customDate');
    const orderModal = document.getElementById('orderModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const printReceiptBtn = document.getElementById('printReceipt');

    // Current date for filtering
    const currentDate = new Date();
    
    // Format date as "Month Day, Year Hour:Minute AM/PM"
    function formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit'
      });
    }

    // Format date as "Month Day, Year"
    function formatShortDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
      });
    }

    // Calculate order total
    function calculateOrderTotal(order) {
      const subtotal = order.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
      const tax = subtotal * 0.1; // 10% tax
      const total = subtotal + tax;
      return { subtotal, tax, total };
    }

    // Render activity cards
    function renderActivityCards(filteredOrders) {
      activityCards.innerHTML = '';
      
      if (filteredOrders.length === 0) {
        emptyState.style.display = 'block';
        return;
      }
      
      emptyState.style.display = 'none';
      
      filteredOrders.forEach(order => {
        const { subtotal, tax, total } = calculateOrderTotal(order);
        
        const card = document.createElement('div');
        card.className = 'activity-card';
        card.dataset.id = order.id;
        
        card.innerHTML = `
          <div class="activity-card-header">
            <div>
              <span class="activity-id">#${order.id}</span>
              <span class="activity-time">${formatDate(order.date)}</span>
            </div>
            <span class="activity-status status-completed">Completed</span>
          </div>
          <div class="activity-card-body">
            <div class="activity-customer">
              <div class="customer-avatar">${order.customer.name.charAt(0)}</div>
              <div class="customer-info">
                <h4>${order.customer.name}</h4>
                <p>Table ${order.customer.table}</p>
              </div>
            </div>
            <div class="activity-items">
              ${order.items.slice(0, 2).map(item => `
                <div class="activity-item">
                  <div>
                    <span class="item-name">${item.name}</span>
                    <span class="item-quantity">x${item.quantity}</span>
                  </div>
                  <div class="item-price">Rs. ${(item.price * item.quantity).toLocaleString()}</div>
                </div>
              `).join('')}
              ${order.items.length > 2 ? `
                <div class="activity-item">
                  <div class="item-name">+${order.items.length - 2} more items</div>
                </div>
              ` : ''}
            </div>
          </div>
          <div class="activity-card-footer">
            <div class="activity-total">Total: Rs. ${total.toLocaleString()}</div>
            <div class="activity-actions">
              <button class="action-btn view" data-id="${order.id}">
                <i class="fas fa-eye"></i> View
              </button>
              <button class="action-btn print" data-id="${order.id}">
                <i class="fas fa-print"></i> Print
              </button>
            </div>
          </div>
        `;
        
        activityCards.appendChild(card);
      });
      
      // Add event listeners to view buttons
      document.querySelectorAll('.action-btn.view').forEach(btn => {
        btn.addEventListener('click', function() {
          const orderId = this.getAttribute('data-id');
          showOrderDetails(orderId);
        });
      });
      
      // Add event listeners to print buttons
      document.querySelectorAll('.action-btn.print').forEach(btn => {
        btn.addEventListener('click', function() {
          const orderId = this.getAttribute('data-id');
          printOrder(orderId);
        });
      });
    }

    // Show order details in modal
    function showOrderDetails(orderId) {
      const order = orders.find(o => o.id === orderId);
      if (!order) return;
      
      const { subtotal, tax, total } = calculateOrderTotal(order);
      
      // Set modal content
      document.getElementById('modalOrderId').textContent = `#${order.id}`;
      document.getElementById('modalOrderDate').textContent = formatDate(order.date);
      document.getElementById('modalOrderPayment').textContent = order.payment;
      document.getElementById('modalCustomerName').textContent = order.customer.name;
      document.getElementById('modalCustomerTable').textContent = `Table ${order.customer.table}`;
      document.getElementById('modalCustomerContact').textContent = order.customer.contact;
      document.getElementById('modalSubtotal').textContent = `Rs. ${subtotal.toLocaleString()}`;
      document.getElementById('modalTax').textContent = `Rs. ${tax.toLocaleString()}`;
      document.getElementById('modalTotal').textContent = `Rs. ${total.toLocaleString()}`;
      
      // Set order items
      const orderItemsContainer = document.getElementById('modalOrderItems');
      orderItemsContainer.innerHTML = order.items.map(item => `
        <tr>
          <td>${item.name}</td>
          <td>${item.quantity}</td>
          <td>Rs. ${item.price.toLocaleString()}</td>
          <td>Rs. ${(item.price * item.quantity).toLocaleString()}</td>
        </tr>
      `).join('');
      
      // Set order notes
      const notesContainer = document.getElementById('modalOrderNotes');
      notesContainer.textContent = order.notes || 'No special instructions provided.';
      
      // Show modal
      orderModal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    }

    // Print order receipt
    function printOrder(orderId) {
      const order = orders.find(o => o.id === orderId);
      if (!order) return;
      
      const { subtotal, tax, total } = calculateOrderTotal(order);
      
      // Create print content
      const printContent = `
        <div style="font-family: Arial, sans-serif; max-width: 300px; margin: 0 auto; padding: 20px;">
          <h2 style="text-align: center; color: #E4002B; margin-bottom: 10px;">TASTY LEGS</h2>
          <p style="text-align: center; margin-bottom: 20px; font-size: 14px;">123 Restaurant Street, City</p>
          <hr style="border: none; border-top: 1px dashed #ccc; margin: 10px 0;">
          <div style="margin-bottom: 15px;">
            <p style="margin: 5px 0; font-size: 14px;"><strong>Order #:</strong> ${order.id}</p>
            <p style="margin: 5px 0; font-size: 14px;"><strong>Date:</strong> ${formatDate(order.date)}</p>
            <p style="margin: 5px 0; font-size: 14px;"><strong>Table:</strong> ${order.customer.table}</p>
          </div>
          <hr style="border: none; border-top: 1px dashed #ccc; margin: 10px 0;">
          <table style="width: 100%; margin-bottom: 15px; font-size: 14px;">
            <thead>
              <tr>
                <th style="text-align: left; padding-bottom: 5px;">Item</th>
                <th style="text-align: right; padding-bottom: 5px;">Total</th>
              </tr>
            </thead>
            <tbody>
              ${order.items.map(item => `
                <tr>
                  <td style="padding: 3px 0;">${item.name} x${item.quantity}</td>
                  <td style="text-align: right; padding: 3px 0;">Rs. ${(item.price * item.quantity).toLocaleString()}</td>
                </tr>
              `).join('')}
            </tbody>
          </table>
          <hr style="border: none; border-top: 1px dashed #ccc; margin: 10px 0;">
          <table style="width: 100%; font-size: 14px; margin-bottom: 15px;">
            <tr>
              <td style="padding: 3px 0;">Subtotal:</td>
              <td style="text-align: right; padding: 3px 0;">Rs. ${subtotal.toLocaleString()}</td>
            </tr>
            <tr>
              <td style="padding: 3px 0;">Tax (10%):</td>
              <td style="text-align: right; padding: 3px 0;">Rs. ${tax.toLocaleString()}</td>
            </tr>
            <tr style="font-weight: bold;">
              <td style="padding: 3px 0;">Total:</td>
              <td style="text-align: right; padding: 3px 0;">Rs. ${total.toLocaleString()}</td>
            </tr>
          </table>
          <hr style="border: none; border-top: 1px dashed #ccc; margin: 10px 0;">
          <p style="text-align: center; font-size: 12px; margin-top: 20px;">Thank you for dining with us!</p>
        </div>
      `;
      
      // Open print window
      const printWindow = window.open('', '', 'width=600,height=600');
      printWindow.document.write(printContent);
      printWindow.document.close();
      printWindow.focus();
      setTimeout(() => {
        printWindow.print();
        printWindow.close();
      }, 500);
    }

    // Filter orders based on search and filter criteria
    function filterOrders() {
      const searchTerm = searchInput.value.toLowerCase();
      const activeFilter = document.querySelector('.filter-btn.active').dataset.filter;
      const customDate = customDateInput.value;
      
      let filteredOrders = orders.filter(order => {
        // Filter by search term
        const matchesSearch = 
          order.id.toLowerCase().includes(searchTerm) ||
          order.customer.name.toLowerCase().includes(searchTerm) ||
          order.items.some(item => item.name.toLowerCase().includes(searchTerm));
        
        if (!matchesSearch) return false;
        
        // Filter by date
        const orderDate = new Date(order.date);
        
        if (customDate) {
          const selectedDate = new Date(customDate);
          return (
            orderDate.getFullYear() === selectedDate.getFullYear() &&
            orderDate.getMonth() === selectedDate.getMonth() &&
            orderDate.getDate() === selectedDate.getDate()
          );
        }
        
        switch (activeFilter) {
          case 'today':
            return (
              orderDate.getFullYear() === currentDate.getFullYear() &&
              orderDate.getMonth() === currentDate.getMonth() &&
              orderDate.getDate() === currentDate.getDate()
            );
          case 'week':
            const weekStart = new Date(currentDate);
            weekStart.setDate(currentDate.getDate() - currentDate.getDay());
            return orderDate >= weekStart;
          case 'month':
            return (
              orderDate.getFullYear() === currentDate.getFullYear() &&
              orderDate.getMonth() === currentDate.getMonth()
            );
          default:
            return true;
        }
      });
      
      renderActivityCards(filteredOrders);
    }

    // Initialize the page
    function init() {
      // Set today's date as default in date picker
      const today = new Date().toISOString().split('T')[0];
      customDateInput.value = today;
      customDateInput.max = today;
      
      // Render initial orders
      renderActivityCards(orders);
      
      // Add event listeners
      searchInput.addEventListener('input', filterOrders);
      
      filterButtons.forEach(button => {
        button.addEventListener('click', function() {
          filterButtons.forEach(btn => btn.classList.remove('active'));
          this.classList.add('active');
          customDateInput.value = '';
          filterOrders();
        });
      });
      
      customDateInput.addEventListener('change', function() {
        if (this.value) {
          filterButtons.forEach(btn => btn.classList.remove('active'));
          filterOrders();
        }
      });
      
      closeModalBtn.addEventListener('click', function() {
        orderModal.style.display = 'none';
        document.body.style.overflow = '';
      });
      
      document.getElementById('closeModal').addEventListener('click', function() {
        orderModal.style.display = 'none';
        document.body.style.overflow = '';
      });
      
      printReceiptBtn.addEventListener('click', function() {
        const orderId = document.getElementById('modalOrderId').textContent.substring(1);
        printOrder(orderId);
      });
      
      // Close modal when clicking outside
      window.addEventListener('click', function(e) {
        if (e.target === orderModal) {
          orderModal.style.display = 'none';
          document.body.style.overflow = '';
        }
      });
      
      // Toggle sidebar
      document.getElementById('toggleSidebar').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
        document.getElementById('mainContent').classList.toggle('active');
      });
    }

    // Initialize the page when loaded
    document.addEventListener('DOMContentLoaded', init);
  </script>
</body>
</html>
<?php $conn->close(); ?>