{{-- reCAPTCHA v3: token dołączany do formularza tuż przed wysłaniem --}}
@if(config('services.recaptcha.site_key'))
<script>
    (function () {
        var siteKey = @json(config('services.recaptcha.site_key'));
        var form = document.getElementById(@json($formId));
        if (!form) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var button = form.querySelector('button[type="submit"]');

            if (typeof grecaptcha === 'undefined') {
                alert('Nie udało się załadować reCAPTCHA. Wyłącz blokowanie skryptów (np. AdBlock) i spróbuj ponownie.');
                return;
            }

            if (button) button.disabled = true;

            grecaptcha.ready(function () {
                grecaptcha.execute(siteKey, { action: @json($action) })
                    .then(function (token) {
                        var input = document.getElementById('g-recaptcha-response');
                        if (input) input.value = token;
                        form.submit();
                    })
                    .catch(function (error) {
                        console.error('reCAPTCHA error:', error);
                        if (button) button.disabled = false;
                        alert('Weryfikacja reCAPTCHA nie powiodła się. Odśwież stronę i spróbuj ponownie.');
                    });
            });
        });
    })();
</script>
@endif
