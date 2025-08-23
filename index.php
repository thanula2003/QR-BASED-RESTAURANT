<?php 
include 'db_connect.php';
session_start();

// Get table ID from URL parameter
$tableId = isset($_GET['table_id']) ? $_GET['table_id'] : '';

// Verify table exists or create a fallback
if ($tableId) {
    // First, check if the tables table exists
    try {
        $tableCheck = $pdo->query("SHOW TABLES LIKE 'tables'")->fetch();
        
        if ($tableCheck) {
            $stmt = $pdo->prepare("SELECT * FROM tables WHERE Table_Id = ?");
            $stmt->execute([$tableId]);
            $table = $stmt->fetch();
            
            if (!$table) {
                // If table ID not found in database, create a fallback table object
                $table = ['Table_Id' => $tableId, 'Table_Name' => 'Table ' . $tableId, 'Status' => 'Available'];
            }
        } else {
            // If the table doesn't exist, create a fallback table object
            $table = ['Table_Id' => $tableId, 'Table_Name' => 'Table ' . $tableId, 'Status' => 'Available'];
        }
        
        // Store table ID in session
        $_SESSION['tableId'] = $tableId;
        
    } catch (Exception $e) {
        // If any database error occurs, use fallback
        $table = ['Table_Id' => $tableId, 'Table_Name' => 'Table ' . $tableId, 'Status' => 'Available'];
        $_SESSION['tableId'] = $tableId;
    }
} else {
    die("Table not specified");
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KFC Style - Chicken Delivery</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
      :root {
        --kfc-red: #E4002B;
        --kfc-dark: #231F20;
        --kfc-light: #FFFFFF;
        --kfc-yellow: #FFC72C;
        --kfc-gray: #F5F5F5;
      }

          /* New Best Sellers Styling */
      #bestSellers .scroll-container {
      display: flex;
      gap: 15px;
      padding: 0 15px 15px;
      overflow-x: auto;
      }

      #bestSellers .card {
      min-width: 250px;
      border-radius: 12px;
      background: white;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
      padding: 15px;
      position: relative;
      }

      #bestSellers .card h4 {
      font-size: 16px;
      font-weight: 700;
      margin-bottom: 5px;
      color: var(--kfc-dark);
      }

      #bestSellers .card .type {
      font-size: 12px;
      color: var(--kfc-red);
      font-weight: 600;
      margin-bottom: 5px;
      }

      #bestSellers .card .description {
      font-size: 14px;
      color: #666;
      margin-bottom: 10px;
      }

      #bestSellers .card .price {
      font-size: 18px;
      font-weight: 800;
      color: var(--kfc-red);
      }

          /* Remove the HOT badge if not needed */
      #bestSellers .card::after {
        display: none;
      }

          /* This code is used to highlight anything that touch */
      .card:active {
          background-color: rgba(228, 0, 43, 0.1); /* Light red feedback */
      }

      * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        -webkit-tap-highlight-color: transparent; /*this code removes the blue highlight when click*/
      }
      
      body {
        font-family: 'Inter', sans-serif;
        background-color: var(--kfc-light);
        color: var(--kfc-dark);
        padding-bottom: 0;
      }
      
      header {
        padding: 13px;
        background-color: var(--kfc-red);
        color: white;
        position: relative;
        text-align: center;
        z-index: 101;
      }
      
      .logo {
        font-family: 'Inter', sans-serif;
        font-size: 28px;
        font-weight: 700;
        letter-spacing: 1px;
        color: white;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
      }
      
      .logo::before {
        content: "🍗";
        font-size: 32px;
      }
      
      .search-bar {
        position: relative;
        margin-top: 15px;
      }
      
      .search-bar input {
        width: 100%;
        padding: 12px 15px;
        border: none;
        border-radius: 25px;
        font-size: 14px;
        background-color: rgba(255,255,255,0.9);
        outline: none;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      }
      
      .categories {
          display: flex;
          gap: 8px;
          padding: 15px;
          overflow-x: auto;
          background: white;
          border-bottom: 1px solid #f0f0f0;
          position: sticky;
          top: 0;
          z-index: 100;
      }
      
      .category {
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        white-space: nowrap;
        background: #f5f5f5;
        color: var(--kfc-dark);
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid #e0e0e0;
      }
      
      .category.active {
        background: var(--kfc-red);
        color: white;
        border-color: var(--kfc-red);
      }
      
      .section {
        padding: 0 15px;
        margin-bottom: 25px;
      }
      
      .section-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding: 0 5px;
      }
      
      .section-title h2 {
        font-size: 20px;
        font-weight: 700;
        margin: 0;
        color: var(--kfc-red);
        position: relative;
      }
      
      .section-title h2::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: -5px;
        width: 40px;
        height: 3px;
        background-color: var(--kfc-red);
      }
      
      .section-title a {
        color: var(--kfc-dark);
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
      }
      
      .scroll-container {
        display: flex;
        gap: 15px;
        overflow-x: auto;
        padding-bottom: 10px;
        padding-top: 5px;
      }
      
      /* Best Sellers Special Styling */
      #bestSellers .card {
        min-width: 200px;
        border: 2px solid var(--kfc-yellow);
        position: relative;
      }
      
      #bestSellers .card::after {
        content: "HOT";
        position: absolute;
        top: 10px;
        right: 10px;
        background: var(--kfc-yellow);
        color: var(--kfc-dark);
        font-weight: bold;
        font-size: 10px;
        padding: 3px 10px;
        border-radius: 10px;
      }
      
      .card {
        background: white;
        border-radius: 12px;
        padding: 15px;
        min-width: 160px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        flex: 0 0 auto;
        cursor: pointer;
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
        border: 1px solid #f0f0f0;
      }
      
      .card:hover, .card.selected {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(228, 0, 43, 0.2);
        border: 1px solid var(--kfc-red);
      }
      
      .card-img-container {
        width: 100%;
        height: 120px;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 12px;
        position: relative;
      }
      
      .card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
      }
      
      .card:hover img {
        transform: scale(1.05);
      }
      
      .card h4 {
        margin: 0 0 5px;
        font-size: 15px;
        font-weight: 700;
        text-align: center;
        color: var(--kfc-dark);
      }
      
      .card .price {
        color: var(--kfc-red);
        font-size: 16px;
        font-weight: 800;
        text-align: center;
      }
      
    .quantity-controls {
      display: none;
      justify-content: center;
      align-items: center;
      gap: 10px;
      margin-top: 10px;
    }
    
    .card.selected .quantity-controls {
      display: flex;
    }
    
    .quantity-btn {
      font-size: 25px;       /* Increase or decrease to resize text inside button */
      width: 36px;           /* Adjust button width */
      height: 36px;          /* Adjust button height */
      border-radius: 50px;    /* Optional: round corners */
      background-color: #e60000; /* Optional: background color */
      color: white;          /* Button text color */
      border: none;          /* Remove border */
      cursor: pointer;       /* Pointer cursor on hover */
      transition: 0.3s;
    }

    .quantity-btn:hover {
      background-color: #b30000; /* Darker on hover */
    }
      
    .buy-btn-container {
    position: fixed;
    bottom: 30px; /*adjest the possition of the order button*/
    right: 20px;
    z-index: 10;
    transition: transform 0.3s ease-in-out; /* Smooth transition for button appearance of the Order button */
    }

    .footer-container {
        max-width: 900px;
        margin: 0 auto;
        padding-bottom: 10px; /* Add padding to prevent content from being hidden behind button */
    }

      .buy-btn {
      background: var(--kfc-red);
      color: white;
      border: none;
      width: 70px;
      height: 70px;
      border-radius: 50%;
      font-weight: bold;
      font-size: 16px;
      cursor: pointer;
      box-shadow:
          0 0 0 3px var(--kfc-light),             /* 👈 Light ring */
          0 4px 20px rgba(228, 0, 43, 0.4);       /* Shadow for depth */
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      position: relative;
      transition: all 0.2s ease;
      }

      .buy-btn:hover {
      background: #c10024;
      transform: scale(1.05);
      box-shadow:
          0 0 0 3px var(--kfc-light),
          0 6px 24px rgba(228, 0, 43, 0.5);
      }

      .item-count {
      position: absolute;
      top: -5px;
      right: -5px;
      background: white;
      color: var(--kfc-red);
      border-radius: 50%;
      width: 28px;
      height: 28px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      border: 2px solid var(--kfc-red);
      font-weight: bold;
      }
      
      .promo-banner {
        background: linear-gradient(to right, var(--kfc-red), #c10024);
        color: white;
        padding: 12px 20px;
        margin: 15px;
        border-radius: 8px;
        text-align: center;
        font-weight: 700;
        font-size: 15px;
        box-shadow: 0 3px 10px rgba(228, 0, 43, 0.2);
      }
      
      .kfc-stripes {
        height: 5px;
        background: repeating-linear-gradient(45deg, var(--kfc-red), var(--kfc-red) 10px, var(--kfc-yellow) 10px, var(--kfc-yellow) 20px);
        margin-bottom: 15px;
      }

      .special-offer-scroll {
      display: flex;
      overflow-x: auto;
      scroll-snap-type: x mandatory;
      scroll-behavior: smooth;
      -webkit-overflow-scrolling: touch;
      width: 100%;
      }

      .special-offer-scroll::-webkit-scrollbar {
      display: none;
      }

      .special-offer-scroll .card {
      flex: 0 0 100%;
      max-width: 100%;
      height: auto;
      scroll-snap-align: start;
      padding: 15px;
      margin: 10px 0;
      box-sizing: border-box;
      border-radius: 15px;
      border: 1px solid #eee;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      background-color: white;
      }

      .special-offer-scroll .card-img-container {
      width: 100%;
      height: 200px;
      overflow: hidden;
      border-radius: 10px;
      margin-bottom: 10px;
      }

      .special-offer-scroll .card-img-container img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      }

      .special-offer-scroll .card h4 {
      font-size: 18px;
      margin-bottom: 5px;
      text-align: center;
      }

      .special-offer-scroll .card .price {
      font-size: 16px;
      color: var(--kfc-red);
      text-align: center;
      font-weight: bold;
      }

      /* scroll indicator styles */
      .scroll-indicators {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 10px;
        padding-bottom: 10px;
      }

      .scroll-indicators .dot {
          width: 10px;
          height: 10px;
          border-radius: 50%;
          background-color: #ccc;
          cursor: pointer;
          transition: background-color 0.3s;
      }

      .scroll-indicators .dot.active {
      background-color: var(--kfc-red); /* Or use #e4002b */
      transform: scale(1.2);
      }
      /* to hear */

          /* Remove the HOT badge if not needed */
      #bestSellers .full-width-card::after {
          display: none;
      }

      .full-width-card:active {
      background-color: rgba(228, 0, 43, 0.1);
      }

      .full-width-card {
      width: 100vw;
      height: auto;
      cursor: pointer;
      transition: background-color 0.2s ease;
      }

      .site-footer {
        background-color: #1a1a1a;
        color: white;
        padding: 30px 20px 15px;
        font-family: 'Segoe UI', sans-serif;
        text-align: center;
      }

      .footer-container {
        max-width: 900px;
        margin: 0 auto;
      }

      .footer-about h2,
      .footer-social h2 {
        font-size: 18px;
        margin-bottom: 10px;
        color: var(--kfc-light, #ffd6dc);
      }

      .footer-about p {
        font-size: 14px;
        line-height: 1.5;
        margin-bottom: 20px;
      }

      .footer-social .social-icons {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-bottom: 20px;
      }

      .footer-social .social-icons a {
        color: white;
        font-size: 20px;
        transition: color 0.3s;
      }

      .footer-social .social-icons a:hover {
        color: var(--kfc-light, #ffd6dc);
      }

      .footer-bottom p {
        font-size: 13px;
        opacity: 0.6;
      }

      /* Mobile responsiveness */
      @media (max-width: 480px) {
        .footer-about p,
        .footer-bottom p {
          font-size: 13px;
        }

        .footer-social .social-icons a {
          font-size: 18px;
        }
      }

        #special-offers-scroll {
            display: flex;
            gap: 15px;
            overflow-x: auto;
            padding-bottom: 10px;
            padding-top: 5px;
        }

        #special-offers-scroll .card {
            min-width: 160px;
            background: white;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            flex: 0 0 auto;
            cursor: pointer;
            border: 1px solid #f0f0f0;
        }
    </style>
  </head>
  <body  id="All">
    <!-- <div class="kfc-stripes"></div>
    -->
    <header>
      <div class="logo">TASTY LEGS</div>
      <!-- <div class="search-bar">
        <input type="text" placeholder="Search for chicken, burgers...">
      </div> -->
    </header>

  <!-- 
    <div class="promo-banner">
      SPECIAL OFFER! 2pc Chicken + Fries Rs. 1200 Only
    </div> -->

    <div class="categories">
      <div class="category active" onclick="redirectToCategory('All')">All</div>
      <div class="category" onclick="redirectToCategory('Chicken Buckets')">Chicken Buckets</div>
      <div class="category" onclick="redirectToCategory('Burgers & wraps')">Burgers & Wraps</div>
      <div class="category" onclick="redirectToCategory('Rice & Meals')">Rice & Meals</div>
      <div class="category" onclick="redirectToCategory('Sides & Snacks')">Sides & Snacks</div>
      <div class="category" onclick="redirectToCategory('Desserts')">Desserts</div>
      <!-- <div class="category" onclick="redirectToCategory('Drinks')">Drinks</div> -->
    </div>

      <script>
        function redirectToCategory(category) {
          const section = document.getElementById(category);
          if (section) {
            const yOffset = -80; // Adjust this value to match your sticky bar height
            const y = section.getBoundingClientRect().top + window.pageYOffset + yOffset;
            window.scrollTo({ top: y, behavior: 'smooth' });
          }
        }
      </script>

   <div class="section">
    <div class="section-title">
        <h2>Special Offer</h2>
    </div>
    <div class="special-offer-scroll" id="bestSellers">
        <?php
        // Fetch only special offers
        $specialOffers = $pdo->query("SELECT * FROM item_details WHERE Item_Status = 'Active' AND Item_Category = 'Special' ORDER BY Item_Id DESC")->fetchAll();
        
        if (empty($specialOffers)) {
            echo '<p style="text-align:center; padding:20px; color:#666;">No special offers available at the moment.</p>';
        } else {
            foreach ($specialOffers as $item) {
                echo '
                <div class="card full-width-card snap-card" data-id="special-'.$item['Item_Id'].'" data-price="'.$item['Item_Price'].'">
                    <div class="card-img-container">
                        <img src="'.$item['Item_Image'].'" alt="'.$item['Item_Name'].'" />
                    </div>
                    <h4 style="text-align:center">'.$item['Item_Name'].'</h4>
                    <div class="price" style="text-align:center">Rs. '.$item['Item_Price'].'</div>
                    <div class="quantity-controls">
                        <button class="quantity-btn minus">-</button>
                        <span class="quantity">1</span>
                        <button class="quantity-btn plus">+</button>
                    </div>
                </div>';
            }
        }
        ?>
    </div>
