-- ScriptDeploy database schema (cPanel/phpMyAdmin friendly)
SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS developer_clients;
DROP TABLE IF EXISTS admin_ip_whitelist;
DROP TABLE IF EXISTS build_jobs;
DROP TABLE IF EXISTS developer_wallet_transactions;
DROP TABLE IF EXISTS developer_wallets;
DROP TABLE IF EXISTS developer_subscriptions;
DROP TABLE IF EXISTS developer_packages;
DROP TABLE IF EXISTS warns;
DROP TABLE IF EXISTS tickets;
DROP TABLE IF EXISTS invoices;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS projects;
DROP TABLE IF EXISTS brands;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(80) NOT NULL UNIQUE,
  email VARCHAR(190) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  support_number VARCHAR(30) NOT NULL,
  role ENUM('buyer','developer','admin') NOT NULL DEFAULT 'buyer',
  status ENUM('active','banned') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE developer_packages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(30) NOT NULL UNIQUE,
  name VARCHAR(80) NOT NULL,
  price_monthly DECIMAL(10,2) NOT NULL,
  brand_limit INT NOT NULL,
  script_limit INT NOT NULL,
  install_limit INT NOT NULL,
  allow_custom_domain TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE developer_subscriptions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  developer_id INT UNSIGNED NOT NULL,
  package_id INT UNSIGNED NOT NULL,
  package_code VARCHAR(30) NOT NULL,
  started_at DATE NOT NULL,
  expires_at DATE NOT NULL,
  status ENUM('active','expired') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_sub_developer FOREIGN KEY (developer_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_sub_package FOREIGN KEY (package_id) REFERENCES developer_packages(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE developer_wallets (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  developer_id INT UNSIGNED NOT NULL UNIQUE,
  balance DECIMAL(12,2) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_wallet_developer FOREIGN KEY (developer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE developer_wallet_transactions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  developer_id INT UNSIGNED NOT NULL,
  type ENUM('deposit','charge') NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  note VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_wallet_tx_developer FOREIGN KEY (developer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE brands (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  developer_id INT UNSIGNED NOT NULL,
  brand_name VARCHAR(140) NOT NULL,
  subdomain VARCHAR(120) NOT NULL UNIQUE,
  tagline VARCHAR(255) NULL,
  status ENUM('active','deactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_brands_developer FOREIGN KEY (developer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE projects (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  developer_id INT UNSIGNED NOT NULL,
  brand_id INT UNSIGNED NOT NULL,
  category_id INT UNSIGNED NOT NULL,
  name VARCHAR(150) NOT NULL,
  description TEXT NOT NULL,
  preview_link VARCHAR(255) NOT NULL,
  base_price DECIMAL(10,2) NOT NULL,
  script_file VARCHAR(255) NULL,
  sql_file VARCHAR(255) NULL,
  status ENUM('active','deactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_projects_developer FOREIGN KEY (developer_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_projects_brand FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE CASCADE,
  CONSTRAINT fk_projects_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  project_id INT UNSIGNED NOT NULL,
  website_name VARCHAR(160) NOT NULL,
  domain_type ENUM('subdomain','custom') NOT NULL,
  domain_name VARCHAR(190) NOT NULL,
  duration_months INT UNSIGNED NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  late_fee DECIMAL(10,2) NOT NULL DEFAULT 0,
  total_price DECIMAL(10,2) NOT NULL,
  status ENUM('active','expired') NOT NULL DEFAULT 'active',
  build_status ENUM('queued','building','delivered') NOT NULL DEFAULT 'delivered',
  delivery_note TEXT NULL,
  source_subdomain VARCHAR(120) NULL,
  expires_at DATE NOT NULL,
  deployed_url VARCHAR(255) NOT NULL,
  admin_url VARCHAR(255) NOT NULL,
  admin_username VARCHAR(80) NOT NULL,
  admin_password VARCHAR(120) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_orders_project FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE build_jobs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL UNIQUE,
  status ENUM('queued','building','done','failed') NOT NULL DEFAULT 'queued',
  progress INT NOT NULL DEFAULT 0,
  message VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_build_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE invoices (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  late_fee DECIMAL(10,2) NOT NULL DEFAULT 0,
  total DECIMAL(10,2) NOT NULL,
  due_date DATE NOT NULL,
  status ENUM('paid','due') NOT NULL DEFAULT 'due',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_invoices_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE admin_ip_whitelist (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ip_address VARCHAR(45) NOT NULL UNIQUE,
  label VARCHAR(120) NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tickets (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  role ENUM('buyer','developer') NOT NULL,
  subject VARCHAR(160) NOT NULL,
  message TEXT NOT NULL,
  status ENUM('open','closed') NOT NULL DEFAULT 'open',
  admin_reply TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_tickets_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE warns (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  developer_id INT UNSIGNED NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_warns_developer FOREIGN KEY (developer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE developer_clients (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  developer_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  project_id INT UNSIGNED NOT NULL,
  order_id INT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_dc_developer FOREIGN KEY (developer_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_dc_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_dc_project FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
  CONSTRAINT fk_dc_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO categories (id, name) VALUES (1,'Ecommerce') ON DUPLICATE KEY UPDATE name=VALUES(name);
INSERT INTO categories (id, name) VALUES (2,'Portfolio') ON DUPLICATE KEY UPDATE name=VALUES(name);
INSERT INTO categories (id, name) VALUES (3,'SaaS') ON DUPLICATE KEY UPDATE name=VALUES(name);
INSERT INTO categories (id, name) VALUES (4,'Agency') ON DUPLICATE KEY UPDATE name=VALUES(name);


INSERT INTO developer_packages (code, name, price_monthly, brand_limit, script_limit, install_limit, allow_custom_domain)
VALUES ('starter','Starter',999,1,5,20,0)
ON DUPLICATE KEY UPDATE name=VALUES(name), price_monthly=VALUES(price_monthly), brand_limit=VALUES(brand_limit), script_limit=VALUES(script_limit), install_limit=VALUES(install_limit), allow_custom_domain=VALUES(allow_custom_domain);

INSERT INTO developer_packages (code, name, price_monthly, brand_limit, script_limit, install_limit, allow_custom_domain)
VALUES ('pro','Pro',2999,3,20,200,0)
ON DUPLICATE KEY UPDATE name=VALUES(name), price_monthly=VALUES(price_monthly), brand_limit=VALUES(brand_limit), script_limit=VALUES(script_limit), install_limit=VALUES(install_limit), allow_custom_domain=VALUES(allow_custom_domain);

INSERT INTO developer_packages (code, name, price_monthly, brand_limit, script_limit, install_limit, allow_custom_domain)
VALUES ('enterprise','Enterprise',7999,-1,-1,-1,1)
ON DUPLICATE KEY UPDATE name=VALUES(name), price_monthly=VALUES(price_monthly), brand_limit=VALUES(brand_limit), script_limit=VALUES(script_limit), install_limit=VALUES(install_limit), allow_custom_domain=VALUES(allow_custom_domain);

INSERT INTO admin_ip_whitelist (ip_address, label, status) VALUES ('127.0.0.1', 'Localhost default admin access', 'active') ON DUPLICATE KEY UPDATE label=VALUES(label), status=VALUES(status);

SET FOREIGN_KEY_CHECKS = 1;
