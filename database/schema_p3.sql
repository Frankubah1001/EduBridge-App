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
