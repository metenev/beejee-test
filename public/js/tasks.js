(function ( window, document, $ ) {

    function createFormAlert($form, message, field, type) {
        if (!type) type = 'error';

        if (field && field != '__global__') {
            var cls = type != 'error' ? 'valid-feedback' : 'invalid-feedback';

            $form.find('[name="' + field + '"]')
                .addClass('is-invalid')
                .closest('.form-group')
                    .append('<div class="' + cls + '">' + message + '</div>');
        } else {
            var cls = 'alert-' + (type == 'error' ? 'danger' : type);

            $form.find('.alerts')
                .append('<div class="alert ' + cls + '">' + message + '</div>');
        }
    }

    function clearFormAlerts($form) {
        $form.find('.invalid-feedback,.valid-feedback,.alert').remove();
        $form.find('.is-invalid,.is-valid').removeClass('is-invalid is-valid');
    }

    function sendForm($form, evt) {
        evt.preventDefault();

        clearFormAlerts($form);

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            dataType: 'json',
            data: $form.serialize(),
            success: function (data) {
                window.location.reload();
            },
            error: function (jqXHR, statusText, errorThrown) {
                // Oops

                if (jqXHR.responseJSON) {
                    var errors = jqXHR.responseJSON.errors;

                    for (var i = 0; i < errors.length; i++) {
                        createFormAlert($form, errors[i].message, errors[i].field, errors[i].type);
                    }

                    if (jqXHR.responseJSON.csrf) {
                        // Update CSRF protection

                        $form.find('input[name="_CSRF_INDEX"]').val(jqXHR.responseJSON.csrf['_CSRF_INDEX']);
                        $form.find('input[name="_CSRF_TOKEN"]').val(jqXHR.responseJSON.csrf['_CSRF_TOKEN']);
                    }
                } else {
                    alert('Error: ' + errorThrown);
                }
            }
        });
    }

    $(document).ready(function () {

        var $sortingForm = $('.sorting form');
        $sortingForm.find('select').one('change', function () {
            $sortingForm.submit();
        });

        // Task actions

        var $taskCreateModal = $('#task-create-modal');
        var $taskCreateForm = $taskCreateModal.find('form');

        $taskCreateModal.on('hidden.bs.modal', function (evt) {
            $taskCreateForm.get(0).reset();
        });

        $taskCreateForm.on('submit', sendForm.bind(null, $taskCreateForm));

        var $taskEditModal = $('#task-edit-modal');
        var $taskEditForm = $taskEditModal.find('form');

        $taskEditModal.on('show.bs.modal', function (evt) {
            var $button = $(evt.relatedTarget);
            $taskEditForm.find('[name="id"]').val($button.data('task-id'));
            $taskEditForm.find('[name="content"]').val($button.data('task-content'));
            $taskEditForm.find('.task-user-name .value').html($button.data('task-user-name'));
            $taskEditForm.find('.task-user-email .value').html($button.data('task-user-email'));
        });
        $taskEditModal.on('hidden.bs.modal', function (evt) {
            $taskEditForm.get(0).reset();
        });

        $taskEditForm.on('submit', sendForm.bind(null, $taskEditForm));

    });

})( window, window.document, window.jQuery );