<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">{{ $bank->name }}</h5>
    </div>
    <div class="card-body">
        <p class="text-muted small">{{ $bank->description }}</p>
        <div class="list-group list-group-flush mt-3">
            @forelse($bank->questions as $question)
                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <span>{{ $question->text }}</span>
                    <!-- We'll handle the 'Remove' logic via AJAX next -->
                    <button class="btn btn-sm btn-outline-danger border-0">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            @empty
                <div class="py-3 text-center text-muted">No questions yet.</div>
            @endforelse
        </div>
    </div>
</div>