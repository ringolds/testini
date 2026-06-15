<x-layout> 
    <x-slot name="title"> 
        {{__('tests.available')}} 
    </x-slot> 
    <div class="container my-5">
        <h1 class="mb-4">{{__('tests.available')}} </h1>

        <div class="d-flex justify-content-end mb-3 gap-2">
            <label class="me-2 align-self-center">{{__('tests.cardsPerPage')}} :</label>
            <a href="{{ request()->fullUrlWithQuery(['per_page' => 12, 'page' => 1]) }}" data-amount="12" class="btn btn-sm per-page-btn {{ request('per_page', 12) == 12 ? 'btn-primary' : 'btn-outline-primary' }} me-1">12</a>
            <a href="{{ request()->fullUrlWithQuery(['per_page' => 24, 'page' => 1]) }}" data-amount="24" class="btn btn-sm per-page-btn {{ request('per_page') == 24 ? 'btn-primary' : 'btn-outline-primary' }}">24</a>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                    {{__('game.filters')}}
                </button>

                <div class="dropdown-menu p-3" style="min-width: 250px;" id="filters">
                    
                    <div class="mb-2">
                        <label class="form-label small text-muted">{{__('game.creationDate')}}</label>
                        <select class="form-select form-select-sm filter" name="date" id="date-filter">
                            <option value="">{{__('game.any')}}</option>
                            <option value="new">{{__('game.creationDate')}} ↓</option>
                            <option value="old">{{__('game.creationDate')}} ↑</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small text-muted">{{__('game.rating')}}</label>
                        <select class="form-select form-select-sm filter" name="rating" id="rating-filter">
                            <option value="">{{__('game.any')}}</option>
                            @auth
                                <option value="personalDesc">
                                    {{__('game.personalRating')}} ↓
                                </option>
                                <option value="personalAsc">
                                    {{__('game.personalRating')}} ↑
                                </option>
                            @endauth
                            <option value="generalDesc">
                                {{__('game.generalRating')}} ↓
                            </option>
                            <option value="generalAsc">
                                {{__('game.generalRating')}} ↑
                            </option>
                        </select>
                    </div>

                    @auth
                    <div class="mb-2">
                        <label class="form-label small text-muted">{{__('game.ratingMode')}}</label>
                        <select class="form-select form-select-sm filter" name="ratingMode" id="rating-mode-filter">
                            <option value="">{{__('game.any')}}</option>
                            <option value="personal">{{__('game.personal')}}</option>
                            <option value="general">{{__('game.general')}}</option>
                        </select>
                    </div>
                    @endauth
                    <button class="btn btn-primary btn-sm w-100 mt-2" id="apply-filters-btn">
                        {{__('game.filter')}}
                    </button>
                </div>
            </div>
        </div>
        <div class="text-danger small mb-2 ajax-errors-test"></div>
        <div id="available-test-field">
            @include('tests.available_index_details', ['tests' => $tests, 'ratingMode'=> $ratingMode])
        </div>           
    </div>
    <script>
        $(document).on('click', '.play-btn', function(e) {
            e.preventDefault();
            
            const url = $(this).attr('href');

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(async response => {
                if (!response.ok) {
                    const data = await response.json();

                    if (data.error) {
                        $('.ajax-errors-test').html(
                            `<div class="alert alert-danger">${data.error}</div>`
                        );
                        return;
                    }
                }

                window.location.href = url;
            })
            .catch(err => {
                console.error(err);
                alert(window.translations.errors.unexpectedError);
            });
        });

        function loadTests(url, push = true) {
            $('#available-test-field').css('opacity', '0.5');

            $.ajax({
                url: url,
                type: 'GET',
                success: function (response) {

                    $('#available-test-field').html(response).css('opacity', '1');

                    if (push) {
                        history.pushState(null, '', url);
                    }
                },
                error: function () {
                    alert(window.translations.errors.unexpectedError);
                }
            });
        }

        $(document).on('click', '.per-page-btn', function (e) {
            e.preventDefault();
            
            const params = new URLSearchParams(window.location.search);
            params.set('per_page', $(this).attr('data-amount'));
            params.set('page', 1);
            const url = '?' + params.toString();
            $('.per-page-btn').removeClass('btn-primary').addClass('btn-outline-primary');
            $(this).removeClass('btn-outline-primary').addClass('btn-primary');

            loadTests(url);
        });

        $(document).on('click', '#pagination-container a', function (e) {
            e.preventDefault();

            const url = $(this).attr('href');
            loadTests(url);
        });

        $(document).on('click', '#apply-filters-btn', function(e) {
            e.preventDefault();
            
            const date = document.getElementById('date-filter').value;
            const rating = document.getElementById('rating-filter').value;
            const mode = document.getElementById('rating-mode-filter')?.value;

            const params = new URLSearchParams(window.location.search);

            if (date) params.set('date', date); else params.delete('date');
            if (rating) params.set('rating', rating); else params.delete('rating');
            if (mode) params.set('ratingMode', mode); else params.delete('ratingMode');

            params.set('page', 1);

            const url = '?' + params.toString();

            loadTests(url);
        });


        $(document).ready(function () {
            loadTests(window.location.href, false);
        });
    </script>
</x-layout>