@if ($errors->any())
<div>
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<h1 class="mb-4">Edit a Bank</h1> 
<form id="edit-bank-form" method="POST" action="{{ route('bank.update', $bank->id) }}"> 
    @csrf
    @method('PUT') 
 
    <div class="mb-3"> 
        <label class="form-label">Name</label> 
        <input type="text" name="name" id="edit_name" value="{{ old('name', $bank->name) }}" class="form-control"> 
        <div class="invalid-feedback" id="error-name"></div> {{-- Placeholder --}}
    </div> 

    <div class="mb-3"> 
        <label class="form-label">Description</label> 
        <textarea name="description" id="edit_description" class="form-control" rows="5">{{ old('description', $bank->description) }}</textarea>
        <div class="invalid-feedback" id="error-description"></div> {{-- Placeholder --}}
    </div>

    <div class="mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="public" value="1" id="public" 
                {{ old('public', $bank->public) ? 'checked' : '' }}>
            <label class="form-check-label" for="public">
                Public (Visible to everyone)
            </label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="collaborative" value="1" id="collaborative" 
                {{ old('collaborative', $bank->collaborative) ? 'checked' : '' }}>
            <label class="form-check-label" for="collaborative">
                Collaborative (Others can edit)
            </label>
        </div>
    </div>
     
    <button type="submit" class="btn btn-primary">Update Bank</button> 
</form> 