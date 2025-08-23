<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Tasty Legs</title>
      <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS (bundle already includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    :root {
      --kfc-red: #E4002B;
      --kfc-dark: #231F20;
      --kfc-light: #FFFFFF;
      --kfc-yellow: #FFC72C;
      --sidebar-width: 250px;
      --header-height: 70px;
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
      background-color: #f5f7fa;
      color: var(--kfc-dark);
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
      height: var(--header-height);
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

    .menu-toggle.btn.btn-outline {
      background-color: transparent !important;
      border-color: rgba(255, 255, 255, 0.5) !important;
      color: white !important;
      margin-right: 10px;
    }

    .menu-toggle.btn.btn-outline:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
        border-color: white !important;
    }

    /* Admin Layout */
    .admin-container {
      display: flex;
      min-height: calc(100vh - var(--header-height));
      position: relative;
    }

    /* Sidebar */
    .admin-sidebar {
      width: 250px;
      background-color: white;
      box-shadow: 2px 0 10px rgba(0,0,0,0.05);
      padding: 15px 0;
      transition: all 0.3s ease;
      overflow-y: auto;
      position: fixed;
      top: var(--header-height);
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

    .menu-toggle.btn.btn-outline {
      background-color: transparent !important;
      border-color: rgba(255, 255, 255, 0.5) !important;
      color: white !important;
    }

    .menu-toggle.btn.btn-outline:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
        border-color: white !important;
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
      padding: 30px;
      background-color: #f9f9f9;
      transition: all 0.3s;
      width: 100%;
      position: relative;
      z-index: 1;
      margin-left: 250px;
    }

    .admin-content.expanded {
      margin-left: 70px;
    }

    /* Sidebar Overlay (Mobile Only) */
    .sidebar-overlay {
      position: fixed;
      top: var(--header-height);
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

    .menu-link {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 15px;
      text-decoration: none;
      color: inherit;
      width: 100%;
    }

    .menu-item:hover .menu-link {
      color: var(--primary-red);
    }

    /* Keep existing active state styling */
    .menu-item.active .menu-link {
      color: var(--primary-red);
    }

    /* Dashboard Cards */
    .dashboard-cards {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .card {
      background-color: white;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 15px;
    }

    .card-title {
      font-size: 16px;
      font-weight: 600;
      color: var(--kfc-dark);
    }

    .card-icon {
      width: 50px;
      height: 50px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
    }

    .card-icon.orders {
      background-color: rgba(228, 0, 43, 0.1);
      color: var(--kfc-red);
    }

    .card-icon.revenue {
      background-color: rgba(76, 175, 80, 0.1);
      color: #4CAF50;
    }

    .card-icon.customers {
      background-color: rgba(33, 150, 243, 0.1);
      color: #2196F3;
    }

    .card-icon.items {
      background-color: rgba(255, 193, 7, 0.1);
      color: var(--kfc-yellow);
    }

    .card-value {
      font-size: 28px;
      font-weight: 700;
      margin-bottom: 5px;
    }

    .card-footer {
      display: flex;
      align-items: center;
      font-size: 13px;
      color: #666;
      margin-top: 10px;
    }

    .card-footer i {
      margin-right: 5px;
    }

    .positive {
      color: #4CAF50;
    }

    .negative {
      color: var(--kfc-red);
    }

    /* Recent Orders */
    .section-title {
      font-size: 20px;
      font-weight: 600;
      margin-bottom: 20px;
      color: var(--kfc-dark);
      position: relative;
      padding-bottom: 10px;
    }

    .section-title::after {
      content: "";
      position: absolute;
      left: 0;
      bottom: 0;
      width: 50px;
      height: 3px;
      background-color: var(--kfc-red);
    }

    .recent-orders {
      background-color: white;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
    }

    .table-responsive {
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    th {
      font-weight: 600;
      color: var(--kfc-dark);
      font-size: 14px;
    }

    td {
      font-size: 14px;
    }

    .status {
      display: inline-block;
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 500;
    }

    .status.pending {
      background-color: rgba(255, 193, 7, 0.1);
      color: #FFC107;
    }

    .status.completed {
      background-color: rgba(76, 175, 80, 0.1);
      color: #4CAF50;
    }

    .status.cancelled {
      background-color: rgba(244, 67, 54, 0.1);
      color: #F44336;
    }

    .status.preparing {
      background-color: rgba(33, 150, 243, 0.1);
      color: #2196F3;
    }

    .action-btn {
      padding: 5px 10px;
      border-radius: 5px;
      border: none;
      background-color: var(--kfc-red);
      color: white;
      cursor: pointer;
      font-size: 12px;
      transition: all 0.2s;
    }

    .action-btn:hover {
      background-color: #c10024;
    }

    /* Charts Section */
    .charts-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-top: 30px;
    }

    .chart-container {
      background-color: white;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
    }

    .chart-title {
      font-size: 16px;
      font-weight: 600;
      margin-bottom: 20px;
      color: var(--kfc-dark);
    }

    .chart {
      height: 300px;
      width: 100%;
      position: relative;
    }

    /* Responsive Styles */
    @media (max-width: 1200px) {
      .charts-row {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 992px) {
      .admin-sidebar {
        transform: translateX(-100%);
      }

      .admin-sidebar.active {
        transform: translateX(0);
      }

      .admin-content {
        margin-left: 0;
      }
    }

    @media (max-width: 768px) {
      .dashboard-cards {
        grid-template-columns: 1fr 1fr;
      }

      .content-wrapper {
        padding: 20px 15px;
      }
    }

    @media (max-width: 576px) {
      .dashboard-cards {
        grid-template-columns: 1fr;
      }
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
    <!-- <div class="user-controls">
      <div class="notification">
        <i class="fas fa-bell"></i>
        <span class="badge">3</span>
      </div>
      <div class="user-profile">
        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Admin User">
        <span>Admin</span>
      </div>
    </div> -->
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
      <a href="admin_dashboard.php" class="menu-link">
        <i class="fas fa-tachometer-alt"></i>
        <span>Dashboard</span>
      </a>
    </li>
    <li class="menu-item active">
      <a href="add_items.php" class="menu-link">
        <i class="fas fa-utensils"></i>
        <span>Menu Management</span>
      </a>
    </li>
    <li class="menu-item">
      <a href="activity_logs.php" class="menu-link">
        <i class="fas fa-chart-line"></i>
        <span>Activity Logs</span>
      </a>
    </li>
  </ul>
</aside>
    
    <!-- Main Content -->
    <main class="admin-content">
      <!-- Dashboard Cards -->
      <div class="dashboard-cards">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Today's Orders</h3>
            <div class="card-icon orders">
              <i class="fas fa-shopping-bag"></i>
            </div>
          </div>
          <div class="card-value">42</div>
          <div class="card-footer">
            <i class="fas fa-arrow-up positive"></i>
            <span class="positive">12% from yesterday</span>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Revenue</h3>
            <div class="card-icon revenue">
              <i class="fas fa-dollar-sign"></i>
            </div>
          </div>
          <div class="card-value">Rs. 125,400</div>
          <div class="card-footer">
            <i class="fas fa-arrow-up positive"></i>
            <span class="positive">8% from yesterday</span>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Active Customers</h3>
            <div class="card-icon customers">
              <i class="fas fa-users"></i>
            </div>
          </div>
          <div class="card-value">18</div>
          <div class="card-footer">
            <i class="fas fa-arrow-down negative"></i>
            <span class="negative">3% from yesterday</span>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Menu Items</h3>
            <div class="card-icon items">
              <i class="fas fa-utensils"></i>
            </div>
          </div>
          <div class="card-value">36</div>
          <div class="card-footer">
            <a href="add_items.php" style="color: var(--kfc-red); text-decoration: none;">
              <i class="fas fa-plus"></i>
              <span>Add new item</span>
            </a>
          </div>
        </div>
      </div>

      <!-- Recent Orders -->
      <div class="recent-orders">
        <h2 class="section-title">Recent Orders</h2>
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Table</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>#ORD-1254</td>
                <td>T5</td>
                <td>John Smith</td>
                <td>3</td>
                <td>Rs. 2,850</td>
                <td><span class="status completed">Completed</span></td>
                <td><button class="action-btn">View</button></td>
              </tr>
              <tr>
                <td>#ORD-1253</td>
                <td>T12</td>
                <td>Sarah Johnson</td>
                <td>5</td>
                <td>Rs. 4,200</td>
                <td><span class="status preparing">Preparing</span></td>
                <td><button class="action-btn">View</button></td>
              </tr>
              <tr>
                <td>#ORD-1252</td>
                <td>T8</td>
                <td>Michael Brown</td>
                <td>2</td>
                <td>Rs. 1,750</td>
                <td><span class="status pending">Pending</span></td>
                <td><button class="action-btn">View</button></td>
              </tr>
              <tr>
                <td>#ORD-1251</td>
                <td>T3</td>
                <td>Emily Davis</td>
                <td>4</td>
                <td>Rs. 3,600</td>
                <td><span class="status completed">Completed</span></td>
                <td><button class="action-btn">View</button></td>
              </tr>
              <tr>
                <td>#ORD-1250</td>
                <td>T7</td>
                <td>Robert Wilson</td>
                <td>1</td>
                <td>Rs. 850</td>
                <td><span class="status cancelled">Cancelled</span></td>
                <td><button class="action-btn">View</button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Charts Section -->
      <div class="charts-row">
        <div class="chart-container">
          <h3 class="chart-title">Revenue Overview</h3>
          <div class="chart" id="revenueChart">
            <!-- Chart will be rendered here with JavaScript -->
            <canvas id="revenueCanvas"></canvas>
          </div>
        </div>

        <div class="chart-container">
          <h3 class="chart-title">Popular Items</h3>
          <div class="chart" id="popularItemsChart">
            <!-- Chart will be rendered here with JavaScript -->
            <canvas id="itemsCanvas"></canvas>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>

    // Initialize Charts
    document.addEventListener('DOMContentLoaded', function() {
      // Revenue Chart
      const revenueCtx = document.getElementById('revenueCanvas').getContext('2d');
      const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
          datasets: [{
            label: 'Revenue (Rs.)',
            data: [85000, 92000, 105000, 115000, 125000, 140000, 150000],
            backgroundColor: 'rgba(228, 0, 43, 0.1)',
            borderColor: 'rgba(228, 0, 43, 1)',
            borderWidth: 2,
            tension: 0.4,
            fill: true
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                drawBorder: false
              }
            },
            x: {
              grid: {
                display: false
              }
            }
          }
        }
      });

      // Popular Items Chart
      const itemsCtx = document.getElementById('itemsCanvas').getContext('2d');
      const itemsChart = new Chart(itemsCtx, {
        type: 'bar',
        data: {
          labels: ['Zinger Burger', 'Chicken Bucket', 'French Fries', 'Rice Meal', 'Coleslaw'],
          datasets: [{
            label: 'Orders',
            data: [120, 85, 65, 45, 30],
            backgroundColor: [
              'rgba(228, 0, 43, 0.7)',
              'rgba(228, 0, 43, 0.6)',
              'rgba(228, 0, 43, 0.5)',
              'rgba(228, 0, 43, 0.4)',
              'rgba(228, 0, 43, 0.3)'
            ],
            borderColor: [
              'rgba(228, 0, 43, 1)',
              'rgba(228, 0, 43, 1)',
              'rgba(228, 0, 43, 1)',
              'rgba(228, 0, 43, 1)',
              'rgba(228, 0, 43, 1)'
            ],
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                drawBorder: false
              }
            },
            x: {
              grid: {
                display: false
              }
            }
          }
        }
      });

      // Simulate real-time data updates
      setInterval(() => {
        // Update revenue chart with random data
        const newRevenueData = revenueChart.data.datasets[0].data.map(value => {
          const change = Math.floor(Math.random() * 10000) - 5000;
          return Math.max(50000, value + change);
        });
        revenueChart.data.datasets[0].data = newRevenueData;
        revenueChart.update();

        // Update items chart with random data
        const newItemsData = itemsChart.data.datasets[0].data.map(value => {
          const change = Math.floor(Math.random() * 10) - 5;
          return Math.max(10, value + change);
        });
        itemsChart.data.datasets[0].data = newItemsData;
        itemsChart.update();
      }, 5000);
    });

    // Notification click
    document.querySelector('.notification').addEventListener('click', function() {
      alert('You have 3 new notifications');
    });

    // User profile click
    document.querySelector('.user-profile').addEventListener('click', function() {
      alert('User profile menu would open here');
    });
  </script>
</body>
</html>