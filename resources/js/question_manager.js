let type;
let button;

function editQuestion(id) {
    $('#question-content').css('opacity', '0.5');

    $.ajax({
        url: '/question/' + id + '/edit',
        type: 'GET',
        success: function(response) {
            $('#question-content').html(response).css('opacity', '1');
        },
        error: function() {
            alert('Could not load edit form.');
            $('#question-content').css('opacity', '1');
        }
    });
}

function addToBank(id, target_id) {
    $('#question-content').css('opacity', '0.5');

    $.ajax({
        url: '/question/' + id + '/bank/'+ target_id,
        type: 'POST',
        success: function(response) {
            $('#bank-content').html(response.html).css('opacity', '1');
            $('#bank-content').attr('data-target-id', target_id);
        },
        error: function() {
            alert('Could not add question.');
            $('#question-content').css('opacity', '1');
        }
    });
}

function removeQuestion(id, target) {
    $('#question-content').css('opacity', '0.5');

    $.ajax({
        url: '/question/' + id + '/bank/' + target,
        type: 'DELETE',
        success: function(response) {
            $('#question-'+id).remove();
            $('#question-content').css('opacity', '1');
        },
        error: function() {
            alert('Could not delete question.');
            $('#question-content').css('opacity', '1');
        }
    });
}

function deleteQuestion(id) {
    $('#question-content').css('opacity', '0.5');

    $.ajax({
        url: '/question/' + id,
        type: 'DELETE',
        success: function(response) {
            $('#question-'+id).remove();
            $('#question-content').css('opacity', '1');
        },
        error: function() {
            alert('Could not delete question.');
            $('#question-content').css('opacity', '1');
        }
    });
}

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    type = window.location.pathname.includes('/test') ? 'test' : 'bank';
    button = '.' + type + '-btn';

    $(document).on('click', '.edit-question-btn', function() {
        const id = $(this).data('id');
        editQuestion(id);
    });

    $(document).on('submit', '#edit-question-form', function(e) {
        e.preventDefault();
        
        let form = $(this);

        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');

        let formData = new FormData(this);

        let activeId = $('#'+ type + '-content').data('id');

        formData.append('content_type', type);
        formData.append('content_id', activeId);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#question-content').html(response.html).css('opacity', '1');
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    
                    $.each(errors, function(key, messages) {
                        let input = form.find(`[name="${key}"]`);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(messages[0]);
                    });
                } else {
                    alert('An unexpected error occurred.');
                }
            }
        });
    });

    $(document).on('submit', '#add-existing-question-form', function(e) {
        e.preventDefault();
        
        let target = $('#bank-content').data('targetId');
        const id = $(this).data('id');
        addToBank(id, target)
    });

    $(document).on('submit', '#delete-question-form', function(e) {
        if (e.isDefaultPrevented()) return;
        
        e.preventDefault();

        const id = $(this).data('id');
        deleteQuestion(id);
    });

    $(document).on('submit', '#remove-question-form', function(e) {
        if (e.isDefaultPrevented()) return;
        
        e.preventDefault();

        const target = $(this).data('targetId');
        const id = $(this).data('id');
        removeQuestion(id, target);
    });

});