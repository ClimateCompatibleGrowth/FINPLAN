//get data
function getomcosts(results) {
    var data = results['data'];
    var cedata = results['ceData'];
    var cfdata = results['cfData'];
    var endYear = results['endYear'];
    var bothCurr = results['bothCurr'].split(',');

    var iddata = 0;
    if (cfdata['id'])
        iddata = cfdata['id'];
    Cookies('iddata', iddata);

    var datar = [];
    for (var i = cedata['FOyear']; i <= endYear; i++) {
        var data = new Array();
        data['id'] = i;
        data['item'] = i.toString();
        for (var j = 0; j < bothCurr.length; j++) {
            data[bothCurr[j]] = cfdata[bothCurr[j] + '_' + i];
        }
        datar.push(data);
    }
    return datar;
}

function showData(results) {
    var currencies = results['currencies'];
    var bothCurr = results['bothCurr'].split(',');
    var cols = [];
    for (var j = 0; j < bothCurr.length; j++) {
        var currencyName = $.grep(currencies, function (v) {
            return v.id === bothCurr[j];
        })[0]['value'];
        cols.push({
            name: bothCurr[j],
            map: bothCurr[j],
            text: currencyName + " (Million)",
            editable: true
        });
    }
    CreateGrid(cols, getomcosts(results))
}