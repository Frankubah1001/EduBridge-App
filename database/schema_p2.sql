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
