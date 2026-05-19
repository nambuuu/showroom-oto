USE `car_showroom_db`;


INSERT INTO `brands` (`name`, `logo`, `country`) VALUES
('Toyota', 'toyota.png', 'Japan'),
('BMW', 'bmw.png', 'Germany'),
('Ford', 'ford.png', 'USA');


INSERT INTO `cars`
(`brand_id`, `model_name`, `year`, `price`, `category`, `color_options`, `description`, `status`, `is_featured`)
VALUES
(1, 'Camry', 2024, 1250000000, 'sedan', '["Black","White","Silver"]', 'Luxury sedan from Toyota', 'available', 1),

(2, 'X5', 2023, 4200000000, 'suv', '["Blue","Black"]', 'Premium SUV from BMW', 'available', 1),

(3, 'Ranger', 2024, 980000000, 'truck', '["Orange","Gray"]', 'Popular pickup truck from Ford', 'coming_soon', 0);

INSERT INTO `car_images`
(`car_id`, `image`, `is_main`)
VALUES
(1, 'camry_main.jpg', 1),
(2, 'bmw_x5_main.jpg', 1),
(3, 'ford_ranger_main.jpg', 1);

INSERT INTO `car_specifications`
(`car_id`, `engine`, `horsepower`, `torque`, `transmission`,
 `fuel_type`, `fuel_efficiency`, `seating`, `drive_type`,
 `top_speed`, `acceleration`)
VALUES

(1, '2.5L Hybrid', 208, '250Nm', 'Automatic',
 'Hybrid', '5.5L/100km', 5, 'FWD',
 210, 8.1),

(2, '3.0L Twin Turbo', 335, '450Nm', 'Automatic',
 'Petrol', '9.2L/100km', 7, 'AWD',
 243, 5.5),

(3, '2.0L Bi-Turbo Diesel', 210, '500Nm', 'Automatic',
 'Diesel', '7.8L/100km', 5, '4WD',
 180, 10.2);

INSERT INTO `bookings`
(`car_id`, `full_name`, `email`, `phone`,
 `preferred_date`, `preferred_time`,
 `message`, `status`)
VALUES

(1, 'Nguyen Van A', 'vana@gmail.com', '0911111111',
 '2026-05-20', '09:00:00',
 'I want to test drive the Camry.', 'pending'),

(2, 'Tran Thi B', 'thib@gmail.com', '0922222222',
 '2026-05-22', '14:30:00',
 'Interested in BMW X5 financing.', 'approved'),

(3, 'Le Van C', 'vanc@gmail.com', '0933333333',
 '2026-05-25', '10:15:00',
 'Need more information about Ranger.', 'done');


INSERT INTO `contacts`
(`full_name`, `email`, `phone`, `subject`, `message`, `is_read`)
VALUES

('Pham Minh D', 'minhd@gmail.com', '0944444444',
 'Car availability',
 'Do you still have Toyota Camry in stock?', 0),

('Hoang Lan E', 'lane@gmail.com', '0955555555',
 'Installment plan',
 'Can I buy BMW X5 with installment?', 1),

('Doan Quoc F', 'quocf@gmail.com', '0966666666',
 'Showroom address',
 'Please send me your showroom location.', 0);


-- tk=admin1
-- password = admin123

INSERT INTO `admin_users`
(`username`, `password`, `full_name`, `email`, `role`)
VALUES

('admin1',
'$2y$10$abJC110V4wxb05y33/ibXe3316dgRIgWAW4QqvBEUNuaVIFhUKhbm',
'Nguyen Van Nam',
'admin@gmail.com',
'superadmin');
