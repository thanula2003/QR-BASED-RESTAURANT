<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Tasty Legs</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    :root {
      --kfc-red: #E4002B;
      --kfc-dark: #231F20;
      --kfc-light: #FFFFFF;
      --kfc-yellow: #FFC72C;
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

    /* Dashboard Content */
    .content-wrapper {
      padding: 30px;
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
      .dashboard-cards {
        grid-template-columns: 1fr 1fr;
      }

      .header {
        padding: 0 15px;
      }

      .content-wrapper {
        padding: 20px 15px;
      }
    }

    @media (max-width: 576px) {
      .dashboard-cards {
        grid-template-columns: 1fr;
      }

      .header-title {
        font-size: 18px;
      }

      .user-name {
        display: none;
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
      <a href="#" class="menu-item active">
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
      <a href="#" class="menu-item">
        <i class="fas fa-users"></i>
        <span>Customers</span>
      </a>
      <a href="#" class="menu-item">
        <i class="fas fa-table"></i>
        <span>Tables</span>
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
      <a href="#" class="menu-item">
        <i class="fas fa-chart-line"></i>
        <span>Reports</span>
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
        <h1 class="header-title">Dashboard</h1>
      </div>
      <div class="header-right">
        <div class="notification-icon">
          <i class="fas fa-bell"></i>
          <span class="notification-badge">3</span>
        </div>
        <div class="user-profile">
          <div class="user-avatar">AD</div>
          <span class="user-name">Admin</span>
        </div>
      </div>
    </header>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
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
            <a href="#" style="color: var(--kfc-red); text-decoration: none;">
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
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Toggle Sidebar
    const toggleSidebar = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');

    toggleSidebar.addEventListener('click', () => {
      sidebar.classList.toggle('active');
      mainContent.classList.toggle('active');
    });

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
    document.querySelector('.notification-icon').addEventListener('click', function() {
      alert('You have 3 new notifications');
    });

    // User profile click
    document.querySelector('.user-profile').addEventListener('click', function() {
      alert('User profile menu would open here');
    });
  </script>
</body>
</html>