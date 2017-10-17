@extends('layouts.home')

@section('content')
    <section class="hero-area bg-opacity">
        <div class="container">
            <div class="row">
                <div class="col-md-3 text-center">
                    <div class="block">
                        <a class="btn btn-cover" href="https://github.com/paywithiota/woocommerce-Pay-with-IOTA/blob/master/README.md" target="_blank" role="button">Documentation</a>
                        <a class="btn btn-cover" href="https://github.com/paywithiota/woocommerce-Pay-with-IOTA" target="_blank" role="button">WooCommerce</a>
                    </div>
                </div>
                <div class="col-md-6 text-center">
                    <h1 class="">PayWithIOTA</h1>
                    <h2><span>A Payment gateway for IOTA </span></h2>
                </div>
                <div class="col-md-3 text-center">
                    <div class="block">
                        <a class="btn btn-cover" href="/login" role="button">Login</a>
                        <a class="btn btn-cover" href="/register" role="button">Register</a>
                    </div>
                </div>
            </div><!-- .row close -->
        </div><!-- .container close -->
    </section><!-- header close -->


    <!--
    Feature start
    ==================== -->
    <section class="feature section">
        <div class="container">
            <div class="row">
                <div class="heading">
                    <h2>Why IOTA</h2>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">

                        <h4>Micro-transactions</h4>
                        <p>For the first time ever true micro and even nano-transactions are enabled</p>
                    </div>
                    <div class="feature-box">
                        <h4>Data Transfer</h4>
                        <p>Core feature of IOTA is the ability to transfer data through the Tangle.</p>
                    </div>
                    <div class="feature-box">
                        <h4>Voting</h4>
                        <p>An important part of this sector is e-Voting.The exploration into this field of use-cases has already begun by several companies and academics. </p>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <img src="{{ asset('img/iota.png') }}" alt="">
                </div>
                <div class="col-md-4">
                    <div class="feature-box">

                        <h4>Masked Messaging</h4>
                        <p>MAM enables nodes to exchange data through the Tangle, fully authenticated and encrypted.</p>
                    </div>
                    <div class="feature-box">
                        <h4>Everything as a Service</h4>
                        <p>IOTA enables a whole new realm where anything with a chip in it can be leased in real time. </p>
                    </div>
                    <div class="feature-box">
                        <h4>Anything that needs a scalable ledger</h4>
                        <p> with the next generation ledger that IOTA created developers will be able to invent even more solutions.</p>
                    </div>
                </div>
            </div>
        </div><!-- .container close -->
    </section><!-- #service close -->

    <section class="feature-list section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="heading">
                        <h2>WHY US</h2>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-6 text-center">
                    <img class="" src="{{ asset('img/zero-fee.png') }}" alt="">
                </div>
                <div class="col-md-6">
                    <div class="content mt-10">
                        <h4 class="subheading">Zero Processing Fee</h4>
                        <p>We're taking zero processing fee. you can  download plugin from  <a href="https://github.com/paywithiota/woocommerce-Pay-with-IOTA" target="_blank">here.</a> We're not taking any charge on transactions and support</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="content mt-100">
                        <h4 class="subheading">24 X 7 Support</h4>
                        <p>We have free email support for integrating your system with us, at nivesh@centire.in</p>
                    </div>
                </div>
                <div class="col-md-6 mt-100 text-center">
                    <img class="img-responsive" src="{{ asset('img/phone-support.png') }}" alt="">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mt-100 text-center">
                    <img class="" src="{{ asset('img/secure.png') }}" alt="">
                </div>
                <div class="col-md-6">
                    <div class="content mt-100">
                        <h4 class="subheading">Secure Api Access</h4>
                        <p>Helps web merchants accept IOTA payments easy on their website. We have no fees, unlike other payment gateways. We have a simple and secure API that you can integrate into your system.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="call-to-action section bg-opacity bg-1">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <a href="/register" class="btn btn-main call-back-button">Create Your Account</a>
                </div>
            </div>
        </div>
    </section><!-- #call-to-action close -->
@endsection