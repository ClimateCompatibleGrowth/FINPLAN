//get data
function getoldbonds(results) {
    var ctdata = results['results'];
    var startYear = results['startYear'];
    var endYear = results['endYear'];
    var bothCurr = results['bothCurr'].split(',');
    var tableid = results['tableid'];
    var datar = [];
    if (tableid == '3.1.') {
        for (var i = startYear; i <= endYear; i++) {

            var data = new Array();
            data['id'] = i;
            data['item'] = i.toString();
            for (var j = 0; j < bothCurr.length; j++) {
                data['BD_' + bothCurr[j]] =checkval( ctdata['BD_' + bothCurr[j] + '_' + i]);
                data['B_' + bothCurr[j]] = checkval(ctdata['B_' + bothCurr[j] + '_' + i]);
                data['I_' + bothCurr[j]] = checkval(ctdata['I_' + bothCurr[j] + '_' + i]);
                data['R_' + bothCurr[j]] = checkval(ctdata['R_' + bothCurr[j] + '_' + i]);
            }
            datar.push(data);
        }
    } else {
        for (var i = startYear; i <= endYear; i++) {

            var data = new Array();
            data['id'] = i;
            data['item'] = i.toString();
            data['BDL'] =checkval( ctdata['BDL_' + i]);
            data['BL'] = checkval(ctdata['BL_' + i]);
            data['IL'] = checkval(ctdata['IL_' + i]);
            data['RL'] = checkval(ctdata['RL_' + i]);
            datar.push(data);
        }
    }
    return datar;
}

function showData(results) {
    var bothCurr = results['bothCurr'].split(',');
    var currencies = results['currencies'];
    var tableid = results['tableid'];
    var cols = [];
    var columngroups = [];
    if (tableid == '3.1.') {
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
                name: 'BD_' + bothCurr[j],
                columngroup: currencyName,
                map: 'BD_' + bothCurr[j],
                text: 'Issue'
            });
            cols.push({
                name: 'B_' + bothCurr[j],
                columngroup: currencyName,
                map: 'B_' + bothCurr[j],
                text: 'Outstanding'
            });
            cols.push({
                name: 'I_' + bothCurr[j],
                columngroup: currencyName,
                map: 'I_' + bothCurr[j],
                text: 'Interest'
            });
            cols.push({
                name: 'R_' + bothCurr[j],
                columngroup: currencyName,
                map: 'R_' + bothCurr[j],
                text: 'Repayments'
            });
        }
    } else {
        cols.push({
            name: 'BDL',
            map: 'BDL',
            text: 'Issue'
        });
        cols.push({
            name: 'BL',
            map: 'BL',
            text: 'Outstanding'
        });
        cols.push({
            name: 'IL',
            map: 'IL',
            text: 'Interest'
        });
        cols.push({
            name: 'RL',
            map: 'RL',
            text: 'Repayments'
        });
    }
    CreateGrid(cols, getoldbonds(results), columngroups);
}