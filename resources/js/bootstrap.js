window._ = require('lodash');

try {
    window.$ = window.jQuery = require('jquery');
    // window.$ = require('jquery');
    console.log("JQUERY lOADED");
} catch (e) { 
    console.log(e, "ERROR LOADING JQUERY");
}

try {
    require('bootstrap');
    console.log("BOOTSTRAP lOADED");
} catch (e) {
    console.log(e, "ERROR LOADING BOOTSTRAP")
}

try {
    if (Stripe) require('./stripe')
    console.log("STRIPE IS LOADED")
} catch (e) {
    console.log(e, "ERROR LOADING STRIPE")
}



/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });
