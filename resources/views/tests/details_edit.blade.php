@if ($errors->any())
<div>
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<h1 class="mb-4">{{__('tests.edit')}}</h1> 
<form id="edit-test-form" method="POST" action="{{ route('test.update', $test->id) }}"> 
    @csrf
    @method('PUT') 
 
    <div class="mb-3"> 
        <label class="form-label">{{__('tests.name')}} </label> 
        <input type="text" name="name" id="edit_name" value="{{ old('name', $test->name) }}" class="form-control"> 
        <div class="invalid-feedback" id="error-name"></div> {{-- Placeholder --}}
    </div> 

    <div class="mb-3"> 
        <label class="form-label">{{__('tests.description')}} </label> 
        <textarea name="description" id="edit_description" class="form-control" rows="5">{{ old('description', $test->description) }}</textarea>
        <div class="invalid-feedback" id="error-description"></div> {{-- Placeholder --}}
    </div>

    <div class="mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="public" value="1" id="public" 
                {{ old('public', $test->public) ? 'checked' : '' }}>
            <label class="form-check-label" for="public">
                {{__('tests.public')}} 
            </label>
        </div>
    </div>
     
    <button type="submit" class="btn btn-primary">{{__('tests.update')}} </button> 
</form> 