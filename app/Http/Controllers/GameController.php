<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Result;
use App\Models\ResultItem;
use App\Models\Test;

class GameController extends Controller
{
    public function start(Test $test){
        if(request()->user()->can('start', $test)){
            $result = Result::create([
                'user_id' => request()->user()->id,
                'test_id' => $test->id,
                'start_time'=> now()
            ]);

            $staticQuestions = $test->questions()->pluck('id')->toArray();
            $randomQuestions = [];

            foreach($test->banks as $bank){
                $count = $bank->pivot->random_count;

                $available = $bank->questions()->count();

                if($available<$count){
                    $count = $available;
                    $test->banks()->updateExistingPivot($bank->id, [
                        'random_count' => $count
                    ]);
                }

                $bankIds = $bank->questions()->inRandomOrder()->take($count)->pluck('id')->toArray();
                $randomQuestions = array_merge($randomQuestions, $bankIds);
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
                    ->inRandomOrder()
                    ->limit(3)
                    ->get();

                if($randomItems->count()<1){
                    abort(404, 'No matching question options found.');
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
            
            return view('games.game_entry', [
                'question' => $question,
                'resultItem' => $resultItem,
                'answerMode' => $answerMode,
                'description' => $description,
                'choices' => $choices,
                'answerType' => $componentType
            ]);
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
                    $correct = !strcasecmp($answer, $userAnswer);
                    break;
                case 'App\Models\QuestionImage':
                    $userAnswer = $request->input('multiple_choice');
                    $answer = $answer->component->id;
                    $correct = $answer == $userAnswer;
                    break;
                case 'App\Models\QuestionMap':
                    $userAnswer = $request->input('answer_map_target');
                    $answer = $answer->component->target_region;
                    $correct = !strcasecmp($answer, $userAnswer);
                    break;
                default:
                    abort(422, 'Invalid question component type detected.');
            }

            $resultItem->is_correct = $correct;
            $resultItem->user_answer_content = $userAnswer;
            $resultItem->update();

            session()->put("verified_map_item_{$resultItem->id}", [
                'user_answer' => $userAnswer
            ]);

            $currentIndex = $resultItem->order; 

            $nextIndex = null;
            $totalItems = $result->items->count();

            for ($i = 0; $i < $totalItems; $i++) {
                $checkIndex = ($currentIndex + $i) % $totalItems;
                $checkIndex++;

                $item = $result->items()->where('order', '=', $checkIndex)->first();

                if ($item && $item->is_correct === null) {
                    $nextIndex = $item->id;
                    break;
                }
            }

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
                abort(403, "Access denied. You must submit an answer first.");
            }

            $userAnswer   = $sessionData['user_answer'] ?? $resultItem->user_answer_content;

            $config = [
                'js_path' => $resultItem->question->answer->component->map->js_path,
                'target' => $resultItem->question->answer->component->target_region,
                'user_answer' => $userAnswer,
            ];
        }
        else{
           abort(404, "Map not found for this question");
        }

        $config['mode'] = $mode;
        
        return response()->json($config);
    }
}
