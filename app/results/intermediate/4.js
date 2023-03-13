//get data
function geteconomicparameters(results) {
    var ctdata = results['results'];
    var startYear = results['startYear'];
    var endYear = results['endYear'];
    var curTypeSel = results['curTypeSel'].split(',');
    var bothCurr = results['bothCurr'].split(',');
    var tableid = results['tableid'];
    var datar = [];
    if (tableid == '4.1.') {
        for (var i = startYear; i <= endYear; i++) {
            var data = new Array();
            data['id'] = i;
            data['item'] = i.toString();
            for (var j = 0; j < bothCurr.length; j++) {
                data[bothCurr[j]] = checkval(ctdata[bothCurr[j]+'_'+i]);
                data['I_'+bothCurr[j]] = checkval(ctdata['I_'+bothCurr[j] + '_' + i]);
            }
            datar.push(data);
        }
    } else {
        for (var i = startYear; i <= endYear; i++) {
            var data = new Array();
            data['id'] = i;
            data['item'] = i.toString();
            for (var j = 0; j < curTypeSel.length; j++) {
                data[curTypeSel[j]] = checkval(ctdata[curTypeSel[j] + '_' + i]);
            }
            datar.push(data);
        }
    }
    return datar;
}

function showData(results) {
    var baseCurrency = results['baseCurrency'];
    var curTypeSel = results['curTypeSel'].split(',');
    var bothCurr = results['bothCurr'].split(',');
    var currencies = results['currencies'];
    var tableid = results['tableid'];
    var baseCurrencyName = $.grep(currencies, function (v) {
        return v.id === baseCurrency;
    })[0]['value'];
    var cols = [];
    var columngroups = [];
    if (tableid == '4.1.') {
        for (var j = 0; j < bothCurr.length; j++) {
            var currencyName = $.grep(currencies, function (v) {
                return v.id === bothCurr[j];
            })[0]['value'];

            columngroups.push({
                text: currencyName + ' (Million)',
                align: 'center',
                name: currencyName
            });
            cols.push({
                name: 'I_' + bothCurr[j],
                columngroup: currencyName,
                map: 'I_' + bothCurr[j],
                text: 'Rate(%)'
            });
            cols.push({
                name: bothCurr[j],
                columngroup: currencyName,
                map: bothCurr[j],
                text: 'Index'
            });
        }
    } else {
        for (var j = 0; j < curTypeSel.length; j++) {
            var currencyName = $.grep(currencies, function (v) {
                return v.id === bothCurr[j];
            })[0]['value'];
        cols.push({
            name: curTypeSel[j],
            map: curTypeSel[j],
            text: baseCurrencyName + '(' + currencyName + ')'
        });
    }
    }
    CreateGrid(cols, geteconomicparameters(results), columngroups);
}