@vite('resources/js/map_manager.js')
@if ($errors->any())
<div>
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<h1 class="mb-4">{{__('questions.create')}}</h1> 
<form method="POST" id="create-question-form" action="{{ route('question.store') }}" enctype="multipart/form-data"> 
    <input type="hidden" name="type" value="{{$type}}">
    @csrf 
    @if($type=="separate")
        <div class="form-group mb-3">
            <label for="bankSelect">{{__('questions.bank')}}</label>
            <select class="form-control" id="bankSelect" name="bank_id">
                @foreach ($banks as $bank)
                    <option value="{{$bank->id}}" {{ old('bank_id') == $bank->id ? 'selected' : '' }}>{{$bank->name}}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="testSelect">{{__('questions.test')}}</label>
            <select class="form-control" id="testSelect" name="test_id">
                <option value="" {{ old('test_id') == '-1' || !old('test_id') ? 'selected' : '' }}>{{__('questions.none')}}</option>
                @foreach($tests as $test)
                    <option value="{{ $test->id }}" {{ old('test_id') == $test->id ? 'selected' : '' }}>
                        {{ $test->name }}
                    </option>
                @endforeach
            </select>
        </div>
    @endif
    @if($type =="test")
        <input type="hidden" name="bank_id" value="{{$banks->firstWhere('default', true)?->id}}">
        <input type="hidden" name="test_id" value="{{$target_id}}">
    @endif
    @if($type =="bank")
        <input type="hidden" name="bank_id" value="{{$target_id}}">
        <input type="hidden" name="test_id" value="">
    @endif
    <div class="card mb-4 p-3">
        <p class="text-dark fs-3 fw-semibold">{{__('questions.question')}}</p>
        <div class="form-group mb-3">
            <label for="questionType">{{__('questions.question')}} {{__('questions.type')}}</label>
            <select class="form-control type-selector" id="questionType" name="question_type" data-target-wrapper="#questionFields">
                <option value="text" {{ old('question_type') === 'text' ? 'selected' : '' }}>{{__('questions.text')}}</option>
                <option value="image" {{ old('question_type') === 'image' ? 'selected' : '' }}>{{__('questions.image')}}</option>
                <option value="map" {{ old('question_type') === 'map' ? 'selected' : '' }}>{{__('questions.map')}}</option>
            </select>
        </div>

        <div id="questionFields">
            <div class="form-group mb-3 dynamic-field-text">
                <label>{{__('questions.text')}}</label>
                <input type="text" class="form-control" name="question_text" value="{{old('question_text')}}">
            </div>
            <div class="form-group mb-3 dynamic-field-image d-none">
                <label>{{__('questions.image')}}</label>
                <input type="file" class="form-control" name="question_image">
                <label>{{__('questions.imageDescription')}}</label>
                <input type="text" class="form-control" name="question_image_alt" value="{{old('question_image_alt')}}">
                <label>{{__('questions.imageQuestion')}}</label>
                <input type="text" class="form-control" name="question_image_text" value="{{old('question_image_text')}}">
            </div>
            <div class="form-group mb-3 dynamic-field-map d-none">
                <label for="mapSelectQuestion">{{__('questions.map')}}</label>
                <select class="form-control" id="mapSelectQuestion" name="question_map_id">
                    <option value="" {{ old('map_id') == '-1' || !old('map_id') ? 'selected' : '' }}>{{__('questions.none')}}</option>
                    @foreach($maps as $map)
                        <option value="{{ $map->id }}" {{ old('test_id') == $map->id ? 'selected' : '' }}>
                            {{ $map->name }}
                        </option>
                    @endforeach
                </select>
                <div id="question-map" class="interactive-map" style="width: 100%; height: 600px; min-height: 600px; background-color: #ffffff;" 
                            data-config-endpoint="">
                </div>
                <input type="hidden" class="selected-target" name="question_map_target" value="{{old('question_map_target')}}">
                <label>{{__('questions.mapQuestion')}}</label>
                <input type="text" class="form-control" name="question_map_text" value="{{old('question_map_text')}}">
            </div>
        </div>
    </div>

    <div class="card mb-4 p-3">
        <p class="text-dark fs-3 fw-semibold">{{__('questions.answer')}}</p>
        <div class="form-group mb-3">
            <label for="answerType">{{__('questions.answer')}} {{__('questions.type')}}</label>
            <select class="form-control type-selector" id="answerType" name="answer_type" data-target-wrapper="#answerFields">
                <option value="text" {{ old('answer_type') === 'text' ? 'selected' : '' }}>{{__('questions.text')}}</option>
                <option value="image" {{ old('answer_type') === 'image' ? 'selected' : '' }}>{{__('questions.image')}}</option>
                <option value="map" {{ old('answer_type') === 'map' ? 'selected' : '' }}>{{__('questions.map')}}</option>
            </select>
        </div>

        <div id="answerFields">
            <div class="form-group mb-3 dynamic-field-text">
                <label>{{__('questions.text')}}</label>
                <input type="text" class="form-control" name="answer_text" value="{{old('answer_text')}}">
            </div>
            <div class="form-group mb-3 dynamic-field-image d-none">
                <label>{{__('questions.image')}}</label>
                <input type="file" class="form-control" name="answer_image">
                <label>{{__('questions.imageDescription')}}</label>
                <input type="text" class="form-control" name="answer_image_alt" value="{{old('answer_image_alt')}}">
            </div>
            <div class="form-group mb-3 dynamic-field-map d-none">
                <label for="mapSelectAnswer">{{__('questions.map')}}</label>
                <select class="form-control" id="mapSelectAnswer" name="answer_map_id">
                    <option value="" {{ old('map_id') == '-1' || !old('map_id') ? 'selected' : '' }}>{{__('questions.none')}}</option>
                    @foreach($maps as $map)
                        <option value="{{ $map->id }}" {{ old('test_id') == $map->id ? 'selected' : '' }}>
                            {{ $map->name }}
                        </option>
                    @endforeach
                </select>
                <div id="answer-map" class="interactive-map" style="width: 100%; height: 600px; min-height: 600px; background-color: #ffffff;" 
                            data-config-endpoint="">
                </div>
                <input type="hidden" class="selected-target" name="answer_map_target" value="{{old('answer_map_target')}}">
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">{{__('questions.createButton')}}</button> 
</form> 
<script>
    (function () {
        
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

        const questionSelect = document.getElementById('mapSelectQuestion');
        const answerSelect = document.getElementById('mapSelectAnswer');

        if (questionSelect) {
            questionSelect.addEventListener('change', function(event) {
                const selectedId = event.target.value;
                const container = document.getElementById("question-map");
                
                if (container && selectedId) {
                    container.setAttribute('data-config-endpoint', '/map/' + selectedId + '/config');
                }
            });
        }

        if (answerSelect) {
            answerSelect.addEventListener('change', function(event) {
                const selectedId = event.target.value;
                const container = document.getElementById("answer-map");
                
                if (container && selectedId) {
                    container.setAttribute('data-config-endpoint', '/map/' + selectedId + '/config');
                }
            });
        }
    })();
</script>