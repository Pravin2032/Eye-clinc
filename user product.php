<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EyeStyle - Premium Eyewear</title>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --secondary: #f59e0b;
            --light: #f9fafb;
            --dark: #1f2937;
            --gray: #6b7280;
            --gray-light: #f3f4f6;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f9fafb;
            color: var(--dark);
        }
        
        .nav {
            background-color: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary);
        }
        
        .cart-btn {
            background: transparent;
            border: none;
            font-size: 1.3rem;
            cursor: pointer;
            position: relative;
        }
        
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--secondary);
            color: white;
            font-size: 0.7rem;
            font-weight: bold;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .search-bar {
            display: flex;
            max-width: 500px;
            width: 100%;
        }
        
        .search-input {
            flex: 1;
            padding: 0.5rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.25rem 0 0 0.25rem;
            font-size: 1rem;
        }
        
        .search-btn {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0 0.25rem 0.25rem 0;
            cursor: pointer;
        }
        
        .search-btn:hover {
            background-color: var(--primary-dark);
        }
        
        .categories {
            background-color: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .category-title {
            margin-bottom: 1rem;
            font-size: 1.25rem;
            color: var(--dark);
        }
        
        .category-container {
            display: flex;
            overflow-x: auto;
            gap: 1rem;
            padding-bottom: 0.5rem;
        }
        
        .category-btn {
            background-color: var(--gray-light);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.25rem;
            font-size: 0.9rem;
            cursor: pointer;
            font-weight: 500;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }
        
        .category-btn:hover, .category-btn.active {
            background-color: var(--primary);
            color: white;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }
        
        .product-card {
            background-color: white;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .product-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .product-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--secondary);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: bold;
        }
        
        .product-info {
            padding: 1rem;
        }
        
        .product-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .product-price {
            color: var(--primary);
            font-weight: bold;
            font-size: 1.2rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .price-old {
            text-decoration: line-through;
            color: var(--gray);
            font-size: 0.9rem;
            font-weight: normal;
        }
        
        .product-rating {
            color: var(--secondary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .rating-count {
            color: var(--gray);
            font-size: 0.9rem;
        }
        
        .add-to-cart {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 0.75rem 1rem;
            border-radius: 0.25rem;
            font-size: 0.9rem;
            cursor: pointer;
            width: 100%;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        
        .add-to-cart:hover {
            background-color: var(--primary-dark);
        }
        
        .checkout, .bill {
            position: fixed;
            top: 0;
            right: -100%;
            width: 100%;
            max-width: 600px;
            height: 100vh;
            background-color: white;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            overflow-y: auto;
            transition: right 0.3s ease;
            z-index: 200;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.25rem;
            font-size: 1rem;
        }
        
        .btn {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.25rem;
            font-size: 1rem;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        
        .btn:hover {
            background-color: var(--primary-dark);
        }
        
        .payment-methods {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .payment-method {
            border: 1px solid #e5e7eb;
            padding: 1rem;
            border-radius: 0.25rem;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s;
        }
        
        .payment-method:hover {
            border-color: var(--primary);
        }
        
        .payment-method.selected {
            border-color: var(--primary);
            background-color: rgba(79, 70, 229, 0.1);
        }
        
        .payment-details {
            display: none;
            margin-bottom: 1.5rem;
        }
        
        .bill-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }
        
        .bill-table th, .bill-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .bill-table th {
            font-weight: 600;
            color: var(--dark);
            background-color: var(--gray-light);
        }
        
        .cart-item {
            display: grid;
            grid-template-columns: 80px 1fr auto;
            gap: 1rem;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .cart-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 0.25rem;
        }
        
        .cart-item-info h3 {
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }
        
        .cart-item-price {
            color: var(--primary);
            font-weight: bold;
        }
        
        .cart-quantity {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .quantity-btn {
            background-color: var(--gray-light);
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        .cart-summary {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .summary-row.total {
            font-weight: bold;
            font-size: 1.1rem;
            color: var(--primary);
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }
        
        .cart-empty {
            text-align: center;
            padding: 2rem;
            color: var(--gray);
        }
        
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 100;
            display: none;
        }
        
        @media (max-width: 768px) {
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            }
        }
        .close-btn {
        position: absolute;
        top: -4px;
        right: -45px;
        background-color: transparent;
        border: none;
        font-size: 48px;
        font-weight: bold;
        color: #000;
        cursor: pointer;
        transition: color 0.2s ease;
        
    }

    .close-btn:hover {
        color: red;
    }
    </style>
</head>
<body>
    <div class="overlay" id="overlay"></div>
    
    <nav class="nav">
        <div class="nav-content">
            <div class="logo">
                VisionCare Clinic Eyewear</div>
            <div style="position: relative; padding: 20px; border: 1px solid #ccc;">
    <button class="close-btn" onclick="redirectToDashboard()">×</button>
    <!-- Content goes here -->

            <button class="cart-btn" onclick="toggleCart()">
                🛒
                <span class="cart-count" id="cartCount">0</span>
            </button>
        </div>
    </nav>

    <div class="container" id="mainContent">
        <div class="header-content">
            <h1>Premium Eyewear Collection</h1>
            <div class="search-bar">
                <input type="text" class="search-input" placeholder="Search for products..." id="searchInput">
                <button class="search-btn" onclick="searchProducts()">🔍</button>
            </div>
        </div>

        <div class="categories">
            <h2 class="category-title">Categories</h2>
            <div class="category-container">
                <button class="category-btn active" data-category="all">All Products</button>
                <button class="category-btn" data-category="eyeglasses">Eyeglasses</button>
                <button class="category-btn" data-category="sunglasses">Sunglasses</button>
                <button class="category-btn" data-category="premium">Premium Collection</button>
                <button class="category-btn" data-category="sports">Sports Eyewear</button>
                <button class="category-btn" data-category="kids">Kids Collection</button>
                <button class="category-btn" data-category="contacts">Contact Lenses</button>
            </div>
        </div>

        <div class="products-grid" id="productsGrid"></div>
    </div>

    <div class="checkout" id="cartPanel">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.5rem;">Your Cart</h2>
        <div id="cartItems"></div>
        <div id="cartSummary" class="cart-summary"></div>
        <button class="btn" style="width: 100%; margin-top: 1.5rem;" onclick="showCheckout()">
            Proceed to Checkout
        </button>
    </div>

    <div class="checkout" id="checkout">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.5rem;">Checkout</h2>
        <form id="checkoutForm" onsubmit="handleSubmit(event)">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" class="form-input" required name="fullName">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-input" required name="email">
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" class="form-input" required name="phone">
            </div>
            <div class="form-group">
                <label>Address</label>
                <textarea class="form-input" required name="address" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Pincode</label>
                <input type="text" class="form-input" required name="pincode" maxlength="6">
            </div>

            <h3 style="margin: 1.5rem 0 1rem;">Payment Method</h3>
            <div class="payment-methods">
                <div class="payment-method" onclick="selectPayment('creditCard', this)">
                    💳 Credit/Debit Card
                </div>
                <div class="payment-method" onclick="selectPayment('upi', this)">
                    📱 UPI
                </div>
                <div class="payment-method" onclick="selectPayment('netbanking', this)">
                    🏦 Net Banking
                </div>
                <div class="payment-method" onclick="selectPayment('cod', this)">
                    💵 Cash on Delivery
                </div>
            </div>

            <div id="creditCardDetails" class="payment-details">
                <div class="form-group">
                    <label>Card Number</label>
                    <input type="text" class="form-input" placeholder="1234 5678 9012 3456" maxlength="19" id="cardNumber">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Expiry Date</label>
                        <input type="text" class="form-input" placeholder="MM/YY" maxlength="5" id="cardExpiry">
                    </div>
                    <div class="form-group">
                        <label>CVV</label>
                        <input type="text" class="form-input" placeholder="123" maxlength="3" id="cardCvv">
                    </div>
                </div>
                <div class="form-group">
                    <label>Cardholder Name</label>
                    <input type="text" class="form-input" placeholder="John Doe" id="cardName">
                </div>
            </div>

            <div id="upiDetails" class="payment-details">
                <div class="form-group">
                    <label>UPI ID</label>
                    <input type="text" class="form-input" placeholder="username@upi" id="upiId">
                </div>
            </div>

            <div id="netbankingDetails" class="payment-details">
                <div class="form-group">
                    <label>Select Bank</label>
                    <select class="form-input" id="bankSelect">
                        <option value="">Select your bank</option>
                        <option value="sbi">State Bank of India</option>
                        <option value="hdfc">HDFC Bank</option>
                        <option value="icici">ICICI Bank</option>
                        <option value="axis">Axis Bank</option>
                        <option value="pnb">Punjab National Bank</option>
                    </select>
                </div>
            </div>

            <div id="codDetails" class="payment-details">
                <p style="color: #6b7280;">Pay in cash when your order is delivered. Additional fee of ₹50 will be charged.</p>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="button" class="btn" style="background-color: #6b7280; width: 50%;" onclick="backToCart()">
                    Back to Cart
                </button>
                <button type="submit" class="btn" style="width: 50%;">
                    Place Order
                </button>
            </div>
        </form>
    </div>

    <div class="bill" id="bill">
        <h2 style="text-align: center; margin-bottom: 1.5rem; font-size: 1.5rem;">Invoice</h2>
        <div class="invoice-date">
                    <p><strong>VisionCare Clinic Eyewear</strong></p>
                    <p>B4, Second Floor, Office, Shree Sadashiv Housing Society</p>
                    <p> Shivajinagar Pune Maharashtra 411016</p>
                    <p>GSTIN: 27AABCU9603R1ZX</p>
                </div>
        
                <div id="customerDetails" style="
    margin-bottom: 1.5rem;
    float: right;
"></div>


        <table class="bill-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody id="billItems"></tbody>
        </table>

        <div id="billSummary" style="margin-top: 1.5rem;"></div>

        <div style="text-align: center; margin-top: 2rem;">
            <p id="paymentMethod" style="color: #6b7280; margin-bottom: 1rem;"></p>
            <div style="display: flex; justify-content: center; gap: 1rem;">
                <button class="btn" onclick="window.print()">Print Invoice</button>
                <button class="btn" style="background-color: #6b7280;" onclick="closeInvoice()">Close</button>
            </div>
        </div>
    </div>
    <script>
        // Product data with 20 eyewear products in INR
const products = [
    {
        id: 1,
        name: "RayBan Wayfarer Classic",
        category: "sunglasses",
        price: 5000,
        oldPrice: 7000,
        rating: 4.8,
        reviews: 342,
        image: "asset/p1.webp",
        badge: "Bestseller",
        description: "Iconic design with UV protection and polarized lenses"
    },
    {
        id: 2,
        name: "Oakley Holbrook",
        category: "sunglasses",
        price: 2000,
        oldPrice: 4500,
        rating: 4.7,
        reviews: 289,
        image: "asset/p2.webp",
        badge: "Premium",
        description: "Sports sunglasses with PRIZM lens technology"
    },
    {
        id: 3,
        name: "Vincent Chase Eyeglasses",
        category: "eyeglasses",
        price: 2000,
        oldPrice: 3999,
        rating: 4.5,
        reviews: 523,
        image: "asset/p3.webp",
        badge: "Popular",
        description: "Lightweight full-rim rectangle frames with anti-glare coating"
    },
    {
        id: 4,
        name: "Lenskart Air Glasses",
        category: "eyeglasses",
        price: 1700,
        oldPrice: 2999,
        rating: 4.3,
        reviews: 456,
        image: "asset/p4.webp",
        description: "Ultra-lightweight frames with blue light filter"
    },
    {
        id: 5,
        name: "Persol Italian Luxury",
        category: "premium",
        price: 10000,
        oldPrice: 12000,
        rating: 4.9,
        reviews: 178,
        image: "asset/p5.webp",
        badge: "Premium",
        description: "Handcrafted in Italy with premium acetate and crystal lenses"
    },
    {
        id: 6,
        name: "John Jacobs Aviator",
        category: "sunglasses",
        price: 4999,
        oldPrice: 5499,
        rating: 4.6,
        reviews: 312,
        image: "asset/p6.webp",
        description: "Classic aviator design with gradient tint lenses"
    },
    {
        id: 7,
        name: "Bausch & Lomb Monthly",
        category: "contacts",
        price: 1000,
        oldPrice: 1599,
        rating: 4.4,
        reviews: 289,
        image: "asset/p7.webp",
        description: "Set of 6 monthly replacement soft contact lenses"
    },
    {
        id: 8,
        name: "Titan Eyeplus Round",
        category: "eyeglasses",
        price: 3499,
        oldPrice: 4299,
        rating: 4.2,
        reviews: 176,
        image: "asset/p8.webp",
        description: "Classic round frames with high-index lenses"
    },
    {
        id: 9,
        name: "Oakley Sports Shield",
        category: "sports",
        price: 1199,
        oldPrice: 1459,
        rating: 4.8,
        reviews: 205,
        image: "asset/p9.webp",
        badge: "New",
        description: "High-performance sports eyewear with interchangeable lenses"
    },
    {
        id: 10,
        name: "Kids Fun Frames",
        category: "kids",
        price: 1299,
        oldPrice: 1999,
        rating: 4.5,
        reviews: 142,
        image: "asset/p10.webp",
        description: "Durable, colorful frames with impact-resistant lenses for kids"
    },
    {
        id: 11,
        name: "Acuvue Daily Disposable",
        category: "contacts",
        price: 1799,
        oldPrice: 2199,
        rating: 4.7,
        reviews: 356,
        image: "asset/p11.webp",
        badge: "Bestseller",
        description: "Pack of 30 daily disposable contact lenses with UV protection"
    },
    {
        id: 12,
        name: "Tom Ford Designer",
        category: "premium",
        price: 2499,
        oldPrice: 2999,
        rating: 4.9,
        reviews: 85,
        image: "asset/p12.webp",
        badge: "Luxury",
        description: "Designer acetate frames with signature T-logo detailing"
    },
    {
        id: 13,
        name: "Ray-Ban Clubmaster",
        category: "sunglasses",
        price: 4000,
        oldPrice: 5999,
        rating: 4.6,
        reviews: 278,
        image: "asset/p13.webp",
        description: "Iconic browline design with premium G-15 lenses"
    },
    {
        id: 14,
        name: "Speedo Swimming Goggles",
        category: "sports",
        price: 1499,
        oldPrice: 1999,
        rating: 4.3,
        reviews: 124,
        image: "asset/p14.webp",
        description: "Anti-fog coated swimming goggles with UV protection"
    },
    {
        id: 15,
        name: "Kids Flexible Frame",
        category: "kids",
        price: 1299,
        oldPrice: 1699,
        rating: 4.4,
        reviews: 98,
        image: "asset/p15.webp",
        description: "Bendable, nearly unbreakable frames for active children"
    },
    {
        id: 16,
        name: "Fastrack Hexagonal",
        category: "eyeglasses",
        price: 1599,
        oldPrice: 2299,
        rating: 4.1,
        reviews: 156,
        image: "asset/p16.webp",
        description: "Trendy hexagonal frames with zero power fashion lenses"
    },
    {
        id: 17,
        name: "Prada Lifestyle",
        category: "premium",
        price: 1899,
        oldPrice: 2299,
        rating: 4.8,
        reviews: 114,
        image: "asset/p17.avif",
        badge: "Designer",
        description: "Italian-crafted premium acetate frames with Prada logo"
    },
    {
        id: 18,
        name: "Decathlon Sports Glasses",
        category: "sports",
        price: 2999,
        oldPrice: 3499,
        rating: 4.2,
        reviews: 187,
        image: "asset/p18.webp",
        description: "Multi-sport glasses with adjustable nose pads and temple tips"
    },
    {
        id: 19,
        name: "ColorVue Fun Lenses",
        category: "contacts",
        price: 999,
        oldPrice: 1299,
        rating: 4.0,
        reviews: 76,
        image: "asset/p19.webp",
        description: "Colored contact lenses for costume and fashion use"
    },
    {
        id: 20,
        name: "Toddler First Frames",
        category: "kids",
        price: 1099,
        oldPrice: 1499,
        rating: 4.7,
        reviews: 63,
        image: "asset/p20.webp",
        badge: "Cute",
        description: "Extra small, super flexible frames for toddlers with adjustable strap"
    }
];

// Initialize the cart
let cart = [];

// DOM elements
const productsGrid = document.getElementById('productsGrid');
const cartItems = document.getElementById('cartItems');
const cartSummary = document.getElementById('cartSummary');
const cartCount = document.getElementById('cartCount');
const cartPanel = document.getElementById('cartPanel');
const checkout = document.getElementById('checkout');
const bill = document.getElementById('bill');
const overlay = document.getElementById('overlay');
const searchInput = document.getElementById('searchInput');

// Format price in Indian Rupees
function formatPrice(price) {
    return '₹' + price.toLocaleString('en-IN');
}

// Display products
function displayProducts(productsToShow = products) {
    productsGrid.innerHTML = '';
    
    productsToShow.forEach(product => {
        const productCard = document.createElement('div');
        productCard.className = 'product-card';
        productCard.style.position = 'relative';
        
        productCard.innerHTML = `
            <img src="${product.image}" alt="${product.name}" class="product-img">
            ${product.badge ? `<div class="product-badge">${product.badge}</div>` : ''}
            <div class="product-info">
                <h3 class="product-title">${product.name}</h3>
                <div class="product-price">
                    ${formatPrice(product.price)}
                    ${product.oldPrice ? `<span class="price-old">${formatPrice(product.oldPrice)}</span>` : ''}
                </div>
                <div class="product-rating">
                    ${'★'.repeat(Math.floor(product.rating))}${product.rating % 1 >= 0.5 ? '½' : ''}
                    <span class="rating-count">(${product.reviews})</span>
                </div>
                <p style="margin-bottom: 1rem; color: var(--gray); font-size: 0.9rem;">
                    ${product.description}
                </p>
                <button class="add-to-cart" onclick="addToCart(${product.id})">Add to Cart</button>
            </div>
        `;
        
        productsGrid.appendChild(productCard);
    });
}

// Filter products by category
function filterProducts(category) {
    const categoryButtons = document.querySelectorAll('.category-btn');
    categoryButtons.forEach(button => {
        button.classList.remove('active');
        if (button.dataset.category === category) {
            button.classList.add('active');
        }
    });
    
    if (category === 'all') {
        displayProducts();
    } else {
        const filteredProducts = products.filter(product => product.category === category);
        displayProducts(filteredProducts);
    }
}

// Search products
function searchProducts() {
    const searchTerm = searchInput.value.toLowerCase().trim();
    if (searchTerm === '') {
        displayProducts();
        return;
    }
    
    const filteredProducts = products.filter(product => 
        product.name.toLowerCase().includes(searchTerm) || 
        product.description.toLowerCase().includes(searchTerm) ||
        product.category.toLowerCase().includes(searchTerm)
    );
    
    displayProducts(filteredProducts);
}

// Add event listeners to category buttons
document.querySelectorAll('.category-btn').forEach(button => {
    button.addEventListener('click', () => {
        filterProducts(button.dataset.category);
    });
});

// Add to cart
function addToCart(productId) {
    const product = products.find(p => p.id === productId);
    const existingItem = cart.find(item => item.product.id === productId);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            product: product,
            quantity: 1
        });
    }
    
    updateCart();
    alert(`${product.name} added to cart!`);
}

// Update cart UI
function updateCart() {
    // Update cart count
    cartCount.textContent = cart.reduce((total, item) => total + item.quantity, 0);
    
    // Update cart items
    if (cart.length === 0) {
        cartItems.innerHTML = '<div class="cart-empty">Your cart is empty</div>';
        cartSummary.innerHTML = '';
        return;
    }
    
    cartItems.innerHTML = '';
    let subtotal = 0;
    
    cart.forEach(item => {
        const itemTotal = item.product.price * item.quantity;
        subtotal += itemTotal;
        
        const cartItem = document.createElement('div');
        cartItem.className = 'cart-item';
        cartItem.innerHTML = `
            <img src="${item.product.image}" alt="${item.product.name}" class="cart-img">
            <div class="cart-item-info">
                <h3>${item.product.name}</h3>
                <p class="cart-item-price">${formatPrice(item.product.price)}</p>
            </div>
            <div class="cart-quantity">
                <button class="quantity-btn" onclick="updateQuantity(${item.product.id}, ${item.quantity - 1})">-</button>
                <span>${item.quantity}</span>
                <button class="quantity-btn" onclick="updateQuantity(${item.product.id}, ${item.quantity + 1})">+</button>
            </div>
        `;
        
        cartItems.appendChild(cartItem);
    });
    
    // Calculate other costs
    const shipping = subtotal > 5000 ? 0 : 150;
    const tax = subtotal * 0.18; // 18% GST
    const total = subtotal + shipping + tax;
    
    // Update summary
    cartSummary.innerHTML = `
        <div class="summary-row">
            <span>Subtotal:</span>
            <span>${formatPrice(subtotal)}</span>
        </div>
        <div class="summary-row">
            <span>Shipping:</span>
            <span>${shipping === 0 ? 'Free' : formatPrice(shipping)}</span>
        </div>
        <div class="summary-row">
            <span>Tax (18% GST):</span>
            <span>${formatPrice(tax)}</span>
        </div>
        <div class="summary-row total">
            <span>Total:</span>
            <span>${formatPrice(total)}</span>
        </div>
    `;
}

// Update quantity
function updateQuantity(productId, newQuantity) {
    if (newQuantity <= 0) {
        cart = cart.filter(item => item.product.id !== productId);
    } else {
        const item = cart.find(item => item.product.id === productId);
        if (item) {
            item.quantity = newQuantity;
        }
    }
    
    updateCart();
}

// Toggle cart panel
function toggleCart() {
    if (cartPanel.style.right === '0px') {
        cartPanel.style.right = '-100%';
        overlay.style.display = 'none';
    } else {
        cartPanel.style.right = '0px';
        checkout.style.right = '-100%';
        bill.style.right = '-100%';
        overlay.style.display = 'block';
    }
}

// Show checkout
function showCheckout() {
    if (cart.length === 0) {
        alert('Your cart is empty!');
        return;
    }
    
    cartPanel.style.right = '-100%';
    checkout.style.right = '0px';
}

// Back to cart
function backToCart() {
    checkout.style.right = '-100%';
    cartPanel.style.right = '0px';
}

// Select payment method
function selectPayment(method, element) {
    // Hide all payment details
    document.querySelectorAll('.payment-details').forEach(el => {
        el.style.display = 'none';
    });
    
    // Remove selected class from all
    document.querySelectorAll('.payment-method').forEach(el => {
        el.classList.remove('selected');
    });
    
    // Show selected payment details
    document.getElementById(`${method}Details`).style.display = 'block';
    element.classList.add('selected');
    
    // Store selected payment method
    document.getElementById('checkoutForm').dataset.paymentMethod = method;
}

// Handle form submission
function handleSubmit(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const customerData = {};
    
    formData.forEach((value, key) => {
        customerData[key] = value;
    });
    
    const paymentMethod = form.dataset.paymentMethod;
    if (!paymentMethod) {
        alert('Please select a payment method');
        return;
    }
    
    // Generate bill
    generateBill(customerData, paymentMethod);
    
    // Show bill
    checkout.style.right = '-100%';
    bill.style.right = '0px';
}

// Generate bill
function generateBill(customerData, paymentMethod) {
    // Set customer details
    document.getElementById('customerDetails').innerHTML = `
        <p><strong>Name:</strong> ${customerData.fullName}</p>
        <p><strong>Email:</strong> ${customerData.email}</p>
        <p><strong>Phone:</strong> ${customerData.phone}</p>
        <p><strong>Address:</strong> ${customerData.address}</p>
        <p><strong>Pincode:</strong> ${customerData.pincode}</p>
    `;
    
    // Set payment method
    let paymentText = '';
    switch (paymentMethod) {
        case 'creditCard':
            paymentText = 'Paid via Credit/Debit Card';
            break;
        case 'upi':
            paymentText = 'Paid via UPI';
            break;
        case 'netbanking':
            paymentText = 'Paid via Net Banking';
            break;
        case 'cod':
            paymentText = 'Cash on Delivery';
            break;
    }
    document.getElementById('paymentMethod').textContent = paymentText;
    
    // Set bill items
    const billItems = document.getElementById('billItems');
    billItems.innerHTML = '';
    
    let subtotal = 0;
    cart.forEach(item => {
        const itemTotal = item.product.price * item.quantity;
        subtotal += itemTotal;
        
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${item.product.name}</td>
            <td>${item.quantity}</td>
            <td>${formatPrice(item.product.price)}</td>
            <td>${formatPrice(itemTotal)}</td>
        `;
        
        billItems.appendChild(tr);
    });
    
    // Calculate other costs
    const shipping = subtotal > 5000 ? 0 : 150;
    const codFee = paymentMethod === 'cod' ? 50 : 0;
    const tax = subtotal * 0.18; // 18% GST
    const total = subtotal + shipping + tax + codFee;
    
    // Update bill summary
    document.getElementById('billSummary').innerHTML = `
        <div class="summary-row">
            <span>Subtotal:</span>
            <span>${formatPrice(subtotal)}</span>
        </div>
        <div class="summary-row">
            <span>Shipping:</span>
            <span>${shipping === 0 ? 'Free' : formatPrice(shipping)}</span>
        </div>
        <div class="summary-row">
            <span>Tax (18% GST):</span>
            <span>${formatPrice(tax)}</span>
        </div>
        ${codFee > 0 ? `
        <div class="summary-row">
            <span>COD Fee:</span>
            <span>${formatPrice(codFee)}</span>
        </div>
        ` : ''}
        <div class="summary-row total">
            <span>Total:</span>
            <span>${formatPrice(total)}</span>
        </div>
    `;
}

// Close invoice
function closeInvoice() {
    bill.style.right = '-100%';
    overlay.style.display = 'none';
    
    // Clear cart
    cart = [];
    updateCart();
    
    // Reset forms
    document.getElementById('checkoutForm').reset();
    
    alert('Thank you for your purchase! Your order has been placed successfully.');
}

// Close panels when clicking overlay
overlay.addEventListener('click', () => {
    cartPanel.style.right = '-100%';
    checkout.style.right = '-100%';
    bill.style.right = '-100%';
    overlay.style.display = 'none';
});

// Search on Enter key
searchInput.addEventListener('keyup', (e) => {
    if (e.key === 'Enter') {
        searchProducts();
    }
});

// Format credit card number
document.getElementById('cardNumber').addEventListener('input', function(e) {
    let value = this.value.replace(/\D/g, '');
    if (value.length > 16) value = value.slice(0, 16);
    
    // Add spaces every 4 digits
    value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
    
    this.value = value;
});

// Format expiry date
document.getElementById('cardExpiry').addEventListener('input', function(e) {
    let value = this.value.replace(/\D/g, '');
    if (value.length > 4) value = value.slice(0, 4);
    
    if (value.length > 2) {
        value = value.slice(0, 2) + '/' + value.slice(2);
    }
    
    this.value = value;
});
// Print functionality
window.addEventListener('beforeprint', function() {
    // Hide non-printable elements
    document.querySelector('.nav').style.display = 'none';
    document.getElementById('mainContent').style.display = 'none';
    document.getElementById('overlay').style.display = 'none';
    
    // Style the invoice for printing
    const bill = document.getElementById('bill');
    bill.style.position = 'static';
    bill.style.right = '0';
    bill.style.width = '100%';
    bill.style.maxWidth = '100%';
    bill.style.height = 'auto';
    bill.style.overflow = 'visible';
    bill.style.boxShadow = 'none';
    
    // Hide print buttons
    const printButtons = bill.querySelectorAll('.btn');
    printButtons.forEach(button => {
        button.style.display = 'none';
    });
});

window.addEventListener('afterprint', function() {
    // Restore elements after printing
    document.querySelector('.nav').style.display = 'block';
    document.getElementById('mainContent').style.display = 'block';
    
    // Restore bill styling
    const bill = document.getElementById('bill');
    bill.style.position = 'fixed';
    bill.style.width = '100%';
    bill.style.maxWidth = '600px';
    bill.style.height = '100vh';
    bill.style.overflow = 'auto';
    bill.style.boxShadow = '-2px 0 10px rgba(0, 0, 0, 0.1)';
    
    // Show print buttons
    const printButtons = bill.querySelectorAll('.btn');
    printButtons.forEach(button => {
        button.style.display = 'inline-block';
    });
});
function redirectToDashboard() {
    window.location.href = 'dashboard user.php'; // Change 'dashboard.php' to your actual dashboard URL
}


// Initialize the page
displayProducts();
updateCart();
    </script>
    </body>
    </html>