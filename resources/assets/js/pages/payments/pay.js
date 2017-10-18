if( currentPageName === 'PaymentsPay' )
{
    var $payNowButton = $( '#payNow' );
    var readyToPay = false;
    var transferInputs = [];
    var $payNowWithAddress = $( '#payNowWithAddress' );
    var $payNowWithSeed = $( '.pay_with_seed' );
    var $customSeed = $( '#customSeed' );

    // On Pay with input click
    $( document ).on( "click", '.pay_with_input', function()
    {
        // Pay via account
        if( $( this ).val() === 'account' )
        {
            $payNowWithAddress.hide();
            $payNowButton.show();
            $payNowWithSeed.hide();

            // If user is not logged in
            if( ! $( this ).data( 'login' ) )
            {
                if( confirm( "You will be redirected to login page, Do you want to continue?" ) )
                {
                    window.location.href = queryParam( window.location.href, 'login', 1 );
                    return false;
                }
                else
                {
                    $( '.pay_with_input[value="direct"]' ).prop( "checked", true );
                    $payNowWithAddress.show();
                    $payNowButton.hide();
                }
            }
        }
        else if( $( this ).val() === 'direct' ) // Pay directly
        {
            $payNowWithAddress.show();
            $payNowButton.hide();
            $payNowWithSeed.hide();
            alert( "Please scan the QR code OR Copy Address and send " + amountWithUnit );
        }
        else
        {
            $payNowWithAddress.hide();
            $payNowButton.show();
            $payNowWithSeed.show()
        }
    } );

    // On clicking done button
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
                alert( "Thank you for your payment. We will send an email to receiver once the payment is verified." );
                window.location.href = '/';
            }
        }
    } );

    /**
     * Get account seed
     * @returns {boolean|string}
     */
    function getSeed()
    {
        // User account seed
        var accountSeed = $( '.pay_with_input[value="seed"]' ).is( ':checked' ) ? $customSeed.val() : seed;

        if( ! accountSeed )
        {
            alert( "Please enter your account seed OR choose another method to pay." );
            $customSeed.focus();
            $payNowButton.html( $payNowButton.data( "ready-text" ) ).removeAttr( 'disabled' );
            return false;
        }

        return accountSeed;
    }

    /**
     * Get balance
     * @param $payAutomatically
     */
    function getBalance( $payAutomatically )
    {
        // User account seed
        var accountSeed = getSeed();

        if( accountSeed )
        {
            var startIndex = typeof iotaAddressStartIndex === "undefined" ? 0 : parseInt( iotaAddressStartIndex, 10 );
            var endIndex = typeof iotaAddressEndIndex === "undefined" ? 49 : parseInt( iotaAddressEndIndex, 10 );
            endIndex = endIndex > 0 ? endIndex : 49;

            $payNowButton.html( "Retrieving Your IOTA Balance..." ).attr( 'disabled', true );

            setTimeout( function()
            {

                // Get inputs by seed
                iota.api.getInputs( accountSeed, {start: startIndex, end: endIndex}, function( error, inputs )
                {
                    if( error )
                    {
                        alert( error );
                        $payNowButton.html( "Try Again" ).removeAttr( "disabled" );
                        return false;
                    }

                    var totalBalance = inputs.totalBalance;

                    if( typeof totalBalance !== "undefined" && totalBalance >= amount )
                    {
                        transferInputs = inputs;
                        readyToPay = true;
                        $payNowButton.removeAttr( 'disabled' );
                        $payNowButton.html( $payNowButton.data( 'ready-text' ) );

                        if( $payAutomatically === 1 )
                        {
                            $payNowButton.click();
                        }
                    }
                    else
                    {
                        alert( "Your account doesn't have sufficient balance. The current balance is " + totalBalance + ". Please try with another account or method." );
                        $payNowButton.html( "Try Again" ).removeAttr( "disabled" );
                        return false;
                    }
                } );
            }, 1000 );
        }
    }

    /**
     * Get new address
     * @param iota
     * @param seed
     * @param inputs
     * @param callback
     */
    function getNewAddress( iota, seed, inputs, callback )
    {
        var $maxIndex = 0;

        // Get max index
        if( inputs && inputs.length )
        {
            $maxIndex = Math.max.apply( Math, inputs.map( function( i )
            {
                return i["keyIndex"];
            } ) );
        }

        var $nextKeyIndex = $maxIndex + 1;

        // Specify option for faster generating new address
        var options = {
            'index': $nextKeyIndex,
            'checksum': false,
            'total': 1,
            'security': 2,
            'returnAll': false
        };

        // Generate new address by options
        iota.api.getNewAddress( seed, options, function( error, senderAddress )
        {
            if( error )
            {
                callback( error );
                return false;
            }

            callback( null, senderAddress[0] );
        } );
    }

    /**
     * Prepare Transfer
     * @param iota
     * @param seed
     * @param transferData
     * @param options
     * @param callback
     */
    function prepareTransfer( iota, seed, transferData, options, callback )
    {
        // Prepare trytes data
        iota.api.prepareTransfers( seed, transferData, options, function( error, success )
        {
            if( error )
            {
                callback( error );
                return false;
            }
            else
            {
                callback( null, success );
            }
        } );
    }

    /**
     * On Pay now button click
     */
    $( document ).on( "click", '#payNow', function()
    {
        var accountSeed = getSeed();

        if( accountSeed )
        {
            if( readyToPay )
            {
                // Update button text
                $payNowButton.html( $payNowButton.data( 'loading-text' ) ).attr( 'disabled', true );

                setTimeout( function()
                {
                    // Get new address for transfer
                    getNewAddress( iota, accountSeed, transferInputs.inputs, function( error, senderAddress )
                    {
                        if( error )
                        {
                            alert( error );
                            $payNowButton.html( $payNowButton.data( "ready-text" ) ).removeAttr( 'disabled' );
                            return false;
                        }

                        console.log( senderAddress );

                        var $transferData = [
                            {
                                address: address,
                                value: parseInt( amount, 10 ),
                                message: "PAIDVIAPAYWITHIOTAPAYMENTGATEWAY",
                                tag: "PAYWITHIOTADOTCOM"
                            }
                        ];

                        var $options = {
                            'inputs': transferInputs.inputs,
                            'address': senderAddress,
                            'security': 2
                        };

                        console.log( $transferData );
                        console.log( $options );

                        /**
                         * Prepare transfer
                         */
                        prepareTransfer( iota, accountSeed, $transferData, $options, function( error, data )
                        {
                            if( error )
                            {
                                alert( error );
                                $payNowButton.html( $payNowButton.data( "ready-text" ) ).removeAttr( 'disabled' );
                                return false;
                            }

                            // Start transfer
                            startTransfer( iota, accountSeed, $transferData, $options, function( error, data )
                            {
                                if( error )
                                {
                                    alert( error );
                                    $payNowButton.html( $payNowButton.data( "ready-text" ) ).removeAttr( 'disabled' );
                                    return false;
                                }

                                if( typeof data !== "undefined" && typeof data[0] !== "undefined" && typeof data[0]["address"] !== "undefined" )
                                {
                                    $.ajax( {
                                        url: queryParam( routes['Payments.Update.Metadata'], "payment_id", paymentId ),
                                        data: data[0],
                                        type: "POST",
                                        dataType: "JSON",
                                        success: function()
                                        {
                                            alert( "Thank you for your payment. The payment is accepted and will be marked as complete as soon as it is verified on network." );

                                            if( returnUrl )
                                            {
                                                window.location.href = returnUrl;
                                            }
                                            else
                                            {
                                                window.location.href = '/';
                                            }
                                        }
                                    } );
                                }
                            } );
                        } );
                    } );

                }, 1000 );

            }
            else
            {
                // Check balance and Pay
                getBalance( 1 );
            }
        }
    } );

    /**
     * Send transfer
     * @param iota
     * @param seed
     * @param transferData
     * @param $options
     * @param callback
     */
    function startTransfer( iota, seed, transferData, $options, callback )
    {
        /**
         * Options
         *
         */
        var opts = {
            json: false,
            depth: 4,
            mwm: 14,
            force: false
        };

        // Call api to transfer funds
        iota.api.sendTransfer( seed, opts.depth, opts.mwm, transferData, $options, function( error, data )
        {
            if( error )
            {
                return callback( error );
            }

            callback( null, data )
        } );
    }
}