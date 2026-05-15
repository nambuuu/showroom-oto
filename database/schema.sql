-- =============================================
-- CAR SHOWROOM DATABASE - SCHEMA
-- Database: car_showroom_db
-- =============================================

CREATE DATABASE IF NOT EXISTS `car_showroom_db` 
  DEFAULT CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE `car_showroom_db`;

-- ---------------------------------------------
-- 1. brands – Hãng Xe
-- ---------------------------------------------
CREATE TABLE `brands` (
  `id`      INT           NOT NULL AUTO_INCREMENT,
  `name`    VARCHAR(100)  NOT NULL,
  `logo`    VARCHAR(255)  DEFAULT NULL,
  `country` VARCHAR(100)  DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------
-- 2. cars – Danh Sách Xe
-- ---------------------------------------------
CREATE TABLE `cars` (
  `id`            INT            NOT NULL AUTO_INCREMENT,
  `brand_id`      INT            NOT NULL,
  `model_name`    VARCHAR(100)   NOT NULL,
  `year`          YEAR           NOT NULL,
  `price`         DECIMAL(12,2)  NOT NULL DEFAULT 0.00,
  `category`      ENUM('sedan','suv','hatchback','truck','coupe','mpv','convertible') NOT NULL DEFAULT 'sedan',
  `color_options` TEXT           DEFAULT NULL COMMENT 'JSON array các màu có sẵn',
  `description`   TEXT           DEFAULT NULL,
  `status`        ENUM('available','sold_out','coming_soon') NOT NULL DEFAULT 'available',
  `is_featured`   TINYINT(1)     NOT NULL DEFAULT 0,
  `created_at`    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_cars_brand` (`brand_id`),
  CONSTRAINT `fk_cars_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------
-- 3. car_images – Ảnh Xe
-- ---------------------------------------------
CREATE TABLE `car_images` (
  `id`      INT           NOT NULL AUTO_INCREMENT,
  `car_id`  INT           NOT NULL,
  `image`   VARCHAR(255)  NOT NULL,
  `is_main` TINYINT(1)    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `fk_images_car` (`car_id`),
  CONSTRAINT `fk_images_car` FOREIGN KEY (`car_id`) REFERENCES `cars`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------
-- 4. car_specifications – Thông Số Kỹ Thuật
-- ---------------------------------------------
CREATE TABLE `car_specifications` (
  `id`              INT           NOT NULL AUTO_INCREMENT,
  `car_id`          INT           NOT NULL,
  `engine`          VARCHAR(100)  DEFAULT NULL,
  `horsepower`      INT           DEFAULT NULL,
  `torque`          VARCHAR(50)   DEFAULT NULL,
  `transmission`    VARCHAR(50)   DEFAULT NULL,
  `fuel_type`       VARCHAR(50)   DEFAULT NULL,
  `fuel_efficiency` VARCHAR(50)   DEFAULT NULL,
  `seating`         INT           DEFAULT NULL,
  `drive_type`      VARCHAR(50)   DEFAULT NULL,
  `top_speed`       INT           DEFAULT NULL,
  `acceleration`    DECIMAL(4,1)  DEFAULT NULL COMMENT '0-100 km/h (giây)',
  PRIMARY KEY (`id`),
  KEY `fk_specs_car` (`car_id`),
  CONSTRAINT `fk_specs_car` FOREIGN KEY (`car_id`) REFERENCES `cars`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------
-- 5. bookings – Lịch Lái Thử
-- ---------------------------------------------
CREATE TABLE `bookings` (
  `id`             INT           NOT NULL AUTO_INCREMENT,
  `car_id`         INT           NOT NULL,
  `full_name`      VARCHAR(100)  NOT NULL,
  `email`          VARCHAR(100)  NOT NULL,
  `phone`          VARCHAR(20)   NOT NULL,
  `preferred_date` DATE          NOT NULL,
  `preferred_time` TIME          NOT NULL,
  `message`        TEXT          DEFAULT NULL,
  `status`         ENUM('pending','approved','rejected','done') NOT NULL DEFAULT 'pending',
  `created_at`     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_bookings_car` (`car_id`),
  CONSTRAINT `fk_bookings_car` FOREIGN KEY (`car_id`) REFERENCES `cars`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------
-- 6. contacts – Liên Hệ
-- ---------------------------------------------
CREATE TABLE `contacts` (
  `id`         INT           NOT NULL AUTO_INCREMENT,
  `full_name`  VARCHAR(100)  NOT NULL,
  `email`      VARCHAR(100)  NOT NULL,
  `phone`      VARCHAR(20)   DEFAULT NULL,
  `subject`    VARCHAR(200)  NOT NULL,
  `message`    TEXT          NOT NULL,
  `is_read`    TINYINT(1)    NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------
-- 7. admin_users – Tài Khoản Quản Trị
-- ---------------------------------------------
CREATE TABLE `admin_users` (
  `id`         INT           NOT NULL AUTO_INCREMENT,
  `username`   VARCHAR(50)   NOT NULL,
  `password`   VARCHAR(255)  NOT NULL COMMENT 'Bcrypt hash',
  `full_name`  VARCHAR(100)  NOT NULL,
  `email`      VARCHAR(100)  NOT NULL,
  `role`       ENUM('superadmin','customer') NOT NULL DEFAULT 'customer',
  `last_login` TIMESTAMP     NULL DEFAULT NULL,
  `created_at` TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_username` (`username`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
