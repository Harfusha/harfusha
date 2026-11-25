<?php
declare(strict_types=1);

use App\Support\Html;
use App\Support\Language;

/** @var Language $lang */
?>
<section class="shell project-preview lab-shell" id="mesh-to-cad-explorer" aria-labelledby="mesh2cad-lab-title">
    <div class="lab-header">
        <div>
            <span class="eyebrow"><?= $lang->value('INTERAKTIVNÍ EXPERIMENT · SCAN2CAD STUDIO', 'INTERACTIVE LAB · SCAN2CAD STUDIO') ?></span>
            <h2 id="mesh2cad-lab-title"><?= $lang->value('Mesh to CAD Explorer: Fáze geometrické rekonstrukce', 'Mesh to CAD Explorer: Geometric Reconstruction Stages') ?></h2>
            <p><?= $lang->value('Interaktivní průchod fázemi převodu zašuměného 3D skenu na analytická CAD tělesa. Ukazuje rozdíl mezi polygonální sítí a parametrickou B-Rep geometrií.', 'Interactive walkthrough of scan-to-CAD reconstruction pipeline stages, comparing raw polygonal triangle meshes against parametric analytical B-Rep geometry.') ?></p>
        </div>
        <div class="lab-badge-group">
            <span class="badge badge-neutral"><?= $lang->value('Deterministický model', 'Deterministic model') ?></span>
            <span class="badge badge-success"><?= $lang->value('Metrické tolerance', 'Metric tolerances') ?></span>
        </div>
    </div>

    <div class="mesh2cad-lab-shell requires-js" data-mesh2cad-lab>
        <div class="lab-scenario-selector" role="group" aria-label="<?= $lang->value('Fáze rekonstrukce', 'Reconstruction stages') ?>">
            <button type="button" class="tab-btn active" data-cad-stage="raw-scan" aria-pressed="true">
                <span class="tab-badge badge-warning">01</span>
                <span><?= $lang->value('1. Surový 3D sken (Mesh)', '1. Raw 3D Scan (Mesh)') ?></span>
            </button>
            <button type="button" class="tab-btn" data-cad-stage="segmented" aria-pressed="false">
                <span class="tab-badge badge-neutral">02</span>
                <span><?= $lang->value('2. Segmentace a filtrace šumu', '2. Segmentation & Filtering') ?></span>
            </button>
            <button type="button" class="tab-btn" data-cad-stage="analytical-cad" aria-pressed="false">
                <span class="tab-badge badge-success">03</span>
                <span><?= $lang->value('3. B-Rep CAD těleso (STEP)', '3. B-Rep CAD Solid (STEP)') ?></span>
            </button>
        </div>

        <div class="cad-stage-visual">
            <div class="cad-preview-container">
                <svg viewBox="0 0 400 220" class="cad-schematic-svg" role="img" aria-label="<?= $lang->value('Schéma geometrické přesnosti', 'Geometric accuracy schematic') ?>">
                    <rect width="400" height="220" fill="var(--surface2)" rx="8" />
                    <!-- Grid Lines -->
                    <line x1="40" y1="30" x2="40" y2="190" stroke="var(--stroke)" stroke-width="1" stroke-dasharray="3 3" />
                    <line x1="200" y1="30" x2="200" y2="190" stroke="var(--stroke)" stroke-width="1" stroke-dasharray="3 3" />
                    <line x1="360" y1="30" x2="360" y2="190" stroke="var(--stroke)" stroke-width="1" stroke-dasharray="3 3" />
                    
                    <!-- Stage 1: Noisy Mesh Profile -->
                    <g class="cad-layer-raw" data-layer="raw-scan">
                        <path d="M 60,160 L 80,158 L 100,163 L 120,159 L 140,161 L 160,158 L 180,162 L 200,118 L 220,78 L 240,82 L 260,79 L 280,81 L 300,77 L 320,83 L 340,80" 
                              fill="none" stroke="#f59e0b" stroke-width="2.5" />
                        <!-- Noise points -->
                        <circle cx="90" cy="157" r="2.5" fill="#f59e0b" />
                        <circle cx="130" cy="164" r="2.5" fill="#f59e0b" />
                        <circle cx="210" cy="98" r="2.5" fill="#f59e0b" />
                        <circle cx="270" cy="84" r="2.5" fill="#f59e0b" />
                        <circle cx="310" cy="76" r="2.5" fill="#f59e0b" />
                        <text x="70" y="55" fill="var(--fg)" font-size="12" font-family="var(--mono)">Triangle noise: ±0.42 mm</text>
                    </g>

                    <!-- Stage 2: Segmented Regions -->
                    <g class="cad-layer-segmented hidden" data-layer="segmented">
                        <rect x="55" y="152" width="130" height="16" fill="rgba(14,165,233,0.2)" stroke="#0ea5e9" stroke-width="1.5" stroke-dasharray="4 2" />
                        <rect x="235" y="72" width="110" height="16" fill="rgba(14,165,233,0.2)" stroke="#0ea5e9" stroke-width="1.5" stroke-dasharray="4 2" />
                        <line x1="185" y1="160" x2="235" y2="80" stroke="#0ea5e9" stroke-width="2" stroke-dasharray="2 2" />
                        <text x="70" y="55" fill="var(--fg)" font-size="12" font-family="var(--mono)">RANSAC Planar & Cylindrical Fit</text>
                    </g>

                    <!-- Stage 3: Analytical Solid -->
                    <g class="cad-layer-cad hidden" data-layer="analytical-cad">
                        <polygon points="60,160 185,160 240,80 340,80 340,95 248,95 195,175 60,175" fill="rgba(16,185,129,0.25)" stroke="#10b981" stroke-width="3" />
                        <circle cx="120" cy="167" r="6" fill="none" stroke="#10b981" stroke-width="2" />
                        <circle cx="290" cy="87" r="6" fill="none" stroke="#10b981" stroke-width="2" />
                        <text x="70" y="55" fill="#10b981" font-size="12" font-family="var(--mono)">Analytical B-Rep Deviation: ≤ 0.04 mm</text>
                    </g>
                </svg>
            </div>

            <div class="cad-stage-meta">
                <div class="meta-row">
                    <span class="meta-label"><?= $lang->value('Geometrický formát:', 'Geometric format:') ?></span>
                    <strong class="meta-val" data-cad-format>STL / OBJ (Mesh)</strong>
                </div>
                <div class="meta-row">
                    <span class="meta-label"><?= $lang->value('Počet elementů:', 'Entity count:') ?></span>
                    <span class="meta-val" data-cad-entities>42,800 <?= $lang->value('trojúhelníků', 'triangles') ?></span>
                </div>
                <div class="meta-row">
                    <span class="meta-label"><?= $lang->value('Rozměrová přesnost:', 'Dimensional tolerance:') ?></span>
                    <span class="meta-val" data-cad-accuracy>±0.42 mm (<?= $lang->value('Šum optického skenu', 'Optical scan noise') ?>)</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label"><?= $lang->value('Editovatelnost v CAD:', 'CAD Parametric Editability:') ?></span>
                    <span class="meta-val text-error" data-cad-editability><?= $lang->value('Needitovatelné (pouze polygonální síť)', 'Non-editable (raw triangles only)') ?></span>
                </div>
                <p class="cad-stage-desc" data-cad-desc>
                    <?= $lang->value('Surový výstup ze skeneru obsahuje tisíce aproximačních plošek. Nelze v něm měnit průměry otvorů, tloušťky stěn ani exportovat do výrobního formátu STEP.', 'Raw scanner output consists of thousands of triangulated facets. Wall thicknesses, hole diameters, and chamfers cannot be parametrically edited or exported as manufacturing STEP solids.') ?>
                </p>
            </div>
        </div>

        <div class="context-disclaimer">
            <strong><?= $lang->value('Hranice technologie:', 'Technology boundary:') ?></strong>
            <?= $lang->value('Ukázka demonstruje algoritmický postup fittingu analytických ploch (roviny, válce, zaoblení). Přesnost fittingu závisí na kvalitě vstupního mračna bodů a referenčním měřítku kalibračního tělesa.', 'Walkthrough demonstrates algorithmic surface fitting (planes, cylinders, fillets). Fit accuracy is bounded by point cloud density and physical calibration artifacts.') ?>
        </div>
    </div>

    <noscript>
        <div class="lab-fallback">
            <h3><?= $lang->value('Přehled fází geometrické rekonstrukce', 'Geometric Reconstruction Pipeline Overview') ?></h3>
            <table class="table-fallback">
                <thead>
                    <tr>
                        <th><?= $lang->value('Fáze', 'Stage') ?></th>
                        <th><?= $lang->value('Datový typ', 'Data Type') ?></th>
                        <th><?= $lang->value('Přesnost', 'Tolerance') ?></th>
                        <th><?= $lang->value('Použití', 'Application') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong><?= $lang->value('1. Surový sken', '1. Raw Scan') ?></strong></td>
                        <td>STL / OBJ</td>
                        <td>±0.42 mm</td>
                        <td><?= $lang->value('Vizuální reference, vizualizace', 'Visual reference only') ?></td>
                    </tr>
                    <tr>
                        <td><strong><?= $lang->value('2. Segmentace', '2. Segmentation') ?></strong></td>
                        <td>Segmented Mesh</td>
                        <td>±0.15 mm</td>
                        <td><?= $lang->value('Detekce analytických primitiv', 'Detection of geometric primitives') ?></td>
                    </tr>
                    <tr>
                        <td><strong><?= $lang->value('3. CAD těleso', '3. CAD Solid') ?></strong></td>
                        <td>STEP / IGES B-Rep</td>
                        <td>≤ 0.04 mm</td>
                        <td><?= $lang->value('CNC obrábění, přesná výroba, parametrické úpravy', 'CNC machining, manufacturing, parametric modeling') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </noscript>
</section>