</div>
<div class="scroll-indicators" id="specialOfferIndicators"></div>
</div>

    <div class="section" id="Chicken Buckets">
      <div class="section-title">
        <h2>Chicken Buckets</h2>
      </div>
      <div class="scroll-container" id="buckets">
        <?php
        $buckets = $pdo->prepare("SELECT * FROM item_details WHERE Item_Category = 'buckets' AND Item_Status = 'Active' ORDER BY Item_Id DESC");
        $buckets->execute();
        foreach ($buckets->fetchAll() as $item) {
            echo '
            <div class="card" data-id="buckets-'.$item['Item_Id'].'" data-price="'.$item['Item_Price'].'">
                <div class="card-img-container">
                    <img src="'.$item['Item_Image'].'" alt="'.$item['Item_Name'].'" />
                </div>
                <h4>'.$item['Item_Name'].'</h4>
                <div class="price">Rs. '.$item['Item_Price'].'</div>
                <div class="quantity-controls">
                    <button class="quantity-btn minus">-</button>
                    <span class="quantity">1</span>
                    <button class="quantity-btn plus">+</button>
                </div>
            </div>';
        }
        ?>
      </div>
    </div>

    <div class="section" id="Burgers & wraps">
      <div class="section-title">
        <h2>Burgers & Wraps</h2>
      </div>
      <div class="scroll-container" id="burgers">
        <?php
        $burgers = $pdo->prepare("SELECT * FROM item_details WHERE Item_Category = 'burgers' AND Item_Status = 'Active' ORDER BY Item_Id DESC");
        $burgers->execute();
        foreach ($burgers->fetchAll() as $item) {
            echo '
            <div class="card" data-id="burgers-'.$item['Item_Id'].'" data-price="'.$item['Item_Price'].'">
                <div class="card-img-container">
                    <img src="'.$item['Item_Image'].'" alt="'.$item['Item_Name'].'" />
                </div>
                <h4>'.$item['Item_Name'].'</h4>
                <div class="price">Rs. '.$item['Item_Price'].'</div>
                <div class="quantity-controls">
                    <button class="quantity-btn minus">-</button>
                    <span class="quantity">1</span>
                    <button class="quantity-btn plus">+</button>
                </div>
            </div>';
        }
        ?>
      </div>
    </div>

    <div class="section" id="Rice & Meals">
      <div class="section-title">
        <h2>Rice & Meals</h2>
      </div>
      <div class="scroll-container" id="riceMeals">
        <?php
        $riceMeals = $pdo->prepare("SELECT * FROM item_details WHERE Item_Category = 'rice' AND Item_Status = 'Active' ORDER BY Item_Id DESC");
        $riceMeals->execute();
        foreach ($riceMeals->fetchAll() as $item) {
            echo '
            <div class="card" data-id="riceMeals-'.$item['Item_Id'].'" data-price="'.$item['Item_Price'].'">
                <div class="card-img-container">
                    <img src="'.$item['Item_Image'].'" alt="'.$item['Item_Name'].'" />
                </div>
                <h4>'.$item['Item_Name'].'</h4>
                <div class="price">Rs. '.$item['Item_Price'].'</div>
                <div class="quantity-controls">
                    <button class="quantity-btn minus">-</button>
                    <span class="quantity">1</span>
                    <button class="quantity-btn plus">+</button>
                </div>
            </div>';
        }
        ?>
      </div>
    </div>

    <div class="section" id="Sides & Snacks">
      <div class="section-title">
        <h2>Sides & Snacks</h2>
      </div>
      <div class="scroll-container" id="sides">
        <?php
        $sides = $pdo->prepare("SELECT * FROM item_details WHERE Item_Category = 'sides' AND Item_Status = 'Active' ORDER BY Item_Id DESC");
        $sides->execute();
        foreach ($sides->fetchAll() as $item) {
            echo '
            <div class="card" data-id="sides-'.$item['Item_Id'].'" data-price="'.$item['Item_Price'].'">
                <div class="card-img-container">
                    <img src="'.$item['Item_Image'].'" alt="'.$item['Item_Name'].'" />
                </div>
                <h4>'.$item['Item_Name'].'</h4>
                <div class="price">Rs. '.$item['Item_Price'].'</div>
                <div class="quantity-controls">
                    <button class="quantity-btn minus">-</button>
                    <span class="quantity">1</span>
                    <button class="quantity-btn plus">+</button>
                </div>
            </div>';
        }
        ?>
      </div>
    </div>

        <div class="section" id="Desserts">
    <div class="section-title">
        <h2>Desserts</h2>
    </div>
    <div class="scroll-container" id="desserts">
        <?php
        // ✅ 1. Define & execute the query first
        $desserts = $pdo->prepare("SELECT * FROM Item_Details WHERE Item_Category = 'Desserts' AND Item_Status = 'Active' ORDER BY Item_Id DESC");
        $desserts->execute(); // ✅ 2. Execute the query
        
        // ✅ 3. Now fetch and loop
        foreach ($desserts->fetchAll() as $item) {
            echo '
            <div class="card" data-id="desserts-'.$item['Item_Id'].'" data-price="'.$item['Item_Price'].'">
                <div class="card-img-container">
                    <img src="'.$item['Item_Image'].'" alt="'.$item['Item_Name'].'" />
                </div>
                <h4>'.$item['Item_Name'].'</h4>
                <div class="price">Rs. '.$item['Item_Price'].'</div>
                <div class="quantity-controls">
                    <button class="quantity-btn minus">-</button>
                    <span class="quantity">1</span>
                    <button class="quantity-btn plus">+</button>
                </div>
            </div>';
        }
        ?>
    </div>
