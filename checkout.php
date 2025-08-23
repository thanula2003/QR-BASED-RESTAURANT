<?php
include 'db_connect.php';
session_start();

// Get table ID from session or URL
$tableId = isset($_SESSION['tableId']) ? $_SESSION['tableId'] : (isset($_GET['table_id']) ? $_GET['table_id'] : '');

// If no table ID found, show error
if (!$tableId) {
    die("Table not specified. Please scan the QR code again.");
}

// Process order when placed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    // Get order data from session or form
    $order_items = json_decode($_POST['Order_Items'], true);
    $payment_method = $_POST['payment_method'];
    $total_amount = $_POST['total_amount'];
    
    // Create order in database
    $stmt = $pdo->prepare("INSERT INTO orders (table_id, total_amount, payment_method) VALUES (?, ?, ?)");
    $stmt->execute([$table_id, $total_amount, $payment_method]);
    $order_id = $pdo->lastInsertId();
    
    // Add order items
    foreach ($order_items as $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, item_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
    }
    
    // Mark table as occupied
    $stmt = $pdo->prepare("UPDATE tables SET status = 'occupied' WHERE table_id = ?");
    $stmt->execute([$table_id]);
    
    // Clear cart
    unset($_SESSION['cartItems']);
    unset($_SESSION['totalItems']);
    
    // Show confirmation
    echo "<script>showOrderConfirmation();</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Checkout - Tasty Legs</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<style>
    :root {
      --kfc-red: #E4002B;
      --kfc-dark: #231F20;
      --kfc-light: #FFFFFF;
      --kfc-yellow: #FFC72C;
      --success-green: #4CAF50;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      -webkit-tap-highlight-color: transparent;
    }

    html, body {
      height: 100%;
      overflow: hidden;
    }


    .scroll-buttons {
      position: fixed;
      right: 20px;
      bottom: calc(100px + 40vh);
      display: flex;
      flex-direction: column;
      gap: 10px;
      z-index: 99;
      transition: bottom 0.3s ease;
    }

    .scroll-btn {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: var(--kfc-red);
      color: white;
      border: none;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 2px 10px rgba(0,0,0,0.2);
      transition: all 0.3s ease;
    }

    .scroll-btn:hover {
      background-color: #c10024;
      transform: scale(1.1);
    }

    .scroll-btn i {
      font-size: 18px;
    }

    /* Hide buttons when not needed */
    .scroll-up.hidden,
    .scroll-down.hidden {
      display: none;
    }

    @media (max-width: 768px) {
      .scroll-buttons {
        bottom: calc(80px + 40vh);
        right: 10px;
      }
      
      .scroll-btn {
        width: 36px;
        height: 36px;
      }
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: var(--kfc-light);
      color: var(--kfc-dark);
      /* padding-bottom: 180px; */
      /* overflow: hidden; */
    }
    
    header {
      padding: 10px;
      background-color: var(--kfc-red);
      color: white;
      position: relative;
      text-align: center;
      z-index: 99;
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

    .checkout-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
    }

    .checkout-title {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 0;
        color: var(--kfc-dark);
        position: relative;
    }

    .checkout-title::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: -8px;
        width: 50px;
        height: 3px;
        background-color: var(--kfc-red);
    }

    .add-more-btn {
      margin-left: auto;
        background: var(--kfc-red);
        color: white;
        border: 1px solid var(--kfc-red);
        padding: 8px 15px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }

    .add-more-btn:hover {
        background: #c10024;
        border-color: #c10024;
    }

    @media (max-width: 768px) {
        .checkout-header {
            /* flex-direction: column;
            align-items: flex-start; */
        }
        
        .add-more-btn {
            align-self: flex-start;
            margin-top: 0px;
        }
    }
    
    .checkout-items {
      padding: 15px;
    }
    
    .checkout-title {
      font-size: 22px;
      font-weight: 700;
      margin-bottom: 20px;
      color: var(--kfc-dark);
      position: relative;
    }
    
    .checkout-title::after {
      content: "";
      position: absolute;
      left: 0;
      bottom: -8px;
      width: 50px;
      height: 3px;
      background-color: var(--kfc-red);
    }
    
    .checkout-item {
      display: flex;
      align-items: center;
      gap: 15px;
      padding: 15px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.08);
      margin-bottom: 15px;
    }
    
    .checkout-item-img {
      width: 80px;
      height: 80px;
      border-radius: 8px;
      object-fit: cover;
    }
    
    .checkout-item-details {
      flex: 1;
    }

    .checkout-container {
      max-height: 70vh;
      overflow-y: auto;
      padding-bottom: 20px;
    }
    
    .checkout-item-name {
      font-size: 16px;
      font-weight: 600;
      margin-bottom: 5px;
    }
    
    .checkout-item-price {
      color: var(--kfc-red);
      font-weight: 700;
      font-size: 16px;
    }

    .checkout-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1px; /* Reduced from 20px */
        flex-wrap: wrap;
        gap: 15px;
        padding: 2px 0; /* Reduced vertical padding */
        position: sticky;
        top: 0;
        background: white;
        z-index: 10;
    }
    
    .quantity-controls {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-top: 8px;
    }
    
    .quantity-btn {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: var(--kfc-red);
      color: white;
      border: none;
      font-weight: bold;
      font-size: 16px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .quantity {
      font-weight: 600;
      min-width: 20px;
      text-align: center;
    }

    .place-order-btn.highlight {
      animation: pulse 1s ease;
    }

    @keyframes pulse {
      0% { box-shadow: 0 0 0 0 rgba(228, 0, 43, 0.4); }
      70% { box-shadow: 0 0 0 10px rgba(228, 0, 43, 0); }
      100% { box-shadow: 0 0 0 0 rgba(228, 0, 43, 0); }
    }
    
    .payment-section {
      display: flex;
      padding: 8px 8px;
      gap: 12px;
      max-height: calc(100vh - 180px);
      background: #fff;
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
      z-index: 100;
      height: auto;
      overflow-y: auto;
      border-radius: 8px;
      margin-top: 10px;
      transition: all 0.3s ease;
      scroll-behavior: smooth;
    }

    .checkout-column {
      flex: 1;
      min-width: 250px;
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .price-summary-column {
      flex: 0 0 35%;
      max-width: 350px;
      padding: 12px;
      background: #f8f8f8;
      border-radius: 10px;
      margin-right: 12px;
    }

    .price-summary-section {
      background: #f8f8f8;
      padding: 18px;
      border-radius: 10px;
      margin: 20px 0;
    }

    .payment-methods-column {
      flex: 1;
      padding: 12px;
    }

    .price-summary-card {
      background: #f9f9f9;
      padding: 10px;
      border-radius: 10px;
      border-left: 0px solid var(--kfc-red);
    }

    .payment-methods-card {
      background: #f9f9f9;
      padding: 18px;
      border-radius: 10px;
      border-left: 3px solid var(--kfc-red);
    }

    .section-title {
      margin: 0 0 12px 0;
      padding-bottom: 8px;
      border-bottom: 2px solid var(--kfc-red);
      color: var(--kfc-dark);
      font-size: 18px;
      font-weight: 600;
    }

    .summary-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 8px;
      font-size: 15px;
    }

    .summary-row.total-row {
      margin-top: 8px;
      padding-top: 8px;
      border-top: 1px dashed var(--kfc-red);
      font-weight: 700;
      font-size: 16px;
    }

    .payment-method {
      position: relative;
      z-index: 1;
      display: flex;
      align-items: center;
      padding: 12px 15px;
      background: white;
      border-radius: 8px;
      margin-bottom: 10px;
      cursor: pointer;
      transition: all 0.2s;
      border: 1px solid #eee;
    }

    .payment-method:hover {
      background: #f5f5f5;
    }

    .payment-method.active {
      background: rgba(228, 0, 43, 0.1);
      border: 1px solid var(--kfc-red);
    }

    .payment-method-icon {
      margin-right: 10px;
      font-size: 20px;
    }
    
    .payment-method-label {
      font-weight: 500;
    }

    .place-order-btn {
      width: 100%;
      padding: 10px;
      background: var(--kfc-red);
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      font-size: 16px;
      cursor: pointer;
      margin-top: 15px;
      transition: all 0.2s;
    }

    .place-order-btn:hover {
      background: #c10024;
      transform: translateY(-2px);
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .card-form {
      display: none;
      background: white;
      padding: 20px;
      border-radius: 8px;
      margin-top: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    #cardForm {
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      border-radius: 8px;
      overflow: hidden;
      transition: max-height 0.3s ease-out;
      position: relative;
      background: #fff;
      width: 100%;
      margin-top: 10px;
      padding: 15px;
    }

    .card-form.active {
      display: block;
    }
    
    .form-group {
      margin-bottom: 18px;
    }
    
    .form-label {
      display: block;
      margin-bottom: 5px;
      font-size: 14px;
      font-weight: 500;
    }
    
    .form-input {
      width: 100%;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-family: 'Inter', sans-serif;
    }

    .card-form input:focus {
      border-color: var(--kfc-red);
      outline: none;
      box-shadow: 0 0 0 2px rgba(228, 0, 43, 0.2);
    }
    
    .card-row {
      display: flex;
      gap: 15px;
    }
    
    .card-row .form-group {
      flex: 1;
    }
    
    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      backdrop-filter: blur(3px);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 1000;
      inset: 0; /* Covers top, right, bottom, left */
    }
    
    .modal-content {
      background: white;
      width: 90%;
      max-width: 400px;
      border-radius: 12px;
      overflow: hidden;
      animation: modalFadeIn 0.3s ease;
    }
    
    @keyframes modalFadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .modal-header {
      padding: 15px;
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
      padding: 15px;
      max-height: 70vh;
      overflow-y: auto;
    }
    
    .order-details {
      margin-bottom: 15px;
    }
    
    .order-item {
      display: flex;
      justify-content: space-between;
      margin-bottom: 0px;
      padding-bottom: 0px;
      border-bottom: 0px solid #f0f0f0;
    }

    .order-items-list {
      margin-bottom: 10px;
    }
    
    .order-item:last-child {
      border-bottom: none;
    }
    
    .order-total {
      margin-top: 15px;
      padding-top: 15px;
      border-top: 1px solid #eee;
      font-weight: 600;
    }
    
    .order-time {
      text-align: center;
      margin-top: 15px;
      color: var(--kfc-dark);
      font-size: 14px;
      color: #666;
      margin-top: 20px;
      text-align: center;
    }
    
    .code-input-modal .modal-body {
      text-align: center;
    }
    
    .code-instructions {
      margin-bottom: 15px;
      font-size: 15px;
    }
    
    .code-field {
      width: 100%;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 8px;
      margin-bottom: 15px;
      text-align: center;
      font-size: 16px;
    }
    
    .confirm-btn {
      width: 100%;
      padding: 12px;
      background: var(--kfc-red);
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
    }

    .confirmation-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.9);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1000;
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
      color: var(--success-green);
    }

    .animation-content {
      animation: fadeInOut 3s forwards;
    }

    .checkmark {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      display: block;
      stroke-width: 4;
      stroke: var(--success-green);
      stroke-miterlimit: 10;
      margin: 0 auto;
      box-shadow: inset 0px 0px 0px var(--success-green);
      animation: fill-checkmark 0.4s ease-in-out 0.4s forwards, scale-checkmark 0.3s ease-in-out 0.9s both;
    }

    .checkmark__circle {
      stroke-dasharray: 166;
      stroke-dashoffset: 166;
      stroke-width: 4;
      stroke-miterlimit: 10;
      stroke: var(--success-green);
      fill: none;
      animation: stroke-checkmark-circle 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
    }

    .checkmark__check {
      transform-origin: 50% 50%;
      stroke-dasharray: 48;
      stroke-dashoffset: 48;
      animation: stroke-checkmark 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
    }

    .confirmation-content {
      background: white;
      padding: 25px;
      border-radius: 12px;
      max-width: 600px;
      width: 90%;
      max-height: 80vh;
      overflow-y: auto;
      display: none;
      box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    }

    .confirmation-content h2 {
      text-align: center;
      color: var(--kfc-red);
      margin-bottom: 20px;
      font-size: 24px;
    }

    .ordered-items-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
      gap: 15px;
      margin: 20px 0;
    }

    .ordered-item {
      position: relative;
      border-radius: 8px;
      overflow: hidden;
    }

    .ordered-item-img {
      width: 100%;
      height: 100px;
      object-fit: cover;
      border-radius: 8px;
      border: 1px solid #eee;
      transition: transform 0.3s;
    }

    .ordered-item-img:hover {
      transform: scale(1.05);
    }

    .item-quantity {
      position: absolute;
      bottom: 8px;
      right: 8px;
      background: rgba(0, 0, 0, 0.7);
      color: white;
      padding: 3px 10px;
      border-radius: 12px;
      font-size: 12px;
      font-weight: 600;
    }

    .confirmation-sections {
      display: flex;
      gap: 20px;
      margin: 25px 0;
    }

    .confirmation-section {
      flex: 1;
      min-width: 250px;
      background: #f9f9f9;
      padding: 18px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .price-summary .summary-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
      font-size: 15px;
    }

    .price-summary .summary-row.total-row {
      margin-top: 15px;
      padding-top: 15px;
      border-top: 2px dashed var(--kfc-red);
      font-weight: 700;
      font-size: 16px;
    }

    .payment-details {
      margin-top: 10px;
      margin-bottom: 10px;
    }

    .payment-info {
      background: white;
      padding: 15px;
      border-radius: 8px;
      border-left: 3px solid var(--kfc-red);
    }

    .payment-info p {
      margin: 8px 0;
      font-size: 15px;
    }

    .payment-info p strong {
      color: var(--kfc-dark);
    }

    .close-confirmation {
      background: var(--kfc-red);
      color: white;
      border: none;
      padding: 14px;
      border-radius: 8px;
      cursor: pointer;
      width: 100%;
      font-size: 16px;
      font-weight: 600;
      transition: all 0.3s;
      margin-top: 15px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .close-confirmation:hover {
      background: #c10024;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    @keyframes stroke {
      100% {
        stroke-dashoffset: 0;
      }
    }

    @keyframes fadeInOut {
      0% {
        opacity: 0;
        transform: scale(0.8);
      }
      20% {
        opacity: 1;
        transform: scale(1.1);
      }
      40% {
        opacity: 1;
        transform: scale(1);
      }
      80% {
        opacity: 1;
        transform: scale(1);
      }
      100% {
        opacity: 0;
        transform: scale(0.9);
      }
    }

    @media (max-width: 768px) {
      .payment-section {
        flex-direction: column;
        padding: 0px;
        gap: 5px;
      }
      
      .checkout-column {
        width: 100%;
        max-width: 100%;
      }
      
      .price-summary-card,
      .payment-methods-card {
        padding: 9px;
      }
    }

    @media (max-width: 600px) {
      .confirmation-sections {
        flex-direction: column;
      }
      
      .confirmation-section {
        width: 100%;
      }
      
      .confirmation-content {
        padding: 20px;
      }
      
      .ordered-items-grid {
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
        gap: 10px;
      }
      
      .ordered-item-img {
        height: 80px;
      }
    }

    @keyframes stroke-checkmark-circle {
      0% {
        stroke-dashoffset: 166;
      }
      100% {
        stroke-dashoffset: 0;
      }
    }

    @keyframes stroke-checkmark {
      0% {
        stroke-dashoffset: 48;
      }
      100% {
        stroke-dashoffset: 0;
      }
    }

    @keyframes fill-checkmark {
      0% {
        box-shadow: inset 0px 0px 0px var(--success-green);
      }
      100% {
        box-shadow: inset 0px 0px 0px 50px rgba(76, 175, 80, 0);
      }
    }

    @keyframes scale-checkmark {
      0%, 100% {
        transform: none;
      }
      50% {
        transform: scale3d(1.1, 1.1, 1);
      }
    }

    #cardForm {
      display: none;
      position: relative;
      width: 100%;
      background: white;
      padding: 0 20px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      overflow: hidden;
      
      transition: max-height 0.3s ease-out;
      z-index: 10;
    }

  #orderItemsContainer {
    transition: margin-top 0.3s ease;
    overflow-y: auto;
    max-height: calc(100vh - 313px); /* Use calc for better control */
    scroll-behavior: smooth;
    scrollbar-width: none;
    -ms-overflow-style: none;
    margin-bottom: 10px; /* Reduced from 180px */
  }

  /* For Chrome, Safari, and Opera */
  #orderItemsContainer::-webkit-scrollbar {
    display: none;
  }

    .card-active #orderItemsContainer {
      margin-top: 0;
    }

    .quantity-btn:hover {
      background: #c10024;
    }

    .quantity-btn:active {
      transform: scale(0.95);
    }

    .quantity-btn:disabled {
      background: #ccc;
      cursor: not-allowed;
    }

    #cardPaymentModal .modal-content {
      max-width: 400px;
      width: 90%;
    }

    #cardPaymentModal .form-group {
      margin-bottom: 15px;
    }

    #cardPaymentModal .place-order-btn {
      margin-top: 20px;
    }

    #closeCardModal {
      cursor: pointer;
      font-size: 24px;
      color: #666;
    }

    .download-pdf-btn {
      background: var(--kfc-red);
      color: white;
      border: none;
      padding: 14px;
      border-radius: 8px;
      cursor: pointer;
      width: 100%;
      font-size: 16px;
      font-weight: 600;
      transition: all 0.3s;
      margin-top: 10px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .download-pdf-btn:hover {
      background: #c10024;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

</style>
</head>
<body>
  <div class="scroll-buttons">
    <button class="scroll-btn scroll-up" aria-label="Scroll up">
      <i class="fas fa-chevron-up"></i>
    </button>
    <button class="scroll-btn scroll-down" aria-label="Scroll down">
      <i class="fas fa-chevron-down"></i>
    </button>
  </div>
  
  <header>
      <div class="logo">TASTY LEGS</div>
  </header>

  <div class="checkout-items">
      <div class="checkout-header">
          <h2 class="checkout-title">Your Order</h2>
          <button id="addMoreItemsBtn" class="add-more-btn">
              <i class="fas fa-plus"></i> Add More
          </button>
      </div>
      <div id="orderItemsContainer">
          <!-- Order items will be dynamically added here -->
      </div>
  </div>

  <div class="payment-section">
    <!-- Price Summary Section -->
    <div class="price-summary-column">
      <div class="price-summary-card">
        <div class="summary-row">
          <span class="summary-label">Subtotal:</span>
          <span class="summary-value" id="subtotal">Rs. 0</span>
        </div>
        <div class="summary-row">
          <span class="summary-label">Tax (10%):</span>
          <span class="summary-value" id="tax">Rs. 0</span>
        </div>
        <div class="summary-row total-row">
          <span class="summary-label">Total:</span>
          <span class="summary-value" id="total">Rs. 0</span>
        </div>
      </div>
    </div>

    <!-- Payment Method Section -->
    <div class="payment-methods-column">
      <div>
        <div class="payment-method" id="cardPayment">
          <i class="fas fa-credit-card payment-method-icon"></i>
          <span class="payment-method-label">Credit/Debit Card</span>
        </div>
        
        <div class="modal-overlay" id="cardPaymentModal">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title">Enter Card Details</h3>
              <span class="modal-close" id="closeCardModal">&times;</span>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label class="form-label">Card Number</label>
                <input type="text" class="form-input" placeholder="1234 5678 9012 3456" maxlength="16">
              </div>
              <div class="form-group">
                <label class="form-label">Cardholder Name</label>
                <input type="text" class="form-input" placeholder="John Doe">
              </div>
              <div class="card-row">
                <div class="form-group">
                  <label class="form-label">Expiry Date</label>
                  <input type="text" class="form-input" placeholder="MM/YY" maxlength="5">
                </div>
                <div class="form-group">
                  <label class="form-label">CVV</label>
                  <input type="text" class="form-input" placeholder="123" maxlength="3">
                </div>
              </div>
              <button class="place-order-btn" id="confirmCardPayment">Place Order</button>
            </div>
          </div>
        </div>
    
        <div class="payment-method" id="cashPayment">
          <i class="fas fa-money-bill-wave payment-method-icon"></i>
          <span class="payment-method-label">Pay by Cash</span>
        </div>
      </div>
      
      <button class="place-order-btn" id="placeOrderBtn">Place Order</button>
    </div>
  </div>

  <!-- Code Input Modal (for cash payments) -->
  <div class="modal-overlay code-input-modal" id="codeInputModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Cash Payment Verification</h3>
        <span class="modal-close" id="closeCodeModal">&times;</span>
      </div>
      <div class="modal-body">
        <p class="code-instructions">Get the code from the restaurant agent and enter it below:</p>
        <input type="text" class="code-field" placeholder="Enter 6-digit code" id="cashPaymentCode">
        <button class="confirm-btn" id="confirmCodeBtn">Confirm Code</button>
      </div>
    </div>
  </div>

  <!-- Order Confirmation Modal -->
  <div class="modal-overlay" id="orderConfirmationModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Order Confirmed</h3>
        <span class="modal-close" id="closeModal">&times;</span>
      </div>
      <div class="modal-body" id="modalBody">
        <div class="order-placed-animation">
          <div class="animation-content">
            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
              <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
              <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
            </svg>
            <h2>Order Confirmed!</h2>
            <p>Your order has been placed successfully</p>
          </div>
        </div>

        <div class="confirmation-content" id="confirmationContent">
          <h2>Order Details</h2>
          
          <div class="ordered-items-grid" id="orderedItemsGrid">
            <!-- Items will be added dynamically -->
          </div>
          
          <div class="confirmation-sections">
            <div class="confirmation-section price-summary">
              <h3 class="section-title">Price Summary</h3>
              <div class="summary-row">
                <span class="summary-label">Subtotal:</span>
                <span class="summary-value" id="orderSubtotal">$0.00</span>
              </div>
              <div class="summary-row">
                <span class="summary-label">Tax:</span>
                <span class="summary-value" id="orderTax">$0.00</span>
              </div>
              <div class="summary-row">
                <span class="summary-label">Delivery Fee:</span>
                <span class="summary-value" id="orderDelivery">$0.00</span>
              </div>
              <div class="summary-row total-row">
                <span class="summary-label">Total:</span>
                <span class="summary-value" id="orderTotal">$0.00</span>
              </div>
            </div>
          </div>
          
          <button class="close-confirmation" id="closeConfirmation">Close</button>
        </div>
      </div>
    </div>
  </div>
<script>
  // Get table ID from PHP
  let tableId = "<?php echo $tableId; ?>";

  // If no table ID found, try to get from sessionStorage
  if (!tableId) {
      tableId = sessionStorage.getItem('tableId');
  }

  // If still no table ID, show error
  if (!tableId) {
      document.body.innerHTML = `
          <div style="text-align: center; padding: 50px;">
              <h2>Table not specified</h2>
              <p>Please scan the QR code again to start your order.</p>
              <a href="index.php">Return to Home</a>
          </div>
      `;
  }

  // Payment state
  let paymentMethod = null;
  let subtotal = 0;
  let tax = 0;
  let total = 0;

  // DOM elements
  const orderItemsContainer = document.getElementById('orderItemsContainer');
  const subtotalElement = document.getElementById('subtotal');
  const taxElement = document.getElementById('tax');
  const totalElement = document.getElementById('total');
  const cardPayment = document.getElementById('cardPayment');
  const cashPayment = document.getElementById('cashPayment');
  const placeOrderBtn = document.getElementById('placeOrderBtn');
  const codeInputModal = document.getElementById('codeInputModal');
  const closeCodeModal = document.getElementById('closeCodeModal');
  const confirmCodeBtn = document.getElementById('confirmCodeBtn');
  const cashPaymentCode = document.getElementById('cashPaymentCode');
  const cardPaymentModal = document.getElementById('cardPaymentModal');
  const closeCardModal = document.getElementById('closeCardModal');
  const confirmCardPayment = document.getElementById('confirmCardPayment');

  // Initialize Place Order button as hidden
  placeOrderBtn.style.display = 'none';

  // Get order items from sessionStorage
  let orderItems = [];
  const savedCart = sessionStorage.getItem('cartItems');
  if (savedCart) {
    orderItems = JSON.parse(savedCart);
  }

  // Scroll functionality variables
  let scrollUpBtn, scrollDownBtn;

  // Initialize scroll buttons after order items are rendered
  function initScrollButtons() {
    scrollUpBtn = document.querySelector('.scroll-up');
    scrollDownBtn = document.querySelector('.scroll-down');
    const checkoutItems = document.querySelectorAll('.checkout-item');
    
    // If no scroll buttons or items, return early
    if (!scrollUpBtn || !scrollDownBtn || checkoutItems.length === 0) {
      if (scrollUpBtn) scrollUpBtn.classList.add('hidden');
      if (scrollDownBtn) scrollDownBtn.classList.add('hidden');
      return;
    }
    
    // Function to update button visibility based on scroll position
    function updateScrollButtons() {
      // Show/hide up button based on scroll position
      if (orderItemsContainer.scrollTop === 0) {
        scrollUpBtn.classList.add('hidden');
      } else {
        scrollUpBtn.classList.remove('hidden');
      }
      
      // Show/hide down button based on scroll position
      const atBottom = Math.abs(orderItemsContainer.scrollHeight - orderItemsContainer.scrollTop - orderItemsContainer.clientHeight) < 5;
      
      if (atBottom) {
        scrollDownBtn.classList.add('hidden');
      } else {
        scrollDownBtn.classList.remove('hidden');
      }
    }
    
    // Initially update button visibility
    updateScrollButtons();
    
    // Scroll down to next two items
    scrollDownBtn.addEventListener('click', function() {
      const checkoutItems = document.querySelectorAll('.checkout-item');
      if (checkoutItems.length === 0) return;
      
      // Get the current scroll position
      const currentScrollPos = orderItemsContainer.scrollTop;
      
      // Find which item is currently at the top of the visible area
      let currentItemIndex = -1;
      for (let i = 0; i < checkoutItems.length; i++) {
        const itemPos = checkoutItems[i].offsetTop - orderItemsContainer.offsetTop;
        if (itemPos >= currentScrollPos) {
          currentItemIndex = i;
          break;
        }
      }
      
      // If we couldn't find the current item, start from the beginning
      if (currentItemIndex === -1) currentItemIndex = 0;
      
      // Calculate the index of the item to scroll to (current + 2)
      const targetIndex = Math.min(currentItemIndex + 2, checkoutItems.length - 1);
      
      // Scroll to the target item
      const targetItem = checkoutItems[targetIndex];
      const targetPosition = targetItem.offsetTop - orderItemsContainer.offsetTop;
      
      orderItemsContainer.scrollTo({
        top: targetPosition,
        behavior: 'smooth'
      });
    });
    
    // Scroll up to top of the page immediately
    scrollUpBtn.addEventListener('click', function() {
      orderItemsContainer.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
    
    // Update button visibility when scrolling manually
    orderItemsContainer.addEventListener('scroll', updateScrollButtons);
    
    // Also check scroll position on resize in case container size changes
    window.addEventListener('resize', updateScrollButtons);
  }

  // On page load, check if there are saved cart items
  document.addEventListener('DOMContentLoaded', function() {
    // Get table ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const tableId = urlParams.get('table_id');
    
    // Restore cart items from sessionStorage
    const savedCart = sessionStorage.getItem('cartItems');
    if (savedCart) {
        const cartItems = JSON.parse(savedCart);
        
        // Update quantity badges on menu items
        cartItems.forEach(item => {
            const itemElement = document.querySelector(`.menu-item[data-id="${item.id}"]`);
            if (itemElement) {
                const quantityBadge = itemElement.querySelector('.quantity-badge');
                if (quantityBadge) {
                    quantityBadge.textContent = item.quantity;
                    quantityBadge.style.display = 'block';
                }
            }
        });
        
        // Update cart total badge
        const totalItems = cartItems.reduce((total, item) => total + item.quantity, 0);
        const cartBadge = document.querySelector('.cart-badge');
        if (cartBadge) {
            cartBadge.textContent = totalItems;
            cartBadge.style.display = totalItems > 0 ? 'block' : 'none';
        }
    }
    
    // Initialize the page with order items
    renderOrderItems();
    calculateTotals();
  });  

  /**
   * Calculate order totals and update the UI
   */
  function calculateTotals() {
    subtotal = orderItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    tax = subtotal * 0.1; // 10% tax
    total = subtotal + tax;
    
    subtotalElement.textContent = `Rs. ${subtotal.toLocaleString()}`;
    taxElement.textContent = `Rs. ${tax.toLocaleString()}`;
    totalElement.textContent = `Rs. ${total.toLocaleString()}`;
  }

  /**
   * Render order items in the checkout list
   */
  function renderOrderItems() {
    orderItemsContainer.innerHTML = '';
    
    if (orderItems.length === 0) {
      orderItemsContainer.innerHTML = '<p class="empty-cart">Your cart is empty</p>';
      // Hide scroll buttons when cart is empty
      document.querySelectorAll('.scroll-btn').forEach(btn => btn.classList.add('hidden'));
      return;
    }
    
    orderItems.forEach(item => {
      const itemElement = document.createElement('div');
      itemElement.className = 'checkout-item';
      itemElement.innerHTML = `
        <img src="${item.img}" alt="${item.name}" class="checkout-item-img">
        <div class="checkout-item-details">
          <div class="checkout-item-name">${item.name}</div>
          <div class="checkout-item-price">Rs. ${item.price.toLocaleString()}</div>
          <div class="quantity-controls">
            <button class="quantity-btn minus" data-id="${item.id}">-</button>
            <span class="quantity">${item.quantity}</span>
            <button class="quantity-btn plus" data-id="${item.id}">+</button>
          </div>
        </div>
      `;
      
      orderItemsContainer.appendChild(itemElement);
    });
    
    // Add event listeners to quantity buttons
    document.querySelectorAll('.quantity-btn.minus').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const itemId = btn.getAttribute('data-id');
        updateItemQuantity(itemId, -1);
      });
    });
    
    document.querySelectorAll('.quantity-btn.plus').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const itemId = btn.getAttribute('data-id');
        updateItemQuantity(itemId, 1);
      });
    });
    
    // Initialize scroll buttons after items are rendered
    setTimeout(initScrollButtons, 100);
  }

  /**
   * Update item quantity in the order
   */
  function updateItemQuantity(itemId, change) {
    const itemIndex = orderItems.findIndex(item => item.id === itemId);
    
    if (itemIndex !== -1) {
      orderItems[itemIndex].quantity += change;
      
      if (orderItems[itemIndex].quantity <= 0) {
        orderItems.splice(itemIndex, 1);
      }
      
      // Update sessionStorage
      sessionStorage.setItem('cartItems', JSON.stringify(orderItems));
      
      renderOrderItems();
      calculateTotals();
    }
  }

  /**
   * Validate card payment details
   */
  function validateCardPayment() {
    const inputs = document.querySelectorAll('#cardPaymentModal .form-input');
    let isValid = true;
    
    inputs.forEach(input => {
      if (!input.value.trim()) {
        input.style.borderColor = 'red';
        isValid = false;
      } else {
        input.style.borderColor = '#ddd';
      }
    });
    
    return isValid;
  }

  /**
   * Submit order to server (for form submission)
   */
  async function submitOrder(paymentMethod) {
    try {
      const formData = new FormData();
      formData.append('table_id', tableId);
      formData.append('order_items', JSON.stringify(orderItems));
      formData.append('payment_method', paymentMethod);
      formData.append('total_amount', total);
      formData.append('place_order', true);
      
      const response = await fetch('process_order.php', {
        method: 'POST',
        body: formData
      });
      
      const result = await response.json();
      
      if (result.success) {
        // Clear cart and show confirmation
        sessionStorage.removeItem('cartItems');
        sessionStorage.removeItem('totalItems');
        showOrderConfirmation(paymentMethod === 'cash');
        return true;
      } else {
        alert('Error placing order: ' + result.message);
        return false;
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Error placing order. Please try again.');
      return false;
    }
  }

  /**
   * Place order using JSON API
   */
  async function placeOrder() {
    const orderData = {
      items: orderItems,
      tableId: tableId,
      total: total,
      paymentMethod: paymentMethod
    };
    
    try {
      const response = await fetch('process_order.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(orderData)
      });
      
      const result = await response.json();
      
      if (result.success) {
        // Clear cart and show success
        sessionStorage.removeItem('cartItems');
        sessionStorage.removeItem('totalItems');
        showOrderConfirmation(result.orderId);
        return true;
      } else {
        alert('Failed to place order: ' + result.error);
        return false;
      }
    } catch (error) {
      alert('Error placing order: ' + error.message);
      return false;
    }
  }

  /**
   * Show order confirmation
   */
  function showOrderConfirmation(isCashPayment = false) {
    const confirmationOverlay = document.createElement('div');
    confirmationOverlay.className = 'confirmation-overlay';
    confirmationOverlay.id = 'confirmationOverlay';
    
    // Format table number with leading zero if needed
    const formattedTableId = tableId.toString().padStart(2, '0');
    
    confirmationOverlay.innerHTML = `
      <div class="order-placed-animation" id="orderPlacedAnimation">
        <div class="animation-content">
          <svg class="checkmark" viewBox="0 0 52 52">
            <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
            <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
          </svg>
          <h2>Order Placed!</h2>
        </div>
      </div>
      
      <div class="confirmation-content" id="confirmationContent">
        <h2>Order Confirmation</h2>
        <div class="table-info" style="text-align: center; margin-bottom: 15px; font-size: 16px; color: var(--kfc-dark);">
          <strong>Ordered by - Table Number ${formattedTableId}</strong>
        </div>
        <div class="order-items-list">
          <h3 class="section-title">Order Items</h3>
          ${orderItems.map(item => `
            <div class="order-item">
              <span>${item.name} (x${item.quantity})</span>
              <span>Rs. ${(item.price * item.quantity).toLocaleString()}</span>
            </div>
          `).join('')}
        </div>
        <div class="price-summary-section">
          <h3 class="section-title">Price Summary</h3>
          <div class="summary-row">
            <span>Subtotal:</span>
            <span>Rs. ${subtotal.toLocaleString()}</span>
          </div>
          <div class="summary-row">
            <span>Tax (10%):</span>
            <span>Rs. ${tax.toLocaleString()}</span>
          </div>
          <div class="summary-row total-row">
            <span>Total:</span>
            <span>Rs. ${total.toLocaleString()}</span>
          </div>
        </div>
        <div class="order-time">
          ${new Date().toLocaleString()}
        </div>
        <button class="download-pdf-btn" id="downloadPdfBtn">
          <i class="fas fa-download"></i> Download Receipt
        </button>
        <button class="close-confirmation">Continue Shopping</button>
      </div>
    `;
    
    document.body.appendChild(confirmationOverlay);
    
    setTimeout(() => {
      confirmationOverlay.classList.add('active');
    }, 10);
    
    setTimeout(() => {
      const animation = confirmationOverlay.querySelector('#orderPlacedAnimation');
      const content = confirmationOverlay.querySelector('#confirmationContent');
      animation.style.display = 'none';
      content.style.display = 'block';
    }, 3000);
    
    confirmationOverlay.querySelector('.close-confirmation').addEventListener('click', () => {
      window.location.href = 'index.php?table_id=' + tableId; // Redirect with table_id
      confirmationOverlay.classList.remove('active');
      setTimeout(() => {
        document.body.removeChild(confirmationOverlay);
      }, 300);
    });
  }
  

  // Event Listeners
  cardPayment.addEventListener('click', function() {
    paymentMethod = 'card';
    cardPayment.classList.add('active');
    cashPayment.classList.remove('active');
    placeOrderBtn.style.display = 'none';
    cardPaymentModal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  });

  cashPayment.addEventListener('click', function() {
    if (cashPayment.classList.contains('active')) {
      paymentMethod = null;
      cashPayment.classList.remove('active');
      placeOrderBtn.style.display = 'none';
    } else {
      paymentMethod = 'cash';
      cashPayment.classList.add('active');
      cardPayment.classList.remove('active');
      placeOrderBtn.style.display = 'block';
      cardPaymentModal.style.display = 'none';
      document.body.style.overflow = '';
      
      // Scroll to Place Order button
      setTimeout(() => {
        placeOrderBtn.scrollIntoView({ 
          behavior: 'smooth',
          block: 'nearest'
        });
      }, 100);
      
      // Highlight effect
      placeOrderBtn.classList.add('highlight');
      setTimeout(() => {
        placeOrderBtn.classList.remove('highlight');
      }, 1000);
    }
  });

  placeOrderBtn.addEventListener('click', function() {
    if (orderItems.length === 0) {
      alert('Please add items to your order');
      return;
    }
    
    if (!paymentMethod) {
      alert('Please select a payment method');
      return;
    }
    
    if (paymentMethod === 'cash') {
      codeInputModal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    } else if (paymentMethod === 'card') {
      // For card payment, place order directly
      placeOrder();
    }
  });

  confirmCardPayment.addEventListener('click', function() {
    // For card payment, validate and place order
    if (validateCardPayment()) {
      placeOrder();
    }
  });

  confirmCodeBtn.addEventListener('click', function() {
    if (!cashPaymentCode.value) {
      alert('Please enter the verification code');
      return;
    }
    // For cash payment with code verification, place order
    placeOrder();
  });

  // Close modal handlers
  closeCardModal.addEventListener('click', function() {
    cardPaymentModal.style.display = 'none';
    document.body.style.overflow = '';
  });

  closeCodeModal.addEventListener('click', function() {
    codeInputModal.style.display = 'none';
    document.body.style.overflow = '';
  });

  document.getElementById('closeModal').addEventListener('click', function() {
    document.getElementById('orderConfirmationModal').style.display = 'none';
    // Redirect to home page after order confirmation
    window.location.href = 'index.php?table_id=' + tableId;
  });

  // Close modals when clicking outside
  window.addEventListener('click', function(e) {
    if (e.target === codeInputModal) {
      codeInputModal.style.display = 'none';
      document.body.style.overflow = '';
    }
    if (e.target === cardPaymentModal) {
      cardPaymentModal.style.display = 'none';
      document.body.style.overflow = '';
    }
    if (e.target === document.getElementById('orderConfirmationModal')) {
      document.getElementById('orderConfirmationModal').style.display = 'none';
      // Redirect to home page after order confirmation
      window.location.href = 'index.php?table_id=' + tableId;
    }
  });

  function adjustBodyPadding() {
    const paymentSectionHeight = document.querySelector('.payment-section').offsetHeight;
    document.body.style.paddingBottom = `${paymentSectionHeight + 20}px`; // +20px buffer
  }

  // Call on load and resize
  window.addEventListener('load', adjustBodyPadding);
  window.addEventListener('resize', adjustBodyPadding);

  // Add More Items button functionality
  document.getElementById('addMoreItemsBtn').addEventListener('click', function() {
      // Save current cart items to sessionStorage
      sessionStorage.setItem('cartItems', JSON.stringify(orderItems));
      
      // Calculate total items for badge
      const totalItems = orderItems.reduce((total, item) => total + item.quantity, 0);
      sessionStorage.setItem('totalItems', totalItems);
      
      // Redirect to home page with table ID
      window.location.href = 'index.php?table_id=' + tableId;
  });

  /**
   * Generate and download PDF receipt
   */
    function generatePDF() {
        // Create a temporary div to hold the content for PDF
        const pdfContent = document.createElement('div');
        pdfContent.style.width = '100%';
        pdfContent.style.maxWidth = '400px';
        pdfContent.style.padding = '20px';
        pdfContent.style.backgroundColor = 'white';
        pdfContent.style.fontFamily = "'Inter', sans-serif";
        pdfContent.style.border = '3px solid #FFC72C'; // KFC yellow border
        pdfContent.style.borderRadius = '8px'; // Rounded corners
        pdfContent.style.margin = '0 auto'; // Center the content
        
        // Format table number with leading zero
        const formattedTableId = tableId.toString().padStart(2, '0');
        
        // Get current date and time
        const now = new Date();
        const orderDateTime = now.toLocaleString();
        
        // Build PDF content with better mobile formatting
        pdfContent.innerHTML = `
            <div style="text-align: center; margin-bottom: 20px;">
                <h1 style="color: #E4002B; margin: 0 0 10px 0; font-size: 24px; font-weight: bold;">TASTY LEGS</h1>
                <h2 style="color: #231F20; margin: 0 0 15px 0; font-size: 18px; font-weight: 600;">Order Confirmation</h2>
                <p style="font-size: 16px; color: #231F20; margin: 0; font-weight: 600;">
                    <strong>Ordered by - Table Number ${formattedTableId}</strong>
                </p>
                <p style="font-size: 14px; color: #666; margin: 5px 0 0 0;">
                    Order Date: ${orderDateTime}
                </p>
            </div>
            
            <div style="margin-bottom: 20px;">
                <h3 style="color: #E4002B; border-bottom: 2px solid #E4002B; padding-bottom: 5px; margin-bottom: 10px; font-size: 16px;">Order Items</h3>
                ${orderItems.map(item => `
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px;">
                        <span style="flex: 2; padding-right: 10px;">${item.name} (x${item.quantity})</span>
                        <span style="flex: 1; text-align: right; color: #E4002B; font-weight: 600;">Rs. ${(item.price * item.quantity).toLocaleString()}</span>
                    </div>
                `).join('')}
            </div>
            
            <div style="margin-bottom: 20px;">
                <h3 style="color: #E4002B; border-bottom: 2px solid #E4002B; padding-bottom: 5px; margin-bottom: 10px; font-size: 16px;">Price Summary</h3>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px;">
                    <span>Subtotal:</span>
                    <span>Rs. ${subtotal.toLocaleString()}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px;">
                    <span>Tax (10%):</span>
                    <span>Rs. ${tax.toLocaleString()}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 10px; padding-top: 10px; border-top: 2px dashed #E4002B; font-weight: bold; font-size: 16px;">
                    <span>Total:</span>
                    <span style="color: #E4002B;">Rs. ${total.toLocaleString()}</span>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 20px; color: #666; font-size: 12px; border-top: 1px solid #eee; padding-top: 15px;">
                <p style="margin: 0 0 5px 0;">Thank you for dining with us!</p>
                <p style="margin: 0; font-weight: 600;">TASTY LEGS Restaurant</p>
            </div>
        `;
        
        // Add to document temporarily
        document.body.appendChild(pdfContent);
        
        // Use html2canvas to capture the content
        html2canvas(pdfContent, {
            scale: 2, // Higher quality
            useCORS: true,
            logging: false,
            backgroundColor: '#ffffff',
            width: pdfContent.offsetWidth,
            height: pdfContent.offsetHeight,
            windowWidth: pdfContent.scrollWidth,
            windowHeight: pdfContent.scrollHeight
        }).then(canvas => {
            // Remove temporary element
            document.body.removeChild(pdfContent);
            
            // Create PDF with better mobile compatibility
            const pdf = new window.jspdf.jsPDF({
                orientation: 'portrait',
                unit: 'mm',
                format: 'a4' // Use A4 for better mobile compatibility
            });
            
            // Calculate dimensions
            const pageWidth = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();
            
            // Calculate image dimensions to fit page
            const imgWidth = pageWidth - 20; // Leave 10mm margin on each side
            const imgHeight = (canvas.height * imgWidth) / canvas.width;
            
            // Check if content needs multiple pages
            if (imgHeight > pageHeight) {
                // Content is too long, split into multiple pages
                let remainingHeight = imgHeight;
                let position = 0;
                
                while (remainingHeight > 0) {
                    // Add new page if not the first page
                    if (position > 0) {
                        pdf.addPage();
                    }
                    
                    // Calculate how much to show on this page
                    const pageImgHeight = Math.min(remainingHeight, pageHeight - 20);
                    
                    // Create a temporary canvas for this page
                    const tempCanvas = document.createElement('canvas');
                    const tempCtx = tempCanvas.getContext('2d');
                    tempCanvas.width = canvas.width;
                    tempCanvas.height = (pageImgHeight * canvas.width) / imgWidth;
                    
                    // Draw the portion of the image for this page
                    tempCtx.drawImage(
                        canvas,
                        0, position * (canvas.height / imgHeight),
                        canvas.width, tempCanvas.height,
                        0, 0,
                        canvas.width, tempCanvas.height
                    );
                    
                    // Add to PDF
                    const tempImgData = tempCanvas.toDataURL('image/png');
                    pdf.addImage(tempImgData, 'PNG', 10, 10, imgWidth, pageImgHeight);
                    
                    // Update position and remaining height
                    position += pageImgHeight;
                    remainingHeight -= pageImgHeight;
                }
            } else {
                // Content fits on one page, center it vertically
                const yPosition = (pageHeight - imgHeight) / 2;
                const imgData = canvas.toDataURL('image/png');
                pdf.addImage(imgData, 'PNG', 10, yPosition, imgWidth, imgHeight);
            }
            
            // Download the PDF
            const fileName = `TastyLegs_Receipt_Table${formattedTableId}_${now.getTime()}.pdf`;
            pdf.save(fileName);
            
        }).catch(error => {
            console.error('Error generating PDF:', error);
            alert('Error generating PDF. Please try again.');
        });
    }

  document.addEventListener('click', function(e) {
      if (e.target && (e.target.id === 'downloadPdfBtn' || e.target.closest('#downloadPdfBtn'))) {
          generatePDF();
      }
  });

  document.getElementById('downloadPdfBtnModal').addEventListener('click', function() {
      generatePDF();
  });
</script>
</body>
</html>
