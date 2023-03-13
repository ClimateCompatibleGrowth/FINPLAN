//get data
function getfinancing(results) {
    var ctdata = results['results'];
    var startYear = results['startYear'];
    var endYear = results['endYear'];
    var tableid = results['tableid'];
    var datar = [];
    switch (tableid) {
        case "5.1.":
            for (var i = startYear; i <= endYear; i++) {
                var data = new Array();
                data['id'] = i;
                data['item'] = i.toString();
                data['IL'] = checkval(ctdata['IL_' + i]);
                data['CL'] = checkval(ctdata['CL_' + i]);
                data['GIL'] = checkval(ctdata['GIL_' + i]);
                datar.push(data);
            }
            break;

        case "5.2.":
            for (var i = startYear; i <= endYear; i++) {
                var data = new Array();
                data['id'] = i;
                data['item'] = i.toString();
                data['SLL'] = checkval(ctdata['SLL_' + i]);
                data['SLOLC'] = checkval(ctdata['SLOLC_' + i]);
                data['SLLI'] = checkval(ctdata['SLLI_' + i]);
                datar.push(data);
            }
            break;

        case "5.3.":
            for (var i = startYear; i <= endYear; i++) {
                var data = new Array();
                data['id'] = i;
                data['item'] = i.toString();
                data['SDL'] = ctdata['SDL_' + i];
                data['SDBL'] = ctdata['SDBL_' + i];
                data['SDIL'] = ctdata['SDIL_' + i];
                datar.push(data);
            }
            break;
    }
    return datar;
}

function showData(results) {
    var tableid = results['tableid'];
    var cols = [];

    switch (tableid) {
        case "5.1.":
            cols.push({
                name: 'IL',
                map: 'IL',
                text: 'Total investments'
            });
            cols.push({
                name: 'CL',
                map: 'CL',
                text: 'Committed investments'
            });
            cols.push({
                name: 'GIL',
                map: 'GIL',
                text: 'Global investments'
            });
            break;
        case "5.2.":
            cols.push({
                name: 'SLL',
                map: 'SLL',
                text: 'Drawdowns'
            });
            cols.push({
                name: 'SLOLC',
                map: 'SLOLC',
                text: 'Balance'
            });
            cols.push({
                name: 'SLLI',
                map: 'SLLI',
                text: 'Interest'
            });
            break;
        case "5.3.":
            cols.push({
                name: 'SDL',
                map: 'SDL',
                text: 'Flow to'
            });
            cols.push({
                name: 'SDBL',
                map: 'SDBL',
                text: 'Balance'
            });
            cols.push({
                name: 'SDIL',
                map: 'SDIL',
                text: 'Interest'
            });
            break;
    }
    CreateGrid(cols, getfinancing(results));
}