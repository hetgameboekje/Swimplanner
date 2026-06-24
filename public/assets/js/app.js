$(function () {
    // Demo-gedrag: dynamisch een extra "Kern"-onderdeel toevoegen aan de
    // lesplanning. Pure front-end, slaat niets op.
    let volgendVolgnummer = $('#lesplanning-onderdelen tbody tr').length + 1;

    $('#onderdeel-toevoegen').on('click', function () {
        const naam = 'Kern ' + (volgendVolgnummer - 1);
        const rij = `
            <tr>
                <td>${volgendVolgnummer}</td>
                <td><input type="text" class="form-control form-control-sm" value="${naam}"></td>
                <td><input type="text" class="form-control form-control-sm" placeholder="bv. 15 min"></td>
                <td><textarea class="form-control form-control-sm" rows="2"></textarea></td>
                <td><textarea class="form-control form-control-sm" rows="2"></textarea></td>
                <td><textarea class="form-control form-control-sm" rows="2"></textarea></td>
                <td><textarea class="form-control form-control-sm" rows="2"></textarea></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger onderdeel-verwijderen"><i class="bi bi-trash"></i></button></td>
            </tr>`;
        $('#lesplanning-onderdelen tbody').append(rij);
        volgendVolgnummer++;
    });

    $(document).on('click', '.onderdeel-verwijderen', function () {
        $(this).closest('tr').remove();
    });
});
