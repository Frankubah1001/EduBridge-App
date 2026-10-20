-- EduBridge AI — Production MySQL schema v2 (PART 1/3: identity + taxonomy)
CREATE DATABASE IF NOT EXISTS edubridge CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE edubridge;
SET FOREIGN_KEY_CHECKS=0;
CREATE TABLE IF NOT EXISTS roles(
  id INT AUTO_INCREMENT PRIMARY KEY, slug VARCHAR(30) UNIQUE NOT NULL,
  name VARCHAR(60) NOT NULL, description VARCHAR(255) DEFAULT '') ENGINE=InnoDB;
INSERT IGNORE INTO roles(slug,name) VALUES
 ('student','Student'),('teacher','Teacher'),('parent','Parent'),
 ('mentor','Mentor'),('admin','Admin'),('school_admin','School Admin');
CREATE TABLE IF NOT EXISTS schools(
  id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(160) NOT NULL,
  code VARCHAR(30) UNIQUE NOT NULL, address VARCHAR(255) DEFAULT '',
  lga VARCHAR(80) DEFAULT '', state VARCHAR(40) DEFAULT '',
  contact_name VARCHAR(120) DEFAULT '', contact_phone VARCHAR(30) DEFAULT '',
  plan ENUM('free','pro','enterprise') DEFAULT 'free',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS users(
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL, email VARCHAR(160) UNIQUE NOT NULL,
  phone VARCHAR(30) DEFAULT '', password_hash VARCHAR(255) DEFAULT '',
  role_id INT NOT NULL DEFAULT 1, class_level VARCHAR(40) DEFAULT '',
  school_id INT DEFAULT NULL, avatar VARCHAR(255) DEFAULT '',
  xp INT DEFAULT 0, streak INT DEFAULT 0, last_active DATE DEFAULT NULL,
  status ENUM('active','suspended','deleted') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX(role_id), INDEX(school_id), INDEX(email)) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS guardians(
  id INT AUTO_INCREMENT PRIMARY KEY, parent_user_id INT NOT NULL,
  student_user_id INT NOT NULL, relation VARCHAR(40) DEFAULT 'parent',
  UNIQUE(parent_user_id, student_user_id)) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS consents(
  id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL,
  type ENUM('ndpr','minor_guardian','marketing','media') NOT NULL,
  granted TINYINT(1) DEFAULT 1, granted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(user_id)) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS levels(
  id INT AUTO_INCREMENT PRIMARY KEY, slug VARCHAR(30) UNIQUE NOT NULL,
  name VARCHAR(80) NOT NULL, sort INT DEFAULT 0) ENGINE=InnoDB;
INSERT IGNORE INTO levels(slug,name,sort) VALUES
 ('primary','Primary',1),('jss','Junior Secondary (JSS 1-3)',2),
 ('sss','Senior Secondary (SS 1-3)',3),('waec','WAEC / NECO',4),('jamb','JAMB UTME',5);
CREATE TABLE IF NOT EXISTS subjects(
  id INT AUTO_INCREMENT PRIMARY KEY, level_id INT NOT NULL,
  slug VARCHAR(40) NOT NULL, name VARCHAR(80) NOT NULL,
  icon VARCHAR(10) DEFAULT '📘', sort INT DEFAULT 0,
  UNIQUE(level_id, slug), INDEX(level_id)) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS topics(
  id INT AUTO_INCREMENT PRIMARY KEY, subject_id INT NOT NULL,
  slug VARCHAR(60) NOT NULL, name VARCHAR(120) NOT NULL, sort INT DEFAULT 0,
  UNIQUE(subject_id, slug), INDEX(subject_id)) ENGINE=InnoDB;
