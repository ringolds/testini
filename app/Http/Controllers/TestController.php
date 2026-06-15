<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Bank;
use App\Models\Test;
use App\Rules\ValidQuestionCount;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $tests = $user->tests;
        $mode = "manage";
        $target_id = null;
        return view('tests.index', compact('tests', 'mode', 'target_id'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tests.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = array(
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tests')->where(fn ($query) => 
                    $query->where('user_id', Auth::id())
                ),
            ],
            'description' => 'required|min:5|max:500'
        );    

        $validated = $request->validate($rules);
    
        Test::create([ 
            'name' => $validated['name'], 
            'description' => $validated['description'], 
            'user_id'=> Auth::id(),
            'public'=> FALSE,
        ]); 

        return redirect()->route('test.index')->with('success', __('tests.createSuccess'));   
    }

    /**
     * Display the specified resource.
     */
    public function show(Test $test)
    {
        $user = request()->user();
        if ($user->cannot('view', $test)){
            return redirect()->route('home');
        }

        $test->load('questions.tests');

        $mode = request()->query('mode', 'manage');
        $target_id = request()->query('target-id', $test->id);
        $type = request()->query('type');

        if (request()->ajax()) {
            return view('tests.details', compact('test', 'mode', 'target_id', 'type'))->render();
        }

        return view('tests.show', compact('test', 'mode', 'target_id', 'type'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Test $test)
    {
        $user = request()->user();
        if ($user->cannot('update', $test)){
            return redirect()->route('home');
        }

        if (request()->ajax()) {
            return view('tests.details_edit', compact('test'))->render();
        }

        return view('tests.edit', compact('test'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Test $test)
    {
        if($request->user()->cannot('update', $test)){
            return redirect()->route('home');
        }

        $rules = array(
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tests')->where(fn ($query) => 
                    $query->where('user_id', Auth::id()))->ignore($test->id),
            ],
            'description' => 'required|min:5|max:500'
        );    

        $validated = $request->validate($rules);

        $request->merge([
            'public' => $request->has('public'),
        ]);

        $test->name = $validated['name'];
        $test->description = $validated['description'];

        $test->save();

        if(request()->ajax()){
                return response()->json([
                'id' => $test->id,
                'name' => $test->name,
                'success' => __('tests.updateSuccess')
            ]);
        }

        return redirect()->route('test.index')->with('success', __('tests.updateSuccess'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Test $test)
    {
        $user = request()->user();
        if ($user->can('delete', $test)){
            $test->delete();

            if (request()->ajax()) {
                return response()->json(['success' => true, 'id' => $test->id]);
            }

            return redirect()->route('test.index');
        }
    }

    public function publish(Test $test){
        $user = request()->user();
        if($user->can('publish', $test)){
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
                if(request()->ajax()){
                    return response()->json(['error' => __('errors.imageTest')], 422);
                }
                else{
                    return back()->withErrors(['error' => __('errors.imageTest')]);
                }
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

            $randomQuestions = array_merge($staticQuestions, $guaranteedImageIds);

            

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
                        ->whereNotIn('id', $randomQuestions)
                        ->inRandomOrder()
                        ->take($count)
                        ->pluck('id')
                        ->toArray();

                    $randomQuestions = array_merge($randomQuestions, $fetchedIds);
                }
            }

            $allQuestions = $randomQuestions;
            
            $shuffledQuestionIds = collect($allQuestions)->shuffle()->all();

            if(count($shuffledQuestionIds)<=0){
                if(request()->ajax()){
                    return response()->json(['error' => __('errors.noQuestions')], 422);
                }
                else{
                    return back()->withErrors(['error' => __('errors.noQuestions')]);
                }
            }

            $test->update([
                'public' => true,
            ]);


            if(request()->ajax()){
                return response()->json([
                    'id' => $test->id,
                    'name' => $test->name,
                    'success' => __('tests.updateSuccess')
                ]);
            }

            return redirect()->route('test.index')->with('success', __('tests.updateSuccess'));
        }

        return redirect()->route('home');
    }

    public function unpublish(Test $test){
        $user = request()->user();
        if($user->can('unpublish', $test)){
            $test->update([
                'public' => false,
            ]);

            if(request()->ajax()){
                return response()->json([
                    'id' => $test->id,
                    'name' => $test->name,
                    'success' => __('tests.updateSuccess')
                ]);
            }

            return redirect()->route('test.index')->with('success', __('tests.updateSuccess'));
        }

        return redirect()->route('home');
    }

    public function addQuestion(Test $test){
        $user = request()->user();
        $banks = $user->banks()->get();
        $mode = "add";
        $target_id = $test->id;
        if (request()->ajax()) {
                return view('banks.index_details', compact('banks', 'mode', 'target_id'))->render();
            }
        return view('banks.index', compact('banks', 'mode', 'target_id'));
    }

    public function addBank(Test $test){
        $user = request()->user();
        $used_banks = $test->banks()->get();
        $allBanks = $user->banks()->get();

        $banks = $allBanks->diff($used_banks);

        $mode="addBank";
        $target_id = $test->id;

        if (request()->ajax()) {
                return view('banks.index_details', compact('banks', 'mode', 'target_id'))->render();
            }
        return view('banks.index', compact('banks', 'mode', 'target_id'));
        
    }

    public function saveBank(Request $request, Test $test, Bank $bank){
        $user = $request->user();
        if ($user->can('addBankToTest', [$test, $bank])){

            $rules = [
                'count' => ['required', 'integer', 'min:1', new ValidQuestionCount($bank)]
            ];

            $request->validate($rules);
            
            $test->banks()->attach($bank, ['random_count'=>$request->input('count')]);
            
            $mode="manage";
            $target_id = $test->id;
            $type = "test";

            if ($request->ajax()) {
                $mode = "manage";
                $html = view('tests.details', compact('test', 'mode', 'target_id', 'type'))->render();

                return response()->json([
                    'success' => true,
                    'html'    => $html
                ]);
            }

            return view('tests.show', compact('test', 'mode', 'target_id', 'type'));
        }
        return redirect()->route('home');
    }

    public function removeBank(Request $request, Test $test, Bank $bank){
        $user = $request->user();
        if ($user->can('removeBankFromTest', [$test, $bank])){         
            $test->banks()->detach($bank);
            
            $mode="manage";
            $target_id = $test->id;
            $type = "test";

            if ($request->ajax()) {
                $mode = "manage";
                $html = view('tests.details', compact('test', 'mode', 'target_id', 'type'))->render();

                return response()->json([
                    'success' => true,
                    'html'    => $html
                ]);
            }

            return view('tests.show', compact('test', 'mode', 'target_id', 'type'));
        }
        return redirect()->route('home');
    }

    public function changeBankCount(Test $test, Bank $bank){
        $user = request()->user();
        if ($user->can('changeBankCount', [$test, $bank])){  
            $count = $test->banks->where('id', $bank->id)->first()->pivot->random_count;       
            if (request()->ajax()) {
                $mode = "change";
                $test = $test->id;
                $html = view('components.bank_row', compact('count', 'bank', 'test', 'mode'))->render();

                return response()->json([
                    'success' => true,
                    'html'    => $html
                ]);
            }
        }
        return redirect()->route('home');
    }

    public function updateBankCount(Request $request, Test $test, Bank $bank){
        $user = $request->user();
        if ($user->can('changeBankCount', [$test, $bank])){

            $rules = [
                'count' => ['required', 'integer', 'min:1', new ValidQuestionCount($bank)]
            ];

            $request->validate($rules);
            if ($request->ajax()) {
                $test->banks()->updateExistingPivot($bank->id, [
                    'random_count' => $request->input('count')
                ]);
                $count = $request->input('count');
                $mode = "view";
                $test = $test->id;
                $html = view('components.bank_row', compact('count', 'bank', 'test', 'mode'))->render();

                return response()->json([
                    'success' => true,
                    'html'    => $html
                ]);
            }
        }
        return redirect()->route('home');
    }

    public function availableTestIndex(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        if (!in_array($perPage, [12, 24])) {
            $perPage = 12; 
        }
        $userId = $request->user()?->id;
        $finalQuery = Test::with('user')->where(function ($query) use ($userId){
            $query->where('tests.public', true);
            if ($userId) {
                $query->orWhere('tests.user_id', $userId);
            }
        })->where(function ($query) {
            $query->whereHas('questions')
                ->orWhereHas('banks', function ($q) {
                    $q->where('random_count', '>', 0)->has('questions');
                });
        });

        $ratingMode = "general";

        if($userId){
            $ratingMode = $request->user()->rating_mode;

            if($request->ratingMode){
                $ratingMode= $request->ratingMode;
            }
        }

        
        if($userId){
            if ($request->rating === 'personalDesc') {
            $finalQuery->leftJoin('ratings as personal_rating', function ($join) use ($userId) {
                $join->on('tests.id', '=', 'personal_rating.test_id')
                    ->where('personal_rating.user_id', '=', $userId);
            })
            ->select('tests.*')
            ->selectRaw('COALESCE(personal_rating.stars, 0) as personal_rating')
            ->orderByDesc('personal_rating');
            $ratingMode = "personal";
            }

            if ($request->rating === 'personalAsc') {
                $finalQuery->leftJoin('ratings as personal_rating', function ($join) use ($userId) {
                    $join->on('tests.id', '=', 'personal_rating.test_id')
                        ->where('personal_rating.user_id', '=', $userId);
                })
                ->select('tests.*')
                ->selectRaw('COALESCE(personal_rating.stars, 0) as personal_rating')
                ->orderBy('personal_rating');
                $ratingMode = "personal";
            }
        }

        if ($request->rating === 'generalDesc') {
            $finalQuery->withAvg('ratings', 'stars')->orderByDesc('ratings_avg_stars');
            $ratingMode = "general";
        }

        if ($request->rating === 'generalAsc') {
            $finalQuery->withAvg('ratings', 'stars')->orderBy('ratings_avg_stars');
            $ratingMode = "general";
        }

        if ($request->date === 'new') {
            $finalQuery->orderBy('created_at', 'desc');
        }

        if ($request->date === 'old') {
            $finalQuery->orderBy('created_at', 'asc');
        }

        if (!$request->date && !$request->rating) {
            $finalQuery->latest();
        }

        $tests = $finalQuery->paginate($perPage)->withQueryString();

        if ($userId) {
            $request->user()->update([
                'rating_mode' => $ratingMode
            ]);
        }
        if($request->ajax()){
            return view('tests.available_index_details', compact('tests', 'ratingMode'));
        }
        return view('tests.available_index', compact('tests', 'ratingMode'));
    }
}
