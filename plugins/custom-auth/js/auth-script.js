jQuery(document).ready(function($) {
    $('#auth-button').on('click', function(e) {
        e.preventDefault();
        $('#auth-modal').fadeIn(50);
    });

    $('.auth-close').on('click', function() {
        $('#auth-modal').fadeOut();
    });

    $(window).on('click', function(e) {
        if ($(e.target).is('#auth-modal')) {
            $('#auth-modal').fadeOut();
        }
    });

    // Tab switching
    $('.auth-tab').on('click', function() {
        $('.auth-tab').removeClass('active');
        $(this).addClass('active');

        $('.auth-form').removeClass('active');
        const tabId = $(this).data('tab') + '-form';
        $('#' + tabId).addClass('active');
    });

    // Login form submission
    $('#login-form form').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $message = $form.find('.auth-message');
        const $submit = $form.find('.auth-submit');

        $submit.prop('disabled', true);
        $message.html('Processing...').removeClass('error success');

        $.ajax({
            url: authAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'custom_login',
                nonce: authAjax.nonce,
                email: $form.find('input[name="email"]').val(),
                password: $form.find('input[name="password"]').val()
            },
            success: function(response) {
                if (response.success) {
                    $message.html('Login successful! Redirecting...').addClass('success');
                    window.location.href = response.data.redirect;
                } else {
                    $message.html(response.data).addClass('error');
                    $submit.prop('disabled', false);
                }
            },
            error: function() {
                $message.html('Server error occurred').addClass('error');
                $submit.prop('disabled', false);
            }
        });
    });

    // Register form submission
    $('#register-form form').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $message = $form.find('.auth-message');
        const $submit = $form.find('.auth-submit');

        $submit.prop('disabled', true);
        $message.html('Processing...').removeClass('error success');

        $.ajax({
            url: authAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'custom_register',
                nonce: authAjax.nonce,
                email: $form.find('input[name="email"]').val(),
                username: $form.find('input[name="username"]').val(),
                password: $form.find('input[name="password"]').val(),
                fullname: $form.find('input[name="fullname"]').val()
            },
            success: function(response) {
                if (response.success) {
                    $message.html('Registration successful! Redirecting...').addClass('success');
                    window.location.href = response.data.redirect;
                } else {
                    $message.html(response.data).addClass('error');
                    $submit.prop('disabled', false);
                }
            },
            error: function() {
                $message.html('Server error occurred').addClass('error');
                $submit.prop('disabled', false);
            }
        });
    });
});