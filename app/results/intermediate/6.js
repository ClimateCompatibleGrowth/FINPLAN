//get data
function getnewloans(results) {
    var startYear = results['startYear'];
    var endYear = results['endYear'];
    var ctdata = results['results'];
    var ctdatacal = results['resultscal'];
    var baseCurrency = results['baseCurrency'];
    var bothCurr = results['bothCurr'].split(',');
    var currencies = results['currencies'];
    var plants=results['plants'];
    var rows=results['rows'];
    var tableid = results['tableid'];
    var baseCurrencyName = $.grep(currencies, function (v) {
        return v.id === baseCurrency;
    })[0]['value'];
    var datar = [];
    switch (tableid) {
        case "6.1.":
            for (var i = startYear; i <= endYear; i++) {
                var data = new Array();
                data['id'] = i;
                data['item'] = i.toString();
                for (var j = 0; j < bothCurr.length; j++) {
                    data['A_'+bothCurr[j]] = checkval(ctdata['A_'+bothCurr[j]+'_'+i]);
                    data['B_'+bothCurr[j]] = checkval(ctdatacal['B_'+bothCurr[j] + '_' + i]);
                    data['I_'+bothCurr[j]] = checkval(ctdatacal['I_'+bothCurr[j] + '_' + i]);
                    data['R_'+bothCurr[j]] = checkval(ctdatacal['R_'+bothCurr[j] + '_' + i]);
                }

                data['TD'] = checkval(ctdatacal['TD_'+i]);
                data['TB'] = checkval(ctdatacal['TB_'+i]);
                data['TI'] = checkval(ctdatacal['TI_'+i]);
                data['TR'] = checkval(ctdatacal['TR_'+i]);

                datar.push(data);
            }
        break;

        case "6.2.":
            if(plants){
                $("#tabdetail").show();
                var controls="<ul class='nav nav-tabs' id='plantnavs'>";
                for(var i=0; i<plants.length; i++){
                    var active="";
                    if(i==0)
                    active="active";

                    controls+="<li role='presentation' class='"+active+"'> \
                    <a class='pointer' onclick='getDataDetail("+plants[i]['id']+", \"loans\", this, "+rows[i]+")' id='plant_"+plants[i]['id']+"> \
                        <span lang='en'>"+plants[i]['name']+"</span></a></li>";
                }
                $("#tabdetail").html(controls);
            }
            var rowid=results['rowid'];
            for (var i = startYear; i <= endYear; i++) {
                var data = new Array();
                data['id'] = i;
                data['item'] = i.toString();
                for (var j = 0; j < bothCurr.length; j++) {
                    data['DD_'+bothCurr[j]+'_L1'] = checkval(ctdata['DD_'+bothCurr[j]+'_L1_'+i]);
                    data['Bal_'+bothCurr[j]+'_L1'] = checkval(ctdata['Bal_'+bothCurr[j] + '_L1_' + i]);
                    data['Int_'+bothCurr[j]+'_L1'] = checkval(ctdata['Int_'+bothCurr[j] + '_L1_' + i]);
                    data['Repy_'+bothCurr[j]+'_L1'] = checkval(ctdata['Repy_'+bothCurr[j] + '_L1_' + i+'_'+rowid]);
                }
                datar.push(data);
            }

        break;
        
        case "6.3.":
        for (var i = startYear; i <= endYear; i++) {
            var data = new Array();
            data['id'] = i;
            data['item'] = i.toString();
            for (var j = 0; j < bothCurr.length; j++) {
                data['L_'+bothCurr[j]] = checkval(ctdata['L_'+bothCurr[j]+'_'+i]);
                data['B_'+bothCurr[j]] = checkval(ctdata['B_'+bothCurr[j] + '_' + i]);
                data['I_'+bothCurr[j]] = checkval(ctdata['I_'+bothCurr[j] + '_' + i]);
                data['R_'+bothCurr[j]] = checkval(ctdata['R_'+bothCurr[j] + '_' + i]);
            }
            datar.push(data);
        }
        break;

        case "6.4.":
        for (var i = startYear; i <= endYear; i++) {
            var data = new Array();
            data['id'] = i;
            data['item'] = i.toString();
            data['LLC'] = checkval(ctdata['LLC_' + i]);
            data['BLC'] = checkval(ctdata['BLC_' + i]);
            data['ILC'] = checkval(ctdata['ILC_' + i]);
            data['RLC'] = checkval(ctdata['RLC_' + i]);
            datar.push(data);
        }
        break;
    }
    return datar;
}

function showData(results, tableid) {
    var baseCurrency = results['baseCurrency'];
    var bothCurr = results['bothCurr'].split(',');
    var currencies = results['currencies'];
    var tableid = results['tableid'];
    var baseCurrencyName = $.grep(currencies, function (v) {
        return v.id === baseCurrency;
    })[0]['value'];
    var cols = [];
    var columngroups = [];
    switch (tableid) {
        case "6.1.":
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
                    name: 'A_' + bothCurr[j],
                    columngroup: currencyName,
                    map: 'A_' + bothCurr[j],
                    text: 'Drawdown'
                });
                cols.push({
                    name: 'B_'+bothCurr[j],
                    columngroup: currencyName,
                    map: 'B_'+bothCurr[j],
                    text: 'Balance'
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

            columngroups.push({
                text: 'Total in '+baseCurrencyName + ' (Million)',
                align: 'center',
                name: 'total'
            });
            cols.push({
                name: 'TD',
                columngroup: 'total',
                map: 'TD',
                text: 'Drawdown'
            });
            cols.push({
                name: 'TB',
                columngroup: 'total',
                map: 'TB',
                text: 'Balance'
            });
            cols.push({
                name: 'TI',
                columngroup: 'total',
                map: 'TI',
                text: 'Interest'
            });
            cols.push({
                name: 'TR',
                columngroup: 'total',
                map: 'TR',
                text: 'Repayment'
            });
        break;

        case "6.2.":
            
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
                    name: 'DD_' + bothCurr[j]+'_L1',
                    columngroup: currencyName,
                    map: 'DD_' + bothCurr[j]+'_L1',
                    text: 'Drawdown'
                });
                cols.push({
                    name: 'Bal_'+bothCurr[j]+'_L1',
                    columngroup: currencyName,
                    map: 'Bal_'+bothCurr[j]+'_L1',
                    text: 'Balance'
                });
                cols.push({
                    name: 'Int_'+bothCurr[j]+'_L1',
                    columngroup: currencyName,
                    map: 'Int_'+bothCurr[j]+'_L1',
                    text: 'Interest'
                });
                cols.push({
                    name: 'Reply_'+bothCurr[j]+'_L1',
                    columngroup: currencyName,
                    map: 'Reply_'+bothCurr[j]+'_L1',
                    text: 'Repayment'
                });
            }
        break;

        case "6.3.":

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
                    name: 'L_' + bothCurr[j],
                    columngroup: currencyName,
                    map: 'L_' + bothCurr[j],
                    text: 'Drawdown'
                });
                cols.push({
                    name: 'B_'+bothCurr[j],
                    columngroup: currencyName,
                    map: 'B_'+bothCurr[j],
                    text: 'Balance'
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
        case "6.4.":

            cols.push({
                name: 'LLC',
                map: 'LLC',
                text: 'Drawdown'
            });
            cols.push({
                name: 'BLC',
                map: 'BLC',
                text: 'Balance'
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
    CreateGrid(cols, getnewloans(results), columngroups);
}