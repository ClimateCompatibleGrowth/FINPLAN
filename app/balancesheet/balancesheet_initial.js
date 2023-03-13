//get data
function showData(results) {
    var ctdata=results["ctData"];

    $("#additionalData").load("app/balancesheet/balancesheet_initial.html", function(){
        $("#chartGrid").hide();
        $("#decDown").hide();
        $("#decUp").hide();
        $("#exportgrid").hide();

        if(results.currencies != ''){
            var currencyName = $.grep(results.currencies, function (v) {
                return v.id === results.baseCurrency;
            })[0]['value'];
        }

        ctdata['Assets'] = currencyName;
        ctdata['Equity_and_Liabilities'] = currencyName;

        console.log('ctdaata ', ctdata)
        
        setValues(ctdata);
    })
}

function calculateNetFxdAsst(n){
    $("#NetFxdAsst").val( ($("#GrossFixedAssets").val()*1) - ($("#LessDepreciation").val()*1) + ($("#ConsumerContribution").val()*1));
    $('#' + n.id).val(n.value.replace(/[^\d,-]+/g, ''));
}

function validate(n){
    //console.log(n.value)
    //let Equity = n.value;
    let GrossFixedAssets = parseFloat($('#GrossFixedAssets').val()|| 0);
    let LessDepreciation = parseFloat($('#LessDepreciation').val()|| 0);
    let ConsumerContribution = parseFloat($('#ConsumerContribution').val()|| 0);
    let NetFxdAsst = parseFloat($('#NetFxdAsst').val()|| 0);
    let WorkProgress = parseFloat($('#WorkProgress').val()|| 0);
    let Receivables = parseFloat($('#Receivables').val()|| 0);
    let ShortTermDeposits = parseFloat($('#ShortTermDeposits').val()|| 0);

    let Equity = parseFloat($('#Equity').val()|| 0);
    let RetainedEarning = parseFloat($('#RetainedEarning').val()|| 0);
    let NetBondsOut = parseFloat($('#NetBondsOut').val() || 0);
    let NetloanOut = parseFloat($('#NetloanOut').val()|| 0);
    let ConsumerDeposits = parseFloat($('#ConsumerDeposits').val()|| 0);
    let Currentmaturity = parseFloat($('#Currentmaturity').val()|| 0);

    let  SumAsset= GrossFixedAssets + LessDepreciation + ConsumerContribution + NetFxdAsst + WorkProgress + Receivables + ShortTermDeposits;
    let SumEquity = Equity + RetainedEarning + NetBondsOut + NetloanOut + ConsumerDeposits + Currentmaturity;

    console.log('NetBondsOut ', NetBondsOut)

    console.log('SumEquity ', SumEquity)
    console.log('SumAsset ', SumAsset)
    if (SumEquity != SumAsset){
        ShowWarningMessage(`Assets (${SumAsset}) are not equal to Equity and Liabilities (${SumEquity}) `)
    }else{
        ShowSuccessMessage(`Assets (${SumAsset}) are equal to Equity and Liabilities (${SumEquity}) `)
    }
}