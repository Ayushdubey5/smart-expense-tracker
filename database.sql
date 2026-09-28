-- ============================================================
-- Smart Student Expense Tracker - Database Schema
-- IGNOU BCSP-064 Project
-- ============================================================

DROP DATABASE IF EXISTS expense_tracker;
CREATE DATABASE expense_tracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE expense_tracker;

-- ---------------- USERS ----------------
CREATE TABLE users (
    user_id       INT AUTO_INCREMENT PRIMARY KEY,
    full_name     VARCHAR(100) NOT NULL,
    email         VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role          ENUM('student','admin') NOT NULL DEFAULT 'student',
    status        ENUM('active','blocked') NOT NULL DEFAULT 'active',
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------- CATEGORIES ----------------
CREATE TABLE categories (
    category_id   INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(60) NOT NULL UNIQUE,
    icon          VARCHAR(10) DEFAULT '💰',
    color         VARCHAR(30) DEFAULT '#8b5cf6',
    status        ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------- EXPENSES ----------------
CREATE TABLE expenses (
    expense_id     INT AUTO_INCREMENT PRIMARY KEY,
    user_id        INT NOT NULL,
    category_id    INT NOT NULL,
    amount         DECIMAL(10,2) NOT NULL,
    description    VARCHAR(255),
    expense_date   DATE NOT NULL,
    payment_method ENUM('UPI','Cash','Card','NetBanking','Other') DEFAULT 'UPI',
    notes          TEXT,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(category_id),
    INDEX idx_user_date (user_id, expense_date)
) ENGINE=InnoDB;

-- ---------------- BUDGETS (per month) ----------------
CREATE TABLE budgets (
    budget_id    INT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT NOT NULL,
    budget_month TINYINT NOT NULL,
    budget_year  SMALLINT NOT NULL,
    amount       DECIMAL(10,2) NOT NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_month (user_id, budget_month, budget_year),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------- SAVING GOALS ----------------
CREATE TABLE saving_goals (
    goal_id       INT AUTO_INCREMENT PRIMARY KEY,
    user_id       INT NOT NULL,
    goal_name     VARCHAR(100) NOT NULL,
    target_amount DECIMAL(10,2) NOT NULL,
    target_date   DATE,
    status        ENUM('active','completed','cancelled') DEFAULT 'active',
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------- GOAL CONTRIBUTIONS ----------------
CREATE TABLE goal_contributions (
    contribution_id   INT AUTO_INCREMENT PRIMARY KEY,
    goal_id           INT NOT NULL,
    amount            DECIMAL(10,2) NOT NULL,
    contribution_date DATE NOT NULL,
    notes             VARCHAR(255),
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (goal_id) REFERENCES saving_goals(goal_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- SEED DATA
-- ============================================================

-- Admin account (password = Admin@123)
INSERT INTO users (full_name, email, password_hash, role) VALUES
('System Admin', 'admin@moneo.local',
 '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1kQxjKgQZQz9v8f1QK6nJk5rL8VwCka', 'admin');

-- Demo student (password = Student@123)
INSERT INTO users (full_name, email, password_hash, role) VALUES
('Ayush Sharma', 'ayush@student.local',
 '$2y$10$KIXQx3D5H4z6L1lJk5oN.uZfB0Q0Q1zQ2XvJqjKp9r5Y5RxT8uJgW', 'student');

-- Default categories
INSERT INTO categories (category_name, icon, color) VALUES
('Food & Dining', '🍜', '#f6c96b'),
('Education',     '📚', '#f28b82'),
('Travel',        '🚇', '#7cb2ff'),
('Shopping',      '🛍️', '#b18aff'),
('Entertainment', '🎬', '#ff9bd2'),
('Bills',         '💡', '#ffa870'),
('Health',        '💊', '#5ed1c5'),
('Other',         '💰', '#7ed9a4');