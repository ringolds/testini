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

<h1 class="mb-4">{{__('questions.edit')}}</h1> 
<form class="edit-question-form" id="edit-question-form-{{$question->id}}" method="POST" action="{{ route('question.update', $question) }}" enctype="multipart/form-data"> 
    @csrf 
    @method('PUT') 
    <div class="card mb-4 p-3">
        <h4>Question</h4>
        @php
            $type = $question->prompt->component_type;
            $comp = $question->prompt->component;
            $description = $question->description;
        @endphp
        @if($type === 'App\Models\QuestionText')
            <div class="form-group mb-3 dynamic-field-text">
                <label>{{__('questions.text')}}</label>
                <input type="text" class="form-control" name="question_text" value="{{old('question_text', $comp->text)}}">
            </div>
        @elseif($type === 'App\Models\QuestionImage')
            <div class="form-group mb-3 dynamic-field-image">
                <label>{{__('questions.image')}}</label>
                <input type="file" class="form-control" name="question_image">
                <label>{{__('questions.imageDescription')}}</label>
                <input type="text" class="form-control" name="question_image_alt" value="{{old('question_image_alt', $comp->alt_text)}}">
                <label>{{__('questions.imageQuestion')}}</label>
                @if($question->description && $question->description->component)
                    <input type="text" class="form-control" name="question_image_text" value="{{old('question_image_text', $description->component->text)}}">
                @else
                    <input type="text" class="form-control" name="question_image_text" value="{{old('question_image_text')}}">
                @endif
            </div>
        @elseif($type === 'App\Models\QuestionMap')
            <div class="form-group mb-3 dynamic-field-map">
                <div id="question-map" class="interactive-map" style="width: 100%; height: 600px; min-height: 600px; background-color: #ffffff;" 
                            data-config-endpoint="{{ route('map.config', $comp->map->id) }}">
                </div>
                <input type="hidden" class="selected-target" name="question_map_target" value="{{old('question_map_target', $comp->target_region)}}">
                <label>{{__('questions.mapQuestion')}}</label>
                @if($question->description && $question->description->component)
                    <input type="text" class="form-control" name="question_map_text" value="{{old('question_map_text', $description->component->text)}}">
                @else
                    <input type="text" class="form-control" name="question_map_text" value="{{old('question_map_text')}}">
                @endif
            </div>
        @else
            <div>{{__('questions.unknownQuestion')}}</div>
        @endif
    </div>

    <div class="card mb-4 p-3">
        <h4>Answer</h4>
        @php
            $type = $question->answer->component_type;
            $comp = $question->answer->component;
        @endphp
        @if($type === 'App\Models\QuestionText')
            <div class="form-group mb-3 dynamic-field-text">
                <label>{{__('questions.text')}}</label>
                <input type="text" class="form-control" name="answer_text" value="{{old('answer_text', $comp->text)}}">
            </div>
        @elseif($type === 'App\Models\QuestionImage')
            <div class="form-group mb-3 dynamic-field-image">
                <label>{{__('questions.image')}}</label>
                <input type="file" class="form-control" name="answer_image">
                <label>{{__('questions.imageDescription')}}</label>
                <input type="text" class="form-control" name="answer_image_alt" value="{{old('answer_image_alt', $comp->alt_text)}}">
            </div>
        @elseif($type === 'App\Models\QuestionMap')
            <div class="form-group mb-3 dynamic-field-map">
                <div id="answer-map" class="interactive-map" style="width: 100%; height: 600px; min-height: 600px; background-color: #ffffff;" 
                            data-config-endpoint="{{ route('map.config', $comp->map->id) }}">
                </div>
                <input type="hidden" class="selected-target" name="answer_map_target" value="{{old('answer_map_target', $comp->target_region)}}">
            </div>
        @else
            <div>{{__('questions.unknownAnswer')}}</div>
        @endif
    </div>
    <button type="submit" class="btn btn-primary">{{__('questions.save')}}</button> 
</form> 