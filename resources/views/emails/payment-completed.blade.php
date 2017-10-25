Hi {{ $payment->user->name }}!


<br><br>

Your payment with id {{ base64_encode($payment->id) }} is now complete. Here are more details:
<br>
Payment Id: {{ base64_encode($payment->id) }}
<br/>
Invoice Id: {{ $payment->invoice_id }}
<br/>
Amount IOTA: {{ (new \App\Util\Iota())->unit($payment->price_iota) }}OTA
<br/>
Address: {{ $payment->address->address }}
<br/>
<a href="https://thetangle.org/address/{{ $payment->address->address }}">Check it on Tangle</a>
<br><br>
Feeling helpful? Send love to BVGHDHVA9LHXTBMFUJIHDHSDTYFJCGUPJW9AGHKLKPHDFRTXZEUESUJUMFB9AVSEREDXVTZHNMJGWJISCZGIPSBFID
<br><br>

Thanks!

<br>

Team PayWithIOTA.com

