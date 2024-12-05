CREATE DATABASE IF NOT EXISTS wedding_decoration_company; -- Wedding Decoration Company
USE wedding_decoration_company;

-- For MYSQL

CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(200) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone_number VARCHAR(25) NULL,
    admin BOOLEAN DEFAULT FALSE,
    status BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE worker (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT, FOREIGN KEY (user_id) REFERENCES user(id),
    supervisor BOOLEAN DEFAULT FALSE,
    can_drive BOOLEAN DEFAULT FALSE,
    special_effects_license BOOLEAN DEFAULT FALSE,
    status BOOLEAN DEFAULT TRUE
);

CREATE TABLE unavailable_worker_dates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    worker_id INT, FOREIGN KEY (worker_id) REFERENCES worker(id),
    date DATE NOT NULL,
    reason VARCHAR(500) NULL,
    validated_by_id INT NULL, FOREIGN KEY (validated_by_id) REFERENCES worker(id)
);

CREATE TABLE item_type (
    id INT AUTO_INCREMENT PRIMARY KEY,
    original_barcode VARCHAR(50) NULL,
    name VARCHAR(100) NULL,
    description VARCHAR(500) NULL,
    rental_price DECIMAL(10, 2) NULL,
    replacement_price DECIMAL(10, 2) NULL,
    image_name VARCHAR(200) NULL,
    need_cleaning_after_use BOOLEAN DEFAULT FALSE,
    one_time_use BOOLEAN DEFAULT FALSE,
    status BOOLEAN DEFAULT TRUE
);

CREATE TABLE storage_unit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NULL,
    address VARCHAR(200) NULL,
    status BOOLEAN DEFAULT TRUE
);

CREATE TABLE item (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_type_id INT, FOREIGN KEY (item_type_id) REFERENCES item_type(id),
    storage_unit_id INT NULL, FOREIGN KEY (storage_unit_id) REFERENCES storage_unit(id),
    observation VARCHAR(500) NULL,
    need_cleaning BOOLEAN DEFAULT FALSE,
    reason_for_damage VARCHAR(500) NULL,
    status BOOLEAN DEFAULT TRUE
);

CREATE TABLE service (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NULL,
    description VARCHAR(500) NULL,
    price DECIMAL(10, 2) NULL,
    requires_special_effects_license BOOLEAN DEFAULT FALSE,
    image_name VARCHAR(200) NULL,
    status BOOLEAN DEFAULT TRUE
);

CREATE TABLE required_items_for_service (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT, FOREIGN KEY (service_id) REFERENCES service(id),
    item_type_id INT, FOREIGN KEY (item_type_id) REFERENCES item_type(id),
    quantity INT NOT NULL
);

CREATE TABLE car (
    id INT AUTO_INCREMENT PRIMARY KEY,
    license_plate VARCHAR(7) NOT NULL UNIQUE,
    model VARCHAR(100) NOT NULL,
    status BOOLEAN DEFAULT TRUE,
    rovinieta_expiration_date DATE NULL
);

CREATE TABLE `event` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location VARCHAR(500) NOT NULL,
    observation VARCHAR(500) NULL,
    date_and_time DATETIME NOT NULL,
    status ENUM('scheduled', 'planned', 'finished') DEFAULT 'scheduled'
);

CREATE TABLE event_items_order (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT, FOREIGN KEY (event_id) REFERENCES event(id),
    item_id INT, FOREIGN KEY (item_id) REFERENCES item(id),
    quantity INT NOT NULL
);

CREATE TABLE event_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT, FOREIGN KEY (event_id) REFERENCES event(id),
    item_id INT, FOREIGN KEY (item_id) REFERENCES item(id)
);

CREATE TABLE event_services_order (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT, FOREIGN KEY (event_id) REFERENCES event(id),
    service_id INT, FOREIGN KEY (service_id) REFERENCES service(id)
);

CREATE TABLE event_workers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT, FOREIGN KEY (event_id) REFERENCES event(id),
    worker_id INT, FOREIGN KEY (worker_id) REFERENCES worker(id),
    required_stay_until_end BOOLEAN DEFAULT FALSE
);

CREATE TABLE event_car (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT, FOREIGN KEY (event_id) REFERENCES event(id),
    car_id INT, FOREIGN KEY (car_id) REFERENCES car(id)
);

CREATE TABLE event_invoice (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT, FOREIGN KEY (event_id) REFERENCES event(id),
    client_info VARCHAR(500) NULL,
    paid BOOLEAN DEFAULT FALSE
);

CREATE TABLE discount_and_extra_fee (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(500) NULL,
    percent BOOLEAN DEFAULT FALSE,
    increase_or_decrease BOOLEAN DEFAULT FALSE,
    value DECIMAL(10, 2) NOT NULL,
    status BOOLEAN DEFAULT TRUE
);

CREATE TABLE event_extra_fees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_invoice_id INT, FOREIGN KEY (event_invoice_id) REFERENCES event_invoice(id),
    discount_and_extra_fee_id INT, FOREIGN KEY (discount_and_extra_fee_id) REFERENCES discount_and_extra_fee(id)
);

CREATE TABLE event_invoice_for_damages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT, FOREIGN KEY (event_id) REFERENCES event(id),
    client_info VARCHAR(500) NULL,
    paid BOOLEAN DEFAULT FALSE
);

CREATE TABLE damaged_items_on_event (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_invoice_for_damages_id INT, FOREIGN KEY (event_invoice_for_damages_id) REFERENCES event_invoice_for_damages(id),
    item_id INT, FOREIGN KEY (item_id) REFERENCES item(id),
    reason VARCHAR(500) NULL,
    price DECIMAL(10, 2) NULL
);

