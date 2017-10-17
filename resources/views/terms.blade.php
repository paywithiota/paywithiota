@extends('layouts.home')

@section('content')
    <section class="feature section">
        <div class="container">
            <div class="row">
                <div class="heading">
                    <h2>Terms</h2>
                </div>
                <div class="col-md-12">
                    <div class="feature-box">
                        <h2 class="subheading">Your Agreement to the Terms</h2>
                        <p>By using this website, you agree to the following terms and conditions.</p>
                        <div class="term-description">
                            <h4>Refunds</h4>
                                    <span>We will offer no refunds as all IOTA transactions are final. We may refund if a payment has not arrived
                                    into your IOTA seed account, due to an error on our side, but this is unlikely as addresses are derived
                                    directly from your seed.</span>
                            <h4>Liability</h4>
                                    <span>This service is provided as-is. There may be temporary service disruptions, even though we strive for
                                    99.9% uptime. We hold no liability and you agree not to engage us in legal action.</span>
                            <h4>Security & Privacy</h4>
                                    <span>We put extreme care into making this site secure. However, it is your responsibility to always save your
                                    seed and transfer to a private account of yours the funds you receive through the site. We will only give
                                    out your data is required to do so by a legal order.</span>
                            <h4>Changes</h4>
                            <span>We reserve the right to change this TOS document. We would email you if we decide to do so. </span>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- .container close -->
    </section><!-- #service close -->

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