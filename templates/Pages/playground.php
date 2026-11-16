<?php
declare(strict_types=1);
use App\Support\Language;
/** @var Language $lang */
?>
<section class="page-hero shell playground-hero">
 <p class="eyebrow">FIELD NOTE / LOCAL AI HUB</p>
 <h1><?= $lang->value('Kontekst, který si můžeš ověřit.', 'Context you can verify.') ?></h1>
 <p><?= $lang->value('Změň typ otázky a stav repozitáře. Uvidíš, proč přesný dotaz začíná indexem, kdy je potřeba širší kontext a kde končí role modelu.', 'Change the question and repository state. See why an exact lookup starts with the index, when broader context helps, and where the model’s role ends.') ?></p>
 <div class="hero-buttons"><a class="btn btn-primary" href="<?= $lang->path('/projects/local-ai-hub#project-experience') ?>"><?= $lang->value('Příběh Local AI Hubu', 'The Local AI Hub case study') ?> →</a><a class="btn btn-ghost" href="<?= $lang->path('/decisions') ?>"><?= $lang->value('Prohlédnout rozhodnutí', 'Explore the decisions') ?> ↗</a></div>
</section>
<section class="section shell context-workbench" data-context-workbench>
 <header class="section-heading"><div><span class="eyebrow">CHANGE A CONSTRAINT / INSPECT THE CONSEQUENCE</span><h2><?= $lang->value('Jedna otázka. Dvě cesty.', 'One question. Two routes.') ?></h2><p><?= $lang->value('Hraj si s modelem, ne se skutečným repozitářem. Výsledek je deterministický a celý zůstává v prohlížeči.', 'Explore a model, not a real repository. The result is deterministic and stays in this browser.') ?></p></div></header>
 <div class="context-workbench-grid">
  <form class="context-controls" data-context-form>
   <fieldset><legend><?= $lang->value('1 · Stav repozitáře', '1 · Repository state') ?></legend><label><input type="radio" name="context-root" value="indexed" checked><?= $lang->value('Už zaregistrovaný a zpracovaný', 'Already registered and preprocessed') ?></label><label><input type="radio" name="context-root" value="new"><?= $lang->value('Nový stabilní kořen', 'New stable root') ?></label></fieldset>
   <fieldset><legend><?= $lang->value('2 · Typ otázky', '2 · Question type') ?></legend><label><input type="radio" name="context-question" value="exact" checked><?= $lang->value('Znám přesný symbol', 'I know the exact symbol') ?></label><label><input type="radio" name="context-question" value="broad"><?= $lang->value('Řeším širší změnu', 'I am exploring a broader change') ?></label></fieldset>
   <label class="context-toggle"><input type="checkbox" name="context-model"> <?= $lang->value('Přidat volitelnou lokální syntézu', 'Add optional local synthesis') ?></label>
   <button class="btn btn-primary" type="submit"><?= $lang->value('Zobrazit tok', 'Show the route') ?> →</button>
  </form>
  <div class="context-trace" aria-live="polite" aria-atomic="true"><div class="context-trace-head"><span class="eyebrow"><?= $lang->value('VÝSLEDEK MODELU', 'MODEL OUTPUT') ?></span><span class="simulation-badge"><?= $lang->value('SIMULACE', 'SIMULATION') ?></span></div><h3 data-context-title><?= $lang->value('Přesný dotaz, připravený index', 'Exact query, prepared index') ?></h3><ol data-context-steps><li><?= $lang->value('Code index najde deklaraci a volání.', 'The code index finds the declaration and callers.') ?></li><li><?= $lang->value('Načte se jen potřebný zdrojový výřez.', 'Only the needed source slice is retrieved.') ?></li><li><?= $lang->value('Změnu ověří test spuštěný hostem.', 'The host runs a test to verify the change.') ?></li></ol><p data-context-boundary><?= $lang->value('Model může pomoci s uvažováním; test ani kontrolu zdroje nenahrazuje.', 'A model can help reason; it does not replace source review or tests.') ?></p></div>
 </div>
 <p class="context-honesty"><?= $lang->value('Asynchronní preprocessing může už zpracovaný kořen znovu použít. Pokud běží, Hub nepolluje ani neodesílá stejný požadavek znovu. Výsledek modelu není testovací důkaz.', 'Asynchronous preprocessing can reuse an indexed root. If work is already in progress, the Hub does not poll or resend the same request. Model output is not test evidence.') ?></p>
</section>