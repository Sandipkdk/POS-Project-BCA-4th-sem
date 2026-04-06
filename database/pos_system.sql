CREATE DATABASE IF NOT EXISTS pos_system;
USE pos_system;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','cashier') DEFAULT 'cashier',
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    status TINYINT DEFAULT 1
);


CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(255) NOT NULL,
    sku VARCHAR(100) UNIQUE,
    price DECIMAL(10,2) NOT NULL,
    cost DECIMAL(10,2) DEFAULT 0,
    stock INT DEFAULT 0,
    reorder_level INT DEFAULT 5,
    tax_rate DECIMAL(5,2) DEFAULT 0,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);


CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_no VARCHAR(50) UNIQUE,
    customer_id INT,
    subtotal DECIMAL(10,2),
    discount DECIMAL(10,2),
    tax DECIMAL(10,2),
    total DECIMAL(10,2),
    payment_method VARCHAR(50),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE sales_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    qty INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);


CREATE TABLE refunds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    qty INT NOT NULL,
    refund_amount DECIMAL(10,2) NOT NULL,
    reason TEXT,
    processed_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sale_id) REFERENCES sales(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (processed_by) REFERENCES users(id)
);




CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    store_name VARCHAR(255),
    store_address TEXT,
    store_phone VARCHAR(50),
    default_tax DECIMAL(5,2) DEFAULT 0,
    receipt_footer TEXT
);



CREATE TABLE payment_methods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    status TINYINT DEFAULT 1
);


optional
CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO users (name, username, password, role) VALUES
('Admin', 'admin', MD5('admin123'), 'admin');


INSERT INTO categories (name) VALUES
('Beverages'), ('Snacks'), ('Household'), ('Electronics');


INSERT INTO settings (store_name, store_address, store_phone, default_tax, receipt_footer) VALUES
('My Store', '123 Street, Baluwatar, Kathmandu', '9876543210', 5.00, 'Thank you for shopping!');

INSERT INTO payment_methods (name) VALUES
('Cash'), ('Card'), ('UPI'), ('Mobile Wallet');


ALTER TABLE products
ADD COLUMN discount_allowed TINYINT DEFAULT 1,       -- Can apply product-level discount
ADD COLUMN product_discount DECIMAL(5,2) DEFAULT 0;  -- % discount per product


ALTER TABLE settings
ADD COLUMN bill_tax_enabled TINYINT DEFAULT 0,      -- 0 = no tax, 1 = apply tax
ADD COLUMN bill_tax_rate DECIMAL(5,2) DEFAULT 0;   -- tax % if enabled


ALTER TABLE sales
ADD COLUMN bill_discount DECIMAL(10,2) DEFAULT 0,  -- discount applied on total bill
ADD COLUMN bill_tax DECIMAL(10,2) DEFAULT 0;       -- tax applied on total bill if enabled


ALTER TABLE sales_items
ADD COLUMN discount DECIMAL(10,2) DEFAULT 0;       -- item-level discount


product_total = base_price - product_discount


subtotal = sum(all product_total)
tax = if bill_tax_enabled ? subtotal * bill_tax_rate / 100 : 0
grand_total = subtotal + tax - bill_discount


