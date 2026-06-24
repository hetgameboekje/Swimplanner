<?php
/** @var \App\BLL\Models\Groep[] $groepen */
/** @var \App\BLL\Models\Materiaal[] $materialen */
/** @var string[] $standaardOnderdelen */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Nieuwe lesplanning</h1>
    <span class="badge text-bg-warning">Demo &mdash; opslaan doet nog niets</span>
</div>

<form>
    <div class="card mb-3">
        <div class="card-header">Gegevens les</div>
        <div class="card-body row g-3">
            <div class="col-md-4">
                <label class="form-label">Instructeur</label>
                <input type="text" class="form-control" value="Timo Bergthaler (ingelogde gebruiker)" disabled>
            </div>
            <div class="col-md-4">
                <label class="form-label">Groep / doelgroep</label>
                <select class="form-select">
                    <?php foreach ($groepen as $groep): ?>
                        <option><?= htmlspecialchars($groep->naam) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Locatie</label>
                <input type="text" class="form-control" placeholder="bv. Zwembad De Roerdomp, baan 3-4">
            </div>
            <div class="col-md-4">
                <label class="form-label">Datum (altijd zondag)</label>
                <input type="date" class="form-control">
                <div class="form-text">Validatie op zondag volgt in de functionele fase.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Begintijd</label>
                <input type="time" class="form-control" value="10:00">
            </div>
            <div class="col-md-4">
                <label class="form-label">Eindtijd</label>
                <input type="time" class="form-control" value="11:00">
            </div>
            <div class="col-md-6">
                <label class="form-label">Beginsituatie</label>
                <textarea class="form-control" rows="2" placeholder="Wat kan de groep al, waar wordt op aangesloten?"></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Doelstelling</label>
                <textarea class="form-control" rows="2" placeholder="Wat moet de zwemmer aan het einde van de les kunnen?"></textarea>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Lesonderdelen</span>
            <button type="button" id="onderdeel-toevoegen" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-plus-lg"></i> Kern-onderdeel toevoegen
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered lvf-table mb-0" id="lesplanning-onderdelen">
                    <thead class="table-light">
                    <tr>
                        <th style="width: 3%">#</th>
                        <th style="width: 12%">Onderdeel</th>
                        <th style="width: 8%">Tijd</th>
                        <th>Doel van de oefening/activiteit</th>
                        <th>Oefening / activiteit</th>
                        <th>Organisatie en materialen</th>
                        <th>Didactische aanwijzingen / werkvorm</th>
                        <th style="width: 3%"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($standaardOnderdelen as $i => $naam): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><input type="text" class="form-control form-control-sm" value="<?= htmlspecialchars($naam) ?>"></td>
                            <td><input type="text" class="form-control form-control-sm" placeholder="bv. 15 min"></td>
                            <td><textarea class="form-control form-control-sm" rows="2"></textarea></td>
                            <td><textarea class="form-control form-control-sm" rows="2"></textarea></td>
                            <td>
                                <select class="form-select form-select-sm mb-1" multiple size="3">
                                    <?php foreach ($materialen as $materiaal): ?>
                                        <option><?= htmlspecialchars($materiaal->naam) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><textarea class="form-control form-control-sm" rows="2"></textarea></td>
                            <td>
                                <?php if ($i >= 4): ?>
                                    <button type="button" class="btn btn-sm btn-outline-danger onderdeel-verwijderen"><i class="bi bi-trash"></i></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary" disabled>Opslaan (later)</button>
        <a href="/lesplanningen" class="btn btn-outline-secondary">Annuleren</a>
    </div>
</form>
