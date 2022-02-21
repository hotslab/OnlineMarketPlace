$('#spinnerDisplay').hide()
$('#productAndEmailForm').show()
$('#stripePaymentForm').hide()

// This is your test publishable API key.
const stripe = Stripe("pk_test_51KV052BFEv6LnQAUShIizxgnTxszSVjWLktsMwQk1ppR1qv6sXmNmYuDYaIyEZlG7aFA8i6XyN1o6uBe0fB9RDSh00BZidLjMb");

let capturedEmail =  null

let elements

checkStatus();

$('#getClientToken').click(event => {
    event.preventDefault()
    $('#productAndEmailForm').hide()
    $('#spinnerDisplay').show()
    capturedEmail = $("#purchaser_email").val() 
    $.ajax({
        method: "POST",
        url: $('#clientRoute').text(),
        data: {
            id: $('#productName').text(),
            product_id: $('#productID').text(),
            currency: $('#productCurrency').text(),
            price: $('#productPrice').text(),
            email: capturedEmail 
        },
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: response => {
            $('#spinnerDisplay').hide()
            $('#stripePaymentForm').show()
            elements = stripe.elements({clientSecret:response.clientSecret})
            const paymentElement = elements.create("payment");
            paymentElement.mount("#payment-element");
        },
        error: xhr => {
            $('#spinnerDisplay').hide()
            $('#productAndEmailForm').show()
            let error = JSON.parse(xhr.responseText)
            showMessage(error?.message || error, 'error')
        }
    })
});



$('#capturePayment').click(async event => {
    event.preventDefault();
    $('#stripePaymentForm').hide()
    $('#spinnerDisplay').show()
    $.ajax({
        method: "POST",
        url: $('#purchaseRoute').text(),
        data: {
            id: $('#productName').text(),
            product_id: $('#productID').text(),
            currency: $('#productCurrency').text(),
            price: $('#productPrice').text(),
            email: capturedEmail
        },
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: response => {
            $('#spinnerDisplay').hide()
            $('#stripePaymentForm').show()
            elements = stripe.elements({ clientSecret: response.clientSecret })
            const paymentElement = elements.create("payment");
            paymentElement.mount("#payment-element");
        },
        error: xhr => {
            $('#spinnerDisplay').hide()
            $('#productAndEmailForm').show()
            let error = JSON.parse(xhr.responseText)
            showMessage(error?.message || error, 'error')
        }
    })
})


async function confirmPayment() {
    const { error } = await stripe.confirmPayment({
        elements,
        confirmParams: {
            // Make sure to change this to your payment completion page
            return_url: $('confirmationRoute').text(),
        },
    });

    // This point will only be reached if there is an immediate error when
    // confirming the payment. Otherwise, your customer will be redirected to
    // your `return_url`. For some payment methods like iDEAL, your customer will
    // be redirected to an intermediate site first to authorize the payment, then
    // redirected to the `return_url`.
    if (error.type === "card_error" || error.type === "validation_error") {
        showMessage(error.message, 'error')
    } else {
        showMessage("An unexpected error occured.", 'error')
    }

    $('#stripePaymentForm').hide()
    $('#spinnerDisplay').show()
}

// Fetches the payment intent status after payment submission
async function checkStatus() {
    const clientSecret = new URLSearchParams(window.location.search).get("payment_intent_client_secret")
    if (!clientSecret) return
    const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret)
    switch (paymentIntent.status) {
        case "succeeded":
            showMessage("Payment succeeded!", 'success')
            break;
        case "processing":
            showMessage("Your payment is processing.", 'success')
            break;
        case "requires_payment_method":
            showMessage("Your payment was not successful, please try again.", 'error')
            break;
        default:
            showMessage("Something went wrong.", 'error')
            break;
    }
}

// ------- UI helpers -------

function showMessage(message, status) {
    if (status == 'error') {
        $('#errorAlert').show()
        if ($('#errorAlert')) $('#errorAlert')[0].innerHTML = message
    } else {
        $('#successAlert').show()
        if ($('#successAlert')) $('#successAlert')[0].innerHTML = message
    }
    // setTimeout(() => status == 'error' ? $('#errorAlert').hide() : $('#successAlert').hide(), 4000)
}