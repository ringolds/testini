<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Result;
use App\Models\ResultItem;
use App\Models\Test;
use Illuminate\Support\Str;

class GameController extends Controller
{
    public function start(Test $test){
        if(request()->user()->can('start', $test)){
            $staticQuestions = $test->questions()->pluck('id')->toArray();

            $staticQuestions = $test->questions()->pluck('id')->toArray();

            $staticImageIds = \App\Models\Question::whereIn('id', $staticQuestions)
                ->whereHas('answer', fn($q) => $q->where('component_type', \App\Models\QuestionImage::class))
                ->pluck('id')
                ->toArray();

            $staticImageCount = count($staticImageIds);

            $bankIds = $test->banks->pluck('id')->toArray();
            $allAvailableBankImageIds = \App\Models\Question::whereHas('banks', fn($q) => $q->whereIn('banks.id', $bankIds))
                ->whereHas('answer', fn($q) => $q->where('component_type', \App\Models\QuestionImage::class))
                ->pluck('id')
                ->unique() // 
                ->toArray();

            $eligibleBankImageIds = array_diff($allAvailableBankImageIds, $staticImageIds);
            $totalBankImagesAvailable = count($eligibleBankImageIds);
            $totalPotentialImages = $staticImageCount + $totalBankImagesAvailable;

            if ($totalPotentialImages === 1) {
                return response()->json(['error' => __('errors.imageTest')], 422);
            }

            $guaranteedImageIds = [];

            if ($staticImageCount === 1 && $totalBankImagesAvailable >= 1) {
                // We need exactly 1 more from any bank to make it 2
                $guaranteedImageIds = \App\Models\Question::whereHas('banks', fn($q) => $q->whereIn('banks.id', $bankIds))
                    ->whereHas('answer', fn($q) => $q->where('component_type', \App\Models\QuestionImage::class))
                    ->inRandomOrder()->limit(1)->pluck('id')->toArray();
            } 
            elseif ($staticImageCount === 0 && $totalBankImagesAvailable >= 2) {
                // We need exactly 2 from the banks to ensure our multiple choice has choices
                $guaranteedImageIds = \App\Models\Question::whereHas('banks', fn($q) => $q->whereIn('banks.id', $bankIds))
                    ->whereHas('answer', fn($q) => $q->where('component_type', \App\Models\QuestionImage::class))
                    ->inRandomOrder()->limit(2)->pluck('id')->toArray();
            }

            $randomQuestions = $guaranteedImageIds;

            $result = Result::create([
                'user_id' => request()->user()->id,
                'test_id' => $test->id,
                'start_time'=> now()
            ]);

            foreach ($test->banks as $bank) {
                $count = $bank->pivot->random_count;
                $available = $bank->questions()->count();

                if($available<$count){
                    $count = $available;
                    $test->banks()->updateExistingPivot($bank->id, [
                        'random_count' => $count
                    ]);
                }

                if ($count > 0) {
                    $available = $bank->questions()->whereNotIn('id', $randomQuestions)->count();
                    if ($available < $count) { $count = $available; }

                    $fetchedIds = $bank->questions()
                        ->whereNotIn('id', $guaranteedImageIds)
                        ->inRandomOrder()
                        ->take($count)
                        ->pluck('id')
                        ->toArray();

                    $randomQuestions = array_merge($randomQuestions, $fetchedIds);
                }
            }

            $allQuestions = array_merge($staticQuestions, $randomQuestions);

            $shuffledQuestionIds = collect($allQuestions)->shuffle()->all();

            foreach ($shuffledQuestionIds as $index => $questionId) {
                ResultItem::create([
                    'result_id'           => $result->id,
                    'question_id'         => $questionId,
                    'order'               => $index + 1,
                    'is_correct'          => null,
                    'duration'            => null,
                    'user_answer_content' => null,
                ]);
            }
            $questions = $result->items;

            return view('games.start', compact('test', 'questions'));
        }
        
        else if(request()->user()->can('continue', $test)){
            $result = request()->user()->results()
            ->where('test_id', $test->id)
            ->whereNull('end_time')->first();

            $questions = $result->items;

            return view('games.start', compact('test', 'questions'));
        }
        return redirect(route('home'));
    }

    public function getQuestion(Result $result, ResultItem $resultItem){
        if(request()->user()->can('getQuestion', [$result, $resultItem])){
            $question = $resultItem->question->prompt->component;
            $description = $resultItem->question->description?->component;
            
            $componentType = $resultItem->question->answer->component_type;

            if($componentType == 'App\Models\QuestionImage'){
                $answerMode = 'multiple';
            }
            else{
                $answerMode = 'single';
            }

            if($answerMode == 'multiple'){
                $randomItems = $result->items()->where('id', '!=', $resultItem->id)
                    ->whereHas('question.answer', function ($query) use ($componentType){
                        $query->where('component_type', $componentType);
                    })
                    ->with(['question.answer.component'])
                    ->inRandomOrder($result->id+$resultItem->id)
                    ->limit(3)
                    ->get();

                if($randomItems->count()<1){
                    abort(404, __('errors.questionNotFound'));
                }

                $choices = $randomItems->map(function ($item) {
                    return $item->question->answer->component;
                });

                $correctAnswerComponent = $resultItem->question->answer->component;
                $choices->push($correctAnswerComponent);
                $choices = $choices->shuffle();
            }
            else{
                $choices = null;
            }
            
            $preExistingResults = null;

            if ($resultItem->is_correct !== null) {
                $preExistingResults = [
                    'next_question_index' => $this->calculateNextIndex($result, $resultItem),
                    'correct'             => $resultItem->is_correct,
                    'userAnswer'          => $resultItem->user_answer_content
                ];

                switch ($resultItem->question->answer->component_type) {
                    case 'App\Models\QuestionText':
                        $preExistingResults['answer'] = (string) $resultItem->question->answer->component->text;
                        break;

                    case 'App\Models\QuestionImage':
                        $preExistingResults['answer'] = (string) $resultItem->question->answer->component->id;
                        break;

                    case 'App\Models\QuestionMap':
                        $preExistingResults['answer'] = (string) $resultItem->question->answer->component->target_region;
                        break;

                    default:
                        abort(422, __('errors.invalidQuestionComponent'));
                }

            }

            $html =  view('games.game_entry', [
                'question' => $question,
                'resultItem' => $resultItem,
                'answerMode' => $answerMode,
                'description' => $description,
                'choices' => $choices,
                'answerType' => $componentType,
            ])->render();

            return response()->json(['html' => $html, 'results'=>$preExistingResults]);
        }
        else{
            return redirect(route('home'));
        }
    }

