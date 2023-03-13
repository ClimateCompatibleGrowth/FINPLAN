//get data
function getdecomissioning(results) {
    var ctdata = results['results'];
    var startYear = results['startYear'];
    var endYear = results['endYear'];
    var tableid = results['tableid'];
    var plants = results['plants'];
    var pid = results['pid'];
    var datar = [];

    if (tableid == "14.2.") {
        if (plants) {
            $("#tabdetail").show();
            var controls = "<ul class='nav nav-tabs' id='plantnavs'>";
            var plants = Object.keys(plants).map(key => {
                return plants[key];
            });

            for (var i = 0; i < plants.length; i++) {
                var active = "";
                if (i == 0)
                    active = "active";

                controls += "<li role='presentation' class='" + active + "'> \
            <a class='pointer' onclick='getDataDetail(" + plants[i]['id'] + ", \"decomissioning\", this)' id='detail_" + plants[i]['id'] + "> \
                <span lang='en'>" + plants[i]['name'] + "</span></a></li>";
            }
            $("#tabdetail").html(controls);
        }
    }
    for (var i = startYear; i <= endYear; i++) {
        var data = new Array();
        data['id'] = i;
        data['item'] = i.toString();
        if (tableid == "14.1.") {
            data['TDCL'] = checkval(ctdata['TDCL_' + i]);
        } else {
            data['DCL'] = checkval(ctdata['DCL_' + pid + '_' + i]);
        }

        data['ADFL'] = checkval(ctdata['ADFL_' + i]);
        datar.push(data);
    }


    return datar;
}

function showData(results) {
    var tableid = results['tableid'];
    var cols = [];

    if (tableid == "14.1.") {
        cols.push({
            name: 'TDCL',
            map: 'TDCL',
            text: 'External Trust'
        });
    } else {
        cols.push({
            name: 'DCL',
            map: 'DCL',
            text: 'External Trust'
        });
    }
    cols.push({
        name: 'ADFL',
        map: 'ADFL',
        text: 'Internal Fund'
    });
    CreateGrid(cols, getdecomissioning(results));
}