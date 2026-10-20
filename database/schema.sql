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
-- EduBridge schema v2 PART 2/3: courses/lessons + exam engine (run after p1)
USE edubridge;
CREATE TABLE IF NOT EXISTS courses(
  id INT AUTO_INCREMENT PRIMARY KEY, code VARCHAR(20) UNIQUE NOT NULL,
  title VARCHAR(180) NOT NULL, level_id INT NOT NULL, subject_id INT NOT NULL,
  description TEXT, thumbnail VARCHAR(255) DEFAULT '', language VARCHAR(20) DEFAULT 'en',
  difficulty ENUM('beginner','intermediate','advanced') DEFAULT 'beginner',
  price_kobo INT DEFAULT 0, status ENUM('draft','review','published','archived') DEFAULT 'draft',
  author_id INT DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FULLTEXT(title, description), INDEX(subject_id), INDEX(status)) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS lessons(
  id INT AUTO_INCREMENT PRIMARY KEY, course_id INT NOT NULL,
  topic_id INT DEFAULT NULL, title VARCHAR(180) NOT NULL, body MEDIUMTEXT,
  media_url VARCHAR(255) DEFAULT '', media_type ENUM('none','video','audio','pdf') DEFAULT 'none',
  duration_min INT DEFAULT 10, sort INT DEFAULT 0,
  status ENUM('draft','review','published','archived') DEFAULT 'draft',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(course_id), FULLTEXT(title, body)) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS enrollments(
  user_id INT NOT NULL, course_id INT NOT NULL, progress FLOAT DEFAULT 0,
  enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(user_id, course_id)) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS exams(
  id INT AUTO_INCREMENT PRIMARY KEY, slug VARCHAR(30) UNIQUE NOT NULL,
  name VARCHAR(60) NOT NULL) ENGINE=InnoDB;
INSERT IGNORE INTO exams(slug,name) VALUES
 ('waec','WAEC'),('neco','NECO'),('jamb','JAMB UTME'),('school','School Test');
CREATE TABLE IF NOT EXISTS questions(
  id INT AUTO_INCREMENT PRIMARY KEY, subject_id INT NOT NULL, topic_id INT DEFAULT NULL,
  exam_id INT NOT NULL, year SMALLINT DEFAULT 2024,
  stem TEXT NOT NULL, explanation TEXT,
  difficulty FLOAT DEFAULT 0.0, discrimination FLOAT DEFAULT 1.0,
  qtype ENUM('mcq','true_false','essay') DEFAULT 'mcq',
  status ENUM('draft','review','published','archived') DEFAULT 'published',
  author_id INT DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FULLTEXT(stem), INDEX(subject_id), INDEX(topic_id), INDEX(exam_id), INDEX(status)) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS options(
  id INT AUTO_INCREMENT PRIMARY KEY, question_id INT NOT NULL,
  label CHAR(1) NOT NULL, body VARCHAR(500) NOT NULL, is_correct TINYINT(1) DEFAULT 0,
  sort INT DEFAULT 0, INDEX(question_id),
  FOREIGN KEY(question_id) REFERENCES questions(id) ON DELETE CASCADE) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS papers(
  id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(180) NOT NULL,
  exam_id INT NOT NULL, subject_id INT NOT NULL, duration_min INT DEFAULT 20,
  total_marks INT DEFAULT 100, shuffle TINYINT(1) DEFAULT 1,
  status ENUM('draft','published','archived') DEFAULT 'draft',
  created_by INT DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(exam_id), INDEX(subject_id)) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS paper_items(
  paper_id INT NOT NULL, question_id INT NOT NULL, sort INT DEFAULT 0, marks INT DEFAULT 1,
  PRIMARY KEY(paper_id, question_id)) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS attempts(
  id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, paper_id INT DEFAULT NULL,
  subject_id INT DEFAULT NULL, score FLOAT DEFAULT 0, total INT DEFAULT 0,
  theta FLOAT DEFAULT 0, duration_sec INT DEFAULT 0, flags INT DEFAULT 0,
  detail_json MEDIUMTEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(user_id), INDEX(paper_id)) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS certificates(
  id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, course_id INT DEFAULT NULL,
  paper_id INT DEFAULT NULL, code VARCHAR(40) UNIQUE NOT NULL,
  issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX(user_id)) ENGINE=InnoDB;
-- EduBridge schema v2 PART 3/3: engagement, mentorship, commerce, growth
USE edubridge;
CREATE TABLE IF NOT EXISTS xp_events(
  id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, xp INT NOT NULL,
  reason VARCHAR(160) DEFAULT '', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(user_id)) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS badges(
  id INT AUTO_INCREMENT PRIMARY KEY, slug VARCHAR(60) UNIQUE NOT NULL,
  name VARCHAR(120) NOT NULL, icon VARCHAR(10) DEFAULT '🏅',
  rule_text VARCHAR(255) DEFAULT '') ENGINE=InnoDB;
