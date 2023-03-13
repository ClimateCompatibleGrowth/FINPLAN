var url = "app/data/data.php";
//get data
function showData(results) {
    $("#additionalData").load("app/financialmanager/financialmanager_other.html", function () {
        var currencies = results['currencies'];
        var baseCurrency = results['baseCurrency'];
        var baseCurrencyName = $.grep(currencies, function (v) {
            return v.id === baseCurrency;
        })[0]['value'];

        $("#currencylabel").html(" (Million "+baseCurrencyName+")");

        $("#chartGrid").hide();
        $("#decDown").hide();
        $("#decUp").hide();
        $("#exportgrid").hide();
        $("#savedata").hide();

        var cfData = results['cfData'];
        var sfData = results['sfData'];
        var bnData = results['bnData'];

        $('#cfid').val(cfData.id);
        $('#STDeposit').val(check(cfData.STDeposit));
        $('#SBFacility').val(check(cfData.SBFacility));
        $('#SLInitial').val(check(cfData.SLInitial));

        $('#sfid').val(sfData.id);
        $('#A_ROR').val(check(sfData.A_ROR));
        $('#Disposal_Year').val(check(sfData.Disposal_Year));
        $('#D_Rate').val(check(sfData.D_Rate));


        $('#bnid').val(bnData.id);
        $('#DRate').val(check(bnData.DRate));
        $('#Loan_Term').val(check(bnData.Loan_Term));
        $('#Security_ratio1').val(check(bnData.Security_ratio1));
        $('#Life_Term').val(check(bnData.Life_Term));
        $('#Security_ratio2').val(check(bnData.Security_ratio2));
        $('#FY_Cash_DebtService').val(check(bnData.FY_Cash_DebtService));
    })
}

function saveDataOtherFinancial() {

    if(!(required("STDeposit", "Spread for short term deposits is required!") &&
    required("SBFacility","Spread for stand-by facility is required!") &&
    required("SLInitial", "Short loans outstanding initial is required!")))
    return false;

    var cfid = $('#cfid').val();
    var STDeposit = $('#STDeposit').val();
    var SBFacility = $('#SBFacility').val();
    var SLInitial = $('#SLInitial').val();
    var other = {
        sid: 1,
        id: cfid,
        STDeposit: STDeposit,
        SBFacility: SBFacility,
        SLInitial: SLInitial
    }

    $.ajax({
        type: "POST",
        url: url,
        data: {
            other: other,
            type:'otherfinancial',
            action: "edit",
            id: "financialmanager_other"
        },
        success: function () {
            ShowSuccessMessage('Data saved successfully');
            $('#fgstudyname').removeClass('has-error');
        },
        failure: function () {
            ShowErrorMessage("Error!");
        }
    });

}

function saveDataShareholders() {

    if(!(
    required("A_ROR", "Approx. average return is required!") &&
    required("Disposal_Year", "Disposal year is required!") &&
    required("D_Rate","Discount rate is required!")))
    return false;

    var sfid = $('#sfid').val();
    var A_ROR = $('#A_ROR').val();
    var Disposal_Year = $('#Disposal_Year').val();
    var D_Rate = $('#D_Rate').val();
    var shareholders = {
        sid: 1,
        id: sfid,
        A_ROR: A_ROR,
        Disposal_Year: Disposal_Year,
        D_Rate: D_Rate
    }

    $.ajax({
        type: "POST",
        url: url,
        data: {
            other: shareholders,
            type:'shareholders',
            action: "edit",
            id: "financialmanager_other"
        },
        success: function () {
            ShowSuccessMessage('Data saved successfully');
            $('#fgstudyname').removeClass('has-error');
        },
        failure: function () {
            ShowErrorMessage("Error!");
        }
    });

}

function saveDataTherms() {

    if(!(required("DRate", "Discount rate is required!") &&
    required("Loan_Term", "Average loan term is required!") &&
    required("Security_ratio1", "Security ratio for loan period is required!") &&
    required("Life_Term","Expected life of project is required!") &&
    required("Security_ratio2", "Security Ratio for project life is required!") &&
    required("FY_Cash_DebtService", "First year of cash to debt service is required!")
    ))
    return false;

    var bnid = $('#bnid').val();
    var DRate = $('#DRate').val();
    var Loan_Term = $('#Loan_Term').val();
    var Security_ratio1 = $('#Security_ratio1').val();
    var Life_Term = $('#Life_Term').val();
    var Security_ratio2 = $('#Security_ratio2').val();
    var FY_Cash_DebtService = $('#FY_Cash_DebtService').val();
    var terms = {
        sid: 1,
        id: bnid,
        DRate: DRate,
        Loan_Term: Loan_Term,
        Security_ratio1: Security_ratio1,
        Life_Term: Life_Term,
        Security_ratio2: Security_ratio2,
        FY_Cash_DebtService: FY_Cash_DebtService
    }

    $.ajax({
        type: "POST",
        url: url,
        data: {
            other: terms,
            type: 'terms',
            action: "edit",
            id: "financialmanager_other"
        },
        success: function () {
            ShowSuccessMessage('Data saved successfully');
            $('#fgstudyname').removeClass('has-error');
        },
        failure: function () {
            ShowErrorMessage("Error!");
        }
    });

}