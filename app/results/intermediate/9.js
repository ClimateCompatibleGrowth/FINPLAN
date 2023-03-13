//get data
function getequity(results) {
    var ctdata = results['results'];
    var cndata = results['resultscn'];
    var startYear = results['startYear'];
    var endYear = results['endYear'];
    var tableid = results['tableid'];
    var datar = [];
    switch (tableid) {
        case "9.1.":
            var x = 0;
            for (var i = startYear - 1; i <= endYear; i++) {
                var data = new Array();
                data['id'] = i;
                data['item'] = i.toString();
                if (x == 0) {
                    data['N'] = 0;
                    data['T'] = ctdata['IE'];
                    data['E'] = 0;
                    data['DivL'] = 0;
                } else {

                    data['N'] = checkval(ctdata['N_' + i]);
                    data['T'] = checkval(ctdata['T_' + i]);
                    data['E'] = checkval(ctdata['E_' + i]);
                    data['DivL'] = checkval(cndata['DivL_' + i]);
                }
                datar.push(data);
                x++;
            }
            break;
    }
    return datar;
}

function showData(results) {
    var tableid = results['tableid'];
    var cols = [];

    switch (tableid) {
        case "9.1.":
            cols.push({
                name: 'N',
                map: 'N',
                text: 'Equity drawdown'
            });
            cols.push({
                name: 'T',
                map: 'T',
                text: 'Equity outstanding'
            });
            cols.push({
                name: 'E',
                map: 'E',
                text: 'Equity repayments'
            });
            cols.push({
                name: 'DivL',
                map: 'DivL',
                text: 'Dividend'
            });
            break;
    }
    CreateGrid(cols, getequity(results));
}