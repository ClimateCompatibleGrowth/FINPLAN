//get data
function getdepreciation(results) {
    var cadata = results['caData'];
    var startYear = results['startYear'];
    var endYear = results['endYear'];
    var datar = [];
    for (var i = startYear; i <= endYear; i++) {
        var data = new Array();
        data['id'] = i;
        data['item'] = i.toString();
        data['Y'] = cadata['Y_' + i];
        datar.push(data);
    }
    return datar;
}

function showData(results) {
    var startYear = results['startYear'];
    var cadata = results['caData'];
    var editablegrid = true;
    $("#additionalData").load("app/taxation/taxation_depreciation.html", function () {
        $("#yearTax").html(startYear - 1);
        setValues(cadata);
        $("#VATRateInvestment").prop('disabled', !$("#VatSales").is(":checked"));
        $("#LossBaseYear").prop('disabled', !$("#TaxLossForward").is(":checked"));
        if ($('input[name=TaxType]:checked').val() == "SR") {
            $("#SteadyTaxRate").prop("disabled", false);
            editablegrid = false;
        } else {
            $("#SteadyTaxRate").prop("disabled", true);
        }

        var cols = [];
        cols.push({
            name: 'Y',
            map: 'Y',
            editable: editablegrid,
            cellclassname: editablegrid == false ? 'readonly' : '',
            text: 'Tax rate (%)'
        });
        CreateGrid(cols, getdepreciation(results));

    });
}

function disable(chk) {
    if (chk == "SR") {
        $('#SteadyTaxRate').attr('disabled', false);
        $('#gsFlexGrid').jqxGrid('setcolumnproperty', 'Y', 'editable', false);
        $('#gsFlexGrid').jqxGrid('setcolumnproperty', 'Y', 'cellclassname', 'readonly');
    } else {
        $('#SteadyTaxRate').attr('disabled', true);
        $('#gsFlexGrid').jqxGrid('setcolumnproperty', 'Y', 'editable', true);
        $('#gsFlexGrid').jqxGrid('setcolumnproperty', 'Y', 'cellclassname', '');
    }

    $("#VATRateInvestment").prop('disabled', !$("#VatSales").is(":checked"));
    $("#LossBaseYear").prop('disabled', !$("#TaxLossForward").is(":checked"));
}