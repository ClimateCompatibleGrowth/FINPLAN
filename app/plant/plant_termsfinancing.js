function showData(results) {
    $("#additionalData").load("app/plant/plant_termsfinancing.html", function () {
        var cfdata = results['cfData'];
        var cidata = results['ciData'];
        var cpdata = results['cpData'];
        var csdata = results['csData'];

        var currencyName = $.grep(results.currencies, function (v) {
            return v.id === results.baseCurrency;
        })[0]['value'];
        cfdata['Total_amount_in_constant_prices'] = currencyName;
        // cfdata['Total_amount_in_constant_prices'] = currencyName;

        var iddata = 0;
        if (cfdata['id'])
            iddata = cfdata['id'];
        Cookies('iddata', iddata);

        var fs = Cookies('fs');
        if (fs == 'L1') {
            $("#tblidc").hide();
            $("#RepaymentOptionUI").parents("tr").hide();
        }

        if (cfdata['InterestOption'] == 'C') {
            $('#InterestRate').prop('readonly', false);
            $('#InterestSpreadRate').prop('readonly', true);
        }

        if (cfdata['InterestOption'] == 'F') {
            $('#InterestRate').prop('readonly', true);
            $('#InterestSpreadRate').prop('readonly', false);
        }

        if (cfdata['OTInitial'] == 'YES')
            $('#OTIRate').prop('readonly', false);

        if (cfdata['DUpfront'] == 'YES')
            $('#DURate').prop('readonly', false);

        if (cfdata['PDrawdown'] == 'YES')
            $('#PDRate').prop('readonly', false);

        if (cfdata['IDCOption'] == 'N') {
            $(".idc :input").prop('readonly', true);
            $('input[name="IDCRepayment"]').prop('disabled', true);
        } else {
            $(".idc :input").prop('readonly', false);
            $('input[name="IDCRepayment"]').prop('disabled', false);
        }

        $('#totcp').html("0");
        var curr = Cookies('curr');

        if (csdata[fs] == 'YES') {
            var amount = 0;
            for (var i = 1; i <= cpdata['CPeriod']; i++) {
                var sfval = fs + '_' + i;
                var infval = 'Tot_' + curr;
                var perval = curr + '_' + i;
                var perc = cidata[perval];
                var totcost = cidata[infval];
                var tot = perc * totcost / 100;
                amount = amount + tot * csdata[sfval] / 100;
            }
            $('#totcp').html(amount);
        }
        setValues(cfdata);
        $("#btnBack").show();
    })
}

function setReadOnly(el, input1, input2) {
    $('#' + input1).prop('readonly', !el.checked);
    if (input2)
        $('#' + input2).prop('readonly', el.checked);
}

function setReadOnlyIDC(el) {
    if (el.value == 'N') {
        $(".idc :input").prop('readonly', true);
        $('input[name="IDCRepayment"]').prop('disabled', true);
    } else {
        $(".idc :input").prop('readonly', false);
        $('input[name="IDCRepayment"]').prop('disabled', false);
    }
}