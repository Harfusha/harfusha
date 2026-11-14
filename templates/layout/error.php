<?php
declare(strict_types=1);
/** @var \Cake\View\View $this */
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title><?= htmlspecialchars($this->fetch('title') ?: 'Harfusha Lab', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></title>
  <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
  <main id="main-content"><?= $this->fetch('content') ?></main>
</body>
</html>
