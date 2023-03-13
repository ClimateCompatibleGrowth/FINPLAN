//get data
function showData(results) {
    var ctdata=results["ctData"];
    
    if(results.currencies != ''){
        var currencyName = $.grep(results.currencies, function (v) {
            return v.id === results.baseCurrency;
        })[0]['value'];
    }
    ctdata['Fixed_revenues_initial_year'] = currencyName;
    ctdata['Other_income_initial_year'] = currencyName;

    $("#additionalData").load("app/sales/sales_revenues.html", function(){
        $("#chartGrid").hide();
        $("#decDown").hide();
        $("#decUp").hide();
        $("#exportgrid").hide();
        setValues(ctdata);
    })
}