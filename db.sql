CREATE DATABASE IF NOT EXISTS food_ordering_system;
USE food_ordering_system;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE restaurants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    address VARCHAR(255),
    contact VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('Pending', 'Confirmed', 'Delivered', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    menu_item_id INT NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE
);

INSERT INTO users (name, email, password, role) VALUES ('Navindi Thisara', 'navindithisara214@gmail.com', 'Navi_thizx14', 'admin');

INSERT INTO users (name, email, password, role) VALUES ('Wageesha Oshadi', 'wageeshaoshadi1@gmail.com', 'Wa_2003', 'user'),('Kisal Kavinda', 'kisal@example.com', 'kisal123', 'user');

INSERT INTO restaurants (name, address, contact) VALUES ('Pizza Palace', '123 Main St, City', '011-1234567'),('Burger House', '456 High St, City', '011-2345678'),('Sushi World', '789 Ocean Ave, City', '011-3456789');

INSERT INTO menu_items (restaurant_id, name, description, price, image) VALUES(1, 'Margherita Pizza', 'Classic cheese and tomato pizza', 1200.00, 'margherita.jpg'),(1, 'Pepperoni Pizza', 'Spicy pepperoni with mozzarella', 1500.00, 'pepperoni.jpg'),(1, 'Veggie Pizza', 'Loaded with fresh vegetables', 1400.00, 'veggie.jpg');

INSERT INTO menu_items (restaurant_id, name, description, price, image) VALUES (2, 'Classic Beef Burger', 'Grilled beef patty with lettuce and tomato', 800.00, 'beef_burger.jpg'), (2, 'Chicken Burger', 'Crispy chicken with mayo and lettuce', 750.00, 'chicken_burger.jpg'), (2, 'Cheese Burger', 'Beef patty with melted cheese', 850.00, 'cheese_burger.jpg');

INSERT INTO menu_items (restaurant_id, name, description, price, image) VALUES (3, 'California Roll', 'Crab, avocado, cucumber roll', 1000.00, 'california_roll.jpg'),(3, 'Salmon Sushi', 'Fresh salmon over rice', 1200.00, 'salmon_sushi.jpg'),(3, 'Tuna Sushi', 'Tuna slices over seasoned rice', 1300.00, 'tuna_sushi.jpg');

INSERT INTO orders (user_id, total_amount, status) VALUES (2, 2000.00, 'Confirmed'), (3, 1750.00, 'Pending');   

INSERT INTO order_items (order_id, menu_item_id, quantity, subtotal) VALUES (1, 1, 1, 1200.00), (1, 4, 1, 800.00),  (2, 6, 2, 1700.00), (2, 8, 1, 1200.00); 