</div>

    <div class="buy-btn-container">
      <button class="buy-btn" onclick="window.location.href='checkout.php'">
        <div class="item-count" id="itemCount">0</div>
        ORDER
      </button>
    </div>

      <footer class="site-footer">
      <div class="footer-container">
          <div class="footer-about">
          <h2>About Us</h2>
          <p>Delicious meals delivered fast. Inspired by KFC, made for your cravings. Taste that hits the spot every time!</p>
          </div>

          <div class="footer-social">
          <h2>Follow Us</h2>
          <div class="social-icons">
              <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
              <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
              <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
              <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
          </div>
          </div>

          <div class="footer-bottom">
          <p>&copy; 2025 Flash Bites. All rights reserved.</p>
          </div>
      </div>
      </footer>
<script>
    // Store selected items and total item count
    let selectedItems = [];
    let totalItems = 0;
    
    // Variables for special offer slider control
    let specialOfferIndex = 0;
    let specialOfferInterval;
    let pauseTimeout;

    // Update the item count badge
    function updateItemCount() {
        document.getElementById('itemCount').textContent = totalItems;
    }
    
    // Function to start the auto-scroll interval
    function startAutoScroll() {
        clearInterval(specialOfferInterval);
        specialOfferInterval = setInterval(autoScrollSpecialOffer, 2000);
    }

    // Function to pause auto-scrolling for 5 seconds
    function pauseAutoScroll() {
        // Clear any existing timeouts
        clearTimeout(pauseTimeout);
        
        // Stop the current auto-scroll interval
        clearInterval(specialOfferInterval);
        
        // Set a timeout to restart auto-scrolling after 5 seconds
        pauseTimeout = setTimeout(() => {
            startAutoScroll();
        }, 2000);
    }
    
    /**
    * Attach event listeners for card clicks and quantity buttons
    */
    function attachCardEvents() {
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('click', function (e) {
                if (e.target.classList.contains('quantity-btn')) return;

                const itemId = this.getAttribute('data-id');
                const itemName = this.querySelector('h4').textContent;
                const itemPrice = parseInt(this.getAttribute('data-price')) || 0;
                const itemImg = this.querySelector('img')?.src || '';
                const existingItemIndex = selectedItems.findIndex(item => item.id === itemId);

                // Only allow adding items on card click
                if (existingItemIndex < 0) {
                    selectedItems.push({ id: itemId, name: itemName, price: itemPrice, img: itemImg, quantity: 1 });
                    totalItems += 1;
                    this.classList.add('selected');
                    updateItemCount();
                    
                    // Store in sessionStorage for persistence
                    sessionStorage.setItem('cartItems', JSON.stringify(selectedItems));
                    sessionStorage.setItem('totalItems', totalItems.toString());
                    
                    // Update quantity badge
                    updateQuantityBadge(itemId, 1);
                }
                
                // Pause auto-scrolling for 5 seconds when interacting with special offer items
                if (card.closest('#bestSellers')) {
                    pauseAutoScroll();
                }
            });
        });

        // Quantity button logic
        document.querySelectorAll('.quantity-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const card = this.closest('.card');
                const itemId = card.getAttribute('data-id');
                const quantityElement = card.querySelector('.quantity');
                let quantity = parseInt(quantityElement.textContent);
                const itemIndex = selectedItems.findIndex(item => item.id === itemId);

                if (this.classList.contains('minus')) {
                    if (quantity > 1) {
                        quantity--;
                        totalItems--;
                        selectedItems[itemIndex].quantity = quantity;
                        quantityElement.textContent = quantity;
                        updateQuantityBadge(itemId, quantity);
                    } else {
                        totalItems -= selectedItems[itemIndex].quantity;
                        selectedItems.splice(itemIndex, 1);
                        card.classList.remove('selected');
                        updateQuantityBadge(itemId, 0);
                    }
                } else {
                    quantity++;
                    totalItems++;
                    selectedItems[itemIndex].quantity = quantity;
                    quantityElement.textContent = quantity;
                    updateQuantityBadge(itemId, quantity);
                }

                updateItemCount();
                
                // Update sessionStorage
                sessionStorage.setItem('cartItems', JSON.stringify(selectedItems));
                sessionStorage.setItem('totalItems', totalItems.toString());
                
                // Pause auto-scrolling for 5 seconds when interacting with special offer items
                if (card.closest('#bestSellers')) {
                    pauseAutoScroll();
                }
            });
        });
    }
    
    // Update quantity badge for a specific item
    function updateQuantityBadge(itemId, quantity) {
        const itemElement = document.querySelector(`.card[data-id="${itemId}"]`);
        if (itemElement) {
            const quantityBadge = itemElement.querySelector('.quantity-badge');
            if (quantityBadge) {
                if (quantity > 0) {
                    quantityBadge.textContent = quantity;
                    quantityBadge.style.display = 'block';
                } else {
                    quantityBadge.style.display = 'none';
                }
            }
        }
    }

    // Store table ID in sessionStorage as backup
    sessionStorage.setItem('tableId', '<?php echo $tableId; ?>');
    
    // Modify the buy button to pass table_id parameter
    document.querySelector('.buy-btn').addEventListener('click', function() {
        // Get selected items from sessionStorage
        const cartItems = JSON.parse(sessionStorage.getItem('cartItems') || '[]');
        if (cartItems.length === 0) {
            alert('Please select at least one item');
            return;
        }
        
        // Redirect to checkout with table_id parameter
        window.location.href = 'checkout.php?table_id=<?php echo $tableId; ?>';
    });

    // Initialize the special offer slider indicators
    function initSpecialOfferIndicators() {
        const container = document.getElementById('bestSellers');
        const cards = container.querySelectorAll('.card');
        const dotsContainer = document.getElementById('specialOfferIndicators');
        
        if (cards.length > 0 && dotsContainer) {
            dotsContainer.innerHTML = '';
            cards.forEach((_, i) => {
                const dot = document.createElement('div');
                dot.className = 'dot' + (i === 0 ? ' active' : '');
                dotsContainer.appendChild(dot);
            });
        }
        
        // Start auto-scrolling
        startAutoScroll();
    }

    // Update indicator dots to match visible slide
    function updateSpecialOfferIndicators(index) {
        const dots = document.querySelectorAll('#specialOfferIndicators .dot');
        dots.forEach(dot => dot.classList.remove('active'));
        if (dots[index]) dots[index].classList.add('active');
    }

    // Auto scroll to next special offer every 5 seconds
    function autoScrollSpecialOffer() {
        const container = document.getElementById('bestSellers');
        const cards = container.querySelectorAll('.card');
        if (cards.length === 0) return;

        specialOfferIndex = (specialOfferIndex + 1) % cards.length;
        const scrollX = specialOfferIndex * container.clientWidth;

        container.scrollTo({ left: scrollX, behavior: 'smooth' });
        updateSpecialOfferIndicators(specialOfferIndex);
    }

    // Initialize the UI by attaching events and restoring cart
    function initialize() {
        // Restore cart from sessionStorage if available
        const savedCart = sessionStorage.getItem('cartItems');
        const savedTotalItems = sessionStorage.getItem('totalItems');
        
        if (savedCart && savedTotalItems) {
            selectedItems = JSON.parse(savedCart);
            totalItems = parseInt(savedTotalItems);
            
            // Update UI to reflect saved cart
            selectedItems.forEach(item => {
                updateQuantityBadge(item.id, item.quantity);
                
                // Mark card as selected
                const card = document.querySelector(`.card[data-id="${item.id}"]`);
                if (card) {
                    card.classList.add('selected');
                    const quantityElement = card.querySelector('.quantity');
                    if (quantityElement) {
                        quantityElement.textContent = item.quantity;
                    }
                }
            });
            
            updateItemCount();
        } else {
            selectedItems = [];
            totalItems = 0;
            updateItemCount();
            
            // Ensure no cards are selected
            document.querySelectorAll('.card.selected').forEach(card => {
                card.classList.remove('selected');
                card.querySelector('.quantity').textContent = '1';
                const quantityBadge = card.querySelector('.quantity-badge');
                if (quantityBadge) {
                    quantityBadge.style.display = 'none';
                }
            });
        }

        attachCardEvents();
        initSpecialOfferIndicators();

        // Handle category tab click (highlight active)
        document.querySelectorAll('.category').forEach(category => {
            category.addEventListener('click', function () {
                document.querySelector('.category.active').classList.remove('active');
                this.classList.add('active');
            });
        });
    }

    // Add shadow to sticky category bar when scrolling past header
    window.addEventListener('scroll', function () {
        const header = document.querySelector('header');
        const categories = document.querySelector('.categories');
        categories.style.boxShadow = window.scrollY > header.offsetHeight ? '0 2px 10px rgba(0,0,0,0.1)' : 'none';
    });

    // Sync dot indicators with manual swipe in Special Offer slider
    document.getElementById('bestSellers').addEventListener('scroll', () => {
        const container = document.getElementById('bestSellers');
        const index = Math.round(container.scrollLeft / container.clientWidth);
        specialOfferIndex = index;
        updateSpecialOfferIndicators(index);
        
        // Pause auto-scrolling for 5 seconds when manually scrolling
        pauseAutoScroll();
    });

    // Smooth scroll for category navigation
    function redirectToCategory(category) {
        const section = document.getElementById(category);
        if (section) {
            const yOffset = -80; // Adjust this value to match your sticky bar height
            const y = section.getBoundingClientRect().top + window.pageYOffset + yOffset;
            window.scrollTo({ top: y, behavior: 'smooth' });
        }
    }

    // Pause auto-scroll when user interacts with slider
    document.getElementById('bestSellers').addEventListener('mouseenter', () => {
        clearInterval(specialOfferInterval);
    });

    // Resume auto-scroll when user leaves slider
    document.getElementById('bestSellers').addEventListener('mouseleave', () => {
        // Only restart if not currently in a pause period
        if (!pauseTimeout) {
            startAutoScroll();
        }
    });

    // Run all setup on page load
    document.addEventListener('DOMContentLoaded', initialize);

// Function to handle button position when scrolling
function handleButtonPosition() {
    const buyBtnContainer = document.querySelector('.buy-btn-container');
    const footer = document.querySelector('.site-footer');
    
    if (!footer || !buyBtnContainer) return;
    
    const footerRect = footer.getBoundingClientRect();
    const viewportHeight = window.innerHeight;
    
    // If footer is in view or about to be in view
    if (footerRect.top < viewportHeight) {
        // Calculate how much to move the button up
        const overlap = viewportHeight - footerRect.top;
        const moveUp = Math.max(0, overlap - 20); // 20px buffer
        
        // Move the button up
        buyBtnContainer.style.transform = `translateY(-${moveUp}px)`;
    } else {
        // Reset position if footer is not in view
        buyBtnContainer.style.transform = 'translateY(0)';
    }
}

// Add scroll event listener
window.addEventListener('scroll', handleButtonPosition);

// Also call on resize in case window size changes
window.addEventListener('resize', handleButtonPosition);

// Initial call to set correct position
handleButtonPosition();
</script>
  </body>
</html>
