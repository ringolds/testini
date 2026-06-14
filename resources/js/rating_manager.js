$(document).ready(function () {

     document.querySelectorAll('.rating-form').forEach(form => {

        const stars = form.querySelectorAll('.star');
        const input = form.querySelector('.rating-input');
        const errorBox = form.querySelector('.ajax-errors-rating');

        let currentRating = parseInt(input.value || 0);

        function paint(rating) {
            stars.forEach(s => {
                const value = parseInt(s.dataset.rating);

                if (value <= rating) {
                    s.classList.add('bi-star-fill', 'text-warning');
                    s.classList.remove('bi-star', 'text-secondary');
                } else {
                    s.classList.add('bi-star', 'text-secondary');
                    s.classList.remove('bi-star-fill', 'text-warning');
                }
            });
        }

        paint(currentRating);

        stars.forEach(star => {
            star.addEventListener('click', function () {
                currentRating = parseInt(this.dataset.rating);
                input.value = currentRating;
                paint(currentRating);

                form.requestSubmit();
            });
        });

        stars.forEach(star => {
            star.addEventListener('mouseenter', function () {
                paint(parseInt(this.dataset.rating));
            });
        });

        form.addEventListener('mouseleave', function () {
            paint(currentRating);
        });

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            errorBox.textContent = '';

            const id = form.dataset.id;

            const formData = new FormData(form);

            $.ajax({
                url: "/rating/" + id,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                
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
        });

    });
});
