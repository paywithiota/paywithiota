Hi {{ $payment->user->name }}!


<br><br>

Your payment with id {{ base64_encode($payment->id) }} is now complete. Here are more details:
<br>
Payment Id: {{ base64_encode($payment->id) }}
<br/>
Invoice Id: {{ $payment->invoice_id }}
<br/>
Price USD: {{ $payment->price_usd }}
<br/>
Price IOTA: {{ $payment->price_iota }}
<br/>
Address: {{ $payment->address->address }}

<br><br>

Thanks!

<br>

Team

