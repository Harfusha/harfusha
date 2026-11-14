#!/usr/bin/env python3
"""Fail-closed, vendor-inclusive release builder for a machine running PHP 8.5.

Only builds after the real Composer, PHPStan, PHPUnit, MySQL and client test suite
returns success. This cannot be run in the isolated authoring environment.
"""
from __future__ import annotations
from pathlib import Path

from release_package import stage_application, write_archive
import argparse
import secrets
import os
import shutil
import tempfile
import subprocess
import sys

ROOT=Path(__file__).resolve().parents[1]


def require(command: list[str]) -> None:
    print('Gate:', ' '.join(command), flush=True)
    subprocess.run(command, cwd=ROOT, check=True)


def main() -> None:
    parser=argparse.ArgumentParser(description='Verify, build and archive the production PHP 8.5 application')
    parser.add_argument('--output',type=Path,required=True)
    args=parser.parse_args()
    for file in ('composer.lock','vendor/autoload.php'):
        if not (ROOT/file).is_file():
            raise SystemExit(f'{file} is missing. Install Composer dependencies and commit the lockfile first.')
    php=subprocess.run(['php','-r','echo PHP_VERSION_ID;'],capture_output=True,text=True,check=True)
    if not (80512<=int(php.stdout.strip())<80600):
        raise SystemExit('Build requires patched PHP 8.5.12+ within the 8.5 branch')
    php_requirements="foreach(['intl','mbstring','pdo_mysql','simplexml','openssl'] as $e){if(!extension_loaded($e)){fwrite(STDERR,'Missing '.$e.PHP_EOL);exit(1);}}"
    require(['php','-r',php_requirements])
    if not shutil.which('composer') or not shutil.which('node'):
        raise SystemExit('Composer and Node.js needed to run release gates on the build machine')
    # All checks must run BEFORE uninstalling dev packages, on the exact source
    # revision that will be archived. DB tests require a disposable MySQL DB.
    require(['composer','validate','--strict'])
    require(['composer','audit','--locked'])
    if os.environ.get('APP_ENV') != 'testing' or os.environ.get('APP_URL') != 'http://localhost:8765':
        raise SystemExit('Release gates require APP_ENV=testing and APP_URL=http://localhost:8765 (disposable DB)')
    # Never let a release-builder run migrations against a database whose name
    # might point to production. Explicit disposable database naming required.
    database = os.environ.get('DB_NAME', '')
    confirmation = os.environ.get('LAB_TEST_DB_CONFIRM', '')
    if not database.endswith(('_test', '_testing', '_ci')) or not database or database != confirmation:
        raise SystemExit('Release gate requires disposable DB_NAME and matching LAB_TEST_DB_CONFIRM')
    if os.environ.get('DB_HOST') not in ('localhost', '127.0.0.1', '::1', 'db'):
        raise SystemExit('Release gate requires local/container DB_HOST')
    if os.environ.get('DB_USER', '').lower() in ('', 'root', 'admin', 'administrator'):
        raise SystemExit('Release gate requires a dedicated unprivileged DB_USER')
    require(['php','bin/cake.php','migrations','migrate','--no-interaction'])
    require(['composer','quality'])
    print('Gate: start actual CakePHP HTTP smoke with ephemeral admin credentials', flush=True)
    smoke_env = os.environ.copy()
    smoke_env['LAB_SMOKE_ADMIN_PASSWORD'] = secrets.token_urlsafe(36)
    smoke_env['ADMIN_PASSWORD_HASH'] = subprocess.run(
        ['php', '-r', 'echo password_hash(getenv("LAB_SMOKE_ADMIN_PASSWORD"), PASSWORD_DEFAULT);'],
        env=smoke_env, text=True, capture_output=True, check=True,
    ).stdout
    with (ROOT / 'tmp/http-smoke.log').open('w', encoding='utf-8') as log:
        server = subprocess.Popen(['php','-S','127.0.0.1:8765','-t','webroot','webroot/index.php'],
            cwd=ROOT, env=smoke_env, stdout=log, stderr=subprocess.STDOUT)
        try:
            subprocess.run(['python3', 'scripts/http-smoke.py'], cwd=ROOT, env=smoke_env, check=True)
        finally:
            server.terminate()
            try:
                server.wait(timeout=5)
            except subprocess.TimeoutExpired:
                server.kill()
                server.wait(timeout=5)
    require(['python3','scripts/audit-public.py'])
    require(['python3','-m','unittest','discover','-s','tests/Python','-v'])
    # Human review receipt is mandatory; never fabricate this in automated CI.
    require(['python3','scripts/review-public-content.py','--verify'])
    require(['python3','scripts/audit-rendered.py'])
    require(['node','--check','webroot/assets/js/app.js'])
    require(['node','--check','webroot/assets/js/lab-core.js'])
    require(['node','--test','tests/Client/lab-core.test.cjs'])
    # Real Chromium DOM smoke (renders offline templates, not CakePHP HTTP E2E).
    require(['python3','tests/Browser/smoke.py'] + (['--chromium', os.environ['CHROMIUM_PATH']] if os.environ.get('CHROMIUM_PATH') else []))
    # Optional TypeScript checker is a required release gate, not hosted code.
    tsc=ROOT/'node_modules/.bin/tsc'
    if not tsc.is_file():
        raise SystemExit('Install typescript@5.8.3 on build machine for strict client-core check')
    require([str(tsc),'--noEmit','--allowJs','--checkJs','--strict','--target','ES2022',
             '--lib','ES2022,DOM','webroot/assets/js/lab-core.js'])
    require(['python3','scripts/review-public-content.py','--verify'])
    out=args.output.resolve()
    if out.is_relative_to(ROOT):
        raise SystemExit('Release ZIP must be written outside the application tree')
    # Never mutate the audited vendor directory or risk shipping development
    # dependencies. Build the deployable tree from the reviewed source/lock
    # inside a disposable directory and install only production dependencies.
    with tempfile.TemporaryDirectory(prefix='harfusha-prod-build-') as temp:
        stage = Path(temp) / 'app'
        stage.mkdir(parents=True)
        count = stage_application(ROOT, stage)
        print(f'Gate: staging {count} reviewed source files in isolated directory', flush=True)
        print('Gate: Composer production-only install in staging', flush=True)
        subprocess.run(['composer','install','--no-dev','--prefer-dist',
                        '--optimize-autoloader','--no-interaction','--no-progress'],
                       cwd=stage, check=True)
        subprocess.run(['composer','check-platform-reqs','--no-dev'], cwd=stage, check=True)
        files, digest = write_archive(stage, out)
    print(f'Production ZIP: {out}, files={files}, SHA256={digest}')

if __name__=='__main__':main()
