$('#spinnerDisplay').hide()
$('#productAndEmailForm').show()
$('#stripePaymentForm').hide()
$('#stripeSavedPaymentForm').hide()

const stripe = Stripe("pk_test_51KV052BFEv6LnQAUShIizxgnTxszSVjWLktsMwQk1ppR1qv6sXmNmYuDYaIyEZlG7aFA8i6XyN1o6uBe0fB9RDSh00BZidLjMb");

let capturedEmail =  null
let capturedAmount = null
let elements = null
let currentErrorTimeout = null
let currentSuccessTimeout = null
let hasSavedDetails = null

checkStatus();

$('#depositPayment').click(async event => {
    event.preventDefault()
    capturedEmail = $("#purchaser_email").val()
    if (!capturedEmail) {
        $('#paymentModal').modal('hide')
        return showMessage('Please add a valid email.', 'error')
    }
    const price = $('#productPrice').text()
    capturedAmount = parseFloat( parseFloat(price).toFixed(2) / 2).toFixed(2)
    $('#paymentModal').modal('hide')
    await getClientToken()
})

$('#fullPayment').click(async event => {
    event.preventDefault()
    capturedEmail = $("#purchaser_email").val()
    if (!capturedEmail) {
        $('#paymentModal').modal('hide')
        return showMessage('Please add a valid email.', 'error')
    }
    capturedAmount = $('#productPrice').text()
    $('#paymentModal').modal('hide')
    await getClientToken()
})

async function getClientToken() {
    $('#productAndEmailForm').hide()
    $('#spinnerDisplay').show()
    $.ajax({
        method: "POST",
        url: $('#clientRoute').text(),
        data: { email: capturedEmail },
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: response => {
            console.log('CARD TOKEN', response)
            hasSavedDetails = response.hasSavedDetails
            if (hasSavedDetails) {
                $('#spinnerDisplay').hide()
                $('#stripeSavedPaymentForm').show()
            } else {
                elements = stripe.elements({ clientSecret: response.clientSecret })
                const paymentElement = elements.create("payment")
                paymentElement.mount("#payment-element")
                $('#spinnerDisplay').hide()
                $('#stripePaymentForm').show()
            }
        },
        error: xhr => {
            $('#spinnerDisplay').hide()
            $('#productAndEmailForm').show()
            let error = JSON.parse(xhr.responseText)
            showMessage(error?.message || error, 'error')
        }
    })
}

$('#captureUnsavedPayment').click(async event => {
    event.preventDefault(); 
    await capturePayment()
})

$('#captureSavedPayment').click(async event => {
    event.preventDefault();
    await capturePayment()
})

async function capturePayment() {
    hasSavedDetails ? $('#stripeSavedPaymentForm').hide() : $('#stripePaymentForm').hide()
    $('#spinnerDisplay').show()
    $.ajax({
        method: "POST",
        url: $('#purchaseRoute').text(),
        data: {
            productID: $('#productID').text(),
            paidAmount: capturedAmount,
            email: capturedEmail
        },
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: response => {
            confirmPayment(response.redirectURL, response.deleteURL)
        },
        error: xhr => {
            $('#spinnerDisplay').hide()
            hasSavedDetails ? $('#stripeSavedPaymentForm').show() : $('#stripePaymentForm').show()
            let error = JSON.parse(xhr.responseText)
            showMessage(error?.message || error, 'error')
        }
    })
}

async function confirmPayment(redirectURL, deleteURL) {
    if (hasSavedDetails) {
        try {
            window.location.replace(redirectURL)
        } catch (error) {
            showMessage(error?.message || JSON.stringify(error), 'error')
            $('#spinnerDisplay').hide()
            $('#stripeSavedPaymentForm').show()
        }
    } else {
        await stripe.confirmSetup({
            elements,
            confirmParams: { return_url: redirectURL },
        }).then( async result => {
            if (result.error) showMessage(result.error.message, 'error')
            await deletePurchase(deleteURL)
            $('#spinnerDisplay').hide()
            $('#stripePaymentForm').show()
        })
    }
}

async function deletePurchase(deleteURL) {
    $.ajax({
        method: "DELETE",
        url: deleteURL,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        error: xhr => {
            let error = JSON.parse(xhr.responseText)
            showMessage(error?.message || error, 'error')
        }
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
function closeAlert(status) {
    status == 'error' ? clearTimeout(currentErrorTimeout) : clearTimeout(currentSuccessTimeout)
    status == 'error' ? currentErrorTimeout = setTimeout(() => $('#errorAlert').hide(), 10000) : currentSuccessTimeout = setTimeout(() => $('#successAlert').hide, 10000)
}

function showMessage(message, status) {
    if (status == 'error') {
        $('#errorAlert').show()
        if ($('#errorAlert')) $('#errorAlert')[0].innerHTML = message
    } else if (status == 'success') {
        $('#successAlert').show()
        if ($('#successAlert')) $('#successAlert')[0].innerHTML = message
    }
    closeAlert(status)
}