<x-layout> 
    <x-slot name="title"> 
        Banks 
    </x-slot> 
    <div class="d-flex overflow-auto pb-3 mb-4" style="gap: 10px; white-space: nowrap;">
        @foreach ($banks as $bank)
            <button 
                type="button"
                onclick="loadBank({{$bank->id}})" 
                class="btn btn-outline-primary px-4 rounded-pill bank-btn"
                data-id="{{ $bank->id }}"
                id="btn-{{ $bank->id }}">
                {{ $bank->name }}
            </button>
        @endforeach
    </div>

    <div id="bank-content">
        <div class="text-center py-5 text-muted">
            Select a bank above to view questions.
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        function loadBank(id) {
            $('#bank-content').css('opacity', '0.5');

            $.ajax({
                url: '/bank/' + id,
                type: 'GET',
                success: function(response) {
                    $('#bank-content').html(response).css('opacity', '1');
                    
                    $('.bank-btn').removeClass('btn-primary').addClass('btn-outline-primary');
                    $('#btn-' + id).removeClass('btn-outline-primary').addClass('btn-primary');
                },
                error: function() {
                    alert('Could not load bank details.');
                    $('#bank-content').css('opacity', '1');
                }
            });
        }

        $(document).ready(function() {
            @if($banks->count() > 0)
                loadBank({{ $banks->first()->id }});
            @endif
        });
    </script>


</x-layout>