$('#mytable_purchase').on('keyup change','.calcpk',function(){
  var row    = $(this).closest('tr');
  var table  = $(this).closest('table');
  

  var d_rate    = row.find('.nkset_dealer_rate');
  var b_qty     = row.find('.quantitycl');
  var free_qty  = row.find('.free_qty');
  var grs_amt   = row.find('.grs_amt');
  var trd_amt   = row.find('.trd_amt');
  var cash_amt  = row.find('.cash_amt');
  var sch_amt   = row.find('.sch_amt');
  var spl_amt   = row.find('.spl_amt');
  var atd_amt   = row.find('.atd_amt');
  var cgst      = row.find('.cgst_amt');
  var sgst      = row.find('.sgst_amt');
  var amount    = row.find('.amount');
  var batch_no  = row.find('.batch_no');
  var mfg_date  = row.find('.mfg_date');

  var decimal = 4;

  var ga = d_rate.val()*b_qty.val(); // Gross Amount Calc
  grs_amt.val(ga.toFixed(decimal));

  var allowed = parseFloat(100);
  // console.log(allowed);
  var atda = ((allowed*(sgst.val()*2))/(allowed+(sgst.val()*2))); // ATD Amount calc
  // console.log(sgst);

  atd_amt.val(atda.toFixed(decimal));

  var trda = (ga*7)/100; // Trade Discount calc
  trd_amt.val(trda.toFixed(decimal));

  var ca  = (cash_amt.val()!='' && $.isNumeric(cash_amt.val()))?parseInt(cash_amt.val()):0;  // Cash Discount
  var sca = (sch_amt.val()!='' && $.isNumeric(sch_amt.val()))?parseInt(sch_amt.val()):0;   // Scheme Discount
  var sa  = (spl_amt.val()!='' && $.isNumeric(spl_amt.val()))?parseInt(spl_amt.val()):0;  // Special Discount

  var final_amt = ga-(trda+ca+sca+sa+atda); // Adjust all discounts from gross amount
  amount.val(final_amt.toFixed(decimal));

  var grand_cus_qty = 0;
  var grand_cus_qty = 0;
  var grand_cus_free_qty = 0;
  var grand_cus_trade_amt = 0;
  var grand_cus_cash_amt = 0;
  var grand_cus_sch_amt = 0;
  var grand_cus_spl_amt = 0;
  var grand_cus_atd_amt = 0;
  var grand_cus_cgst = 0;
  var grand_cus_sgst = 0;
  var grand_cus_t_amt = 0;

  var cus_qty = document.getElementsByName('quantity[]');
  var cus_free_qty = document.getElementsByName('scheme_quantity[]');
  var cus_trade_amt = document.getElementsByName('trade_price[]');
  var cus_cash_amt = document.getElementsByName('cash_amt[]');
  var cus_sch_amt = document.getElementsByName('scheme_amt[]');
  var cus_spl_amt = document.getElementsByName('spl_amt[]');
  var cus_atd_amt = document.getElementsByName('atd_amt[]');
  var cus_cgst = document.getElementsByName('cgst_amount[]');
  var cus_sgst = document.getElementsByName('sgst_amount[]');
  var cus_t_amt = document.getElementsByName('total_amt[]');

  for (var po = 0; po < cus_qty.length; po++)
  {
    grand_cus_qty += parseInt(cus_qty[po].value);
    grand_cus_free_qty += parseInt(cus_free_qty[po].value);
    grand_cus_trade_amt += parseFloat(cus_trade_amt[po].value);
    grand_cus_cash_amt += parseFloat(cus_cash_amt[po].value);
    grand_cus_sch_amt += parseFloat(cus_sch_amt[po].value);
    grand_cus_spl_amt += parseFloat(cus_spl_amt[po].value);
    grand_cus_atd_amt += parseFloat(cus_atd_amt[po].value);
    grand_cus_cgst += parseFloat(cus_cgst[po].value);
    grand_cus_sgst += parseFloat(cus_sgst[po].value);
    grand_cus_t_amt += parseFloat(cus_t_amt[po].value);

  }
  document.getElementById('cus_qty').value=grand_cus_qty; 
  document.getElementById('cus_free_qty').value=grand_cus_free_qty; 
  document.getElementById('cus_trade_amt').value=grand_cus_trade_amt; 
  document.getElementById('cus_cash_amt').value=grand_cus_cash_amt; 
  document.getElementById('cus_sch_amt').value=grand_cus_sch_amt; 
  document.getElementById('cus_spl_amt').value=grand_cus_spl_amt; 
  document.getElementById('cus_atd_amt').value=grand_cus_atd_amt; 
  document.getElementById('cus_cgst').value=grand_cus_cgst; 
  document.getElementById('cus_sgst').value=grand_cus_sgst; 
  document.getElementById('cus_t_amt').value=grand_cus_t_amt; 



})