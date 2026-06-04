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
}
