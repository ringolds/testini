<div class="list-group-item d-flex justify-content-between align-items-center px-3 py-3 mb-2 bg-white rounded border shadow-sm" style="min-height: 68px;">
    
    <div class="d-flex align-items-center gap-4 flex-grow-1 overflow-hidden">
        
        <div class="d-flex align-items-center gap-2 overflow-hidden" style="flex: 1;">
            <strong class="text-secondary small text-uppercase tracking-wider">Question:</strong>
            <div class="text-dark small fw-medium text-truncate">
                {{ $questionSlot }}
            </div>
            @if(isset($descriptionSlot))
                <div class="text-dark small fw-medium text-truncate">
                    {{ $descriptionSlot }}
                </div>
            @endif
        </div>

        <div class="text-muted opacity-25">|</div>

        <div class="d-flex align-items-center gap-2 overflow-hidden" style="flex: 1;">
            <strong class="text-secondary small text-uppercase tracking-wider">Answer:</strong>
            <div class="text-success small fw-medium text-truncate">
                {{ $answerSlot }}
            </div>
        </div>

    </div>

    <div class="ms-3 flex-shrink-0">
        <button class="btn btn-sm btn-outline-danger border-0">
            <i class="bi bi-trash"></i>
        </button>
    </div>
</div>