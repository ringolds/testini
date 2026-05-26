let type;
let content;
let button;
let editButton;
let deleteButton;
let editForm;

function loadCollection(id, ) {
    $(content).css('opacity', '0.5');

    $.ajax({
        url: '/' + type + '/' + id,
        type: 'GET',
        success: function(response) {
            $(content).html(response).css('opacity', '1');
            
            $(button).removeClass('btn-primary').addClass('btn-outline-primary');
            $('#btn-' + id).removeClass('btn-outline-primary').addClass('btn-primary');
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

    if(confirm("Are you sure you want to delete this "+ type + "?")== TRUE){
        $.ajax({
            url: '/' + type + '/' + id,
            type: 'DELETE',
            success: function(response) {
                loadCollection(firstId)
            },
            error: function() {
                alert('Could not delete ' +type+ '.');
                $(content).css('opacity', '1');
            }
        });
    }
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
    deleteButton = '.delete-' + type + '-btn';
    editForm = '#edit-' + type + '-form';

    $(button).on('click', function() {
        const id = $(this).data('id');
        loadCollection(id);
    });

    $(document).on('click', editButton, function() {
        const id = $(this).data('id');
        editCollection(id);
    });

    $(document).on('click', deleteButton, function() {
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
                loadCollection(response.id);
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

    const firstId = $(button).first().data('id');

    if (firstId) {
        loadCollection(firstId);
    }
});