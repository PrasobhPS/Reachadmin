@extends('layouts.app') @section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->

    <div class="d-flex flex-column flex-column-fluid">

        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">

                <div class="outer-data  w-100">

        <div class="container mt-5">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('process.payment') }}" method="POST" id="payment-form1">
            @csrf
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">4242424242424242
                <label for="card-element">Credit or debit card </label>
                <div id="card-element" class="form-control">
                </div>
                <div id="card-errors" role="alert"></div>
            </div>
            <button class="btn btn-primary mt-3">Submit Payment</button>
        </form>
        </div>

        <div class="container mt-5">
            <h4>Payment Transfer</h4>
        <form action="{{ route('do-payment-transfer') }}" method="POST" id="transfer-form">
            @csrf
            <div class="form-group">
                <label for="payment_id">Payment Id</label>
                <input type="text" id="payment_id" name="payment_id" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="text" id="amount" name="amount" class="form-control" value="25">
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <input type="text" id="description" name="description" class="form-control" value="Payment to Specialists">
            </div>
            <button class="btn btn-primary mt-3">Transfer Payment</button>
        </form>

        <form  action="{{ route('do-payment-card-details') }}" method="POST" id="payment-form">
            @csrf
            <div class="form-row">
                <h4>Payment details</h4>

                <div class="form-group">
                <label for="card_number">Card Number</label>
                <div id="card-number" class="form-control"></div>
                </div>
                <div class="form-group">
                    <label for="card_exp_month">Expiration Date</label>
                    <div id="card-expiry" class="form-control"></div>
                </div>
                <div class="form-group">
                    <label for="card_cvc">CVC</label>
                    <div id="card-cvc" class="form-control"></div>
                </div>
                <div id="card-errors" role="alert" style="color: red;"></div>
            </div>

            <button id="submit-button" class="btn btn-primary mt-3">Pay</button>
        </form>

        </div>


                </div>
            </div>
            <!--end::Toolbar container-->
        </div>
    </div>
    <!--end::Content wrapper-->

    <script src="https://js.stripe.com/v3/"></script>

    <script>
        var stripe = Stripe('{{ env('STRIPE_KEY') }}');
        var elements = stripe.elements();

        var style = {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };

        var cardNumber = elements.create('cardNumber', { style: style });
        cardNumber.mount('#card-number');

        var cardExpiry = elements.create('cardExpiry', { style: style });
        cardExpiry.mount('#card-expiry');

        var cardCvc = elements.create('cardCvc', { style: style });
        cardCvc.mount('#card-cvc');

        cardNumber.on('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            stripe.createToken(cardNumber).then(function(result) {
                if (result.error) {
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                } else {
                    stripeTokenHandler(result.token);
                }
            });
        });

        function stripeTokenHandler(token) {
            var form = document.getElementById('payment-form');
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);
            form.appendChild(hiddenInput);

            form.submit();
        }
    </script>


    <script>
        /*document.addEventListener('DOMContentLoaded', async () => {
            const stripe = Stripe('{{ env('STRIPE_KEY') }}', {
                apiVersion: '2020-08-27',
            });

            const elements = stripe.elements({
                clientSecret: ''
            });

            const paymentElement = elements.create('payment');
            paymentElement.mount('#payment-element');

            const paymentForm = document.querySelector('#payment-form');
            paymentForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                paymentForm.querySelector('button').disabled = true;

                const {error} = await stripe.confirmPayment({
                    elements,
                    confirmParams: {
                        return_url: `${window.location.origin}/return`
                    }
                });

                if (error) {
                    document.getElementById('payment-errors').textContent = error.message;
                    paymentForm.querySelector('button').disabled = false;
                }
            });
        });*/
    </script>

    <script>
        /*var stripe = Stripe('{{ env('STRIPE_KEY') }}');
        var elements = stripe.elements();

        var style = {
            base: {
                color: '#32325d',
                lineHeight: '18px',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };

        var card = elements.create('card', { style: style });

        card.mount('#card-element');

        card.addEventListener('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function(event) { 
            event.preventDefault();

            stripe.createToken(card).then(function(result) {
                if (result.error) {
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                } else {
                    var hiddenInput = document.createElement('input');
                    hiddenInput.setAttribute('type', 'hidden');
                    hiddenInput.setAttribute('name', 'stripeToken');
                    hiddenInput.setAttribute('value', result.token.id);
                    form.appendChild(hiddenInput);

                    form.submit();
                }
            });
        });*/
    </script>

    @include('layouts.dashboard_footer')
</div>

@endsection