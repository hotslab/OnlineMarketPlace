$('#spinnerDisplay').hide()
$('#productAndEmailForm').show()
$('#stripePaymentForm').hide()

// This is your test publishable API key.
const stripe = Stripe("pk_test_51KV052BFEv6LnQAUShIizxgnTxszSVjWLktsMwQk1ppR1qv6sXmNmYuDYaIyEZlG7aFA8i6XyN1o6uBe0fB9RDSh00BZidLjMb");

let capturedEmail =  null
let clientSecret = null
let elements = null

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
            productID: $('#productID').text(),
            productName: $('#productName').text(),
            currency: $('#productCurrency').text(),
            currencySymbol: $('#productCurrencySymbol').text(),
            price: $('#productPrice').text(),
            email: capturedEmail 
        },
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: response => {
            console.log('CARD TOKEN', response)
            $('#spinnerDisplay').hide()
            $('#stripePaymentForm').show()
            elements = stripe.elements({clientSecret: response.clientSecret})
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
            productID: $('#productID').text(),
            paidAmount: $('#productPrice').text(),
            isDeposit: false,
            email: capturedEmail
        },
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: response => {
            $('#spinnerDisplay').hide()
            $('#stripePaymentForm').show()
            confirmPayment(response.url)
        },
        error: xhr => {
            $('#spinnerDisplay').hide()
            $('#productAndEmailForm').show()
            let error = JSON.parse(xhr.responseText)
            showMessage(error?.message || error, 'error')
        }
    })
})


async function confirmPayment(url) {
    await stripe.confirmPayment({
        elements,
        confirmParams: { return_url: url },
    }).then(function (result) {
        if (result.error) {
            if (result.error.type === "card_error" || result.error.type === "validation_error") {
                showMessage(result.error.message, 'error')
            } else showMessage("An unexpected error occured.", 'error')
        }
        $('#stripePaymentForm').hide()
        $('#spinnerDisplay').show()
    })
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