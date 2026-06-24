<?php
/** @var \App\BLL\Models\Lesplanning $lesplanning */
?>
<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <title>Lesvoorbereidingsformulier &mdash; <?= htmlspecialchars($lesplanning->datum->format('d-m-Y')) ?></title>
    <style>
        @page { size: A4 landscape; margin: 1.5cm; }
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            color: #000;
            margin: 0;
        }
        .toolbar {
            background: #f1f3f5;
            padding: 0.75rem 1rem;
            display: flex;
            gap: 0.5rem;
            align-items: center;
            border-bottom: 1px solid #ccc;
        }
        .toolbar button, .toolbar a {
            font-family: inherit;
            font-size: 14px;
            padding: 0.4rem 0.9rem;
            border: 1px solid #888;
            background: #fff;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            color: #000;
        }
        .vel {
            max-width: 29.7cm;
            margin: 1.5cm auto;
            padding: 0 0.5cm;
        }
        table.lvf {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0.6cm;
        }
        table.lvf th, table.lvf td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
            text-align: left;
        }
        table.lvf .titel {
            text-align: center;
            font-weight: bold;
            font-size: 13pt;
            padding: 6px;
        }
        table.lvf .label {
            font-weight: bold;
            font-size: 9.5pt;
            padding-bottom: 0;
        }
        table.lvf .waarde {
            min-height: 1.1em;
            border-top: none;
        }
        table.lvf-onderdelen th {
            font-size: 9.5pt;
            background: #f1f3f5;
        }
        table.lvf-onderdelen td {
            font-size: 10pt;
        }
        .materialen {
            margin-top: 4px;
            font-style: italic;
            font-size: 9pt;
        }
        @media print {
            .toolbar { display: none; }
            .vel { margin: 0; max-width: none; }
        }
    </style>
</head>
<body>
<div class="toolbar no-print">
    <button type="button" onclick="window.print()">Printen</button>
    <a href="/lesplanningen">&larr; Terug naar overzicht</a>
</div>

<div class="vel">
    <table class="lvf">
        <tr><td class="titel" colspan="3">LESVOORBEREIDINGSFORMULIER (LVF)</td></tr>
        <tr>
            <td class="label" style="width:40%">Naam</td>
            <td class="label" style="width:40%">Naam praktijkbegeleider</td>
            <td class="label" style="width:20%"></td>
        </tr>
        <tr>
            <td class="waarde"><?= htmlspecialchars($lesplanning->instructeur->naam) ?></td>
            <td class="waarde">&nbsp;</td>
            <td class="waarde"></td>
        </tr>
        <tr>
            <td class="label">Datum</td>
            <td class="label">Locatie (bad, buitenwater, strand)</td>
            <td class="label"></td>
        </tr>
        <tr>
            <td class="waarde"><?= htmlspecialchars($lesplanning->datum->format('d-m-Y')) ?></td>
            <td class="waarde"><?= $lesplanning->locatie !== '' ? htmlspecialchars($lesplanning->locatie) : '&nbsp;' ?></td>
            <td class="waarde"></td>
        </tr>
        <tr>
            <td class="label">Tijd</td>
            <td class="label">Groepsgrootte</td>
            <td class="label"></td>
        </tr>
        <tr>
            <td class="waarde"><?= htmlspecialchars($lesplanning->beginTijd) ?> &ndash; <?= htmlspecialchars($lesplanning->eindTijd) ?></td>
            <td class="waarde"><?= $lesplanning->groep->aantalLeden ?> leden (<?= htmlspecialchars($lesplanning->groep->naam) ?>)</td>
            <td class="waarde"></td>
        </tr>
    </table>

    <table class="lvf">
        <tr>
            <td class="label" style="width:20%">Beginsituatie</td>
            <td><?= nl2br(htmlspecialchars($lesplanning->beginsituatie)) ?>&nbsp;</td>
        </tr>
        <tr>
            <td class="label">Doelstelling</td>
            <td><?= nl2br(htmlspecialchars($lesplanning->doelstelling)) ?>&nbsp;</td>
        </tr>
    </table>

    <table class="lvf lvf-onderdelen">
        <thead>
        <tr>
            <th style="width:10%">Onderdeel</th>
            <th style="width:8%">Tijd</th>
            <th style="width:18%">Doel van de oefening/activiteit</th>
            <th style="width:22%">Oefening/Activiteit</th>
            <th style="width:22%">Organisatie en Materialen</th>
            <th style="width:20%">Didactische aanwijzingen/Werkvorm</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($lesplanning->onderdelen as $onderdeel): ?>
            <tr>
                <td><strong><?= htmlspecialchars($onderdeel->naam) ?></strong></td>
                <td><?= htmlspecialchars($onderdeel->tijdIndicatie) ?>&nbsp;</td>
                <td><?= nl2br(htmlspecialchars($onderdeel->doel)) ?>&nbsp;</td>
                <td><?= nl2br(htmlspecialchars($onderdeel->activiteit)) ?>&nbsp;</td>
                <td>
                    <?= nl2br(htmlspecialchars($onderdeel->organisatieEnMaterialen)) ?>&nbsp;
                    <?php if (!empty($onderdeel->materialen)): ?>
                        <div class="materialen">
                            Materiaal: <?= htmlspecialchars(implode(', ', array_map(static fn ($m) => $m->naam, $onderdeel->materialen))) ?>
                        </div>
                    <?php endif; ?>
                </td>
                <td><?= nl2br(htmlspecialchars($onderdeel->didactischeAanwijzingen)) ?>&nbsp;</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
