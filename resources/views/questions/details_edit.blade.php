@if ($errors->any())
    <div>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<h1 class="mb-4">Edit a question</h1> 
<form method="POST" action="{{ route('question.update', $question) }}" enctype="multipart/form-data"> 
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
                <label>Text</label>
                <input type="text" class="form-control" name="question_text" value="{{old('question_text', $comp->text)}}">
            </div>
        @elseif($type === 'App\Models\QuestionImage')
            <div class="form-group mb-3 dynamic-field-image">
                <label>Image</label>
                <input type="file" class="form-control" name="question_image">
                <label>Image description</label>
                <input type="text" class="form-control" name="question_image_alt" value="{{old('question_image_alt', $comp->alt_text)}}">
                <label>Text question to go along with image (optional)</label>
                @if($question->description && $question->description->component)
                    <input type="text" class="form-control" name="question_image_text" value="{{old('question_image_text', $description->component->text)}}">
                @else
                    <input type="text" class="form-control" name="question_image_text" value="{{old('question_image_text')}}">
                @endif
            </div>
        @elseif($type === 'App\Models\QuestionMap')
            <div class="form-group mb-3 dynamic-field-map d-none">
                <div class="alert alert-secondary">Question Map Placeholder</div>
            </div>
        @else
            <div>Unknown question type</div>
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
                <label>Text</label>
                <input type="text" class="form-control" name="answer_text" value="{{old('answer_text', $comp->text)}}">
            </div>
        @elseif($type === 'App\Models\QuestionImage')
            <div class="form-group mb-3 dynamic-field-image">
                <label>Image</label>
                <input type="file" class="form-control" name="answer_image">
                <label>Image description</label>
                <input type="text" class="form-control" name="answer_image_alt" value="{{old('answer_image_alt', $comp->alt_text)}}">
            </div>
        @elseif($type === 'App\Models\QuestionMap')
            <div class="form-group mb-3 dynamic-field-map">
                <div class="alert alert-secondary">Answer Map Placeholder</div>
            </div>
        @else
            <div>Unknown answer type</div>
        @endif
    </div>
    <button type="submit" class="btn btn-primary">Save question</button> 
</form> 