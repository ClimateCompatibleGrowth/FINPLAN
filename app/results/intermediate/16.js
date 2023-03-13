//get data
function getpurchases(results) {
    var ctdata = results['results'];
    var startYear = results['startYear'];
    var endYear = results['endYear'];
    var tableid = results['tableid'];
    var bothCurr = results['bothCurr'].split(',');
    var currencies = results['currencies'];
    var producttypes = results['producttypes'];
    var cid = results['cid'];
    var prid = results['prid'];
    var datar = [];
    switch (tableid) {
        case "16.1.":
            $("#tabdetail").show();
            var controls = "<ul class='nav nav-tabs' id='plantnavs'>";
            for (var i = 0; i < bothCurr.length; i++) {
                var currencyName = $.grep(currencies, function (v) {
                    return v.id === bothCurr[i];
                })[0]['value'];
                var active = "";
                if (cid == bothCurr[i])
                    active = "active";

                controls += "<li role='presentation' class='" + active + "'> \
            <a class='pointer' onclick='getDataDetail(\"" + bothCurr[i] + "\", \"purchasecurrency\", this)' id='detail_" + bothCurr[i] + "> \
                <span lang='en'>" + currencyName + "</span></a></li>";
            }
            $("#tabdetail").html(controls);
            if (ctdata) {
                for (var i = startYear; i <= endYear; i++) {
                    var data = new Array();
                    data['id'] = i;
                    data['item'] = i.toString();
                    for (var j = 0; j < ctdata.length; j++) {
                        data['Q_' + ctdata[j]['ClientName']] = checkval(ctdata[j]['Q_' + ctdata[j]['TradeCurrency'] + '_' + i]);
                        data['P_' + ctdata[j]['ClientName']] = checkval(ctdata[j]['P_' + ctdata[j]['TradeCurrency'] + '_' + i]);
                        data['E_' + ctdata[j]['ClientName']] = checkval(ctdata[j]['E_' + ctdata[j]['TradeCurrency'] + '_' + i]);
                    }
                    datar.push(data);
                }
            }
            break;

        case "16.2.":
            $("#tabdetail").show();
            var controls = "<ul class='nav nav-tabs' id='plantnavs'>";
            for (var i = 0; i < producttypes.length; i++) {
                var active = "";
                if (prid == producttypes[i]['id'])
                    active = "active";

                controls += "<li role='presentation' class='" + active + "'> \
                <a class='pointer' onclick='getDataDetail(\"" + producttypes[i]['id'] + "\", \"purchaseproduct\", this)' id='detail_" + producttypes[i]['id'] + "> \
                    <span lang='en'>" + producttypes[i]['value'] + "</span></a></li>";
            }
            $("#tabdetail").html(controls);
            if (ctdata) {
                for (var i = startYear; i <= endYear; i++) {
                    var data = new Array();
                    data['id'] = i;
                    data['item'] = i.toString();
                    for (var j = 0; j < ctdata.length; j++) {
                        data['Q_' + ctdata[j]['ClientName']] = checkval(ctdata[j]['Q_' + ctdata[j]['TradeCurrency'] + '_' + i]);
                        data['P_' + ctdata[j]['ClientName']] = checkval(ctdata[j]['P_' + ctdata[j]['TradeCurrency'] + '_' + i]);
                        data['E_' + ctdata[j]['ClientName']] = checkval(ctdata[j]['E_' + ctdata[j]['TradeCurrency'] + '_' + i]);
                    }
                    datar.push(data);
                }
            }
            break;

        case "16.3.":
            for (var i = startYear; i <= endYear; i++) {
                var data = new Array();
                data['id'] = i;
                data['item'] = i.toString();
                data['LC'] = checkval(ctdata['LC_' + i]);
                datar.push(data);
            }
            break;
    }
    return datar;
}

function showData(results) {
    var currencies = results['currencies'];
    var tableid = results['tableid'];
    var ctdata = results['results'];
    var producttypes = results['producttypes'];
    var cid = results['cid'];
    var cols = [];
    var columngroups = [];
    switch (tableid) {
        case "16.1.":
            if (ctdata) {
                for (var j = 0; j < ctdata.length; j++) {
                    var productName = $.grep(producttypes, function (v) {
                        return v.id === ctdata[j]['Name'];
                    })[0]['value'];

                    columngroups.push({
                        text: 'Product: ' + productName + ' |  Client name: ' + ctdata[j]['ClientName'],
                        align: 'center',
                        name: ctdata[j]['ClientName'] + ' - ' + productName
                    });
                    cols.push({
                        name: 'Q_' + ctdata[j]['ClientName'],
                        columngroup: ctdata[j]['ClientName'] + ' - ' + productName,
                        map: 'Q_' + ctdata[j]['ClientName'],
                        text: 'Amount'
                    });
                    cols.push({
                        name: 'P_' + ctdata[j]['ClientName'],
                        columngroup: ctdata[j]['ClientName'] + ' - ' + productName,
                        map: 'P_' + ctdata[j]['ClientName'],
                        text: 'Price'
                    });
                    cols.push({
                        name: 'E_' + ctdata[j]['ClientName'],
                        columngroup: ctdata[j]['ClientName'] + ' - ' + productName,
                        map: 'E_' + ctdata[j]['ClientName'],
                        text: 'Expenditures'
                    });
                }
            }
            break;

        case "16.2.":
            if (ctdata) {
                for (var j = 0; j < ctdata.length; j++) {
                    var currencyName = $.grep(currencies, function (v) {
                        return v.id === ctdata[j]['TradeCurrency'];
                    })[0]['value'];

                    columngroups.push({
                        text: 'Currency: ' + currencyName + ' |  Client name: ' + ctdata[j]['ClientName'],
                        align: 'center',
                        name: ctdata[j]['ClientName'] + ' - ' + currencyName
                    });

                    cols.push({
                        name: 'Q_' + ctdata[j]['ClientName'],
                        columngroup: ctdata[j]['ClientName'] + ' - ' + currencyName,
                        map: 'Q_' + ctdata[j]['ClientName'],
                        text: 'Quantity'
                    });
                    cols.push({
                        name: 'P_' + ctdata[j]['ClientName'],
                        columngroup: ctdata[j]['ClientName'] + ' - ' + currencyName,
                        map: 'P_' + ctdata[j]['ClientName'],
                        text: 'Price'
                    });
                    cols.push({
                        name: 'E_' + ctdata[j]['ClientName'],
                        columngroup: ctdata[j]['ClientName'] + ' - ' + currencyName,
                        map: 'E_' + ctdata[j]['ClientName'],
                        text: 'Expenditures'
                    });
                }
            }
            break;

        case "16.3.":
            cols.push({
                name: 'LC',
                map: 'LC',
                text: 'Expenditures'
            });
            break;
    }
    CreateGrid(cols, getpurchases(results), columngroups);

}