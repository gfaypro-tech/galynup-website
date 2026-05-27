-- CV Builder — Schéma MySQL 5.7
-- À exécuter une seule fois dans ta base OVH

-- Base de connaissance (expériences, compétences, formations, imports libres)
CREATE TABLE IF NOT EXISTS cv_knowledge (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type ENUM('import','experience','competence','formation','autre') NOT NULL DEFAULT 'autre',
  title VARCHAR(255) NOT NULL DEFAULT '',
  content LONGTEXT NOT NULL,
  meta_json TEXT DEFAULT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Candidatures
CREATE TABLE IF NOT EXISTS cv_applications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  company VARCHAR(255) NOT NULL DEFAULT '',
  position VARCHAR(255) NOT NULL DEFAULT '',
  job_posting LONGTEXT NOT NULL,
  step_current TINYINT(1) NOT NULL DEFAULT 1,
  analysis_result LONGTEXT DEFAULT NULL,
  matching_result LONGTEXT DEFAULT NULL,
  cv_content LONGTEXT DEFAULT NULL,
  status ENUM('draft','analysis','matching','dialogue','generating','completed') NOT NULL DEFAULT 'draft',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Questions / réponses du dialogue
CREATE TABLE IF NOT EXISTS cv_dialogue (
  id INT AUTO_INCREMENT PRIMARY KEY,
  application_id INT NOT NULL,
  question_order INT NOT NULL DEFAULT 0,
  question TEXT NOT NULL,
  answer LONGTEXT DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (application_id) REFERENCES cv_applications(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
