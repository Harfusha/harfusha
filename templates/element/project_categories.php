<?php
declare(strict_types=1);

use App\Support\Html;

$categoryStories = [
    'AI & Developer Tools' => $lang->value('Jak agent zjistí, co má v repozitáři číst?', 'How does an agent know what to read in a repository?'),
    'Desktop & Tools' => $lang->value('Když silný engine potřebuje srozumitelné ovládání.', 'When a capable engine needs an understandable control surface.'),
    'Infrastructure' => $lang->value('Kdo spravuje služby, hosty a bezpečné změny?', 'How do services, hosts and guarded changes fit together?'),
    'Games & Simulation' => $lang->value('Jak pravidla světa přežijí cestu od nápadu ke hře?', 'How do world rules survive the path from design to game?'),
    '3D & Engineering' => $lang->value('Co sken skutečně dokazuje o součástce?', 'What does a scan really prove about a part?'),
    'Web Applications' => $lang->value('Co se stane, když úloha trvá déle než HTTP požadavek?', 'What happens when a job outlives its HTTP request?'),
    'Media & Apps' => $lang->value('Přidává další aplikace něco, co už nestačí vyřešit ta stávající?', 'Does another app add something the existing one cannot do?'),
    'Bots & Automation' => $lang->value('Kdy je chat příkaz dobrý nápad pro správu služby?', 'When is a chat command a good way to operate a service?'),
    'Archive' => $lang->value('Co se z nápadu stalo a proč zůstal archivovaný?', 'What became of the idea, and why did it remain archived?'),
];
?>
<section class="shell category-routes" aria-label="<?= $lang->value('Vyber cestu podle otázky', 'Choose a route by question') ?>">
 <div class="category-routes-heading"><span class="eyebrow"><?= $lang->value('NEPŘEHLEDÁVEJ PODLE TECHNOLOGIE', 'DON’T START WITH A TECH STACK') ?></span><p><?= $lang->value('Začni otázkou, která tě sem přivedla.', 'Start with the question that brought you here.') ?></p></div>
 <div class="category-routes-grid" data-category-routes>
  <?php foreach (array_unique(array_map(static fn ($p) => $p->category, $projects)) as $category): ?>
   <button type="button" data-category-shortcut="<?= Html::escape(strtolower($category)) ?>" aria-pressed="false"><span><?= Html::escape(App\Domain\Project::categoryLabel($category, $lang)) ?></span><strong><?= Html::escape($categoryStories[$category] ?? $lang->value('Která část problému stojí za vlastní řešení?', 'Which part of the problem deserves a solution?')) ?></strong><small><?= $lang->value('Otevřít tyto projekty', 'Show these projects') ?> →</small></button>
  <?php endforeach; ?>
 </div>
</section>
