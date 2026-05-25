<x-layout> 
    <x-slot name="title"> 
        Create a new Question
    </x-slot> 
    
    @if ($errors->any())
    <div>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <h1 class="mb-4">Create a New Question</h1> 
    <form method="POST" action="{{ route('question.store') }}" enctype="multipart/form-data"> 
        @csrf 
        <div class="form-group mb-3">
            <label for="bankSelect">Bank</label>
            <select class="form-control" id="bankSelect" name="bank_id">
                @foreach ($banks as $bank)
                    <option value="{{$bank->id}}" {{ old('bank_id') == $bank->id ? 'selected' : '' }}>{{$bank->name}}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="testSelect">Test</label>
            <select class="form-control" id="testSelect" name="test_id">
                <option value="" {{ old('test_id') == '-1' || !old('test_id') ? 'selected' : '' }}>None</option>
                @foreach($tests as $test)
                    <option value="{{ $test->id }}" {{ old('test_id') == $test->id ? 'selected' : '' }}>
                        {{ $test->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="card mb-4 p-3">
            <h4>Question</h4>
            <div class="form-group mb-3">
                <label for="questionType">Type</label>
                <select class="form-control type-selector" id="questionType" name="question_type" data-target-wrapper="#questionFields">
                    <option value="text" {{ old('question_type') === 'text' ? 'selected' : '' }}>Text</option>
                    <option value="image" {{ old('question_type') === 'image' ? 'selected' : '' }}>Image</option>
                    <option value="map" {{ old('question_type') === 'map' ? 'selected' : '' }}>Map</option>
                </select>
            </div>

            <div id="questionFields">
                <div class="form-group mb-3 dynamic-field-text">
                    <label>Text</label>
                    <input type="text" class="form-control" name="question_text" value="{{old('question_text')}}">
                </div>
                <div class="form-group mb-3 dynamic-field-image d-none">
                    <label>Image</label>
                    <input type="file" class="form-control" name="question_image">
                    <label>Image description</label>
                    <input type="text" class="form-control" name="question_image_alt" value="{{old('question_image_alt')}}">
                    <label>Text question to go along with image (optional)</label>
                    <input type="text" class="form-control" name="question_image_text" value="{{old('question_image_text')}}">
                </div>
                <div class="form-group mb-3 dynamic-field-map d-none">
                    <div class="alert alert-secondary">Question Map Placeholder</div>
                </div>
            </div>
        </div>

        <div class="card mb-4 p-3">
            <h4>Answer</h4>
            <div class="form-group mb-3">
                <label for="answerType">Answer Type</label>
                <select class="form-control type-selector" id="answerType" name="answer_type" data-target-wrapper="#answerFields">
                    <option value="text" {{ old('answer_type') === 'text' ? 'selected' : '' }}>Text</option>
                    <option value="image" {{ old('answer_type') === 'image' ? 'selected' : '' }}>Image</option>
                    <option value="map" {{ old('answer_type') === 'map' ? 'selected' : '' }}>Map</option>
                </select>
            </div>

            <div id="answerFields">
                <div class="form-group mb-3 dynamic-field-text">
                    <label>Text</label>
                    <input type="text" class="form-control" name="answer_text" value="{{old('answer_text')}}">
                </div>
                <div class="form-group mb-3 dynamic-field-image d-none">
                    <label>Image</label>
                    <input type="file" class="form-control" name="answer_image">
                    <label>Image description</label>
                    <input type="text" class="form-control" name="answer_image_alt" value="{{old('answer_image_alt')}}">
                </div>
                <div class="form-group mb-3 dynamic-field-map d-none">
                    <div class="alert alert-secondary">Answer Map Placeholder</div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Create question</button> 
    </form> 
</x-layout>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        function handleTypeChange(selectElement) {
            const selectedValue = selectElement.value; 
            const targetWrapperId = selectElement.getAttribute('data-target-wrapper');
            const wrapper = document.querySelector(targetWrapperId);

            if (!wrapper) return;

            const textFields = wrapper.querySelectorAll('.dynamic-field-text');
            const imageFields = wrapper.querySelectorAll('.dynamic-field-image');
            const mapFields = wrapper.querySelectorAll('.dynamic-field-map');

            [...textFields, ...imageFields, ...mapFields].forEach(field => field.classList.add('d-none'));

            const activeFields = wrapper.querySelectorAll(`.dynamic-field-${selectedValue}`);
            activeFields.forEach(field => field.classList.remove('d-none'));
        }

        const selectors = document.querySelectorAll('.type-selector');
        
        selectors.forEach(select => {
            select.addEventListener('change', function () {
                handleTypeChange(this);
            });

            handleTypeChange(select);
        });
    });
</script>