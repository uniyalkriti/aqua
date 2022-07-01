@if($status == 1)
<p> Hey {{$name}},<br>
 Thank you for booking a call/Demo with mSELL. I'll be calling you soon to discuss your revenue workflow and current challenges you are facing in detail.<br>

 <br>We just take 15 minutes of your time on call to map your challenges to capabilities in mSELL. Once that is established, we present a Live Demo as per your desired date and time and as per the requirements we <br>draw out a plan for you for Salesforce Automation Solution powered by mSELL! 
 <br>
 <br>Just to confirm, I'll be reaching out to you at {{$name}}
 <br>
 <br>Talk to you soon, 
 <br>Suraj Pratap Singh</p>
 @elseif($status == 2)
    @if(!empty($message))
     <p>
        Hey, Suraj <br>New Query Register<br>
     Name : {{$name}}<br>
     Email : {{$email}}<br>
     Message : {{$message}}<br>
    </p>
    @else

     <p>
     	Hey, Suraj <br>New Demo Register<br>
     Name : {{$name}}<br>
     Email : {{$email}}<br>
     Mobile No. : {{$phone_no}}<br>
     Website : {{$website}}<br>
    </p>
    @endif

@else
 <p>
 	Hey, Suraj <br>New Trial Register<br>
 Name : {{$name}}<br>
 Email : {{$email}}<br>
 Mobile No. : {{$phone_no}}<br>
 Website : {{$website}}<br>
</p>
 @endif

