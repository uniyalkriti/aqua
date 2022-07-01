$('#mytable').on('keyup change','.calcpk',function(){
  var row    = $(this).closest('tr');
  var table  = $(this).closest('table');
//alert("test");
  if(table.attr('class')!='')
  {
    if(table.attr('class').indexOf("table") >= 0)
    {
       /* For calculation on SFA popup*/
      var order_id = table.find('.oid').val();
      var $total_td = '#total_td'+order_id+'';
      var $mytable = '.table'+order_id;
      var $total_cd = '#total_cd'+order_id;
      var $total_vat = '#total_vat'+order_id;
      var $total_taxable = '#total_taxable'+order_id;
      var $total = '#total'+order_id;
      var $dis = '#dis'+order_id;
      var $tot_disc = '#total_disc'+order_id;
      var $tot_amount_a = '#total_amount_a'+order_id;
      var $final_amount_a = '#final_amount_a'+order_id; // NEW Add 09-04-2018
      var check_gst = $('.pkigst'+order_id+'').val();

    }
  }else{
    /* For calculation direct challan page*/
    var $total_td = '#total_td';
    var $mytable  = '#mytable';
    var $total_cd = '#total_cd';
    var $total_vat = '#total_vat';
    var $total_taxable = '#total_taxable';
    var $total = '#total';
    var $dis = '#dis';
    var $tot_disc = '#total_disc';
    var $tot_amount_a = '#total_amount_a';
    var $final_amount_a = '#final_amount_a'; // NEW Add 09-04-2018
    var check_gst = $('.pkigst').val();
  } 
  

    /*if(row.find('.mrp').val())
    {*/

      var productid  = row.find('.item_details').val();
      var mrp         = row.find('.mrp').val();
      var qty         = row.find('.quantitycl').val();
      var rate        = row.find('.rate').val();
      var trade_type  = row.find('.trade_disc_type').val();
      var trade_val   = row.find('.trade_disc_val').val();
      var trade_amt   = row.find('.trade_disc_amt');
      var cd_type     = row.find('.cd_type').val();
      var cd_val      = row.find('.cd').val();
      var cd_amt      = row.find('.cd_amt');
      var spl_disc_type = row.find('.spl_disc_type').val();
      var spl_disc_val = row.find('.spl_disc_val').val();
      var spl_disc_amt = row.find('.spl_disc_amt');
      var cash_amt = row.find('.cash_amt').val();
      var taxable     = row.find('.taxable_amt');
      var sgst        = row.find('.sgst').val();
      var sgst_amt    = row.find('.sgst_amt');
      var cgst        = row.find('.cgst').val();
      var cgst_amt    = row.find('.cgst_amt');
      var vat         = row.find('.vat').val();
      var vat_amt     = row.find('.vat_amt');
      var amount      = row.find('.amount');
      
     // if(productid == '' || productid == NULL){
     if(productid=='' || !productid){
      var product_id  = row.find('.item_details_retail').val();
      }else{
          var product_id  = productid;
      }
      
     //  var id = $(this).closest('tr').find('.item_details_retail').val();
      
     /// alert(product_id);

      if(!qty || !mrp)
      {
        row.find('.trade_disc_val').val('');
        trade_amt.val('');

        row.find('.spl_disc_val').val('');
        spl_disc_amt.val('');
        row.find('.cash_amt').val('');

        row.find('.cd').val('');
        cd_amt.val('');
        taxable.val('');
            // row.find('.vat').val('');
            vat_amt.val('');
            amount.val('');
          }

          if(trade_val=='' || !trade_val)
          {
            trade_val=0;
          }

          if(spl_disc_val=='' || !spl_disc_val)
          {
            spl_disc_val=0;
          }

          if(cd_val=='' || !cd_val)
          {
            cd_val=0;
          }
          if(cash_amt=='' || !cash_amt)
          {
            cash_amt=0;
          }

          var sub_total = qty*rate;
          var taxable_sub_total = sub_total;
          var allowed = parseFloat(100);


          if(parseInt(check_gst)==1 && sgst>0)
          {            
            var x = ((allowed*(sgst*2))/(allowed+(sgst*2)));
            cd_val = x.toFixed(2);
            row.find('.cd').val(cd_val);
           }
           else if(parseInt(check_gst)==0 && vat>0){
            var x = (allowed*parseFloat(vat))/(allowed+parseFloat(vat));
            cd_val = x.toFixed(2);
            row.find('.cd').val(cd_val);
          }


         // 1 = %, 2 = amount 
         switch(trade_type)
         {
          case '1':
          if(parseFloat(trade_val)>allowed)
          {
            alert('Invalid trade discount');
            row.find('.trade_disc_val').focus();
            return false;
          }else{
            var trd_amt = sub_total*trade_val/100;
          }
          break;

          case '2':
          var trd_amt = parseFloat(trade_val);
          break;

          default:
          break;
        }

        trade_amt.val(trd_amt.toFixed(2));

         // taxable amount after trade discount
         taxable_sub_total = sub_total-trd_amt;
         


         switch(spl_disc_type)
         {
           case '1':
           if(parseFloat(spl_disc_val)>allowed)
           {
             alert('Invalid special discount');
             row.find('.spl_disc_val').focus();
             return false;
           }else{
             var spl_amt = taxable_sub_total*spl_disc_val/100;                         
           }
           break;

           case '2':
           var spl_amt = parseFloat(spl_disc_val);
           break;

           default:
           break;
         }

         spl_disc_amt.val(spl_amt.toFixed(2));

           // taxable amount after special discount
           taxable_sub_total = taxable_sub_total-spl_amt;

           //var cas_amt = parseFloat(cash_amt);
           //cash_amt.val(cas_amt.toFixed(2));
           if(cash_amt>0)
           {
           taxable_sub_total = taxable_sub_total-cash_amt;
           }

           switch(cd_type)
           {
            case '1':
            if(parseFloat(cd_val)>allowed)
            {
              alert('Invalid cash discount');
              row.find('.cd').focus();
              return false;
            }else{
              var cdamt = taxable_sub_total*cd_val/100;
            }
            break;

            case '2':
            var cdamt = parseFloat(cd_val);
            break;
            
            default:
            break;
          }

          cd_amt.val(cdamt.toFixed(2));

          // taxable amount after cash discount
          taxable_sub_total = taxable_sub_total-cdamt;


          taxable.val(taxable_sub_total.toFixed(2));

            // 0 = igst, 1 = cgst,sgst
            if(parseInt(check_gst))
            {
              var tax_amt = taxable_sub_total*sgst/100;
              sgst_amt.val(tax_amt.toFixed(2));
              cgst_amt.val(tax_amt.toFixed(2));
              amount.val((taxable_sub_total+(tax_amt*2)).toFixed(2));
            }else{
              var tax_amt = taxable_sub_total*vat/100;
              vat_amt.val(tax_amt.toFixed(2));
              amount.val((taxable_sub_total+tax_amt).toFixed(2));
            }



            /* Sum all calculated value and put in the total fileds */
            var ttd  = parseFloat(0);
            var tcd  = parseFloat(0);
            var std  = parseFloat(0);    
            var tta  = parseFloat(0);
            var ttxa = parseFloat(0);
            var ta   = parseFloat(0);

            var allowed = parseInt(0);

            $(''+$mytable+' .tdata').each(function(){

              /* Check if duplicate product is selected */
              var loop_product_id = $(this).find('.item_details').val();
              var loop_mrp = $(this).find('.mrp').val();
         //  alert(product_id);
        //   alert(loop_product_id);
              if((product_id==loop_product_id) && (parseInt(mrp)==loop_mrp))
              {
                allowed += 1;
              }


              if($(this).find('.trade_disc_amt').val()>0)
              {
                ttd += parseFloat($(this).find('.trade_disc_amt').val());
              }

              if($(this).find('.spl_disc_amt').val()>0)
              {
                std += parseFloat($(this).find('.spl_disc_amt').val());
              }

              if($(this).find('.cd_amt').val()>0)
              {
                tcd += parseFloat($(this).find('.cd_amt').val());
              }

              if($(this).find('.taxable_amt').val()>0)
              {
                tta += parseFloat($(this).find('.taxable_amt').val());
              }

              
              // 0 = igst, 1 = cgst,sgst
              if(parseInt(check_gst))
              {
                  if($(this).find('.cgst_amt').val()>0)
                  {
                    ttxa += parseFloat($(this).find('.cgst_amt').val()*2);
                  }
              }else{
                  if($(this).find('.vat_amt').val()>0)
                  {
                    ttxa += parseFloat($(this).find('.vat_amt').val());
                  }
              }

              if($(this).find('.amount').val()>0)
              {
                ta += parseFloat($(this).find('.amount').val());
              }
            })
            //alert(allowed);
            /* Check if duplicate product is selected */
            if(allowed>1)
            {
              alert('Duplicate product not allowed.');
              row.find('input').val(0);
              row.find('.mrp').html('');
              return false;
            }

            $($total_td).val(ttd.toFixed(2));
            $($total_cd).val(tcd.toFixed(2));
            $($total_taxable).val(tta.toFixed(2));
            $($total_vat).val(ttxa.toFixed(2));
            $($total).val(ta.toFixed(2));


            /* Final calculation */
            var discount = $($dis).val();    

            if(discount>0)
            {
              var total_disc = ta*discount/100;
              $($tot_disc).val(total_disc.toFixed(2));

              var total_amount_a = ta-total_disc;
              $($tot_amount_a).val(total_amount_a.toFixed(2));

              $($final_amount_a).val(Math.round(total_amount_a));  // NEW Add 09-04-2018
            }else{
              $($tot_disc).val(0.00);
              $($tot_amount_a).val(ta.toFixed(2));

                $($final_amount_a).val(Math.round(ta));  // NEW Add 09-04-2018
            }

          })