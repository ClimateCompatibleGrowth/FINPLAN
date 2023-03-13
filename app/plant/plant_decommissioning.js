function showData(results) {
    $("#additionalData").load("app/plant/plant_decommissioning.html", function () {
        $("#chartGrid").hide();
        $("#decDown").hide();
        $("#decUp").hide();
        $("#exportgrid").hide();
        var currencies = results['currencies'];
        var cfdata = results['cfData']
        var bothCurr = results['bothCurr'].split(',');
        for (var j = 0; j < bothCurr.length; j++) {
            var currencyName = $.grep(currencies, function (v) {
                return v.id === bothCurr[j];
            })[0]['value'];
            $("#decommisioning tbody").append("<tr><td><span lang='en'>Total amount " + currencyName + "</span><input id='" + bothCurr[j] + "_famount' name='" + bothCurr[j] + "_famount' type='text' class='form-control' onkeyup='onlyDecimal(this)'  autocomplete='off' /><span lang='en' class='small text-mutted pull-right'>Million</span></td></tr>")
        }

        setValues(cfdata);

        var iddata = 0;
        if (cfdata['id'])
            iddata = cfdata['id'];
        Cookies('iddata', iddata);
    })
}