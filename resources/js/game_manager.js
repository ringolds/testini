let content;
let firstId;
let firstResultId;

function loadQuestion(resultItemId, resultId) {
    $.ajax({
        url: '/game/' + resultId + '/question/' + resultItemId,
        type: 'GET',
        success: function(response) {
            $(content).html(response).css('opacity', '1');
            $('.question-btn').removeClass('btn-primary').addClass('btn-outline-primary');
            $('#btn-' + resultItemId).removeClass('btn-outline-primary').addClass('btn-primary');
        },
        error: function() {
            alert('Could not load question details.');
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

    content = '#game-content';

    $(document).on('click', '.question-btn', function() {
        const resultItemId = $(this).data('id');
        const resultId =$(this).data('targetId');

        loadQuestion(resultItemId, resultId);
    });

    firstId = $('.question-btn').first().data('id');
    firstResultId = $('.question-btn').first().data('targetId');

    if (firstId) {
        loadQuestion(firstId, firstResultId);
    }
});