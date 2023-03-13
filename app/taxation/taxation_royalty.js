//get data
function getroyalty(results) {
    //console.log(results);
    var ctdata = results['ctData'];
    var startYear=results['startYear'];
    var endYear=results['endYear'];
    var datar=[];
    for (var i=startYear; i<=endYear; i++){
        var data = new Array();
        data['id']=i;
        data['item']=i.toString(); 
        data['R']=ctdata['R_'+i];
        data['C']=ctdata['C_'+i];
        datar.push(data); 
    }
    return datar;
}

function showData(results) {
    var cols=[];
    cols.push({name:'R', map: 'R', text:'Royalty Rate (%)', editable:true});
    cols.push({name:'C', map: 'C', text:'% of Cost', editable:true});
    CreateGrid(cols, getroyalty(results))
}