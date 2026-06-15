let type;
let content;
let button;
let editButton;
let deleteForm;
let editForm;
let firstId;
let firstMode;
let addExistingQuestionButton;
let addNewQuestionButton;

function loadCollection(id, mode, target_id, optional_type=null) {
    if(optional_type!=null){
       $('.' + optional_type + '-content').css('opacity', '0.5');
        if(mode == "add" || mode=="addBank"){
            target_id = document.querySelector('.' + optional_type + '-content[data-mode="' + mode + '"]').getAttribute('data-target-id');
        }
        $.ajax({
            url: '/' + optional_type + '/' + id + '?mode=' + mode+'&target-id='+target_id+'&type='+type,
            type: 'GET',
            success: function(response) {
                $('.' + optional_type + '-content'+'[data-mode="' + mode + '"]').html(response).css('opacity', '1');
                $('.' + optional_type + '-content').css('opacity', '1');
                
                $('.' + optional_type + '-btn'+'[data-mode="' + mode + '"').removeClass('btn-primary').addClass('btn-outline-primary');
                $('#btn-' + optional_type + '-'+id+'[data-mode="' + mode + '"').removeClass('btn-outline-primary').addClass('btn-primary');
                $('.' + optional_type + '-content'+'[data-mode="' + mode + '"]').attr('data-id', id);
                $('.' + optional_type + '-content').attr('data-target-id', target_id)
            },
            error: function() {
                if(optional_type == "bank"){
                    alert(window.translations.errors.cannotLoadDetailsBank);
                }
                else{
                    alert(window.translations.errors.cannotLoadDetailsTest);
                }
                $('.' + optional_type + '-content').css('opacity', '1');
            }
        }); 
    }
    else{
        $(content).css('opacity', '0.5');
        if(mode == "add"){
            target_id = document.querySelector(content).getAttribute('data-target-id');
        }
        $.ajax({
            url: '/' + type + '/' + id + '?mode=' + mode+'&target-id='+target_id+'&type='+type,
            type: 'GET',
            success: function(response) {
                $(content+'[data-mode="' + mode + '"]').html(response).css('opacity', '1');
                $(content).css('opacity', '1');
                
                $(button+'[data-mode="' + mode + '"').removeClass('btn-primary').addClass('btn-outline-primary');
                $('#btn-' + type+'-'+id+'[data-mode="' + mode + '"').removeClass('btn-outline-primary').addClass('btn-primary');
                $(content+'[data-mode="' + mode + '"]').attr('data-id', id);
                $(content).attr('data-target-id', target_id)
            },
            error: function() {
                if(type == "bank"){
                    alert(window.translations.errors.cannotLoadDetailsBank);
                }
                else{
                    alert(window.translations.errors.cannotLoadDetailsTest);
                }
                $(content).css('opacity', '1');
            }
        });
    }
    
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
            alert(window.translations.errors.cannotLoadEdit);
            $(content).css('opacity', '1');
        }
    });
}

function addNewQuestion(id) {
    $(content).css('opacity', '0.5');

    $.ajax({
        url: '/question/create?type='+type+'&id='+id,
        type: 'GET',
        success: function(response) {
            $(content).html(response).css('opacity', '1');
        },
        error: function() {
            alert(window.translations.errors.cannotLoadEdit);
            $(content).css('opacity', '1');
        }
    });
}

function addRandomQuestions(id) {
    $(content).css('opacity', '0.5');

    $.ajax({
        url: '/test/'+id+'/banks',
        type: 'GET',
        success: function(response) {
            $(content).html(response).css('opacity', '1');
        },
        error: function() {
            alert(window.translations.errors.cannotLoadRandomQuestions);
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
            $('#btn-' + type +'-'+ id).remove();
        },
        error: function() {
            if(type == "bank"){
                alert(window.translations.errors.cannotDeleteBank);
            }
            else{
                alert(window.translations.errors.cannotDeleteTest);
            }
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
            alert(window.translations.errors.questionAddNotLoading);
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
    content = '.' + type + '-content';
    button = '.' + type + '-btn';
    editButton = '.edit-' + type + '-btn';
    deleteForm = '#delete-' + type + '-form';
    editForm = '#edit-' + type + '-form';
    addExistingQuestionButton = '.add-existing-question-' + type + '-btn';
    addNewQuestionButton = '.add-new-question-btn';

    $(document).on('click', '.bank-btn', function() {
        const id = $(this).attr('data-id');
        const mode =$(this).attr('data-mode');

        loadCollection(id, mode, id, "bank");
    });

    $(document).on('click', '.test-btn', function() {
        const id = $(this).attr('data-id');
        const mode =$(this).attr('data-mode');

        loadCollection(id, mode, id, "test");
    });

    $(document).on('click', '.add-random-question-btn', function(){
        const id = $(this).attr('data-id');
        addRandomQuestions(id);
    });

    $(document).on('click', editButton, function() {
        const id = $(this).attr('data-id');
        editCollection(id);
    });

    $(document).on('click', addNewQuestionButton, function() {
        const id = $(this).attr('data-id');
        addNewQuestion(id);
    });

    $(document).on('submit', '#create-question-form', function(e) {
        e.preventDefault();

        let form = $(this);

        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');

        let formData = new FormData(this);

        $.ajax({
            url: '/question',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                loadCollection(response.id, 'manage', response.id)
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

    $(document).on('click', addExistingQuestionButton, function() {
        const id = $(addExistingQuestionButton).attr('data-id');
        addExistingQuestion(id);
    });

    $(document).on('submit', deleteForm, function(e) {
        if (e.isDefaultPrevented()) return;

        e.preventDefault();

        const id = $(this).attr('data-id');
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
                $('#btn-'+ type +'-'+response.id).text(response.name);
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
                    alert(window.translations.errors.unexpectedError);
                }
            }
        });
    });

    $(document).on('submit', '#publish-'+type+'-form', function(e) {
        e.preventDefault();
        
        let form = $(this);

        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if(type=="bank"){
                    alert(window.translations.publishedBank);
                }
                else{
                    alert(window.translations.publishedTest);
                }
                loadCollection(response.id, "manage", response.id);
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.error;
                    
                    if (errors) {
                        $('.ajax-errors-' + type).html(`<div class="text-danger">${errors}</div>`);
                        return;
                    }
                } else {
                    alert(window.translations.errors.unexpectedError);
                }
            }
        });
    });

    $(document).on('submit', '#unpublish-test-form', function(e) {
        e.preventDefault();
        
        let form = $(this);

        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                alert(window.translations.unpublishedTest);
                loadCollection(response.id, "manage", response.id);
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.error;
                    
                    if (errors) {
                        $('.ajax-errors-' + type).html(`<div class="text-danger">${errors}</div>`);
                        return;
                    }
                } else {
                    alert(window.translations.errors.unexpectedError);
                }
            }
        });
    });

    $(document).on('submit', '#add-random-questions-form', function(e) {
        e.preventDefault();
        
        let form = $(this);

        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');

        let testId = $(this).attr('data-target-id');
        let bankId = $(this).attr('data-id');

        $.ajax({
            url: '/test/'+testId + '/bank/' + bankId,
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                $(content).html(response.html).css('opacity', '1');
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

                        $('#ajax-errors-bank-' + bankId).html(html);
                        $('#question-content').css('opacity', '1');
                    }   
                } else {
                    alert(window.translations.errors.cannotSaveUpdate);
                }
            }
        });
    });

    firstId = $(button).first().attr('data-id');
    firstMode = $(button).first().attr('data-mode');

    if (firstId) {
        loadCollection(firstId, firstMode, firstId);
    }
});