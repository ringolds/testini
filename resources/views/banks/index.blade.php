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
        <div class="d-flex flex-nowrap overflow-auto scroll-container hide-scrollbar gap-2">
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

        function editBank(id) {
            $('#bank-content').css('opacity', '0.5');

            $.ajax({
                url: '/bank/' + id + '/edit',
                type: 'GET',
                success: function(response) {
                    $('#bank-content').html(response).css('opacity', '1');
                },
                error: function() {
                    alert('Could not load edit form.');
                    $('#bank-content').css('opacity', '1');
                }
            });
        }

        function deleteBank(id) {
            $('#bank-content').css('opacity', '0.5');

            if(confirm("Are you sure you want to delete this bank?")== TRUE){
                $.ajax({
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        loadBank(firstId)
                    },
                    error: function() {
                        alert('Could not delete bank.');
                        $('#bank-content').css('opacity', '1');
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

            $('.bank-btn').on('click', function() {
                const id = $(this).data('id');
                loadBank(id);
            });

            const firstId = $('.bank-btn').first().data('id');
        
            if (firstId) {
                loadBank(firstId);
            }
        });

        $(document).ready(function() {
            $(document).on('click', '.edit-bank-btn', function() {
                const id = $(this).data('id');
                editBank(id);
            });

            $(document).on('click', '.delete-bank-btn', function() {
                const id = $(this).data('id');
                deleteBank(id);
            });

            $(document).on('submit', '#edit-bank-form', function(e) {
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
                        loadBank(response.id);
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
        });
    </script>

</x-layout>