@extends('spark::layouts.app')

@section('content')
    <div class="container">


        <div class="row">
            <div class="col-lg-offset-2 col-lg-8 col-lg-offset-2" style="border:solid 1px;">

                <div class="row">
                    <div class="col-md-12 text-center">
                        <h2>Payment Details</h2>
                    </div>
                </div>

                <div class="row" style="margin: 10px 0;">
                    <div class="col-md-offset-1 col-md-5 col-xs-6">
                        <em>Date: {{ \Carbon\Carbon::now()->format('d M Y') }}</em>
                    </div>
                    <div class="col-md-4 col-md-offset-2 col-xs-6">
                        <em>Invoice #: {{ $payment->invoice_id }}</em>
                    </div>
                </div>


                <div class="row">
                    <div id="qrcode" class="text-center"></div>
                </div>

                <div class="row text-center" style="margin:10px 5px;">
                    <pre>{{$payment->address->address}}</pre>
                </div>

                <div class="row">
                    <h4 class="text-center" style="color: #00008b; font-weight: 800;">{{ (new \App\Util\Iota())->unit($payment->price_iota) }}OTA</h4>
                </div>

                <div class="row">
                    <div class="form-group col-md-offset-4 col-md-4 col-md-offset-4">
                        <div class="radio">
                            <label>
                                <input type="radio" class="pay_with_input"
                                       data-login="{{ isset($user) && $user ? '1' : '0' }}" name="pay_with"
                                       value="account">PayWithIOTA.com
                                account {{ isset($user) && $user ? '' : '(Login Required)' }}</label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" class="pay_with_input" name="pay_with"
                                       value="direct" checked>Send To
                                Address/QR code</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" class="pay_with_input" name="pay_with"
                                          value="seed">Seed (Not Recommended)</label>
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-bottom:30px;">
                    <div class="form-group pay_with_seed text-center" style="display:none;">
                        <div class="col-sm-4"><label for="customSeed">Input your Seed:</label></div>
                        <div class="col-sm-8"><input type="password" placeholder="SEED" value="" class="form-control" id="customSeed"></div>
                    </div>
                </div>


                <div class="row" style="margin-bottom: 30px;">
                    <div class="col-md-5 col-md-offset-4">

                        <button style="display:none;" id="payNow" type="button"
                                data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing"
                                class="btn btn-success btn-lg btn-block"
                                data-ready-text='Pay Now <span class="glyphicon glyphicon-chevron-right"></span>'
                                disabled>
                            <i class='fa fa-circle-o-notch fa-spin'></i> Connecting to IOTA Network
                        </button>

                        <button id="payNowWithAddress" type="button"
                                class="btn btn-success btn-lg btn-block">
                            Sent To Address
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection


@section('before-body-end')
    <script src="{{ asset('/js/iota.js') }}"></script>
    <script src="{{ asset('/js/jquery.qrcode.min.js') }}"></script>
    <script>

        var amount = '{{ $payment->price_iota }}';
        var amountWithUnit = '{{ (new \App\Util\Iota())->unit($payment->price_iota )}}OTA';
        var address = "{{ $payment->address->address }}";
        var returnUrl = "{{ isset($returnUrl) ? $returnUrl : '' }}";
        var canPay = false;
        var $payNowButton = $( '#payNow' );
        var transferInputs;
        var seed = "{{ isset($user) && $user ? $user->iota_seed : '' }}";

        var queryParam = function( uri, key, value )
        {
            var re = new RegExp( "([?&])" + key + "=.*?(&|$)", "i" );
            var separator = uri.indexOf( '?' ) !== - 1 ? "&" : "?";
            if( uri.match( re ) )
            {
                return uri.replace( re, '$1' + key + "=" + value + '$2' );
            }
            else
            {
                return uri + separator + key + "=" + value;
            }
        };

        $( document ).on( "click", '.pay_with_input', function()
        {
            if( $( this ).val() === 'account' )
            {
                $( '#payNowWithAddress' ).hide();
                $payNowButton.show();
                $( '.pay_with_seed' ).hide();

                if( ! $( this ).data( 'login' ) )
                {
                    if( confirm( "You will be redirected to login page, Do you want to continue?" ) )
                    {
                        window.location.href = queryParam( window.location.href, 'login', 1 );
                    }
                    else
                    {
                        $( '.pay_with_input[value="direct"]' ).prop( "checked", true );
                        $( '#payNowWithAddress' ).show();
                        $payNowButton.hide();
                    }
                }
            }
            else if( $( this ).val() == 'direct' )
            {
                $( '#payNowWithAddress' ).show();
                $payNowButton.hide();
                $( '.pay_with_seed' ).hide();
                alert( "Please scan the QR code Or Copy Address and send " + amountWithUnit );
            }
            else
            {
                $( '#payNowWithAddress' ).hide();
                $payNowButton.show();
                $( '.pay_with_seed' ).show()
            }
        } );

        $( document ).ready( function()
        {

            $( document ).on( 'click', '#payNowWithAddress', function()
            {
                if( confirm( "You will be redirected to merchant website's order thank you page. Click OK if you've already sent the money OR cancel to send now." ) )
                {
                    if( returnUrl )
                    {
                        window.location.href = returnUrl;
                    }
                    else
                    {
                        alert( "Thank you for your payment. We will send an email to merchant once the payment is verified." );
                        window.location.href = '/';
                    }
                }
            } );

            $( '#qrcode' ).qrcode( {
                width: 200,
                height: 200,
                text: JSON.stringify( {"address": address, "amount": amount, "tag": ""} )
            } );

            setTimeout( function()
            {

                const httpProviders = [
                    "https://node.tangle.works:443"
                ];

                const httpsProviders = [
                    "https://node.tangle.works:443"
                ];

                const iotaLib = window.IOTA;
                var iota = null;

                const validProviders = getValidProviders();
                const currentProviderProxy = new Proxy( {
                    currentProvider: null
                }, {
                    set: function( obj, prop, value )
                    {
                        obj[prop] = value
                        iota = new iotaLib( {'provider': value} );
                        return true
                    }
                } );

                currentProviderProxy.currentProvider = getRandomProvider();

                // must be https if the hosting site is served over https; SSL rules
                function getValidProviders()
                {
                    if( isRunningOverHTTPS() )
                    {
                        return httpsProviders
                    }
                    else
                    {
                        return httpProviders.concat( httpsProviders )
                    }
                }

                function isRunningOverHTTPS()
                {
                    switch( window.location.protocol )
                    {
                        case 'https:':
                            return true
                        default:
                            return false
                    }
                }

                function getRandomProvider()
                {
                    return validProviders[Math.floor( Math.random() * validProviders.length )]
                }

                /**
                 * Get balance of current user
                 * @param seed
                 * @param callback
                 */
                function getBalance( submit )
                {
                    var accountSeed = seed;
                    if( ! accountSeed )
                    {
                        accountSeed = $( '#customSeed' ).val();

                        if( ! accountSeed )
                        {
                            alert( "Please input your account seed." );
                            $( '#customSeed' ).focus();
                            $payNowButton.html( $payNowButton.data( "ready-text" ) ).removeAttr( 'disabled' );
                            return false;
                        }
                    }

                    const startIndex = 0;
                    const endIndex = 49;

                    iota.api.getInputs( accountSeed, {start: startIndex, end: endIndex}, function( err, inputs )
                    {
                        if( err )
                        {
                            alert( "Error: " + err );
                            return false;
                        }

                        const balance = inputs.totalBalance;

                        if( typeof balance !== "undefined" && balance > 0 )
                        {
                            transferInputs = inputs;
                            canPay = true;
                            $payNowButton.removeAttr( 'disabled' );
                            $payNowButton.html( $payNowButton.data( 'ready-text' ) );

                            if( submit === 1 )
                            {
                                $payNowButton.click();
                            }
                        }
                        else
                        {
                            alert( "Error: Not enough balance" );
                        }
                    } )
                }

                $( document ).on( "click", '#payNow', function()
                {
                    var accountSeed = seed;

                    if( canPay )
                    {
                        if( ! accountSeed )
                        {
                            accountSeed = $( '#customSeed' ).val();

                            if( ! accountSeed )
                            {
                                alert( "Please input your account seed." );
                                $( '#customSeed' ).focus();
                                $payNowButton.html( $payNowButton.data( "ready-text" ) ).removeAttr( 'disabled' );
                                return false;
                            }
                        }

                        $payNowButton.html( $payNowButton.data( 'loading-text' ) ).attr( 'disabled', true );

                        transfer( iota, {}, address, amount, accountSeed, transferInputs, function( error, data )
                        {
                            if( error )
                            {
                                alert( "Error: " + error );
                                return false;
                            }

                            if( typeof data !== "undefined" && typeof data[0] !== "undefined" && typeof data[0]["address"] !== "undefined" )
                            {
                                alert( "Transaction is attached." );

                                if( returnUrl )
                                {
                                    window.location.href = returnUrl;
                                }
                                else
                                {
                                    window.location.href = '/';
                                }
                            }
                        } )
                    }
                    else
                    {
                        if( ! accountSeed )
                        {
                            accountSeed = $( '#customSeed' ).val();

                            if( ! accountSeed )
                            {
                                alert( "Please input your account seed." );
                                $( '#customSeed' ).focus();
                                $payNowButton.html( $payNowButton.data( "ready-text" ) );
                                return false;
                            }
                            else
                            {
                                getBalance( 1 );
                                return false;
                            }
                        }

                        alert( "Something is not right." )
                    }
                } );

                /**
                 * Initiate transfer
                 * @param iota
                 * @param opts
                 * @param address
                 * @param amount
                 * @param seed
                 * @param cb
                 */
                function transfer( iota,
                                   opts,
                                   address,
                                   amount,
                                   seed,
                                   inputs,
                                   cb )
                {

                    opts = {json: false, depth: 4, mwm: 14, force: false};

                    if( address.length === 81 )
                    {
                        address = iota.utils.addChecksum( address )
                    }

                    const transfers = [
                        {
                            address: address,
                            value: parseInt( amount, 10 ),
                            message: '',
                            tag: ''
                        }
                    ];

                    const options = {
                        inputs: inputs.inputs
                    };

                    iota.api.sendTransfer( seed, opts.depth, opts.mwm, transfers, options, function( err, data )
                    {
                        if( err )
                        {
                            return cb( err )
                        }

                        cb( null, data )
                    } )
                }

                // Get balance for user
                if( $( '.pay_with_input:checked' ).val() != 'direct' )
                {

                    getBalance();
                }
            }, 10000 );
        } );

    </script>
@endsection