    public function submitQuestion(Request $request, Result $result, ResultItem $resultItem){
        if($request->user()->can('submitQuestion', [$result, $resultItem])){
            $answer = $resultItem->question->answer;

            switch ($answer->component_type) {
                case 'App\Models\QuestionText':
                    $rules['question_answer'] = 'required|string|max:250';
                    break;

                case 'App\Models\QuestionImage':
                    $rules['multiple_choice'] = 'required|integer';
                    break;

                case 'App\Models\QuestionMap':
                    $rules['answer_map_target'] = 'required|string|max:50';
                    break;

                default:
                    abort(422, 'Invalid question component type detected.');
            }

            $request->validate($rules);
            
            switch ($answer->component_type) {
                case 'App\Models\QuestionText':
                    $userAnswer = $request->input('question_answer');
                    $answer = $answer->component->text;
                    break;
                case 'App\Models\QuestionImage':
                    $userAnswer = $request->input('multiple_choice');
                    $answer = $answer->component->id;
                    break;
                case 'App\Models\QuestionMap':
                    $userAnswer = $request->input('answer_map_target');
                    $answer = $answer->component->target_region;
                    break;
                default:
                    abort(422, 'Invalid question component type detected.');
            }
            
            $correct = Str::lower($answer) === Str::lower($userAnswer);
            $resultItem->is_correct = $correct;
            $resultItem->user_answer_content = $userAnswer;
            $resultItem->update();

            session()->put("verified_map_item_{$resultItem->id}", [
                'user_answer' => $userAnswer
            ]);

            $nextIndex = $this->calculateNextIndex($result, $resultItem);

            if ($nextIndex !== null) {
                return response()->json(['next_question_index' => $nextIndex, 'correct'=>$correct, 'answer'=>str($answer), 'userAnswer'=>$userAnswer, 'finished'=>false]);
            } else {
                $correctCount = $result->items()->where('is_correct', '=', 1)->count();
                $result->score= $correctCount;
                $result->end_time = now();
                $result->update();
                return response()->json(['next_question_index' => $nextIndex, 'correct'=>$correct, 'answer'=>str($answer), 'userAnswer'=>$userAnswer, 'finished'=>true]);
            }
        }
        else{
            return redirect(route('home'));
        }
    }

    private function calculateNextIndex(Result $result, ResultItem $resultItem): ?int
    {
        $currentIndex = $resultItem->order; 

        $totalItems = $result->items->count();

        for ($i = 0; $i < $totalItems; $i++) {
            $checkIndex = ($currentIndex + $i) % $totalItems;
            $checkIndex++;

            $item = $result->items()->where('order', '=', $checkIndex)->first();

            if ($item && $item->is_correct === null) {
                return $item->id;
            }
        }

        return null;
    }

    public function summary(Result $result){
        if(request()->user()->can('getSummary', $result)){
            $durationString = $result->end_time->diffAsCarbonInterval($result->start_time)->forHumans();
            return view('games.summary', ['score'=>$result->score, 'total'=> $result->items()->count(), 'duration'=> $durationString]);
        }
        else{
            return redirect(route('home'));
        }
        
    }

    public function mapConfig(ResultItem $resultItem, string $mode){
        if($mode == 'question' && $resultItem->question->prompt->component_type == 'App\Models\QuestionMap'){
            $config = [
                'js_path' => $resultItem->question->prompt->component->map->js_path,
                'target' => $resultItem->question->prompt->component->target_region    
            ];
        }
        else if($mode == 'answer' && $resultItem->question->answer->component_type == 'App\Models\QuestionMap'){
            $config = [
                'js_path' => $resultItem->question->answer->component->map->js_path,
            ];
        }
        else if($mode == 'result' && $resultItem->question->answer->component_type == 'App\Models\QuestionMap'){
            $sessionData = session()->get("verified_map_item_{$resultItem->id}");
            $isAnswered = !empty($sessionData) || $resultItem->is_correct !== null;

            if (!$isAnswered) {
                abort(403, __('errors.answerPeeking'));
            }

            $userAnswer   = $sessionData['user_answer'] ?? $resultItem->user_answer_content;

            $config = [
                'js_path' => $resultItem->question->answer->component->map->js_path,
                'target' => $resultItem->question->answer->component->target_region,
                'user_answer' => $userAnswer,
            ];
        }
        else{
           abort(404, __('errors.mapNotFound'));
        }

        $config['mode'] = $mode;
        
        return response()->json($config);
    }
}
