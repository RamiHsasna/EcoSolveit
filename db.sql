CREATE TABLE users (
  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  ville VARCHAR(100) NOT NULL,
  pays varchar(100) NOT NULL,
  user_type ENUM('user', 'admin') DEFAULT 'user' NOT NULL,
  status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `reclamation` (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  titre VARCHAR(150) NOT NULL,
  description TEXT,
  type ENUM('technical','event','user','other') NOT NULL,
  statut ENUM('pending', 'in_progress', 'solved') DEFAULT 'pending' NOT NULL,
  date_reclamation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) on delete cascade
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE category (
  id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  category_name varchar(100) NOT NULL UNIQUE,
  description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE eco_event (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  event_name VARCHAR(255) NOT NULL,
  description TEXT,
  ville VARCHAR(100) NOT NULL,
  pays VARCHAR(100) NOT NULL,
  category_id INT NOT NULL,
  user_id INT NOT NULL,
  event_date DATETIME NOT NULL,
  participant_limit INT,
  status ENUM('pending', 'approved', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES category(id) on delete cascade,
  FOREIGN KEY (user_id) REFERENCES users(id) on delete cascade
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE event_participation (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  event_id INT NOT NULL,
  user_id INT NOT NULL,
  FOREIGN KEY (event_id) REFERENCES eco_event(id) on delete cascade,
  FOREIGN KEY (user_id) REFERENCES users(id) on delete cascade,
  UNIQUE KEY unique_participation (event_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE event_realization (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  event_id INT NOT NULL,
  status ENUM('not_started', 'in_progress', 'completed') DEFAULT 'not_started',
  start_date TIMESTAMP NOT NULL,
  end_date TIMESTAMP NOT NULL,
  success_rating INT CHECK (success_rating BETWEEN 1 AND 5),
  FOREIGN KEY (event_id) REFERENCES eco_event(id) on delete cascade
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE notifications (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  type ENUM('event_created', 'event_update', 'event_participation','admin_message','system_alert') NOT NULL,
  user_id INT NOT NULL,
  title VARCHAR(150) NOT NULL,
  message TEXT NOT NULL,
  link TEXT,
  is_read BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) on delete cascade
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
