document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const menuItems = document.querySelectorAll('.menu li');
    const contentSections = document.querySelector('.content-sections');
    const currentDate = document.getElementById('current-date');
    const currentTime = document.getElementById('current-time');
    const signoutBtn = document.getElementById('signout-btn');
    const orderItemsList = document.getElementById('order-items-list');
    const subtotalElement = document.getElementById('subtotal');
    const grandTotalElement = document.getElementById('grand-total');
    const discountInput = document.getElementById('discount-input');
    const cancelOrderBtn = document.getElementById('cancel-order');
    const checkoutBtn = document.getElementById('checkout-btn');
    const productModal = document.getElementById('product-modal');
    const checkoutModal = document.getElementById('checkout-modal');
    const discountModal = document.getElementById('discount-modal');
    const areaStockModal = document.getElementById('area-stock-modal');
    const closeButtons = document.querySelectorAll('.close-btn');
    
    // Sample Data (In a real app, this would come from a database)
    let products = [
        { id: 1, code: 'PRD001', name: 'Coke 1.5L', price: 55.00, stock: 50 },
        { id: 2, code: 'PRD002', name: 'Pancit Canton', price: 12.00, stock: 100 },
        { id: 3, code: 'PRD003', name: 'Lucky Me Beef', price: 15.00, stock: 80 },
        { id: 4, code: 'PRD004', name: 'Cloud 9 30g', price: 10.00, stock: 60 },
        { id: 5, code: 'PRD005', name: 'Marlboro Red', price: 75.00, stock: 30 }
    ];
    
    let discounts = [
        { id: 1, code: 'SENIOR', description: 'Senior Citizen Discount', percentage: 20, validUntil: '2023-12-31' },
        { id: 2, code: 'PWD', description: 'PWD Discount', percentage: 20, validUntil: '2023-12-31' }
    ];
    
    let areaStocks = [
        { id: 1, area: 'Main Shelf', productId: 1, quantity: 20, lastUpdated: '2023-05-01' },
        { id: 2, area: 'Cooler', productId: 1, quantity: 30, lastUpdated: '2023-05-01' }
    ];
    
    let transactions = [
        { id: 1, date: '2023-05-01 09:30:00', items: 5, total: 250.00, paymentMethod: 'cash' },
        { id: 2, date: '2023-05-01 10:15:00', items: 3, total: 120.00, paymentMethod: 'gcash' }
    ];
    
    let currentOrder = {
        items: [],
        subtotal: 0,
        discount: 0,
        total: 0
    };
    
    // Initialize the app
    function init() {
        updateDateTime();
        setInterval(updateDateTime, 1000);
        loadContentSection('pos');
        renderProductsTable();
        renderDiscountsTable();
        renderAreaStockTable();
        renderTransactionsTable();
        setupEventListeners();
    }
    
    // Update date and time
    function updateDateTime() {
        const now = new Date();
        currentDate.textContent = now.toLocaleDateString('en-PH', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        currentTime.textContent = now.toLocaleTimeString('en-PH', { 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit' 
        });
    }
    
    // Load content section
    function loadContentSection(section) {
        // Clear active classes
        menuItems.forEach(item => item.classList.remove('active'));
        
        // Set active class on clicked menu item
        const activeMenuItem = document.querySelector(`.menu li[data-section="${section}"]`);
        if (activeMenuItem) {
            activeMenuItem.classList.add('active');
        }
        
        // Generate and load the section content
        let sectionContent = '';
        
        switch(section) {
            case 'pos':
                sectionContent = `
                    <div class="content-section active" id="pos">
                        <h2>Point of Sale</h2>
                        <div class="search-box">
                            <input type="text" id="product-search" placeholder="Search product by name or code...">
                            <button id="search-btn"><i class="fas fa-search"></i> Search</button>
                        </div>
                        <div class="search-results" id="search-results">
                            <!-- Search results will be populated by JavaScript -->
                        </div>
                    </div>
                `;
                break;
                
            case 'products':
                sectionContent = `
                    <div class="content-section active" id="products">
                        <h2>Product Management</h2>
                        <div class="product-controls">
                            <button id="add-product-btn"><i class="fas fa-plus"></i> Add Product</button>
                            <input type="text" id="product-filter" placeholder="Filter products...">
                        </div>
                        <table class="products-table">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Price (₱)</th>
                                    <th>Stock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="products-list">
                                <!-- Products will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                `;
                break;
                
            case 'transactions':
                sectionContent = `
                    <div class="content-section active" id="transactions">
                        <h2>Transaction History</h2>
                        <div class="transaction-controls">
                            <input type="text" id="transaction-filter" placeholder="Filter transactions...">
                            <button id="refresh-transactions"><i class="fas fa-sync-alt"></i> Refresh</button>
                        </div>
                        <table class="transactions-table">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Total (₱)</th>
                                    <th>Payment Method</th>
                                </tr>
                            </thead>
                            <tbody id="transactions-list">
                                <!-- Transactions will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                `;
                break;
                
            case 'discounts':
                sectionContent = `
                    <div class="content-section active" id="discounts">
                        <h2>Discount Management</h2>
                        <div class="discount-controls">
                            <button id="add-discount-btn"><i class="fas fa-plus"></i> Add Discount</button>
                        </div>
                        <table class="discounts-table">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Description</th>
                                    <th>Percentage</th>
                                    <th>Valid Until</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="discounts-list">
                                <!-- Discounts will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                `;
                break;
                
            case 'area-stock':
                sectionContent = `
                    <div class="content-section active" id="area-stock">
                        <h2>Area Stock Management</h2>
                        <div class="area-stock-controls">
                            <button id="add-area-stock-btn"><i class="fas fa-plus"></i> Add Area Stock</button>
                        </div>
                        <table class="area-stock-table">
                            <thead>
                                <tr>
                                    <th>Area</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="area-stock-list">
                                <!-- Area stock will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                `;
                break;

            case 'system-monitoring':
                window.location.href = 'admin_monitoring.php';
                return;
                break;
        }
        
        contentSections.innerHTML = sectionContent;
        
        // After loading the section, set up its specific event listeners
        setupSectionEventListeners(section);
    }
    
    // Setup all event listeners
    function setupEventListeners() {
        // Menu items
        menuItems.forEach(item => {
            item.addEventListener('click', function() {
                const section = this.getAttribute('data-section');
                loadContentSection(section);
            });
        });
        
        // Sign out button
        signoutBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to sign out?')) {
                // In a real app, this would redirect to login page
                alert('You have been signed out. Redirecting to login...');
            }
        });
        
        // Discount input
        discountInput.addEventListener('change', updateOrderTotals);
        
        // Cancel order button
        cancelOrderBtn.addEventListener('click', function() {
            if (currentOrder.items.length > 0) {
                if (confirm('Are you sure you want to cancel this order?')) {
                    currentOrder = {
                        items: [],
                        subtotal: 0,
                        discount: 0,
                        total: 0
                    };
                    renderOrderItems();
                    updateOrderTotals();
                }
            } else {
                alert('There are no items in the current order to cancel.');
            }
        });
        
        // Checkout button
        checkoutBtn.addEventListener('click', function() {
            if (currentOrder.items.length === 0) {
                alert('Please add items to the order before checkout.');
                return;
            }
            openCheckoutModal();
        });
        
        // Close modal buttons
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const modal = this.closest('.modal');
                modal.style.display = 'none';
            });
        });
    }
    
    // Setup section-specific event listeners
    function setupSectionEventListeners(section) {
        switch(section) {
            case 'pos':
                const searchBtn = document.getElementById('search-btn');
                const productSearch = document.getElementById('product-search');
                
                searchBtn.addEventListener('click', function() {
                    searchProducts(productSearch.value);
                });
                
                productSearch.addEventListener('keyup', function(e) {
                    if (e.key === 'Enter') {
                        searchProducts(productSearch.value);
                    }
                });
                break;
                
            case 'products':
                const addProductBtn = document.getElementById('add-product-btn');
                const productFilter = document.getElementById('product-filter');
                
                addProductBtn.addEventListener('click', function() {
                    openProductModal();
                });
                
                productFilter.addEventListener('keyup', function() {
                    filterProducts(productFilter.value);
                });
                break;
                
            case 'transactions':
                const refreshTransactionsBtn = document.getElementById('refresh-transactions');
                const transactionFilter = document.getElementById('transaction-filter');
                
                refreshTransactionsBtn.addEventListener('click', function() {
                    renderTransactionsTable();
                });
                
                transactionFilter.addEventListener('keyup', function() {
                    filterTransactions(transactionFilter.value);
                });
                break;
                
            case 'discounts':
                const addDiscountBtn = document.getElementById('add-discount-btn');
                
                addDiscountBtn.addEventListener('click', function() {
                    openDiscountModal();
                });
                break;
                
            case 'area-stock':
                const addAreaStockBtn = document.getElementById('add-area-stock-btn');
                
                addAreaStockBtn.addEventListener('click', function() {
                    openAreaStockModal();
                });
                break;
        }
    }
    
    // Product Management Functions
    function renderProductsTable() {
        const productsList = document.getElementById('products-list');
        if (!productsList) return;
        
        productsList.innerHTML = products.map(product => `
            <tr>
                <td>${product.code}</td>
                <td>${product.name}</td>
                <td>₱${product.price.toFixed(2)}</td>
                <td>${product.stock}</td>
                <td>
                    <button class="action-btn edit-btn" data-id="${product.id}">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="action-btn delete-btn" data-id="${product.id}">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </td>
            </tr>
        `).join('');
        
        // Add event listeners to edit and delete buttons
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = parseInt(this.getAttribute('data-id'));
                editProduct(productId);
            });
        });
        
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = parseInt(this.getAttribute('data-id'));
                deleteProduct(productId);
            });
        });
    }
    
    function filterProducts(query) {
        const filteredProducts = products.filter(product => 
            product.name.toLowerCase().includes(query.toLowerCase()) || 
            product.code.toLowerCase().includes(query.toLowerCase())
        );
        
        const productsList = document.getElementById('products-list');
        if (!productsList) return;
        
        productsList.innerHTML = filteredProducts.map(product => `
            <tr>
                <td>${product.code}</td>
                <td>${product.name}</td>
                <td>₱${product.price.toFixed(2)}</td>
                <td>${product.stock}</td>
                <td>
                    <button class="action-btn edit-btn" data-id="${product.id}">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="action-btn delete-btn" data-id="${product.id}">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </td>
            </tr>
        `).join('');
    }
    
    function searchProducts(query) {
        const searchResults = document.getElementById('search-results');
        if (!searchResults) return;
        
        if (!query) {
            searchResults.innerHTML = '<p>Please enter a search term</p>';
            return;
        }
        
        const filteredProducts = products.filter(product => 
            product.name.toLowerCase().includes(query.toLowerCase()) || 
            product.code.toLowerCase().includes(query.toLowerCase())
        );
        
        if (filteredProducts.length === 0) {
            searchResults.innerHTML = '<p>No products found. Please try a different search term.</p>';
            return;
        }
        
        searchResults.innerHTML = `
            <table class="search-results-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Price (₱)</th>
                        <th>Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    ${filteredProducts.map(product => `
                        <tr>
                            <td>${product.code}</td>
                            <td>${product.name}</td>
                            <td>₱${product.price.toFixed(2)}</td>
                            <td>${product.stock}</td>
                            <td>
                                <button class="action-btn add-to-order" data-id="${product.id}">
                                    <i class="fas fa-cart-plus"></i> Add
                                </button>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
        
        // Add event listeners to add to order buttons
        document.querySelectorAll('.add-to-order').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = parseInt(this.getAttribute('data-id'));
                addProductToOrder(productId);
            });
        });
    }
    
    function openProductModal(product = null) {
        const modalTitle = document.getElementById('modal-title');
        const productId = document.getElementById('product-id');
        const productCode = document.getElementById('product-code');
        const productName = document.getElementById('product-name');
        const productPrice = document.getElementById('product-price');
        const productStock = document.getElementById('product-stock');
        const productForm = document.getElementById('product-form');
        
        if (product) {
            // Edit mode
            modalTitle.textContent = 'Edit Product';
            productId.value = product.id;
            productCode.value = product.code;
            productName.value = product.name;
            productPrice.value = product.price;
            productStock.value = product.stock;
        } else {
            // Add mode
            modalTitle.textContent = 'Add New Product';
            productId.value = '';
            productCode.value = '';
            productName.value = '';
            productPrice.value = '';
            productStock.value = '';
        }
        
        productModal.style.display = 'flex';
    }
    
    function editProduct(productId) {
        const product = products.find(p => p.id === productId);
        if (product) {
            openProductModal(product);
        }
    }
    
    function deleteProduct(productId) {
        if (confirm('Are you sure you want to delete this product?')) {
            products = products.filter(p => p.id !== productId);
            renderProductsTable();
            alert('Product deleted successfully.');
        }
    }
    
    function saveProduct(event) {
        event.preventDefault();
        
        const productId = document.getElementById('product-id').value;
        const productCode = document.getElementById('product-code').value;
        const productName = document.getElementById('product-name').value;
        const productPrice = parseFloat(document.getElementById('product-price').value);
        const productStock = parseInt(document.getElementById('product-stock').value);
        
        if (productId) {
            // Update existing product
            const index = products.findIndex(p => p.id === parseInt(productId));
            if (index !== -1) {
                products[index] = {
                    id: parseInt(productId),
                    code: productCode,
                    name: productName,
                    price: productPrice,
                    stock: productStock
                };
            }
        } else {
            // Add new product
            const newId = products.length > 0 ? Math.max(...products.map(p => p.id)) + 1 : 1;
            products.push({
                id: newId,
                code: productCode,
                name: productName,
                price: productPrice,
                stock: productStock
            });
        }
        
        productModal.style.display = 'none';
        renderProductsTable();
        alert('Product saved successfully!');
    }
    
    // Discount Management Functions
    function renderDiscountsTable() {
        const discountsList = document.getElementById('discounts-list');
        if (!discountsList) return;
        
        discountsList.innerHTML = discounts.map(discount => `
            <tr>
                <td>${discount.code}</td>
                <td>${discount.description}</td>
                <td>${discount.percentage}%</td>
                <td>${discount.validUntil}</td>
                <td>
                    <button class="action-btn edit-btn" data-id="${discount.id}">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="action-btn delete-btn" data-id="${discount.id}">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </td>
            </tr>
        `).join('');
        
        // Add event listeners to edit and delete buttons
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const discountId = parseInt(this.getAttribute('data-id'));
                editDiscount(discountId);
            });
        });
        
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const discountId = parseInt(this.getAttribute('data-id'));
                deleteDiscount(discountId);
            });
        });
    }
    
    function openDiscountModal(discount = null) {
        const modalTitle = document.getElementById('discount-modal-title');
        const discountId = document.getElementById('discount-id');
        const discountCode = document.getElementById('discount-code');
        const discountDescription = document.getElementById('discount-description');
        const discountPercentage = document.getElementById('discount-percentage');
        const discountValidUntil = document.getElementById('discount-valid-until');
        const discountForm = document.getElementById('discount-form');
        
        if (discount) {
            // Edit mode
            modalTitle.textContent = 'Edit Discount';
            discountId.value = discount.id;
            discountCode.value = discount.code;
            discountDescription.value = discount.description;
            discountPercentage.value = discount.percentage;
            discountValidUntil.value = discount.validUntil;
        } else {
            // Add mode
            modalTitle.textContent = 'Add New Discount';
            discountId.value = '';
            discountCode.value = '';
            discountDescription.value = '';
            discountPercentage.value = '';
            discountValidUntil.value = '';
        }
        
        discountModal.style.display = 'flex';
    }
    
    function editDiscount(discountId) {
        const discount = discounts.find(d => d.id === discountId);
        if (discount) {
            openDiscountModal(discount);
        }
    }
    
    function deleteDiscount(discountId) {
        if (confirm('Are you sure you want to delete this discount?')) {
            discounts = discounts.filter(d => d.id !== discountId);
            renderDiscountsTable();
            alert('Discount deleted successfully.');
        }
    }
    
    function saveDiscount(event) {
        event.preventDefault();
        
        const discountId = document.getElementById('discount-id').value;
        const discountCode = document.getElementById('discount-code').value;
        const discountDescription = document.getElementById('discount-description').value;
        const discountPercentage = parseInt(document.getElementById('discount-percentage').value);
        const discountValidUntil = document.getElementById('discount-valid-until').value;
        
        if (discountId) {
            // Update existing discount
            const index = discounts.findIndex(d => d.id === parseInt(discountId));
            if (index !== -1) {
                discounts[index] = {
                    id: parseInt(discountId),
                    code: discountCode,
                    description: discountDescription,
                    percentage: discountPercentage,
                    validUntil: discountValidUntil
                };
            }
        } else {
            // Add new discount
            const newId = discounts.length > 0 ? Math.max(...discounts.map(d => d.id)) + 1 : 1;
            discounts.push({
                id: newId,
                code: discountCode,
                description: discountDescription,
                percentage: discountPercentage,
                validUntil: discountValidUntil
            });
        }
        
        discountModal.style.display = 'none';
        renderDiscountsTable();
        alert('Discount saved successfully!');
    }
    
    // Area Stock Management Functions
    function renderAreaStockTable() {
        const areaStockList = document.getElementById('area-stock-list');
        if (!areaStockList) return;
        
        areaStockList.innerHTML = areaStocks.map(stock => {
            const product = products.find(p => p.id === stock.productId);
            return `
                <tr>
                    <td>${stock.area}</td>
                    <td>${product ? product.name : 'Unknown Product'}</td>
                    <td>${stock.quantity}</td>
                    <td>${stock.lastUpdated}</td>
                    <td>
                        <button class="action-btn edit-btn" data-id="${stock.id}">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="action-btn delete-btn" data-id="${stock.id}">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
        
        // Add event listeners to edit and delete buttons
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const stockId = parseInt(this.getAttribute('data-id'));
                editAreaStock(stockId);
            });
        });
        
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const stockId = parseInt(this.getAttribute('data-id'));
                deleteAreaStock(stockId);
            });
        });
    }
    
    function openAreaStockModal(stock = null) {
        const modalTitle = document.getElementById('area-stock-modal-title');
        const areaStockId = document.getElementById('area-stock-id');
        const areaName = document.getElementById('area-name');
        const areaProduct = document.getElementById('area-product');
        const areaQuantity = document.getElementById('area-quantity');
        const areaStockForm = document.getElementById('area-stock-form');
        
        // Populate product dropdown
        areaProduct.innerHTML = products.map(product => 
            `<option value="${product.id}">${product.name}</option>`
        ).join('');
        
        if (stock) {
            // Edit mode
            modalTitle.textContent = 'Edit Area Stock';
            areaStockId.value = stock.id;
            areaName.value = stock.area;
            areaProduct.value = stock.productId;
            areaQuantity.value = stock.quantity;
        } else {
            // Add mode
            modalTitle.textContent = 'Add Area Stock';
            areaStockId.value = '';
            areaName.value = '';
            areaProduct.value = products.length > 0 ? products[0].id : '';
            areaQuantity.value = '';
        }
        
        areaStockModal.style.display = 'flex';
    }
    
    function editAreaStock(stockId) {
        const stock = areaStocks.find(s => s.id === stockId);
        if (stock) {
            openAreaStockModal(stock);
        }
    }
    
    function deleteAreaStock(stockId) {
        if (confirm('Are you sure you want to delete this area stock record?')) {
            areaStocks = areaStocks.filter(s => s.id !== stockId);
            renderAreaStockTable();
            alert('Area stock record deleted successfully.');
        }
    }
    
    function saveAreaStock(event) {
        event.preventDefault();
        
        const areaStockId = document.getElementById('area-stock-id').value;
        const areaName = document.getElementById('area-name').value;
        const areaProduct = parseInt(document.getElementById('area-product').value);
        const areaQuantity = parseInt(document.getElementById('area-quantity').value);
        const today = new Date().toISOString().split('T')[0];
        
        if (areaStockId) {
            // Update existing area stock
            const index = areaStocks.findIndex(s => s.id === parseInt(areaStockId));
            if (index !== -1) {
                areaStocks[index] = {
                    id: parseInt(areaStockId),
                    area: areaName,
                    productId: areaProduct,
                    quantity: areaQuantity,
                    lastUpdated: today
                };
            }
        } else {
            // Add new area stock
            const newId = areaStocks.length > 0 ? Math.max(...areaStocks.map(s => s.id)) + 1 : 1;
            areaStocks.push({
                id: newId,
                area: areaName,
                productId: areaProduct,
                quantity: areaQuantity,
                lastUpdated: today
            });
        }
        
        areaStockModal.style.display = 'none';
        renderAreaStockTable();
        alert('Area stock saved successfully!');
    }
    
    // Transaction Management Functions
    function renderTransactionsTable() {
        const transactionsList = document.getElementById('transactions-list');
        if (!transactionsList) return;
        
        transactionsList.innerHTML = transactions.map(transaction => `
            <tr>
                <td>${transaction.id}</td>
                <td>${transaction.date}</td>
                <td>${transaction.items}</td>
                <td>₱${transaction.total.toFixed(2)}</td>
                <td>${transaction.paymentMethod.toUpperCase()}</td>
            </tr>
        `).join('');
    }
    
    function filterTransactions(query) {
        const filteredTransactions = transactions.filter(transaction => 
            transaction.id.toString().includes(query) || 
            transaction.date.includes(query) ||
            transaction.paymentMethod.toLowerCase().includes(query.toLowerCase())
        );
        
        const transactionsList = document.getElementById('transactions-list');
        if (!transactionsList) return;
        
        transactionsList.innerHTML = filteredTransactions.map(transaction => `
            <tr>
                <td>${transaction.id}</td>
                <td>${transaction.date}</td>
                <td>${transaction.items}</td>
                <td>₱${transaction.total.toFixed(2)}</td>
                <td>${transaction.paymentMethod.toUpperCase()}</td>
            </tr>
        `).join('');
    }
    
    // Order Management Functions
    function addProductToOrder(productId) {
        const product = products.find(p => p.id === productId);
        if (!product) return;
        
        const existingItemIndex = currentOrder.items.findIndex(item => item.productId === productId);
        
        if (existingItemIndex !== -1) {
            // If product already in order, increase quantity
            currentOrder.items[existingItemIndex].quantity += 1;
        } else {
            // Add new product to order
            currentOrder.items.push({
                productId: product.id,
                code: product.code,
                name: product.name,
                price: product.price,
                quantity: 1
            });
        }
        
        renderOrderItems();
        updateOrderTotals();
    }
    
    function removeProductFromOrder(productId) {
        currentOrder.items = currentOrder.items.filter(item => item.productId !== productId);
        renderOrderItems();
        updateOrderTotals();
    }
    
    function updateProductQuantity(productId, quantity) {
        const itemIndex = currentOrder.items.findIndex(item => item.productId === productId);
        
        if (itemIndex !== -1) {
            if (quantity > 0) {
                currentOrder.items[itemIndex].quantity = quantity;
            } else {
                // If quantity is 0 or negative, remove the item
                currentOrder.items.splice(itemIndex, 1);
            }
        }
        
        renderOrderItems();
        updateOrderTotals();
    }
    
    function renderOrderItems() {
        const orderItemsContainer = document.getElementById('order-items-container');
        if (!orderItemsContainer) return;
        
        if (currentOrder.items.length === 0) {
            orderItemsContainer.innerHTML = '<p class="empty-order">No items in the order. Search and add products.</p>';
            return;
        }
        
        orderItemsContainer.innerHTML = `
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Price (₱)</th>
                        <th>Total (₱)</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    ${currentOrder.items.map(item => `
                        <tr>
                            <td>${item.name} (${item.code})</td>
                            <td>
                                <input type="number" min="1" value="${item.quantity}" 
                                    data-id="${item.productId}" class="quantity-input">
                            </td>
                            <td>${item.price.toFixed(2)}</td>
                            <td>${(item.price * item.quantity).toFixed(2)}</td>
                            <td>
                                <button class="action-btn delete-btn" data-id="${item.productId}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
        
        // Add event listeners to quantity inputs
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function() {
                const productId = parseInt(this.getAttribute('data-id'));
                const quantity = parseInt(this.value);
                updateProductQuantity(productId, quantity);
            });
        });
        
        // Add event listeners to delete buttons
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = parseInt(this.getAttribute('data-id'));
                removeProductFromOrder(productId);
            });
        });
    }
    
    function updateOrderTotals() {
        // Calculate subtotal
        currentOrder.subtotal = currentOrder.items.reduce((total, item) => {
            return total + (item.price * item.quantity);
        }, 0);
        
        // Get discount percentage
        const discountPercentage = parseInt(discountInput.value) || 0;
        currentOrder.discount = discountPercentage;
        
        // Calculate total with discount
        const discountAmount = (currentOrder.subtotal * discountPercentage) / 100;
        currentOrder.total = currentOrder.subtotal - discountAmount;
        
        // Update UI
        subtotalElement.textContent = `₱${currentOrder.subtotal.toFixed(2)}`;
        grandTotalElement.textContent = `₱${currentOrder.total.toFixed(2)}`;
    }
    
    function openCheckoutModal() {
        const checkoutSummary = document.getElementById('checkout-summary');
        const paymentMethod = document.getElementById('payment-method');
        const cashPayment = document.getElementById('cash-payment');
        const amountReceived = document.getElementById('amount-received');
        const changeAmount = document.getElementById('change-amount');
        const confirmPaymentBtn = document.getElementById('confirm-payment');
        
        // Build checkout summary
        checkoutSummary.innerHTML = `
            <div class="checkout-items">
                ${currentOrder.items.map(item => `
                    <div class="checkout-item">
                        <span>${item.quantity} x ${item.name}</span>
                        <span>₱${(item.price * item.quantity).toFixed(2)}</span>
                    </div>
                `).join('')}
            </div>
            <div class="checkout-totals">
                <div class="checkout-total">
                    <span>Subtotal:</span>
                    <span>₱${currentOrder.subtotal.toFixed(2)}</span>
                </div>
                <div class="checkout-total">
                    <span>Discount (${currentOrder.discount}%):</span>
                    <span>₱${((currentOrder.subtotal * currentOrder.discount) / 100).toFixed(2)}</span>
                </div>
                <div class="checkout-total grand-total">
                    <span>Total:</span>
                    <span>₱${currentOrder.total.toFixed(2)}</span>
                </div>
            </div>
        `;
        
        // Reset payment method and amount received
        paymentMethod.value = 'cash';
        amountReceived.value = '';
        changeAmount.textContent = 'Change: ₱0.00';
        cashPayment.style.display = 'block';
        
        // Payment method change event
        paymentMethod.addEventListener('change', function() {
            if (this.value === 'cash') {
                cashPayment.style.display = 'block';
            } else {
                cashPayment.style.display = 'none';
            }
        });
        
        // Amount received change event
        amountReceived.addEventListener('input', function() {
            const amount = parseFloat(this.value) || 0;
            const change = amount - currentOrder.total;
            changeAmount.textContent = `Change: ₱${change > 0 ? change.toFixed(2) : '0.00'}`;
        });
        
        // Confirm payment event
        confirmPaymentBtn.addEventListener('click', function() {
            if (paymentMethod.value === 'cash') {
                const amount = parseFloat(amountReceived.value) || 0;
                if (amount < currentOrder.total) {
                    alert('Amount received is less than the total amount. Please provide sufficient payment.');
                    return;
                }
            }
            
            // Create a new transaction
            const newTransaction = {
                id: transactions.length > 0 ? Math.max(...transactions.map(t => t.id)) + 1 : 1,
                date: new Date().toLocaleString('en-PH'),
                items: currentOrder.items.reduce((total, item) => total + item.quantity, 0),
                total: currentOrder.total,
                paymentMethod: paymentMethod.value
            };
            
            transactions.unshift(newTransaction);
            
            // Update product stocks
            currentOrder.items.forEach(item => {
                const productIndex = products.findIndex(p => p.id === item.productId);
                if (productIndex !== -1) {
                    products[productIndex].stock -= item.quantity;
                }
            });
            
            // Reset current order
            currentOrder = {
                items: [],
                subtotal: 0,
                discount: 0,
                total: 0
            };
            
            // Update UI
            checkoutModal.style.display = 'none';
            renderOrderItems();
            updateOrderTotals();
            renderProductsTable();
            renderTransactionsTable();
            
            // Show receipt (in a real app, this would print or save the receipt)
            alert('Payment confirmed! Transaction completed successfully.');
        });
        
        checkoutModal.style.display = 'flex';
    }
    
    // Form Submissions
    document.getElementById('product-form')?.addEventListener('submit', saveProduct);
    document.getElementById('discount-form')?.addEventListener('submit', saveDiscount);
    document.getElementById('area-stock-form')?.addEventListener('submit', saveAreaStock);
    
    // Initialize the application
    init();
});