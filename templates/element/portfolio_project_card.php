<?php
declare(strict_types=1);

use App\Domain\Project;
use App\Support\Html;
use App\Support\Language;

/** @var Project $project */
/** @var Language $lang */
$category = Project::categoryLabel($project->category, $lang);
$summary = $project->summary($lang);
?>
<a class="project-card portfolio-card<?= $project->featured ? ' is-featured' : '' ?>" href="<?= $lang->path('/projects/' . $project->slug) ?>"
    data-project data-category="<?= Html::escape(strtolower($project->category)) ?>"
    data-search-text="<?= Html::escape($project->name . ' ' . implode(' ', $project->stack) . ' ' . $project->category . ' ' . $category . ' ' . $summary) ?>"
>
    <div class="portfolio-card-meta">
        <span class="category"><?= Html::escape($category) ?></span>
        <span class="status <?= Html::escape($project->status) ?>"><?= Html::escape($project->statusLabel($lang)) ?></span>
    </div>
    <?php if ($project->featured): ?>
    <span class="portfolio-card-pick"><?= $lang->value('Vybraná případová studie', 'Selected case study') ?></span>
    <?php endif; ?>
    <h2><?= Html::escape($project->name) ?></h2>
    <p><?= Html::escape($summary) ?></p>
    <div class="stack-list">
        <?php foreach (array_slice($project->stack, 0, 3) as $tag): ?>
        <span><?= Html::escape($tag) ?></span>
        <?php endforeach; ?>
    </div>
    <span class="card-action"><?= $lang->value('Detail projektu', 'Project details') ?> <span aria-hidden="true">↗</span></span>
</a>
