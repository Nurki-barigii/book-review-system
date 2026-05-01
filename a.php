ALTER TABLE books ADD COLUMN digital_file_path VARCHAR(255) NULL AFTER price;
ALTER TABLE orders ADD COLUMN digital_file_path VARCHAR(255) NULL AFTER status;
ALTER TABLE orders ADD COLUMN digital_download_link VARCHAR(255) NULL AFTER digital_file_path;
ALTER TABLE orders ADD COLUMN digital_access_expiry DATETIME NULL AFTER digital_download_link;

-- Add order_type column to orders table
ALTER TABLE orders ADD COLUMN order_type ENUM('physical', 'digital') DEFAULT 'physical' AFTER total_amount;

-- Add digital file columns to books table
ALTER TABLE books ADD COLUMN digital_file_path VARCHAR(255) NULL AFTER price;
ALTER TABLE books ADD COLUMN has_digital TINYINT(1) DEFAULT 0 AFTER digital_file_path;

CREATE TABLE IF NOT EXISTS download_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    downloaded_at DATETIME NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

ALTER TABLE sales ADD COLUMN unit_price DECIMAL(10,2) NULL AFTER quantity_sold;

CREATE TABLE IF NOT EXISTS download_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    downloaded_at DATETIME NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);


ALTER TABLE books 
ADD COLUMN has_digital TINYINT(1) DEFAULT 0 AFTER price,
ADD COLUMN pdf_file VARCHAR(255) NULL AFTER has_digital;