<!DOCTYPE html>
<html lang="en">

        <div class="searchlistdiv watermark" id="searchlistdiv"     style="border: solid 1px #000;"> 
            <p align="center" style="height: 80px;" ><strong style="padding-left: 180px;">{{$company_details->title}}<br>
                <img src="{{asset($company_details->company_image)}}" width="140px" height="50px" class="img-responsive" style="padding-right: 100px; padding-top: 0px;">{{$company_details->address}}<br><b style="padding-left: 180px;">{{$company_details->website}}</b></strong>
            </p>
           
    <table border="1" cellspacing="0" cellpadding="0" height="30px" width="100%">
        <tr>
            <th style="text-align: center;"><i class="ace-icon fa fa-map-pin"></i>&nbsp;&nbsp;Payslip for the month {{date('M-Y',strtotime($month))}}</th>
        </tr>
    </table>
    <table border="0" cellspacing="0" cellpadding="0" width="100%">

        <tr>
            <td style="text-align: left;">&nbsp;&nbsp;Emp Id:</td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="#">{{!empty($user_details->emp_code)?$user_details->emp_code:''}}</a></td>

            <td style="text-align: left;">&nbsp;&nbsp;Location:</td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="#">{{$user_details->l3_name}}</a></td>
            
        </tr>
      
        <tr>
            <td style="text-align: left;">&nbsp;&nbsp;Employer Name: </td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="#">{{$user_details->user_name}}</a></td>

             <td style="text-align: left;">&nbsp;&nbsp;Bank Name:</td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="#">{{$salary_details->bank_name}}</a></td>

            
              
            
        </tr>
        <tr>
            <td style="text-align: left;">&nbsp;&nbsp;Designation:</td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="#">{{$user_details->rolename}}</a></td>

            <td style="text-align: left;">&nbsp;&nbsp;Bank A/C No:</td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="#">{{$salary_details->account_no}}</a></td>
           

            

        </tr>
        <tr>
        
            <td style="text-align: left;">&nbsp;&nbsp;Date of Join:</td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="">{{date('d-M-y',strtotime($user_details->joining_date))}}</a></td>


            <td style="text-align: left;">&nbsp;&nbsp;Days in Month</td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="#">{{$days_in_month}}</a></td>
           
            
            

        </tr>
        <tr>
        
            
            <td style="text-align: left;">&nbsp;&nbsp;UAN No:</td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="">{{$salary_details->uan_no}}</a></td>

            
            <!-- td style="text-align: left;">&nbsp;&nbsp;PF No:</td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="#">{{$salary_details->pf_no}}</a></td> -->
            <td style="text-align: left;">&nbsp;&nbsp;Paid Days</td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="#">{{!empty($paid_days)?$paid_days:'0'}}</a></td>

        </tr>
        <tr>
            <td style="text-align: left;">&nbsp;&nbsp;ESIC No:</td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="#">{{$salary_details->esic_no}}</a></td>

            <td style="text-align: left;">&nbsp;&nbsp;Leave Days</td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="#">{{!empty($leave_days)?$leave_days:'0'}}</a></td>

            


        </tr>
        <tr>
            <td style="text-align: left;">&nbsp;&nbsp;PAN No:</td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="#">{{$salary_details->pan_no}}</a></td>


            <td style="text-align: left;">&nbsp;&nbsp;Absent Days</td>
            <td style="text-align: left;">&nbsp;&nbsp;<a id="#">{{!empty($absent_days)?$absent_days:'0'}}</a></td>


        </tr>
        
     
    </table>
    
   <!--  <table border="1" cellspacing="0" cellpadding="0" width="100%">
            <?php

            $startTimeStamp = strtotime($salary_details->from_date);
            $endTimeStamp = strtotime($salary_details->to_date);

            $timeDiff = abs($endTimeStamp - $startTimeStamp);

            $numberDays2 = $timeDiff/86400;
            $numberDays = intval($numberDays2);
            ?>
            <tr border="0">
                <td style="text-align: left; height:30px;">&nbsp;&nbsp;Total Days: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;30 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Days Present:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$numberDays}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Days Absent: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;30</td>
            </tr>
    </table> -->
    <table border="1" cellspacing="0" cellpadding="0" width="100%">
        <tr>
            <td style="text-align: left; height:30px; ">&nbsp;&nbsp;<strong>Earnings</strong></td>
            <td style="text-align: left; height:30px; ">&nbsp;&nbsp;<strong>Amount</strong></td>

            <td style="text-align: left; height:30px; ">&nbsp;&nbsp;<strong>Deductions</strong></td>
            <td style="text-align: left; height:30px; ">&nbsp;&nbsp;<strong>Amount</strong></td>
        </tr>

        <tr>
            <td style="text-align: left;">&nbsp;&nbsp;Basic Pay: </td>
            <td style="text-align: left;">&nbsp;&nbsp;{{$salary_details->basic_salary}}</td>

            <td style="text-align: left;">&nbsp;&nbsp;Provident Fund (Employeer): </td>
            <td style="text-align: left;">&nbsp;&nbsp;{{$salary_details->pf_amount}}</td>
        </tr>

        <tr>
            <td style="text-align: left;">&nbsp;&nbsp;HRA : </td>
            <td style="text-align: left;">&nbsp;&nbsp;{{$salary_details->hra_amount}}</td>

            <td style="text-align: left;">&nbsp;&nbsp;Provident Fund (Employer): </td>
            <td style="text-align: left;">&nbsp;&nbsp;{{!empty($salary_details->pf_amount)?$salary_details->pf_amount:'0'}}</td>

            
        </tr>
        <tr>
            <td style="text-align: left;">&nbsp;&nbsp;Special Allowance: </td>
            <td style="text-align: left;">&nbsp;&nbsp;{{!empty($salary_details->ta)?$salary_details->ta:'0'}}</td>
            
            <td style="text-align: left;">&nbsp;&nbsp;Tax Deduction (TDS) </td>
            <td style="text-align: left;">&nbsp;&nbsp;0</td>


        </tr>
        <tr>
            <td style="text-align: left;">&nbsp;&nbsp;TA Allowance</td>
            <td style="text-align: left;">&nbsp;&nbsp;{{!empty($salary_details->special_amount)?$salary_details->special_amount:'0'}}</td>
            
            <td style="text-align: left;">&nbsp;&nbsp;Leave + Absent Deduction </td>
            <td style="text-align: left;">&nbsp;&nbsp;{{round($another_deduction,2)}}</td>
            

        </tr>
        <tr>
            <td style="text-align: left;">&nbsp;&nbsp;Provident Fund (Employeer)</td>
            <td style="text-align: left;">&nbsp;&nbsp;{{!empty($salary_details->pf_amount_employee)?$salary_details->pf_amount_employee:'0'}}</td>


            <td style="text-align: left;">&nbsp;&nbsp;E.S.I (Employer):  </td>    
            <td style="text-align: left;">&nbsp;&nbsp;{{!empty($salary_details->esic_amount)?$salary_details->esic_amount:'0'}}</td>

        </tr>
        <tr>
            <td style="text-align: left;">&nbsp;&nbsp;E.S.I (Employer):  </td>    
            <td style="text-align: left;">&nbsp;&nbsp;{{!empty($salary_details->esic_amount)?$salary_details->esic_amount:'0'}}</td>
            
            <td style="text-align: left;">&nbsp;&nbsp;E.S.I (Employee):  </td>    
            <td style="text-align: left;">&nbsp;&nbsp;{{$salary_details->esic_amount_employee}}</td>
            
        </tr>
        <tr>
            <td style="text-align: left; height:30px;">&nbsp;&nbsp;<strong>Gross Earnings</strong></td>
            <td style="text-align: left; height:30px;">&nbsp;&nbsp;<strong>{{$add = $salary_details->basic_salary+$salary_details->pf_amount+$salary_details->hra_amount+$salary_details->special_amount+$salary_details->ta}}</strong></td>

            <td style="text-align: left; height:30px;">&nbsp;&nbsp;<strong>Total Deductions</strong></td>
            <td style="text-align: left; height:30px;">&nbsp;&nbsp;<strong>{{$minus = round($salary_details->pf_amount+$salary_details->pf_amount_employee+$salary_details->esic_amount_employee+$salary_details->esic_amount+$another_deduction,2)}}</strong></td>
        </tr>
     
    </table>
  
    <table border="1" cellspacing="0" cellpadding="0" height="30px" width="100%">
        <tr>
            
            <td style="text-align: left; height:30px; padding-left: 400px;">&nbsp;&nbsp;<strong>Net Pay</strong></td>
            <td style="padding-left: 43px; height:30px;">&nbsp;&nbsp;{{($add-$minus)}}</td>
        </tr>
    </table>
    <br>
    <br>
    <table border="0" cellspacing="0" cellpadding="0" height="30px" width="100%">
        <tr>
            
            <td style="text-align: center; height:30px; ">&nbsp;&nbsp;<strong>*This Is Computer Generated Payslip Signature Not Required*</strong></td>
        </tr>
    </table>
    
</div>
