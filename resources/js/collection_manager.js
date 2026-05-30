let type;
let content;
let button;
let editButton;
let deleteForm;
let editForm;
let firstId;
let firstMode;
let addExistingQuestionButton;

function loadCollection(id, mode, target_id) {
    $(content).css('opacity', '0.5');
    if(mode == "add"){
        target_id = document.querySelector(content).getAttribute('data-target-id');
    }
    $.ajax({
        url: '/' + type + '/' + id + '?mode=' + mode+'&target-id='+target_id,
        type: 'GET',
        success: function(response) {
            $(content+'[data-mode="' + mode + '"]').html(response).css('opacity', '1');
            $(content).css('opacity', '1');
            
            $(button+'[data-mode="' + mode + '"').removeClass('btn-primary').addClass('btn-outline-primary');
            $('#btn-' + id+'[data-mode="' + mode + '"').removeClass('btn-outline-primary').addClass('btn-primary');
            $(content+'[data-mode="' + mode + '"]').attr('data-id', id);
            $(content).attr('data-target-id', target_id)
        },
        error: function() {
            alert('Could not load' +type + ' details.');
            $(content).css('opacity', '1');
        }
    });
}

function editCollection(id) {
    $(content).css('opacity', '0.5');

    $.ajax({
        url: '/' +type + '/' + id + '/edit',
        type: 'GET',
        success: function(response) {
            $(content).html(response).css('opacity', '1');
        },
        error: function() {
            alert('Could not load edit form.');
            $(content).css('opacity', '1');
        }
    });
}

function deleteCollection(id) {
    $(content).css('opacity', '0.5');

    $.ajax({
        url: '/' + type + '/' + id,
        type: 'DELETE',
        success: function(response) {
            loadCollection(firstId, firstMode, firstId)
            $('#btn-' + id).remove();
            alert("deleted")
        },
        error: function() {
            alert('Could not delete ' +type+ '.');
            $(content).css('opacity', '1');
        }
    });
}

function addExistingQuestion(id){
    $(content).css('opacity', '0.5');

    $.ajax({
        url: '/' + type + '/' + id + '/questions',
        type: 'GET',
        success: function(response) {
            $(content).html(response).css('opacity', '1');
            // $(content).attr('data-target-id', id);
        },
        error: function() {
            alert('Could not load question adding.');
            $(content).css('opacity', '1');
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
    content = '#' + type + '-content';
    button = '.' + type + '-btn';
    editButton = '.edit-' + type + '-btn';
    deleteForm = '#delete-' + type + '-form';
    editForm = '#edit-' + type + '-form';
    addExistingQuestionButton = '.add-existing-question-' + type + '-btn';

    // $(button).on('click', function() {
    //     const id = $(this).data('id');
    //     const mode =$(this).data('mode');
    //     loadCollection(id, mode);
    // });

    $(document).on('click', button, function() {
        const id = $(this).data('id');
        const mode =$(this).data('mode');

        loadCollection(id, mode, id);
    });

    $(document).on('click', editButton, function() {
        const id = $(this).data('id');
        editCollection(id);
    });

    $(document).on('click', addExistingQuestionButton, function() {
        const id = $(addExistingQuestionButton).data('id');
        addExistingQuestion(id);
    });

    $(document).on('submit', deleteForm, function(e) {
        if (e.isDefaultPrevented()) return;

        e.preventDefault();

        const id = $(this).data('id');
        deleteCollection(id);
    });

    $(document).on('submit', editForm, function(e) {
        e.preventDefault();
        
        let form = $(this);

        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                $(`#btn-${response.id}`).text(response.name);
                loadCollection(response.id, "manage", response.id);
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

    firstId = $(button).first().data('id');
    firstMode = $(button).first().data('mode');

    if (firstId) {
        loadCollection(firstId, firstMode, firstId);
    }
});