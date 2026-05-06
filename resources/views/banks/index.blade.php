@push('styles')
<style>
    .scroll-container .btn {
      flex: 0 0 auto;
    }
    
    .hide-scrollbar::-webkit-scrollbar {
      display: none;
    }
    
    .hide-scrollbar {
      -ms-overflow-style: none;  
      scrollbar-width: none;  
    }
</style>
@endpush

<x-layout> 
    <x-slot name="title"> 
        Banks 
    </x-slot> 
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">My banks</h1>
        <a href="{{ route('bank.create') }}" class="btn btn-success d-flex align-items-center">
            <i class="bi bi-plus-lg me-2"></i> Create bank
        </a>
    </div>
    <div class="container">
        <div class="d-flex flex-nowrap overflow-auto scroll-container hide-scrollbar">
            @foreach ($banks as $bank)
                <button 
                    type="button"
                    class="btn btn-outline-primary px-4 rounded-pill bank-btn flex-shrink-0"
                    data-id="{{ $bank->id }}"
                    id="btn-{{ $bank->id }}">
                    {{ $bank->name }}
                </button>
            @endforeach
        </div>
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
            $('.bank-btn').on('click', function() {
                const id = $(this).data('id');
                loadBank(id);
            });

            const firstId = $('.bank-btn').first().data('id');
        
            if (firstId) {
                loadBank(firstId);
            }
        });
    </script>


</x-layout>