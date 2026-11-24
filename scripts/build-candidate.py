#!/usr/bin/env python3
"""Build and package the production release ZIP using Docker PHP 8.5.12 for Composer."""
from __future__ import annotations

import argparse
import hashlib
import os
from pathlib import Path
import shutil
import subprocess
import sys
import tempfile

ROOT = Path(__file__).resolve().parents[1]
sys.path.insert(0, str(ROOT / 'scripts'))

from release_package import stage_application, write_archive
from verify_archive import verify_archive


def main() -> None:
    parser = argparse.ArgumentParser(description='Build production candidate ZIP')
    parser.add_argument('--output', type=Path, default=Path(r'C:\Ostatní\harfusha-release\harfusha-lab-candidate.zip'))
    args = parser.parse_args()

    out = args.output.resolve()
    if out.is_relative_to(ROOT):
        raise SystemExit('Output ZIP must be outside source root')
    out.parent.mkdir(parents=True, exist_ok=True)

    with tempfile.TemporaryDirectory(prefix='harfusha-candidate-stage-') as tmpdir:
        stage_dir = Path(tmpdir)
        container_name = f'harfusha-build-{os.getpid()}'
        try:
            app_dir = stage_dir / 'app'
            app_dir.mkdir(parents=True, exist_ok=True)

            print('1. Staging reviewed application files...')
            count = stage_application(ROOT, app_dir)
            print(f'   Staged {count} files.')

            print('2. Running composer install --no-dev in isolated container filesystem...')
            image = 'harfusha-php85-ci:20261118'
            subprocess.run(['docker', 'rm', '-f', container_name], capture_output=True)
            subprocess.run(['docker', 'run', '-d', '--name', container_name, image, 'sleep', '300'], check=True)
            subprocess.run(['docker', 'exec', container_name, 'mkdir', '-p', '/app'], check=True)
            subprocess.run(['docker', 'cp', f'{app_dir}/.', f'{container_name}:/app/'], check=True)

            subprocess.run([
                'docker', 'exec', '-w', '/app', container_name,
                'composer', 'install', '--no-dev', '--prefer-dist',
                '--optimize-autoloader', '--no-interaction', '--no-progress',
            ], check=True)

            print('3. Checking platform requirements...')
            subprocess.run([
                'docker', 'exec', '-w', '/app', container_name,
                'composer', 'check-platform-reqs', '--no-dev',
            ], check=True)

            print('   Copying installed vendor back from container...')
            subprocess.run(['docker', 'cp', f'{container_name}:/app/vendor', f'{app_dir}/vendor'], check=True)
            subprocess.run(['docker', 'rm', '-f', container_name], capture_output=True)

            print('4. Writing archive...')
            files_count, digest = write_archive(app_dir, out)
            print(f'   Archive created: {out}')
            print(f'   Files in archive: {files_count}')
            print(f'   SHA-256: {digest}')

            print('5. Verifying production archive...')
            verified_count = verify_archive(out, variant='production')
            print(f'   Verified {verified_count} hashed files against SHA256SUMS.')

        finally:
            subprocess.run(['docker', 'rm', '-f', container_name], capture_output=True)


if __name__ == '__main__':
    main()
