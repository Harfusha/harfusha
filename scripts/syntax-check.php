<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$dirs = ['src','config','templates','tests','webroot','bin'];
$count = 0;
$fail = 0;
foreach ($dirs as $dir) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/' . $dir, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if (!$file instanceof SplFileInfo || $file->getExtension() !== 'php') {
            continue;
        }
        $cmd = escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($file->getPathname()) . ' 2>&1';
        exec($cmd, $output, $code);
        if ($code !== 0) {
            fwrite(STDERR, implode("\n", $output) . "\n");
            $fail++;
        }
        $output = [];
        $count++;
    }
}
printf("Checked %d PHP files, failures: %d\n", $count, $fail);
exit($fail === 0 ? 0 : 1);
