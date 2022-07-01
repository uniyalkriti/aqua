  for (var i = 0; i < qty.length; i++)
    {
      // alert(tds_amt);
        //alert(i);
        var res = (qty[i].value * (rate[i].value - tds_amt[i].value) );
        amount[i].value = res;
        if(cd_type[i].value==1){
            var cd_dis = (cd[i].value * res) / 100;
            cd_amt[i].value = cd_dis.toFixed(3);
            var taxablamt = ttl_amt[i].value - cd_dis;
            taxable_amt[i].value = taxablamt.toFixed(3);
            va[i].value = taxable_amt[i].value * vat[i].value;
        }
        if(cd_type[i].value==2){
            cd_dis = cd[i].value;
            cd_amt[i].value = cd_dis; 

            var taxablamt = ttl_amt[i].value - cd_dis;
            taxable_amt[i].value = taxablamt.toFixed(3);
            va[i].value = taxable_amt[i].value * vat[i].value;     
        }
        
       //if(state[i].value==28){
           // vat[i].value = 0.0;   
        //}else{
          //  vat[i].value=vat[i].value;
       // }
        var v_amt
        if(vat[i].value==0){
           v_amt = 0;
        }
        else
        {
            v_amt= taxable_amt[i].value*(vat[i].value/100);
        }
        va[i].value = v_amt.toFixed(3);
        var surcharge_amt = v_amt*(surcharge[i].value)/100;
        //v_amt = v_amt+(v_amt*(surcharge[i].value)/100);
      //  alert(v_amt);
        var taxableamt = taxable_amt[i].value*1;
        var gamt = v_amt + surcharge_amt + taxableamt;
        if(gamt!=0){
            amount[i].value = gamt.toFixed(3);
        }
    
			var qty_val = qty[i].value;
			if(qty_val.length>=1){
			var d = qty_val.length-1;
				if(qty_val>=10){
					var sch_val = qty_val.substring(0, d);
					scheme[i].value = sch_val;
				}else{
					//console.log(qty_val);
					scheme[i].value=0;
				}
			}
		
    }