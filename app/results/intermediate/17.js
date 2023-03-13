//get data
function getotherincomes(results) {
    var ckdata=results['ckData'];
    var cndata=results['cnData'];
    var bgdata=results['bgData'];
    var cjdata=results['cjData'];
    var cldata=results['clData'];

    var cbdata=results['cbData'];
    var ccdata=results['ccData'];
    var cddata=results['cdData'];
    var chdata=results['chData'];
    var cidata=results['ciData'];
    var cmdata=results['cmData'];

    var agdata=results['agData'];
    var bsdata=results['bsData'];

    var ctdata=results['results'];
    var startYear = results['startYear'];
    var endYear = results['endYear'];
    var tableid = results['tableid'];
    var datar = [];
    switch (tableid) {
        case "17.1.":
            for (var i = startYear; i <= endYear; i++) {
                var data = new Array();
                data['id'] = i;
                data['item'] = i.toString();
                data['FR'] = checkval(ctdata['FR_' + i]);
                data['OI'] = checkval(ctdata['OI_' + i]);
                data['TOI'] = checkval(ctdata['TOI_' + i]);
                datar.push(data);
            }
            break;
        case "17.2.":
            for (var i = startYear; i <= endYear; i++) {
                var data = new Array();
                data['id'] = i;
                data['item'] = i.toString();
                data['R'] = checkval(cjdata['R_' + i]);
                data['O'] = checkval(cjdata['O_' + i]);
                data['T'] = checkval(bgdata['T_' + i]);
                data['TIL'] =  checkval(cndata['TIL_' + i]);
                data['SDIL'] = checkval(cndata['SDIL_' + i]);
                data['RLC'] =  checkval(ckdata['RLC_' + i]);
                data['PFLY'] = checkval(cldata['PFLY_' + i]);
                data['TaxIL'] =checkval( cndata['TaxIL_' + i]);
                data['CTIL'] = checkval(cndata['CTIL_' + i]);

                data['TLCF'] = checkval(cndata['TLCF_' + i]);
                data['NTI'] = checkval(cndata['NTI_' + i]);

                data['DivL'] = checkval(cndata['DivL_' + i]);
                data['ITL'] = checkval(cndata['ITL_' + i]);
                datar.push(data);
            }
            break;

            case "17.3.":
                for (var i = startYear; i <= endYear; i++) {
                    var data = new Array();
                    data['id'] = i;
                    data['item'] = i.toString();
                    data['PICL'] = checkval(cndata['PICL_' + i]);
                    data['C'] = checkval(bsdata['C_' + i]);
                    data['D'] = checkval(bsdata['D_' + i]);
                    data['SDIL'] = checkval(cndata['SDIL_' + i]);
                    data['SDBL'] = checkval(cndata['SDBL_' + (i-1)]);
                    data['N'] = checkval(cddata['N_' + i]);
                    data['BLC'] = checkval(ccdata['BLC_' + i]*1+cbdata['BDL_'+i]*1);
                    data['LLC'] = checkval(chdata['LLC_' + i]*1+cndata['TCLL_'+i]*1);
                    data['SLL'] = checkval(Math.max(cndata['SLL_'+i]));
                    data['TSL'] = checkval(cndata['TSL_' + i]);
                    datar.push(data);
                }
                break;
                case "17.4.":
                    for (var i = startYear; i <= endYear; i++) {
                        var data = new Array();
                        data['id'] = i;
                        data['item'] = i.toString();
                        data['GIL'] = checkval(agdata['GIL_' + i]);
                        data['TIL'] = checkval(cndata['TIL_' + i]);
                        data['TRL'] = checkval(cndata['TRL_' + i]);
                        data['SLRL'] = checkval(cndata['SLRL_' + i]);
                        data['SDBL'] = checkval(cndata['SDBL_' + i]);
                        data['TERL'] = checkval(cndata['TERL_' + i]);
                        data['DivL'] = checkval(cndata['DivL_' + i]);
                        data['TT'] = checkval(cmdata['TT_' + i]);
                        data['FLC'] = checkval(chdata['FLC_'+i]);
                        data['TAL'] = checkval(cndata['TAL_' + i]);
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
        case "17.1.":
            cols.push({
                name: 'FR',
                map: 'FR',
                text: 'Fixed revenues'
            });
            cols.push({
                name: 'OI',
                map: 'OI',
                text: 'Other income'
            });
            cols.push({
                name: 'TOI',
                map: 'TOI',
                text: 'Total other income'
            });
        break;

        case "17.2.":
            cols.push({
                name: 'R',
                map: 'R',
                text: 'Revenues'
            });
            cols.push({
                name: 'O',
                map: 'O',
                text: 'Oper. costs'
            });
            cols.push({
                name: 'T',
                map: 'T',
                text: 'Depreciation'
            });

            cols.push({
                name: 'TIL',
                map: 'TIL',
                text: 'Tot. interest paid'
            });
            cols.push({
                name: 'SDIL',
                map: 'SDIL',
                text: 'Interest earned'
            });
            cols.push({
                name: 'RLC',
                map: 'RLC',
                text: 'Royalty'
            });
            cols.push({
                name: 'PFLY',
                map: 'PFLY',
                text: 'Prov. foreign loss'
            });
            cols.push({
                name: 'TaxIL',
                map: 'TaxIL',
                text: 'Taxable income'
            });
            cols.push({
                name: 'CTIL',
                map: 'CTIL',
                text: 'Cum. taxable income'
            });
            cols.push({
                name: 'TLCF',
                map: 'TLCF',
                text: 'Taxloss carryforward'
            });
            cols.push({
                name: 'NTI',
                map: 'NTI',
                text: 'Net taxable income'
            });
            cols.push({
                name: 'DivL',
                map: 'DivL',
                text: 'Dividend'
            });
            cols.push({
                name: 'ITL',
                map: 'ITL',
                text: 'Income tax'
            });
            break;

            case "17.3.":
                cols.push({
                    name: 'PICL',
                    map: 'PICL',
                    text: 'Pre investment cash'
                });
                cols.push({
                    name: 'C',
                    map: 'C',
                    text: 'Net increase in consumer contribution'
                });
                cols.push({
                    name: 'D',
                    map: 'D',
                    text: 'Consumer deposits'
                });
    
                cols.push({
                    name: 'SDIL',
                    map: 'SDIL',
                    text: 'Interest earned'
                });
                cols.push({
                    name: 'SDBL',
                    map: 'SDBL',
                    text: 'Cash available in short term deposits'
                });
                cols.push({
                    name: 'N',
                    map: 'N',
                    text: 'New Equity'
                });
                cols.push({
                    name: 'BLC',
                    map: 'BLC',
                    text: 'New+old bonds issue'
                });
                cols.push({
                    name: 'LLC',
                    map: 'LLC',
                    text: 'Loan Drawdown'
                });
                cols.push({
                    name: 'SLL',
                    map: 'SLL',
                    text: 'Drawdown on stand-by facility'
                });
                cols.push({
                    name: 'TSL',
                    map: 'TSL',
                    text: 'Total Sources'
                });

            break;

            case "17.4.":
                cols.push({
                    name: 'GIL',
                    map: 'GIL',
                    text: 'Global investment'
                });
                cols.push({
                    name: 'TIL',
                    map: 'TIL',
                    text: 'Total interest paid'
                });
                cols.push({
                    name: 'TRL',
                    map: 'TRL',
                    text: 'Total repayment'
                });
    
                cols.push({
                    name: 'SLRL',
                    map: 'SLRL',
                    text: 'Repayment of stand-by facility'
                });
                cols.push({
                    name: 'SDBL',
                    map: 'SDBL',
                    text: 'Cash reinvested in short term deposits'
                });
                cols.push({
                    name: 'TERL',
                    map: 'TERL',
                    text: 'Equity repayment'
                });
                cols.push({
                    name: 'DivL',
                    map: 'DivL',
                    text: 'Dividend'
                });
                cols.push({
                    name: 'TT',
                    map: 'TT',
                    text: 'VAT to recover'
                });
                cols.push({
                    name: 'FLC',
                    map: 'FLC',
                    text: 'Export credit fees'
                });
                cols.push({
                    name: 'TAL',
                    map: 'TAL',
                    text: 'Total application'
                });

            break;
    }
    CreateGrid(cols, getotherincomes(results));
}