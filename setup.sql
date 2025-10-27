-- Setup für TypeFast
CREATE DATABASE IF NOT EXISTS typefast;
USE typefast;

-- Tabelle users (AI für hashwertumwandlung der passwörter)
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(64) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabelle results (bereits vorhanden)
CREATE TABLE IF NOT EXISTS results (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  wpm INT NOT NULL,
  accuracy INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_results_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabelle für Coins
CREATE TABLE IF NOT EXISTS user_coins (
  user_id INT UNSIGNED PRIMARY KEY,
  coins INT DEFAULT 0,
  CONSTRAINT fk_coins_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabelle für Wörter (Sätze werden generiert)
CREATE TABLE IF NOT EXISTS words (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  word VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabelle für Features (kaufbare Funktionen)
CREATE TABLE IF NOT EXISTS user_features (
  user_id INT UNSIGNED NOT NULL,
  feature_name VARCHAR(50) NOT NULL,
  purchased_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, feature_name),
  CONSTRAINT fk_features_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Beispiel-Wörter einfügen
INSERT IGNORE INTO words (word) VALUES
  ('the'), ('quick'), ('brown'), ('fox'), ('jumps'), ('over'), ('lazy'), ('dog'),
  ('point'), ('course'), ('most'), ('eye'), ('what'), ('she'), ('however'), ('become'), ('large'),
  ('cat'), ('mouse'), ('bird'), ('tree'), ('water'), ('fire'), ('earth'), ('wind'),
  ('house'), ('car'), ('bike'), ('road'), ('street'), ('city'), ('town'), ('country'),
  ('hello'), ('world'), ('people'), ('time'), ('day'), ('night'), ('morning'), ('evening'),
  ('sun'), ('moon'), ('star'), ('cloud'), ('rain'), ('snow'), ('wind'), ('storm'),
  ('book'), ('read'), ('write'), ('learn'), ('teach'), ('study'), ('work'), ('play'),
  ('happy'), ('sad'), ('angry'), ('scared'), ('excited'), ('calm'), ('busy'), ('free'),
  ('big'), ('small'), ('tall'), ('short'), ('wide'), ('narrow'), ('long'), ('fast'),
  ('slow'), ('old'), ('new'), ('young'), ('fresh'), ('clean'), ('dirty'), ('nice');
