#!/usr/bin/env python3
"""Reproducible recovery verification script for OPS03 and OPS06."""
import hashlib
import os
import subprocess
import sys
import time

BACKUP_FILE = r'C:\Ostatní\harfusha-release\db-backup-20261120-151359.sql'
CONTAINER_NAME = 'harfusha-mysql-ci'
TEST_DB = 'harfusha_recovery_test'
ROOT_PASSWORD = 'root-test-only'
USER = 'lab_ci'
PASSWORD = 'lab-test-only'

def run_admin_mysql(sql: str) -> str:
    cmd = [
        'docker', 'exec', '-i', CONTAINER_NAME,
        'mysql', '-uroot', f'-p{ROOT_PASSWORD}', '-e', sql
    ]
    res = subprocess.run(cmd, stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True)
    if res.returncode != 0:
        raise RuntimeError(f"MySQL admin error: {res.stderr}")
    return res.stdout

def run_mysql_cmd(sql: str) -> str:
    cmd = [
        'docker', 'exec', '-i', CONTAINER_NAME,
        'mysql', f'-u{USER}', f'-p{PASSWORD}', '-e', sql
    ]
    res = subprocess.run(cmd, stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True)
    if res.returncode != 0:
        raise RuntimeError(f"MySQL error: {res.stderr}")
    return res.stdout

def restore_backup(sql_file: str, db_name: str) -> float:
    cmd = [
        'docker', 'exec', '-i', CONTAINER_NAME,
        'mysql', f'-u{USER}', f'-p{PASSWORD}', db_name
    ]
    with open(sql_file, 'rb') as f:
        start = time.perf_counter()
        res = subprocess.run(cmd, stdin=f, stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True)
        duration = time.perf_counter() - start
    if res.returncode != 0:
        raise RuntimeError(f"Restore error: {res.stderr}")
    return duration

def main() -> int:
    if not os.path.exists(BACKUP_FILE):
        print(f"FAIL: Backup file not found: {BACKUP_FILE}", file=sys.stderr)
        return 1

    with open(BACKUP_FILE, 'rb') as f:
        data = f.read()
    sha256 = hashlib.sha256(data).hexdigest()
    size = len(data)

    print(f"=== DB Recovery Verification (OPS03 / OPS06) ===")
    print(f"Source file:   {BACKUP_FILE}")
    print(f"File size:     {size} bytes")
    print(f"SHA-256:       {sha256}")
    print(f"Point of time: 2026-11-20 15:13:59 UTC")

    # Step 1: Create isolated temporary database
    print("\n1. Preparing isolated recovery database...")
    run_admin_mysql(f"DROP DATABASE IF EXISTS {TEST_DB}; CREATE DATABASE {TEST_DB} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; GRANT ALL PRIVILEGES ON {TEST_DB}.* TO '{USER}'@'%'; FLUSH PRIVILEGES;")

    # Step 2: Restore backup
    print("2. Restoring database from backup...")
    duration = restore_backup(BACKUP_FILE, TEST_DB)
    print(f"   SQL import duration: {duration:.3f} s")

    # Step 3: Verify migration versions
    print("3. Validating migration versions...")
    migrations_out = run_mysql_cmd(f"USE {TEST_DB}; SELECT id, version, migration_name FROM cake_migrations ORDER BY id;")
    print(migrations_out.strip())
    if '20261117000100' not in migrations_out or 'AddGithubActivityEventTimes' not in migrations_out:
        print("FAIL: Migration 20261117000100 missing!", file=sys.stderr)
        return 1

    # Step 4: Verify critical columns (merged_at, closed_at)
    print("\n4. Validating table schema and columns...")
    cols_out = run_mysql_cmd(f"USE {TEST_DB}; SHOW COLUMNS FROM github_activity LIKE '%_at';")
    print(cols_out.strip())
    if 'merged_at' not in cols_out or 'closed_at' not in cols_out:
        print("FAIL: Columns merged_at or closed_at missing in github_activity!", file=sys.stderr)
        return 1

    # Step 5: Verify record counts
    print("\n5. Checking restored key table counts...")
    counts_sql = f"""
    USE {TEST_DB};
    SELECT 'github_activity' AS tbl, COUNT(*) AS cnt FROM github_activity
    UNION ALL SELECT 'github_public_repos', COUNT(*) FROM github_public_repos
    UNION ALL SELECT 'project_overrides', COUNT(*) FROM project_overrides
    UNION ALL SELECT 'journal_entries', COUNT(*) FROM journal_entries
    UNION ALL SELECT 'editorial_audit', COUNT(*) FROM editorial_audit
    UNION ALL SELECT 'sync_runs', COUNT(*) FROM sync_runs;
    """
    counts_out = run_mysql_cmd(counts_sql)
    print(counts_out.strip())

    # Step 6: Cleanup isolated database
    run_admin_mysql(f"DROP DATABASE IF EXISTS {TEST_DB};")
    print("\n6. Isolated test database cleaned up.")
    print("Recovery verification completed successfully (PASS).")
    return 0

if __name__ == '__main__':
    sys.exit(main())
