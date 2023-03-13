//get data
function getfuelcosts(results) {
    var startYear = results['startYear'];
    var endYear = results['endYear'];
    var ctdata = results['results'];
    var bothCurr = results['bothCurr'].split(',');
    var tableid = results['tableid'];
    var plants = results['plants'];
    var rows = results['rows'];
    var datar = [];

    if (tableid == "11.1.") {
        if (plants) {
            $("#tabdetail").show();
            var controls = "<ul class='nav nav-tabs' id='plantnavs'>";
            for (var i = 0; i < plants.length; i++) {
                var active = "";
                if (i == 0)
                    active = "active";

                controls += "<li role='presentation' class='" + active + "'> \
                    <a class='pointer' onclick='getDataDetail(" + plants[i]['id'] + ", \"fuels\", this, " + rows[i] + ")' id='detail_" + plants[i]['id'] + "> \
                        <span lang='en'>" + plants[i]['name'] + "</span></a></li>";
            }
            $("#tabdetail").html(controls);
        }
    }

    for (var i = startYear; i <= endYear; i++) {
        var data = new Array();
        data['id'] = i;
        data['item'] = i.toString();
        for (var j = 0; j < bothCurr.length; j++) {
            data[bothCurr[j]] = ctdata[bothCurr[j] + '_' + i];
            data['E_' + bothCurr[j]] = checkval(ctdata['E_' + bothCurr[j] + '_' + i]);
        }
        if (tableid == "11.2.")
            data['LC'] = checkval(ctdata['LC_' + i]);

        datar.push(data);
    }
    return datar;
}

function showData(results) {
    var bothCurr = results['bothCurr'].split(',');
    var currencies = results['currencies'];
    var baseCurrency = results['baseCurrency'];
    var baseCurrencyName = $.grep(currencies, function (v) {
        return v.id === baseCurrency;
    })[0]['value'];
    var tableid = results['tableid'];
    var cols = [];
    var columngroups = [];

    for (var j = 0; j < bothCurr.length; j++) {
        var currencyName = $.grep(currencies, function (v) {
            return v.id === bothCurr[j];
        })[0]['value'];

        columngroups.push({
            text: currencyName + ' (Million)',
            align: 'center',
            name: currencyName + '_'
        });

        cols.push({
            name: bothCurr[j],
            columngroup: currencyName + '_',
            map: bothCurr[j],
            text: 'In Constant Prices'
        });
        cols.push({
            name: 'E_' + bothCurr[j],
            columngroup: currencyName + '_',
            map: 'E_' + bothCurr[j],
            text: 'In Current Prices'
        });
    }

    if (tableid == "11.2.") {
        columngroups.push({
            text: 'Total fuel costs in local currency',
            align: 'center',
            name: 'total'
        });

        cols.push({
            name: 'LC',
            columngroup: 'total',
            map: 'LC',
            text: baseCurrencyName
        });
    }
    CreateGrid(cols, getfuelcosts(results), columngroups);
}