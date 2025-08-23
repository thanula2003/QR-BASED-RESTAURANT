<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Tasty Legs</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
      --sidebar-width: 250px;
      --header-height: 56px;
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
      min-height: 100vh;
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
      width: var(--sidebar-width);
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

    .menu-item.active .menu-link {
      color: var(--primary-red);
    }
    
    /* Sidebar Overlay  */
    .sidebar-overlay {
      position: fixed;
      top: var(--header-height);
      left: 0;
      right: 0;
      bottom: 0;
      z-index: 800;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s;
    }

    .sidebar-overlay.active {
      opacity: 1;
      visibility: visible;
    }
    /* Hide overlay by default */
.sidebar-overlay {
  display: none;
}

/* Only enable on mobile */
@media (max-width: 768px) {
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
    display: block; /* show only in mobile */
  }

  .sidebar-overlay.active {
    opacity: 1;
    visibility: visible;
  }
}


    /* Main Content Styles */
    .main-content {
      margin-left: var(--sidebar-width);
      padding: 30px;
      transition: all 0.3s ease;
      width: calc(100% - var(--sidebar-width));
      background-color: #f5f7fa;
      min-height: calc(100vh - var(--header-height));
      display: flex;
      flex-direction: column;
    }

    .main-content.expanded {
      margin-left: 70px;
      width: calc(100% - 70px);
    }

    .content-wrapper {
      flex: 1;
      display: flex;
      flex-direction: column;
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
      height: 100%;
      display: flex;
      flex-direction: column;
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
      color: var(--dark);
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
      color: var(--primary-red);
    }
    
    .card-icon.pending {
      background-color: rgba(255, 193, 7, 0.1);
      color: var(--warning);
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
      color: var(--yellow);
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
      margin-top: auto;
    }

    .card-footer i {
      margin-right: 5px;
    }

    .positive {
      color: #4CAF50;
    }

    .negative {
      color: var(--primary-red);
    }

    /* Financial Overview Section */
    .financial-overview {
      background-color: white;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
      margin-bottom: 30px;
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .section-title {
      font-size: 20px;
      font-weight: 600;
      margin-bottom: 20px;
      color: var(--dark);
      position: relative;
      padding-bottom: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .section-title::after {
      content: "";
      position: absolute;
      left: 0;
      bottom: 0;
      width: 50px;
      height: 3px;
      background-color: var(--primary-red);
    }

    .time-filter {
      display: flex;
      gap: 10px;
    }

    .time-filter-btn {
      padding: 8px 16px;
      border-radius: 20px;
      border: 1px solid var(--medium-gray);
      background: white;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;
    }

    .time-filter-btn.active {
      background-color: var(--primary-red);
      color: white;
      border-color: var(--primary-red);
    }

    .financial-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 15px;
      margin-bottom: 25px;
    }

    .financial-card {
      background: var(--light-gray);
      border-radius: 10px;
      padding: 15px;
      text-align: center;
    }

    .financial-card .title {
      font-size: 14px;
      color: var(--dark-gray);
      margin-bottom: 8px;
    }

    .financial-card .value {
      font-size: 24px;
      font-weight: 700;
      color: var(--dark);
    }

    .financial-card .change {
      font-size: 12px;
      margin-top: 5px;
    }

    /* Charts Section */
    .charts-container {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      flex: 1;
    }

    .chart-box {
      background: white;
      border-radius: 10px;
      padding: 15px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    .chart-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }

    .chart-title {
      font-size: 16px;
      font-weight: 600;
      color: var(--dark);
    }

    .chart-subtitle {
      font-size: 13px;
      color: var(--dark-gray);
    }

    .chart {
      height: 250px;
      width: 100%;
      flex: 1;
    }

    .items-list {
      margin-top: 15px;
    }

    .item-row {
      display: flex;
      justify-content: space-between;
      padding: 8px 0;
      border-bottom: 1px solid var(--light-gray);
    }

    .item-name {
      font-weight: 500;
    }

    .item-value {
      font-weight: 600;
    }

    .item-row.best {
      color: var(--success);
    }

    .item-row.low {
      color: var(--primary-red);
    }

    /* Responsive Styles */
    @media (max-width: 1200px) {
      .charts-container {
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

      .main-content {
        margin-left: 0;
        width: 100%;
      }
      
      .main-content.expanded {
        margin-left: 0;
        width: 100%;
      }
    }

    @media (max-width: 768px) {
      .dashboard-cards {
        grid-template-columns: 1fr 1fr;
      }

      .financial-cards {
        grid-template-columns: 1fr 1fr;
      }
    }

    @media (max-width: 576px) {
      .dashboard-cards {
        grid-template-columns: 1fr;
      }
      
      .financial-cards {
        grid-template-columns: 1fr;
      }
      
      .time-filter {
        flex-wrap: wrap;
      }
      
      .main-content {
        padding: 15px;
      }
    }
    /* Performers Section Styles */
.performers-section {
  background-color: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
  margin-top: 30px;
}

.performers-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.top-performers, .low-performers {
  background-color: var(--light-gray);
  border-radius: 10px;
  padding: 15px;
}

.performers-title {
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 15px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.performers-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.performer-item {
  display: flex;
  align-items: center;
  background: white;
  padding: 10px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.performer-rank {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background-color: var(--medium-gray);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 600;
  margin-right: 10px;
}

.performer-item:nth-child(1) .performer-rank {
  background-color: var(--yellow);
}

.performer-item:nth-child(2) .performer-rank {
  background-color: var(--dark-gray);
}

.performer-item:nth-child(3) .performer-rank {
  background-color: #CD7F32; /* Bronze color */
}

.performer-info {
  flex: 1;
}

.performer-name {
  font-weight: 500;
  font-size: 14px;
}

.performer-stats {
  font-size: 12px;
  color: var(--dark-gray);
}

.performer-value {
  font-weight: 600;
  font-size: 14px;
}

.view-all a {
  color: var(--primary-red);
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
}

.view-all a:hover {
  text-decoration: underline;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .performers-container {
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
   
  </header>
  
  <!-- Admin Container -->
  <div class="admin-container">
    <!-- Sidebar Overlay (Mobile Only) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
      <div class="sidebar-header">
        <i class="fas fa-store"></i>
        <h3>Restaurant</h3>
      </div>
      <ul class="menu">
        <li class="menu-item active">
          <a href="#" class="menu-link">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
          </a>
        </li>
        <li class="menu-item">
          <a href="add_items.php" class="menu-link">
            <i class="fas fa-utensils"></i>
            <span>Menu Management</span>
          </a>
        </li>
        <li class="menu-item">
          <a href="#" class="menu-link">
            <i class="fas fa-chart-line"></i>
            <span>Activity Logs</span>
          </a>
        </li>
      </ul>
    </aside>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
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
               <h3 class="card-title">Pending Orders</h3>
                <div class="card-icon pending">
                <i class="fas fa-clock"></i> <!-- Changed icon -->
            </div>
          </div>
          <div class="card-value">8</div> <!-- Example pending orders count -->
          <div class="card-footer">
          <a href="#" style="color: var(--primary-red); text-decoration: none;">
                <i class="fas fa-arrow-right"></i>
                <span>See Pending Orders</span>
              </a>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Revenue</h3>
              <div class="card-icon revenue">
                <i class="fas fa-rupee-sign"></i>
              </div>
            </div>
            <div class="card-value">Rs. 42,300</div>
            <div class="card-footer">
              <i class="fas fa-arrow-up positive"></i>
              <span class="positive">8% from yesterday</span>
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
              <a href="#" style="color: var(--primary-red); text-decoration: none;">
                <i class="fas fa-plus"></i>
                <span>Edit Menu item</span>
              </a>
            </div>
          </div>
        </div>

        <!-- Financial Overview Section -->
        <div class="financial-overview">
          <div class="section-title">
            <h2>Financial Overview</h2>
            <div class="time-filter">
              <button class="time-filter-btn active" data-period="daily">Daily</button>
              <button class="time-filter-btn" data-period="weekly">Weekly</button>
              <button class="time-filter-btn" data-period="monthly">Monthly</button>
            </div>
          </div>
          
          <div class="financial-cards">
            <div class="financial-card">
              <div class="title">Total Revenue</div>
              <div class="value" id="revenue-value">Rs. 125,400</div>
              <div class="change positive">+8% from yesterday</div>
            </div>
            <div class="financial-card">
              <div class="title">Total Profit</div>
              <div class="value" id="profit-value">Rs. 42,300</div>
              <div class="change positive">+5% from yesterday</div>
            </div>
            <div class="financial-card">
              <div class="title">Avg. Order Value</div>
              <div class="value" id="avg-order-value">Rs. 2,985</div>
              <div class="change positive">+3% from yesterday</div>
            </div>
            <div class="financial-card">
              <div class="title">Profit Margin</div>
              <div class="value" id="profit-margin">33.7%</div>
              <div class="change negative">-1.2% from yesterday</div>
            </div>
          </div>
          
          <div class="charts-container">
            <div class="chart-box">
              <div class="chart-header">
                <div>
                  <div class="chart-title">Revenue Growth</div>
                  <div class="chart-subtitle">Compared to previous period</div>
                </div>
              </div>
              <div class="chart">
                <canvas id="revenueGrowthChart"></canvas>
              </div>
            </div>
            
            <div class="chart-box">
              <div class="chart-header">
                <div>
                  <div class="chart-title">Top & Low Performers</div>
                  <div class="chart-subtitle">Best and least selling items</div>
                </div>
              </div>
              <div class="chart">
                <canvas id="itemsPerformanceChart"></canvas>
              </div>
            </div>
          </div>
          <!-- Add this section right after the charts-container div -->
<div class="performers-section">
  <div class="section-title">
    <h2>Top & Low Performers</h2>
    <div class="view-all">
      <a href="#">View Full Report <i class="fas fa-arrow-right"></i></a>
    </div>
  </div>
  
  <div class="performers-container">
    <div class="top-performers">
      <h3 class="performers-title">
        <i class="fas fa-trophy" style="color: var(--yellow);"></i>
        Top Performers
      </h3>
      <div class="performers-list">
        <div class="performer-item">
          <div class="performer-rank">1</div>
          <div class="performer-info">
            <div class="performer-name">Zinger Burger</div>
            <div class="performer-stats">142 sold • Rs. 42,600</div>
          </div>
        </div>
        <div class="performer-item">
          <div class="performer-rank">2</div>
          <div class="performer-info">
            <div class="performer-name">Chicken Bucket</div>
            <div class="performer-stats">98 sold • Rs. 29,400</div>
          </div>
        </div>
        <div class="performer-item">
          <div class="performer-rank">3</div>
          <div class="performer-info">
            <div class="performer-name">French Fries</div>
            <div class="performer-stats">76 sold • Rs. 11,400</div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="low-performers">
      <h3 class="performers-title">
        <i class="fas fa-exclamation-triangle" style="color: var(--primary-red);"></i>
        Needs Attention
      </h3>
      <div class="performers-list">
        <div class="performer-item">
          <div class="performer-rank">7</div>
          <div class="performer-info">
            <div class="performer-name">Coleslaw</div>
            <div class="performer-stats">23 sold • Rs. 2,300</div>
          </div>
        </div>
        <div class="performer-item">
          <div class="performer-rank">6</div>
          <div class="performer-info">
            <div class="performer-name">Beverages</div>
            <div class="performer-stats">35 sold • Rs. 7,000</div>
          </div>
        </div>
        <div class="performer-item">
          <div class="performer-rank">5</div>
          <div class="performer-info">
            <div class="performer-name">Desserts</div>
            <div class="performer-stats">45 sold • Rs. 9,000</div>
          </div>        
        </div>
      </div>
    </div>
  </div>
</div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Toggle Sidebar
    const menuToggle = document.querySelector('.menu-toggle');
    const adminSidebar = document.getElementById('adminSidebar');
    const mainContent = document.getElementById('mainContent');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    menuToggle.addEventListener('click', () => {
      adminSidebar.classList.toggle('active');
      sidebarOverlay.classList.toggle('active');
    });

    sidebarOverlay.addEventListener('click', () => {
      adminSidebar.classList.remove('active');
      sidebarOverlay.classList.remove('active');
    });

    // Time period filtering
    const timeFilterBtns = document.querySelectorAll('.time-filter-btn');
    timeFilterBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        timeFilterBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        updateFinancialData(btn.dataset.period);
      });
    });

    // Financial data for different time periods
    const financialData = {
      daily: {
        revenue: 'Rs. 125,400',
        profit: 'Rs. 42,300',
        avgOrder: 'Rs. 2,985',
        margin: '33.7%',
        revenueChange: '+8%',
        profitChange: '+5%',
        avgOrderChange: '+3%',
        marginChange: '-1.2%',
        growthData: [120, 140, 160, 180, 165, 155, 145, 165, 185, 205, 225, 210, 195, 185, 200, 220, 240, 260, 280, 265, 250, 235, 225, 240],
        growthLabels: ['12a', '2a', '4a', '6a', '8a', '10a', '12p', '2p', '4p', '6p', '8p', '10p'],
        itemsData: [142, 98, 76, 65, 45, 35, 23],
        itemsLabels: ['Zinger Burger', 'Chicken Bucket', 'French Fries', 'Rice Meal', 'Desserts', 'Beverages', 'Coleslaw']
      },
      weekly: {
        revenue: 'Rs. 785,600',
        profit: 'Rs. 265,800',
        avgOrder: 'Rs. 3,120',
        margin: '33.8%',
        revenueChange: '+12%',
        profitChange: '+8%',
        avgOrderChange: '+5%',
        marginChange: '+0.5%',
        growthData: [185, 210, 225, 240, 260, 280, 300],
        growthLabels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        itemsData: [650, 480, 420, 380, 280, 220, 180],
        itemsLabels: ['Zinger Burger', 'Chicken Bucket', 'French Fries', 'Rice Meal', 'Desserts', 'Beverages', 'Coleslaw']
      },
      monthly: {
        revenue: 'Rs. 3,245,800',
        profit: 'Rs. 1,095,400',
        avgOrder: 'Rs. 3,050',
        margin: '33.7%',
        revenueChange: '+15%',
        profitChange: '+12%',
        avgOrderChange: '+7%',
        marginChange: '+1.1%',
        growthData: [2800, 2950, 3100, 3020, 3150, 3250, 3350, 3420, 3550, 3650, 3720, 3850],
        growthLabels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        itemsData: [2450, 1980, 1750, 1620, 1420, 1250, 980],
        itemsLabels: ['Zinger Burger', 'Chicken Bucket', 'French Fries', 'Rice Meal', 'Desserts', 'Beverages', 'Coleslaw']
      }
    };

    // Update financial data based on selected period
    function updateFinancialData(period) {
      const data = financialData[period];
      
      document.getElementById('revenue-value').textContent = data.revenue;
      document.getElementById('profit-value').textContent = data.profit;
      document.getElementById('avg-order-value').textContent = data.avgOrder;
      document.getElementById('profit-margin').textContent = data.margin;
      
      document.querySelectorAll('.financial-card .change').forEach((el, index) => {
        const changes = [data.revenueChange, data.profitChange, data.avgOrderChange, data.marginChange];
        const isPositive = changes[index].includes('+');
        el.textContent = `${changes[index]} from previous ${period === 'daily' ? 'day' : period === 'weekly' ? 'week' : 'month'}`;
        el.className = `change ${isPositive ? 'positive' : 'negative'}`;
      });
      
      updateRevenueGrowthChart(data.growthData, data.growthLabels);
      updateItemsPerformanceChart(data.itemsData, data.itemsLabels);
    }

    // Initialize revenue growth chart
    let revenueGrowthChart;
    function updateRevenueGrowthChart(data, labels) {
      const ctx = document.getElementById('revenueGrowthChart').getContext('2d');
      
      if (revenueGrowthChart) {
        revenueGrowthChart.destroy();
      }
      
      revenueGrowthChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Revenue',
            data: data,
            backgroundColor: 'rgba(228, 0, 43, 0.7)',
            borderColor: 'rgba(228, 0, 43, 1)',
            borderWidth: 1,
            borderRadius: 5,
            barPercentage: 0.6,
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
    }

    // Initialize items performance chart with conditional coloring
    let itemsPerformanceChart;
    function updateItemsPerformanceChart(data, labels) {
      const ctx = document.getElementById('itemsPerformanceChart').getContext('2d');
      
      if (itemsPerformanceChart) {
        itemsPerformanceChart.destroy();
      }
      
      // Create different colors based on value
      const maxValue = Math.max(...data);
      const backgroundColors = data.map(value => {
        const opacity = value / maxValue;
        return value < 50 ? 'rgba(228, 0, 43, 0.7)' : `rgba(76, 175, 80, ${0.5 + opacity * 0.5})`;
      });
      
      const borderColors = data.map(value => {
        return value < 50 ? 'rgba(228, 0, 43, 1)' : 'rgba(76, 175, 80, 1)';
      });
      
      itemsPerformanceChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Items Sold',
            data: data,
            backgroundColor: backgroundColors,
            borderColor: borderColors,
            borderWidth: 1,
            borderRadius: 5,
            barPercentage: 0.6,
          }]
        },
        options: {
          indexAxis: 'y',
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  return `Sold: ${context.raw}`;
                }
              }
            }
          },
          scales: {
            x: {
              beginAtZero: true,
              grid: {
                drawBorder: false
              }
            },
            y: {
              grid: {
                display: false
              }
            }
          }
        }
      });
    }

    // Initialize Charts
    document.addEventListener('DOMContentLoaded', function() {
      // Set up financial overview with daily data by default
      updateFinancialData('daily');
    });
  </script>
</body>
</html>