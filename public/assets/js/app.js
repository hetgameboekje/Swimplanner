$(function () {
    // Dynamisch een extra "Kern"-onderdeel toevoegen aan de lesplanning.
    // De index telt door vanaf het aantal al gerenderde rijen, zodat de
    // name="onderdelen[i][...]"-indexen uniek blijven na opslaan.
    const $tabel = $('#lesplanning-onderdelen');
    if ($tabel.length === 0) {
        return;
    }

    let volgendeIndex = $tabel.find('tbody tr').length;
    // " selected" weghalen zodat een nieuw onderdeel nooit een materiaal
    // overneemt dat in een bestaande rij toevallig al was geselecteerd.
    const materiaalOptiesHtml = ($tabel.find('tbody tr:first select').html() ?? '').replace(/\s+selected(="selected")?/gi, '');

    $('#onderdeel-toevoegen').on('click', function () {
        const i = volgendeIndex;
        const naam = 'Kern ' + i;
        const rij = `
            <tr>
                <td>${i + 1}</td>
                <td><input type="text" name="onderdelen[${i}][naam]" class="form-control form-control-sm" value="${naam}"></td>
                <td><input type="text" name="onderdelen[${i}][tijd_indicatie]" class="form-control form-control-sm" placeholder="bv. 15 min"></td>
                <td><textarea name="onderdelen[${i}][doel]" class="form-control form-control-sm" rows="2"></textarea></td>
                <td><textarea name="onderdelen[${i}][activiteit]" class="form-control form-control-sm" rows="2"></textarea></td>
                <td>
                    <input type="text" name="onderdelen[${i}][organisatie_en_materialen]" class="form-control form-control-sm mb-1" placeholder="bv. 2 banen, pop per groepje">
                    <select name="onderdelen[${i}][materiaal_ids][]" class="form-select form-select-sm" multiple size="3">${materiaalOptiesHtml}</select>
                </td>
                <td><textarea name="onderdelen[${i}][didactische_aanwijzingen]" class="form-control form-control-sm" rows="2"></textarea></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger onderdeel-verwijderen"><i class="bi bi-trash"></i></button></td>
            </tr>`;
        $tabel.find('tbody').append(rij);
        volgendeIndex++;
    });

    $(document).on('click', '.onderdeel-verwijderen', function () {
        $(this).closest('tr').remove();
    });
});
