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
            alert(window.translations.errors.cannotLoadEdit);
            $('#question-content').css('opacity', '1');
        }
    });
}

function addToCollection(id, target_id) {
    $('#question-content').css('opacity', '0.5');

    $.ajax({
        url: '/question/' + id + '/'+type+'/'+ target_id,
        type: 'POST',
        success: function(response) {
            $('.'+type+'-content').html(response.html).css('opacity', '1');
            $('.'+type+'-content').attr('data-target-id', target_id);
        },
        error: function() {
            alert(window.translations.errors.cannotAddQuestion);
            $('#question-content').css('opacity', '1');
        }
    });
}


function removeQuestion(id, target) {
    $('#question-content').css('opacity', '0.5');

    $.ajax({
        url: '/question/' + id + '/' +type+'/' + target,
        type: 'DELETE',
        success: function(response) {
            $('#question-'+id).remove();
            $('#question-content').css('opacity', '1');
        },
        error: function() {
            alert(window.translations.errors.cannotDeleteQuestion);
            $('#question-content').css('opacity', '1');
        }
    });
}

function removeBank(id, target) {
    $('#question-content').css('opacity', '0.5');

    $.ajax({
        url: '/test/' + target + '/bank/' + id,
        type: 'DELETE',
        success: function(response) {
            $('#bank-'+id+'-row').remove();
            $('#question-content').css('opacity', '1');
        },
        error: function() {
            alert(window.translations.errors.cannotDeleteQuestion);
            $('#question-content').css('opacity', '1');
        }
    });
}

function editBankCount(id, target){
    $('#question-content').css('opacity', '0.5');

    $.ajax({
        url: '/test/' + target + '/bank/' + id +'/edit',
        type: 'GET',
        success: function(response) {
            $('#bank-'+id+'-row').replaceWith(response.html).css('opacity', '1');;
            $('#question-content').css('opacity', '1');
        },
        error: function() {
            alert(window.translations.errors.cannotEditQuestion);
            $('#question-content').css('opacity', '1');
        }
    });
}

function changeBankCount(id, target, formElement){
    $('#question-content').css('opacity', '0.5');

    let form = $(formElement);

    form.find('.is-invalid').removeClass('is-invalid');
    form.find('.invalid-feedback').text('');

    let formData = new FormData(formElement);

    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $('#bank-'+id+'-row').replaceWith(response.html).css('opacity', '1');;
            $('#question-content').css('opacity', '1');
            $('#ajax-errors-bank' + id).html("");
        },
        error: function(xhr) {
            
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                
                if (errors) {
                    let html = "<ul class='mb-0'>";

                    Object.values(errors).forEach(function (messages) {
                        messages.forEach(function (msg) {
                            html += `<li>${msg}</li>`;
                        });
                    });

                    html += "</ul>";

                    $('#ajax-errors-bank-' + id).html(html);
                    $('#question-content').css('opacity', '1');
                }   
            } else {
                alert(window.translations.errors.cannotSaveUpdate);
            }
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
            alert(window.translations.errors.cannotDeleteQuestion);
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

    $(document).on('click', '.edit-bank-count-btn', function(e){
        if (e.isDefaultPrevented()) return;
        
        e.preventDefault();

        const target = $(this).data('targetId');
        const id = $(this).data('id');
        editBankCount(id, target);
    });

    $(document).on('submit', '.edit-question-form', function(e) {
        e.preventDefault();
        
        let form = $(this);

        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');

        let formData = new FormData(this);

        let activeId = $('.'+ type + '-content').data('id');

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
                    alert(window.translations.errors.unexpectedError);
                }
            }
        });
    });

    $(document).on('submit', '.add-existing-question-form', function(e) {
        e.preventDefault();
        
        let target = $('.'+type+'-content').data('targetId');
        const id = $(this).data('id');
        addToCollection(id, target)
    });

    $(document).on('submit', '.delete-question-form', function(e) {
        if (e.isDefaultPrevented()) return;
        
        e.preventDefault();

        const id = $(this).data('id');
        deleteQuestion(id);
    });

    $(document).on('submit', '.remove-question-form', function(e) {
        if (e.isDefaultPrevented()) return;
        
        e.preventDefault();

        const target = $(this).data('targetId');
        const id = $(this).data('id');
        removeQuestion(id, target);
    });

    $(document).on('submit', '.remove-bank-form', function(e) {
        if (e.isDefaultPrevented()) return;
        
        e.preventDefault();

        const target = $(this).data('targetId');
        const id = $(this).data('id');
        removeBank(id, target);
    });

    $(document).on('submit', '.change-bank-count-form', function(e) {
        if (e.isDefaultPrevented()) return;
        
        e.preventDefault();

        const target = $(this).data('targetId');
        const id = $(this).data('id');
        changeBankCount(id, target, this);
    });

});