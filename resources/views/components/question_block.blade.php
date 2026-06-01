@props(['item', 'mode', 'currentItemId' => null, 'collection_type'])
<div class="list-group list-group-flush mt-3" id="question-content">
            @forelse($item->questions as $question)
                <x-question_row :question="$question" :mode="$mode" :currentItemId="$currentItemId" :type="$collection_type">
                    <x-slot:questionSlot>
                        @if($question->prompt && $question->prompt->component)
                            @php
                                $type = $question->prompt->component_type;
                                $comp = $question->prompt->component;
                                $description = $question->description;
                            @endphp

                            @if($type === 'App\Models\QuestionText')
                                <span>{{ str($comp->text)->limit(50) }}</span>
                            @elseif($type === 'App\Models\QuestionImage')
                                <img src="{{ asset('storage/' . $comp->path) }}" 
                                    alt="{{$comp->alt_text}}", 
                                    class="rounded border" 
                                    style="width: 45px; height: 45px; object-fit: cover;"
                                    title="{{ $comp->alt_text }}">
                            @elseif($type === 'App\Models\QuestionMap')
                                <img src="{{ asset('storage/' . $comp->map->svg_path) }}" 
                                    alt="{{$comp->map->name}}", 
                                    class="rounded border" 
                                    style="width: 45px; height: 45px; object-fit: cover;"
                                    title="{{ $comp->alt_text }}">
                            @else
                                <span class="text-muted fst-italic">Alternative Component</span>
                            @endif
                        @else
                            <span class="text-danger">Missing Prompt</span>
                        @endif
                    </x-slot:questionSlot>
                    @if($question->description && $question->description->component)
                        @php
                            $description = $question->description->component;
                        @endphp
                        <x-slot:descriptionSlot>
                            <span>{{ str($description->text)->limit(50) }}</span>
                        </x-slot:descriptionSlot>
                    @endif
                    <x-slot:answerSlot>
                        @if($question->answer && $question->answer->component)
                            @php
                                $type = $question->answer->component_type;
                                $comp = $question->answer->component;
                            @endphp

                            @if($type === 'App\Models\QuestionText')
                                <span>{{ str($comp->text)->limit(50) }}</span>
                            @elseif($type === 'App\Models\QuestionImage')
                                <img src="{{ asset('storage/' . $comp->path) }}" 
                                    alt="A preview" 
                                    class="rounded border" 
                                    style="width: 45px; height: 45px; object-fit: cover;"
                                    title="{{ $comp->alt_text }}">
                            @elseif($type === 'App\Models\QuestionMap')
                                <img src="{{ asset('storage/' . $comp->map->svg_path) }}" 
                                    alt="{{$comp->map->name}}", 
                                    class="rounded border" 
                                    style="width: 45px; height: 45px; object-fit: cover;"
                                    title="{{ $comp->alt_text }}">
                            @else
                                <span class="text-muted fst-italic">Alternative Component</span>
                            @endif
                        @else
                            <span class="text-danger">Missing Answer</span>
                        @endif
                    </x-slot:answerSlot>
                </x-question_row>
            @empty
                <div class="py-3 text-center text-muted">No questions yet.</div>
            @endforelse
            @if($item instanceof \App\Models\Test && $collection_type=='test')
                @foreach($item->banks as $bank)
                    <x-bank_row :count="$bank->pivot->random_count" :bank="$bank" :test="$currentItemId"></x-bank_row>
                @endforeach
            @endif
        </div>