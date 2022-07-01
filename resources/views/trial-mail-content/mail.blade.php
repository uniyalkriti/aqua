<p>

Hey <b>{{$company_name}}</b>,<br><br>
Thank you for registering for a trial with mSELL – Salesforce Automation Solution <b>“Your Growth Mantra”</b>. <br><br>
By taking this trial you have come one step closer to automate your Sales and Business processes and to increase your Field Force productivity and performance multiple folds.<br>
According to a recent study done by a reputed CRM software solution organization<br>
48% of Consumer goods and FMCG companies are turning to Automation, So Congratulations, you too are going to be a part of this wonderful statistic. <br><br>

Below are your Credentials and access links for a smooth trial experience<br>
<br>
<b>
<u>Web Dashboard</u><br><br>

    • Web Access link: {{$url}}<br>
	• Login Credentials<br><br>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Id: {{$email}}<br>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Password: {{$pass}}<br>
<br>
<u>Mobile App</u><br><br>

    • App Link (Android):  {{$app_url}}<br>

    • Login Credentials (5 Persons)<br>
</b>
<br>
    <table style="border: 1px solid black; border-style: collapse;">
    	<thead>
    		<tr style="border: 1px solid black; padding: 10px;">
    			<th style="border: 1px solid black; padding: 10px; background-color: #e8883a; color: white;">Sr.no</th>
    			<th style="border: 1px solid black; padding: 10px; background-color: #e8883a; color: white;">Id</th>
    			<th style="border: 1px solid black; padding: 10px; background-color: #e8883a; color: white;">Password</th>
    			<th style="border: 1px solid black; padding: 10px; background-color: #e8883a; color: white;">Role</th>
    		</tr>
    	</thead>
    	<tbody>
    		@if(!empty($users_data))
	    		@foreach($users_data as $key => $value)
		    		<tr class="" style="border: 1px solid black; padding: 10px;">
		    			<td style="border: 1px solid black; padding: 10px; background-color: #f8f8f9;">{{$key+1}}</td>
		    			<td style="border: 1px solid black; padding: 10px; background-color: #f8f8f9;">{{$value->person_username}}</td>
		    			<td style="border: 1px solid black; padding: 10px; background-color: #f8f8f9;">{{$value->person_password}}</td>
		    			<td style="border: 1px solid black; padding: 10px; background-color: #f8f8f9;">{{$value->rolename}}</td>
		    		</tr>
	    		@endforeach
    		@endif
    		
    		
    	</tbody>
    </table>
<br>
    Do as you please, play around with our software as you want. It’s yours for a week from now and if you have any queries or face any problems kindly connect with<br>
	<b>Suraj Pratap Singh   +91-8447515262</b> and he will resolve your problems in no time.<br>
<br>
	Thanks and Regards,<br>
	<b>mSELL - Your Growth Mantra<br>
	Sales Force Automation Solution<br></b>
</p>