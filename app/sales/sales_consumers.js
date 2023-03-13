//get data
function getconsumers(results) {
    var ctdata = results['ctData'];
    var startYear = results['startYear'];
    var endYear = results['endYear'];
    var datar = [];
    for (var i = startYear; i <= endYear; i++) {

        var data = new Array();
        data['id'] = i;
        data['item'] = i.toString();
        data['C'] = ctdata['C_' + i];
        data['D'] = ctdata['D_' + i];
        datar.push(data);
    }
    return datar;
}

function showData(results) {

    var cols = [];
    cols.push({
        name: 'C',
        editable: true,
        map: 'C',
        text: 'Contribution'
    });
    cols.push({
        name: 'D',
        editable: true,
        map: 'D',
        text: 'Deposits'
    });
    CreateGrid(cols, getconsumers(results))
}