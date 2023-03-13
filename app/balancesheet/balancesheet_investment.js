//get data
function getinvestment(results) {
    //console.log(results);
    var ctdata = results['ctData'];
    var startYear=results['startYear'];
    var endYear=results['endYear'];
    var baseCurrency=results['baseCurrency'];
    var curTypeSel=results['curTypeSel'].split(',');
    var datar=[];
for (var i=startYear; i<=endYear; i++){
        
            var data = new Array();
            data['id']=i;
            data['item']=i.toString(); 
            for(var j=0; j<curTypeSel.length; j++){
                data['C_'+curTypeSel[j]]=ctdata['C_'+curTypeSel[j]+'_'+i];
            }
            data['C_'+baseCurrency]=ctdata['C_'+baseCurrency+'_'+i];
            datar.push(data); 
    }
return datar;
}

function showData(results) {
    var baseCurrency=results['baseCurrency'];
    var curTypeSel=results['curTypeSel'].split(',');
    var currencies=results['currencies'];

    if(currencies != ''){
        var baseCurrencyName = $.grep(currencies, function(v) {
            return v.id === baseCurrency;
        })[0]['value'];
    }

    var cols=[];
    if(results['curTypeSel'].length>0){
    for(var j=0; j<curTypeSel.length; j++){

        if(currencies != ''){
            var currencyName = $.grep(currencies, function(v) {
                return v.id === curTypeSel[j];
            })[0]['value'];
        }

        cols.push({name:'C_'+curTypeSel[j], editable:true, map: 'C_'+curTypeSel[j], text:currencyName});
    }
}
    cols.push({name:'C_'+baseCurrency, map: 'C_'+baseCurrency, editable:true, text:baseCurrencyName});
    CreateGrid(cols, getinvestment(results));

    $("#notevalueentered").html("Note that it is assumed that inflation/escalation is already accounted for. Therefore, committed investments need to be entered in nominal terms in the year of expenditure.");

}