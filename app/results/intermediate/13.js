//get data
function getdepreciation(results) {
    var ctdata = results['results'];
    var startYear = results['startYear'];
    var endYear = results['endYear'];
    var tableid = results['tableid'];
    var plants = results['plants'];
    var rows = results['rows'];
    var datar = [];

    if (tableid == "13.2.") {
        if (plants) {
            $("#tabdetail").show();
            var controls = "<ul class='nav nav-tabs' id='plantnavs'>";
            for (var i = 0; i < plants.length; i++) {
                var active = "";
                if (i == 0)
                    active = "active";

                controls += "<li role='presentation' class='" + active + "'> \
        <a class='pointer' onclick='getDataDetail(" + plants[i]['id'] + ", \"depreciation\", this, " + rows[i] + ")' id='detail_" + plants[i]['id'] + "> \
            <span lang='en'>" + plants[i]['name'] + "</span></a></li>";
            }
            $("#tabdetail").html(controls);
        }
    }

    for (var i = startYear; i <= endYear; i++) {
        var data = new Array();
        data['id'] = i;
        data['item'] = i.toString();
        if (tableid == "13.1.") {
            data['T'] = checkval(ctdata['T_' + i]);
        } else {
            data['D'] = checkval(ctdata['D_' + i]);
        }

        datar.push(data);
    }
    return datar;
}

function showData(results) {
    var tableid = results['tableid'];
    var cols = [];

    if (tableid == "13.1.") {
        cols.push({
            name: 'T',
            map: 'T',
            text: 'Total depreciation'
        });
    } else {
        cols.push({
            name: 'D',
            map: 'D',
            text: 'Depreciation'
        });
    }
    CreateGrid(cols, getdepreciation(results));
}