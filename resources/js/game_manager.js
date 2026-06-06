let content;
let firstId;
let firstResultId;

function loadQuestion(resultItemId, resultId) {
    $.ajax({
        url: '/game/' + resultId + '/question/' + resultItemId,
        type: 'GET',
        success: function(response) {
            $(content).html(response.html).css('opacity', '1');
            $(content).attr('data-id', resultItemId);
            $(content).attr('data-target-id', resultId);
            $('.question-btn').removeClass('btn-primary').addClass('btn-outline-primary');
            $('#btn-' + resultItemId).removeClass('btn-outline-primary').addClass('btn-primary');
            if(response.results !== null && response.results !== undefined){
                handleAnsweredQuestion(response.results, resultItemId, resultId)
            }
        },
        error: function() {
            alert('Could not load question details.');
            $(content).css('opacity', '1');
        }
    }); 
}

function makeMultipleChoice(id){
    $('.multiple-choice-btn').removeClass('btn-primary').removeClass('text-white')
        .addClass('btn-outline-light').addClass('text-dark border');
    $('#multiple-choice-'+id).removeClass('btn-outline-light').removeClass('text-dark border')
        .addClass('btn-primary').addClass('text-white');
    
    $('#multiple-choice').attr('value', id)
}

function loadSummary(resultId){
    $.ajax({
        url: '/game/' + resultId+'/summary',
        type: 'GET',
        success: function(response) {
            $('#game-window').html(response).css('opacity', '1');
        },
        error: function() {
            alert('Could not load question details.');
            $(content).css('opacity', '1');
        }
    });
}

function submitQuestion(resultItemId, resultId, formElement){
    let form = $(formElement);

        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');

        let formData = new FormData(formElement);

        $.ajax({
            url: '/game/'+resultId + '/question/' + resultItemId,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                handleAnsweredQuestion(response, resultItemId, resultId)
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
}

function handleAnsweredQuestion(response, resultItemId, resultId){
    if(response.correct == 1){
        $('#btn-' + resultItemId).removeClass('btn-outline-primary text-primary text-dark').addClass('btn-success text-white');
    }
    else{
        $('#btn-' + resultItemId).removeClass('btn-outline-primary text-primary text-dark').addClass('btn-danger text-white');
    }
    $('#submit-btn').remove();
    if(response.finished){
        let summaryButton = `<button type="button" id="summary-btn" data-id="${resultId}";
            class="btn btn-success mt-4 ms-2">Finish quiz</button>`;
        $('#game-entry-'+resultItemId).append(summaryButton);
    }
    else{
        let nextActionButton = `<button type="button" id="next-btn" data-id="${response.next_question_index}"
            class="btn btn-success mt-4 ms-2">Next question</button>`;
        $('#game-entry-'+resultItemId).append(nextActionButton);
    }
    
    //multiple choice
    $('.multiple-choice-btn').prop('disabled', true);
    $('.multiple-choice-btn').css('opacity', '1');
    $('.multiple-choice-btn').removeClass('btn-primary').removeClass('text-white')
        .addClass('btn-outline-light').addClass('text-dark border');
    
    let correctBtn = $(`.multiple-choice-btn[data-answer-id="${response.answer}"]`);
    correctBtn.removeClass('btn-outline-light text-dark').addClass('btn-success text-white');

    if(response.answer != response.userAnswer){
        let chosenBtn = $(`.multiple-choice-btn[data-answer-id="${response.userAnswer}"]`);
        chosenBtn.removeClass('btn-outline-light text-dark').addClass('btn-danger text-white');
    }
    //singular answer
        //text answer
    $('#answer-form').find('input[type="text"]').prop('readonly', true);
    if ($('#answer-form').find('input[name="question_answer"]').length > 0) {
        let textInput = $('#answer-form').find('input[name="question_answer"]');
        
        if (response.correct == 1) {
            textInput.removeClass('is-invalid')
                .addClass('bg-success text-white border border-warning');
        } else {
            textInput.addClass('is-invalid bg-danger text-white');
            
            if (textInput.siblings('.correct-answer-feedback').length === 0) {
                textInput.after(`<div class="text-success small mt-1 correct-answer-feedback">Correct answer: <strong>${response.answer}</strong></div>`);
            }
        }
    }
        //map answer
    if ($('#answer-form').find('input[name="answer_map_target"]').length > 0) {
        $('#answer-map').attr('data-config-endpoint', '/game/'+resultItemId+'/config/result')
    }
}

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    content = '#game-content';

    $(document).on('click', '.question-btn', function() {
        const resultItemId = $(this).data('id');
        const resultId =$(this).data('targetId');

        loadQuestion(resultItemId, resultId);
    });

    $(document).on('click', '.multiple-choice-btn', function() {
        const id = $(this).data('answerId');
        makeMultipleChoice(id);
    });

    $(document).on('submit', '#answer-form', function(e) {
        e.preventDefault();
        const resultItemId = $(content).attr('data-id');
        const resultId =$(content).attr('data-target-id');
        submitQuestion(resultItemId, resultId, this);
    });

    $(document).on('click', '#next-btn', function(){
        const resultItemId = $(this).data('id');
        const resultId =$(content).attr('data-target-id');
        loadQuestion(resultItemId, resultId);
    });

    $(document).on('click', '#summary-btn', function(){
        const resultId =$(content).attr('data-target-id');
        loadSummary(resultId);
    });



    firstId = $('.question-btn').first().data('id');
    firstResultId = $('.question-btn').first().data('targetId');

    if (firstId) {
        loadQuestion(firstId, firstResultId);
    }
});