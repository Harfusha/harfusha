-- MySQL 8.0+ or MariaDB 10.6+; UTF-8 end to end.
CREATE TABLE IF NOT EXISTS project_overrides (
  slug VARCHAR(80) NOT NULL PRIMARY KEY,
  summary_cs TEXT NOT NULL,
  summary_en TEXT NOT NULL,
  problem_cs TEXT NULL,
  problem_en TEXT NULL,
  solution_cs TEXT NULL,
  solution_en TEXT NULL,
  status VARCHAR(20) NOT NULL,
  updated_at DATETIME NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS journal_entries (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title_cs VARCHAR(240) NOT NULL,
  title_en VARCHAR(240) NOT NULL,
  body_cs TEXT NOT NULL,
  body_en TEXT NOT NULL,
  category VARCHAR(40) NOT NULL,
  source_url VARCHAR(255) NOT NULL DEFAULT '',
  is_public TINYINT(1) NOT NULL DEFAULT 1,
  published_at DATETIME NOT NULL,
  INDEX idx_journal_public (is_public, published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS github_activity (
  source_url VARCHAR(255) NOT NULL PRIMARY KEY,
  title VARCHAR(250) NOT NULL,
  repository VARCHAR(180) NOT NULL,
  kind VARCHAR(10) NOT NULL,
  state VARCHAR(10) NOT NULL,
  published_at DATETIME NOT NULL,
  merged_at DATETIME NULL DEFAULT NULL,
  closed_at DATETIME NULL DEFAULT NULL,
  INDEX idx_activity_time (published_at),
  INDEX idx_activity_kind_state (kind, state, published_at),
  INDEX idx_activity_repo (repository)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sync_runs (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  source VARCHAR(40) NOT NULL,
  item_count INT UNSIGNED NOT NULL,
  completed_at DATETIME NOT NULL,
  INDEX idx_sync_run (source, completed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS github_public_repos (
  repository VARCHAR(180) NOT NULL PRIMARY KEY,
  description VARCHAR(350) NOT NULL DEFAULT '',
  repo_url VARCHAR(255) NOT NULL,
  pushed_at DATETIME NOT NULL,
  INDEX idx_public_pushed (pushed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Append-only editorial mutation receipts, no secrets or full editorial content.
CREATE TABLE IF NOT EXISTS editorial_audit (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  entity_type VARCHAR(32) NOT NULL,
  entity_key VARCHAR(100) NOT NULL,
  action VARCHAR(32) NOT NULL,
  content_digest CHAR(64) NOT NULL,
  occurred_at DATETIME NOT NULL,
  INDEX idx_editorial_audit_date (occurred_at),
  INDEX idx_editorial_audit_subject (entity_type, entity_key, occurred_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
