//get data
function getexportcredits(results) {
    var startYear = results['startYear'];
    var endYear = results['endYear'];
    var ctdata = results['results'];
    var financesources = results['financesources'];
    var curTypeSel = results['curTypeSel'].split(',');
    var bothCurr = results['bothCurr'].split(',');
    var tableid = results['tableid'];
    var plants = results['plants'];
    var rows = results['rows'];
    var rowid = results['rowid'];
    var datar = [];
    var plantid=results['pid'];
    if (plantid==null && plants)
        plantid=plants[0]['id'];

    switch (tableid) {
        case "8.1.":
        case "8.2.":
            if (tableid == "8.1.") {
                if (plants) {
                    $("#tabdetail").show();
                    var controls = "<ul class='nav nav-tabs' id='plantnavs'>";
                    for (var i = 0; i < plants.length; i++) {
                        var active = "";
                        if (i == 0)
                            active = "active";

                        controls += "<li role='presentation' class='" + active + "'> \
                    <a class='pointer' onclick='getDataDetail(" + plants[i]['id'] + ", \"exportcredits\", this, " + rows[i] + ")' id='detail_" + plants[i]['id'] + "> \
                        <span lang='en'>" + plants[i]['name'] + "</span></a></li>";
                    }
                    $("#tabdetail").html(controls);
                }
            }
            for (var i = startYear; i <= endYear; i++) {
                var data = new Array();
                data['id'] = i;
                data['item'] = i.toString();
                for (var k = 0; k < financesources.length; k++) {
                    if (financesources[k]['type'] == 'E') {
                        var fid = financesources[k]['id'];
                        for (var j = 0; j < bothCurr.length; j++) {
                            data['DD_' + bothCurr[j] + '_' + fid] = checkval(ctdata['DD_' + bothCurr[j] + '_' + fid + '_' + i]);
                            data['Bal_' + bothCurr[j] + '_' + fid] =checkval( ctdata['Bal_' + bothCurr[j] + '_' + fid + '_' + i]);
                            data['Int_' + bothCurr[j] + '_' + fid] =checkval( ctdata['Int_' + bothCurr[j] + '_' + fid + '_' + i]);
                            if (tableid == "8.1.") {
                                data['Repy_' + bothCurr[j] + '_' + fid] = checkval(ctdata['Repy_' + bothCurr[j] + '_' + fid + '_' + i+'_'+plantid]);
                            } else {
                                data['Repy_' + bothCurr[j] + '_' + fid] = checkval(ctdata['Repy_' + bothCurr[j] + '_' + fid + '_' + i]);
                            }
                        }
                    }
                }
                datar.push(data);
            }
            break;

        case "8.3.":
            for (var i = startYear; i <= endYear; i++) {
                var data = new Array();
                data['id'] = i;
                data['item'] = i.toString();
                for (var k = 0; k < financesources.length; k++) {
                    if (financesources[k]['type'] == 'E') {
                        var fid = financesources[k]['id'];
                        data['LLC_' + fid] = checkval(ctdata['LLC_' + fid + '_' + i]);
                        data['BLC_' + fid] = checkval(ctdata['BLC_' + fid + '_' + i]);
                        data['ILC_' + fid] = checkval(ctdata['ILC_' + fid + '_' + i]);
                        data['RLC_' + fid] = checkval(ctdata['RLC_' + fid + '_' + i]);
                    }
                }
                datar.push(data);
            }
            break;

        case "8.4.":
            for (var i = startYear; i <= endYear; i++) {
                var data = new Array();
                data['id'] = i;
                data['item'] = i.toString();
                for (var j = 0; j < curTypeSel.length; j++) {
                    data['L_' + curTypeSel[j]] = checkval(ctdata['L_' + curTypeSel[j] + '_' + i]);
                    data['B_' + curTypeSel[j]] = checkval(ctdata['B_' + curTypeSel[j] + '_' + i]);
                    data['I_' + curTypeSel[j]] = checkval(ctdata['I_' + curTypeSel[j] + '_' + i]);
                    data['R_' + curTypeSel[j]] = checkval(ctdata['R_' + curTypeSel[j] + '_' + i]);
                }
                datar.push(data);
            }
            break;

        case "8.5.":
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

function showData(results) {
    var bothCurr = results['bothCurr'].split(',');
    var curTypeSel = results['curTypeSel'].split(',');
    var currencies = results['currencies'];
    var financesources = results['financesources'];
    var tableid = results['tableid'];
    var cols = [];
    var columngroups = [];
    switch (tableid) {

        case "8.1.":
        case "8.2.":
            for (var k = 0; k < financesources.length; k++) {
                if (financesources[k]['type'] == 'E') {
                    var fid = financesources[k]['id'];
                    for (var j = 0; j < bothCurr.length; j++) {
                        var currencyName = $.grep(currencies, function (v) {
                            return v.id === bothCurr[j];
                        })[0]['value'];

                        columngroups.push({
                            text: financesources[k]['value'] + ' - ' + currencyName + ' (Million)',
                            align: 'center',
                            name: fid + currencyName
                        });
                        cols.push({
                            name: 'DD_' + bothCurr[j] + '_' + fid,
                            columngroup: fid + currencyName,
                            map: 'DD_' + bothCurr[j] + '_' + fid,
                            text: 'Drawdowns'
                        });
                        cols.push({
                            name: 'Bal_' + bothCurr[j] + '_' + fid,
                            columngroup: fid + currencyName,
                            map: 'Bal_' + bothCurr[j] + '_' + fid,
                            text: 'Balance'
                        });
                        cols.push({
                            name: 'Int_' + bothCurr[j] + '_' + fid,
                            columngroup: fid + currencyName,
                            map: 'Int_' + bothCurr[j] + '_' + fid,
                            text: 'Interest'
                        });
                        cols.push({
                            name: 'Repy_' + bothCurr[j] + '_' + fid,
                            columngroup: fid + currencyName,
                            map: 'Repy_' + bothCurr[j] + '_' + fid,
                            text: 'Repayment'
                        });
                    }
                }
            }
        break;
        case "8.3.":
            for (var k = 0; k < financesources.length; k++) {
                if (financesources[k]['type'] == 'E') {
                    var fid = financesources[k]['id'];
                    columngroups.push({
                        text: financesources[k]['value'] + ' (Million)',
                        align: 'center',
                        name: fid
                    });
                    cols.push({
                        name: 'LLC_' + fid,
                        columngroup: fid,
                        map: 'LLC_' + fid,
                        text: 'Drawdowns'
                    });
                    cols.push({
                        name: 'BLC_' + fid,
                        columngroup: fid,
                        map: 'BLC_' + fid,
                        text: 'Balance'
                    });
                    cols.push({
                        name: 'ILC_' + fid,
                        columngroup: fid,
                        map: 'ILC_' + fid,
                        text: 'Interest'
                    });
                    cols.push({
                        name: 'RLC_' + fid,
                        columngroup: fid,
                        map: 'RLC_' + fid,
                        text: 'Repayment'
                    });
                }
            }
            break;

        case "8.4.":
            for (var j = 0; j < curTypeSel.length; j++) {
                var currencyName = $.grep(currencies, function (v) {
                    return v.id === curTypeSel[j];
                })[0]['value'];
                columngroups.push({
                    text: currencyName + ' (Million)',
                    align: 'center',
                    name: currencyName
                });
                cols.push({
                    name: 'L_' + curTypeSel[j],
                    columngroup: currencyName,
                    map: 'L_' + curTypeSel[j],
                    text: 'Drawdowns'
                });
                cols.push({
                    name: 'B_' + curTypeSel[j],
                    columngroup: currencyName,
                    map: 'B_' + curTypeSel[j],
                    text: 'Balance'
                });
                cols.push({
                    name: 'I_' + curTypeSel[j],
                    columngroup: currencyName,
                    map: 'I_' + curTypeSel[j],
                    text: 'Interest'
                });
                cols.push({
                    name: 'R_' + curTypeSel[j],
                    columngroup: currencyName,
                    map: 'R_' + curTypeSel[j],
                    text: 'Repayment'
                });
            }
            break;

        case "8.5.":
            cols.push({
                name: 'LLC',
                map: 'LLC',
                text: 'Drawdowns'
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
    CreateGrid(cols, getexportcredits(results), columngroups);

}