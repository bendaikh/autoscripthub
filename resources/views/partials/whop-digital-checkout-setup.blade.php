{{-- Use the real customer email (account / guest checkout). Never invent an email. --}}
@php
    $whopSay = function (string $key) {
        if (!empty($landing_ui_locale)) {
            \Fickrr\Helpers\LandingPageI18n::setLocale($landing_ui_locale);
            return \Fickrr\Helpers\LandingPageI18n::t($key);
        }
        return __($key);
    };
@endphp
<script>
(function () {
    var CHECKOUT_ID = 'whop-digital-checkout';
    var customerEmail = String(@json($whop_prefill_email ?? ($embed['whop_prefill_email'] ?? '')) || '').trim();

    function resolveCustomerEmail() {
        if (customerEmail) {
            return customerEmail;
        }
        var el = document.getElementById(CHECKOUT_ID);
        if (el) {
            var fromAttr = (el.getAttribute('data-whop-checkout-prefill-email') || '').trim();
            if (fromAttr) {
                customerEmail = fromAttr;
                return customerEmail;
            }
        }
        try {
            var fromQuery = new URLSearchParams(window.location.search).get('email');
            if (fromQuery && fromQuery.trim()) {
                customerEmail = fromQuery.trim();
                return customerEmail;
            }
        } catch (e) {}
        return '';
    }

    function showFrameError(message) {
        var box = document.getElementById('whop-setup-error');
        if (!box) {
            box = document.createElement('div');
            box.id = 'whop-setup-error';
            box.style.cssText = 'color:#b42318;background:#fef3f2;border:1px solid #fecdca;padding:10px 12px;border-radius:8px;margin:8px 0;font:14px/1.4 system-ui,sans-serif;';
            var host = document.getElementById(CHECKOUT_ID);
            if (host && host.parentNode) {
                host.parentNode.insertBefore(box, host);
            }
        }
        box.textContent = message || 'Checkout could not be prepared. Please refresh and try again.';
        box.style.display = 'block';
    }

    function digitalAddressForEmail(email) {
        var local = (email.split('@')[0] || 'Customer').replace(/[^\w\s.-]/g, ' ').trim() || 'Customer';
        return {
            name: local,
            country: 'US',
            line1: '3500 S Dupont Hwy',
            city: 'Dover',
            state: 'DE',
            postalCode: '19901'
        };
    }

    function applyCustomerIdentity() {
        if (typeof wco === 'undefined') {
            return Promise.reject(new Error('Whop checkout is not ready'));
        }

        var email = resolveCustomerEmail();
        var el = document.getElementById(CHECKOUT_ID);

        if (!email) {
            if (el) {
                el.removeAttribute('data-whop-checkout-hide-email');
            }
            showFrameError(@json($whopSay('Customer email is missing. Please go back and enter your email.')));
            return Promise.reject(new Error('missing customer email'));
        }

        if (el) {
            el.setAttribute('data-whop-checkout-prefill-email', email);
            el.setAttribute('data-whop-checkout-hide-email', 'true');
        }

        var tasks = [];

        if (typeof wco.setEmail === 'function') {
            tasks.push(
                Promise.resolve(wco.setEmail(CHECKOUT_ID, email)).catch(function (err) {
                    console.error('Whop setEmail failed', err);
                    throw err;
                })
            );
        }

        if (typeof wco.setAddress === 'function') {
            tasks.push(
                Promise.resolve(wco.setAddress(CHECKOUT_ID, digitalAddressForEmail(email))).catch(function (err) {
                    console.error('Whop setAddress failed', err);
                    throw err;
                })
            );
        }

        return Promise.all(tasks);
    }

    window.onWhopCheckoutStateChange = function (state) {
        if (state !== 'ready') {
            return;
        }
        applyCustomerIdentity().catch(function () {
            showFrameError(@json($whopSay('Unable to apply your account email to checkout. Please refresh and try again.')));
        });
    };

    window.onWhopAddressValidationError = function (error) {
        var message = (error && (error.message || error.code)) ? (error.message || error.code) : '';
        showFrameError(message || @json($whopSay('Billing details could not be validated. Please refresh and try again.')));
    };

    var attempts = 0;
    var timer = setInterval(function () {
        attempts += 1;
        if (typeof wco === 'undefined') {
            if (attempts > 40) {
                clearInterval(timer);
            }
            return;
        }
        clearInterval(timer);
        applyCustomerIdentity().catch(function () {});
    }, 250);
})();
</script>
