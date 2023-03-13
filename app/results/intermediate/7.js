//get data
function getnewloans(results) {
    var startYear = results['startYear'];
    var endYear = results['endYear'];
    var ctdata = results['results'];
    var bothCurr = results['bothCurr'].split(',');
    var tableid = results['tableid'];

    var datar = [];
    switch (tableid) {       
        case "7.1.":
        for (var i = startYear; i <= endYear; i++) {
            var data = new Array();
            data['id'] = i;
            data['item'] = i.toString();
            for (var j = 0; j < bothCurr.length; j++) {
                data['B_'+bothCurr[j]] = checkval(ctdata['B_'+bothCurr[j]+'_'+i]);
                data['O_'+bothCurr[j]] = checkval(ctdata['O_'+bothCurr[j] + '_' + i]);
                data['I_'+bothCurr[j]] = checkval(ctdata['I_'+bothCurr[j] + '_' + i]);
                data['R_'+bothCurr[j]] = checkval(ctdata['R_'+bothCurr[j] + '_' + i]);
            }
            datar.push(data);
        }
        break;

        case "7.2.":
        for (var i = startYear; i <= endYear; i++) {
            var data = new Array();
            data['id'] = i;
            data['item'] = i.toString();
            data['BLC'] = checkval(ctdata['BLC_' + i]);
            data['OLC'] = checkval(ctdata['OLC_' + i]);
            data['ILC'] = checkval(ctdata['ILC_' + i]);
            data['RLC'] = checkval(ctdata['RLC_' + i]);
            datar.push(data);
        }
        break;
    }
    return datar;
}

function showData(results) {
    var bothCurr = results['bothCurr'].split(',');
    var currencies = results['currencies'];
    var tableid = results['tableid'];
    var cols = [];
    var columngroups = [];
    switch (tableid) {

        case "7.1.":
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
                    name: 'B_' + bothCurr[j],
                    columngroup: currencyName,
                    map: 'B_' + bothCurr[j],
                    text: 'Issue'
                });
                cols.push({
                    name: 'O_'+bothCurr[j],
                    columngroup: currencyName,
                    map: 'O_'+bothCurr[j],
                    text: 'Outstanding'
                });
                cols.push({
                    name: 'I_'+bothCurr[j],
                    columngroup: currencyName,
                    map: 'I_'+bothCurr[j],
                    text: 'Interest'
                });
                cols.push({
                    name: 'R_'+bothCurr[j],
                    columngroup: currencyName,
                    map: 'R_'+bothCurr[j],
                    text: 'Repayment'
                });
            }
        break;
        case "7.2.":

            cols.push({
                name: 'BLC',
                map: 'BLC',
                text: 'Issue'
            });
            cols.push({
                name: 'OLC',
                map: 'OLC',
                text: 'Outstanding'
            });
            cols.push({
                name: 'ILC',
                map: 'ILC',
                text: 'Interest'
            });
            cols.push({
                name: 'RLC',
                map: 'RLC',
                text: 'Repayment'
            });
        break;
    }
    if(columngroups.length==0){
        CreateGrid(cols, getnewloans(results));
    }else{
        CreateGrid(cols, getnewloans(results), columngroups);
    }
   
}