INSERT IGNORE INTO badges(slug,name,icon,rule_text) VALUES
 ('first-steps','First Steps','🌱','Complete first lesson'),
 ('quiz-rookie','Quiz Rookie','🎯','Finish first quiz'),
 ('streak-3','3-Day Streak','🔥','Study 3 days in a row'),
 ('sharp-shooter','Sharp Shooter','⭐','Score 80%+ in any quiz'),
 ('marathon','Marathon','🏅','Earn 500 XP'),
 ('helper','Community Helper','💬','Post in the forum');
CREATE TABLE IF NOT EXISTS user_badges(
  user_id INT NOT NULL, badge_id INT NOT NULL,
  awarded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(user_id, badge_id)) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS mentors(
  id INT AUTO_INCREMENT PRIMARY KEY, user_id INT DEFAULT NULL,
  name VARCHAR(120) NOT NULL, subject_id INT DEFAULT NULL,
  school VARCHAR(160) DEFAULT '', rating FLOAT DEFAULT 5.0,
  status ENUM('online','offline','busy') DEFAULT 'offline') ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS sessions(
  id INT AUTO_INCREMENT PRIMARY KEY, mentor_id INT NOT NULL,
  title VARCHAR(180) NOT NULL, starts_at DATETIME NOT NULL,
  join_url VARCHAR(255) DEFAULT '', status ENUM('scheduled','live','ended') DEFAULT 'scheduled') ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS messages(
  id INT AUTO_INCREMENT PRIMARY KEY, sender_id INT NOT NULL, receiver_id INT NOT NULL,
  body TEXT, read_at DATETIME DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(sender_id), INDEX(receiver_id)) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS forum_threads(
  id INT AUTO_INCREMENT PRIMARY KEY, author_id INT NOT NULL, subject_id INT DEFAULT NULL,
  title VARCHAR(200) NOT NULL, tag VARCHAR(40) DEFAULT 'General',
  status ENUM('open','flagged','removed','closed') DEFAULT 'open',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FULLTEXT(title)) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS plans(
  id INT AUTO_INCREMENT PRIMARY KEY, slug VARCHAR(30) UNIQUE NOT NULL,
  name VARCHAR(60) NOT NULL, price_kobo INT DEFAULT 0, term VARCHAR(20) DEFAULT 'term',
  features TEXT) ENGINE=InnoDB;
INSERT IGNORE INTO plans(slug,name,price_kobo,term) VALUES
 ('free','Free',0,'forever'),('pro','Pro',150000,'term'),('school','School',0,'custom');
CREATE TABLE IF NOT EXISTS payments(
  id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, plan_id INT NOT NULL,
  amount_kobo INT NOT NULL, provider ENUM('paystack','flutterwave','transfer','voucher') DEFAULT 'paystack',
  reference VARCHAR(80) UNIQUE NOT NULL, status ENUM('pending','success','failed') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX(user_id)) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS vouchers(
  id INT AUTO_INCREMENT PRIMARY KEY, code VARCHAR(40) UNIQUE NOT NULL,
  plan_id INT NOT NULL, max_uses INT DEFAULT 100, used INT DEFAULT 0,
  expires_at DATE DEFAULT NULL, status ENUM('active','expired','disabled') DEFAULT 'active') ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS notifications(
  id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL,
  channel ENUM('inapp','email','sms','push') DEFAULT 'inapp',
  title VARCHAR(160) NOT NULL, body VARCHAR(500) DEFAULT '',
  sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, read_at DATETIME DEFAULT NULL,
  INDEX(user_id)) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS bundles(
  id INT AUTO_INCREMENT PRIMARY KEY, subject_id INT NOT NULL,
  version INT DEFAULT 1, size_kb INT DEFAULT 0, url VARCHAR(255) DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX(subject_id)) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS sync_queue(
  id INT AUTO_INCREMENT PRIMARY KEY, user_id INT DEFAULT NULL,
  op VARCHAR(40) NOT NULL, payload MEDIUMTEXT, synced TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS audit_log(
  id INT AUTO_INCREMENT PRIMARY KEY, actor_id INT DEFAULT NULL,
  action VARCHAR(80) NOT NULL, entity VARCHAR(60) DEFAULT '',
  entity_id INT DEFAULT NULL, meta VARCHAR(500) DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX(actor_id)) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS study_plans(
  id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL,
  subject_id INT DEFAULT NULL, minutes_day INT DEFAULT 45,
  exam_date DATE DEFAULT NULL, plan_json MEDIUMTEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX(user_id)) ENGINE=InnoDB;
ALTER TABLE users ADD CONSTRAINT fk_users_role FOREIGN KEY(role_id) REFERENCES roles(id);
SET FOREIGN_KEY_CHECKS=1;
