<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Question; 
use Illuminate\Support\Facades\DB;
use App\Models\QuestionImage;
use App\Models\QuestionText;
use App\Models\Bank;
use App\Models\Test;
use App\Models\Map;
use App\Models\QuestionMap;
use App\Rules\ValidMapTarget;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {   
        if(request()->user()->cannot('create', Question::class)){
            return redirect()->route('home');
        }
        $user = Auth::user();
        $banks = $user->banks;
        $tests = $user->tests;
        $maps = Map::all();

        if(request()->ajax()){
            $type = request()->query('type');
            $target_id = request()->query('id');

            return view('questions.create_details', compact('banks', 'tests', 'maps', 'type', 'target_id'));
        }
        return view('questions.create', compact('banks', 'tests', 'maps'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(request()->user()->cannot('create', Question::class)){
            return redirect()->route('home');
        }

        $rules = [
            'type' => 'required|in:separate,bank,test',
            'question_type' => 'required|in:text,image,map',
            'answer_type'   => 'required|in:text,image,map',
            'bank_id' => [
                'required',
                Rule::exists('banks', 'id')->where(fn ($query)=>
                    $query->where('user_id', Auth::id())
                ),
            ],
            'test_id' => [
                'nullable',
                Rule::exists('tests', 'id')->where(fn ($query)=>
                    $query->where('user_id', Auth::id())
                ),
            ],
            'question_text'       => 'required_if:question_type,text|nullable|string|min:1|max:250',
            'question_image'      => 'required_if:question_type,image|nullable|image|max:2048',
            'question_image_text' => 'nullable|string|max:250',
            'question_image_alt'  => 'nullable|string|max:250',
            'answer_text'         => 'required_if:answer_type,text|nullable|string|min:1|max:250',
            'answer_image'        => 'required_if:answer_type,image|nullable|image|max:2048',
            'answer_image_alt'  => 'nullable|string|max:250',
        ];

        $defaultBank = Bank::where('user_id', Auth::id())->where('default', 1)->first();
        $validated = $request->validate($rules);

        if($request->input('question_type')==='map'){
            $rules = [
                'question_map_id' => 'required_if:question_type,map|nullable|exists:maps,id|',
                'question_map_text' => 'nullable|string|max:250',
                'question_map_target' => ['required_if:question_type,map|nullable', 
                    new ValidMapTarget($request->input('question_map_id'))],
            ];
            $request->validate($rules);
        }

        if($request->input('answer_type')==='map'){
            $rules = [
                'answer_map_id' => 'required_if:answer_type,map|nullable|exists:maps,id',
                'answer_map_target' => ['required_if:answer_type,map|nullable', 
                    new ValidMapTarget($request->input('answer_map_id'))],
            ];
            $request->validate($rules);
        }

        DB::transaction(function () use ($request, $validated, $defaultBank) {
            
            $question = Question::create(['user_id' => Auth::id()]);

            $this->createQuestionComponents($question, $request, "question");
            $this->createQuestionComponents($question, $request, "answer");


            $question->banks()->syncWithoutDetaching([$validated['bank_id']]);
            if($validated['bank_id']!=$defaultBank){
                $question->banks()->syncWithoutDetaching($defaultBank);
            }
            if (!is_null($validated['test_id'])) {
                $question->tests()->syncWithoutDetaching([$validated['test_id']]);
            }
        });

        if ($request->ajax()) {
            if ($validated['type'] == 'test') {
                $item = Test::findOrFail($validated['test_id']);
                $currentItemId = $validated['test_id'];
            } 
            else if($validated['type'] == 'bank'){
                $item = Bank::findOrFail($validated['bank_id']);
                $currentItemId = $validated['bank_id'];
            }
            else{
                return redirect()->back()->with('success', __('questions.createSuccess'));
            }

            $mode = "manage";
            $collection_type = $request->input('type');

            $html = view('components.question_block', compact('item', 'mode', 'collection_type', 'currentItemId'))->render();

            return response()->json([
                'success' => true,
                'id'    => $currentItemId
            ]);
        }
        return redirect()->back()->with('success', __('questions.createSuccess'));
    }

    private function createQuestionComponents(Question $question, Request $request, string $role){
        if($role === "question"){
            $type = $request->input('question_type');
        }
        else{
            $type = $request->input('answer_type');
        }

        if ($type === 'text') {
            if($role === "question"){
                $text = QuestionText::create(['text' => $request->input('question_text')]);
            }
            else{
                $text = QuestionText::create(['text' => $request->input('answer_text')]);
            }
           
            $question->components()->create([
                'role' => $role,
                'component_type' => QuestionText::class,
                'component_id' => $text->id,
                'order' => 1,
            ]);
        } 
        
        elseif ($type === 'image') {
            $order = 1;
            if($role === "question"){
                if ($request->filled('question_image_text')) {
                    $descriptionText = QuestionText::create(['text' => $request->input('question_image_text')]);
                    $question->components()->create([
                        'role' => 'description',
                        'component_type' => QuestionText::class,
                        'component_id' => $descriptionText->id,
                        'order' => $order,
                    ]);
                    $order++;
                }

                $file = $request->file('question_image');
                $alt_text = $request->input('question_image_alt', null);
            }
            else{
                $file = $request->file('answer_image');
                $alt_text = $request->input('answer_image_alt', null);
            }
            $path = $file->store('questions/images', 'public');
            $imageSizes = getimagesize($file->getRealPath());
            $width = $imageSizes[0] ?? null;
            $height = $imageSizes[1] ?? null;

            $image = QuestionImage::create([
                'path'          => $path,
                'alt_text'      => $alt_text,
                'mime_type'     => $file->getClientMimeType(),                  
                'size'          => $file->getSize(),                            
                'width'         => $width,                                      
                'height'        => $height,                                    
            ]);
                
            $question->components()->create([
                'role' => $role,
                'component_type' => QuestionImage::class,
                'component_id' => $image->id,
                'order' => $order,
            ]);
        }

        elseif($type === 'map'){
            $order = 1;
            if($role === "question"){
                if ($request->filled('question_map_text')) {
                    $descriptionText = QuestionText::create(['text' => $request->input('question_map_text')]);
                    $question->components()->create([
                        'role' => 'description',
                        'component_type' => QuestionText::class,
                        'component_id' => $descriptionText->id,
                        'order' => $order,
                    ]);
                    $order++;
                }
            }

            $map = QuestionMap::create([
                'map_id' => $request->input($role.'_map_id'),
                'target_region' => $request->input($role.'_map_target')
            ]);
                
            $question->components()->create([
                'role' => $role,
                'component_type' => QuestionMap::class,
                'component_id' => $map->id,
                'order' => $order,
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Question $question)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {

        if(request()->user()->cannot('update', $question)){
            return redirect()->route('home');
        }
        
        if (request()->ajax()) {
            return view('questions.details_edit', compact('question'))->render();
        }

        return view('questions.edit', compact('question'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        if($request->user()->cannot('update', $question)){
            return redirect()->route('home');
        }

        $rules = [
            'question_text'       => 'nullable|string|min:1|max:250',
            'question_image'      => 'nullable|image|max:2048',
            'question_image_text' => 'nullable|string|max:250',
            'question_image_alt'  => 'nullable|string|max:250',
            'answer_text'         => 'nullable|string|min:1|max:250',
            'answer_image'        => 'nullable|image|max:2048',
            'answer_image_alt'  => 'nullable|string|max:250',
            'question_map_text' => 'nullable|string|max:250',
            'question_map_target' => ['nullable', 
                new ValidMapTarget($request->input('question_map_id'))],
            'answer_map_target' => ['nullable', 
                new ValidMapTarget($request->input('answer_map_id'))],
        ];

        $validated = $request->validate($rules);

        DB::transaction(function () use ($validated, $question) {
            $this->editQuestionComponents($question, $validated, "question");
            $this->editQuestionComponents($question, $validated, "answer");
        });

        if ($request->ajax()) {
            if ($request->input('content_type') === 'test') {
                $item = Test::findOrFail($request->input('content_id'));
            } 
            else {
                $item = Bank::findOrFail($request->input('content_id'));
            }

            $mode = "manage";
            $collection_type = $request->input('content_type');

            $html = view('components.question_block', compact('item', 'mode', 'collection_type'))->render();

            return response()->json([
                'success' => true,
                'html'    => $html
            ]);
        }
        
        return redirect()->route('home');
    }

    private function editQuestionComponents(Question $question, array $validated, string $role){
        if($role === "answer"){
            $item = $question->answer->component;
            $type = $question->answer->component_type;
            $description = NULL;
        }
        else{
            $item = $question->prompt->component;
            $type = $question->prompt->component_type;
            $description = $question->description;
        }


        if($type === 'App\Models\QuestionText' && isset($validated[$role.'_text'])){
            $item->text = $validated[$role.'_text'];
        }
        else if($type === 'App\Models\QuestionImage'){
            $order = 1;
            //Part about description, if exists, update, else create
            if($role === 'question'){
                if(isset($validated['question_image_text'])){
                    if($description!=NULL){
                        $descriptionText = $description->component;
                        $descriptionText->text = $validated['question_image_text'];
                        $descriptionText->save();
                    }
                    else{
                        $descriptionText = QuestionText::create(['text' => $validated['question_image_text']]);
                        $question->components()->create([
                            'role' => 'description',
                            'component_type' => QuestionText::class,
                            'component_id' => $descriptionText->id,
                            'order' => $order,
                        ]);
                        $order++;
                    }
                    
                }
            }
            
            if(isset($validated[$role.'_image'])){
                $file = $validated[$role.'_image'];
                $path = $file->store('questions/images', 'public');
                $imageSizes = getimagesize($file->getRealPath());
                $width = $imageSizes[0] ?? null;
                $height = $imageSizes[1] ?? null;

                $item->path = $path;
                $item->mime_type = $file->getClientMimeType();
                $item->size=$file->getSize();
                $item->width=$width;
                $item->height=$height;
            }

            if(isset($validated[$role.'_image_alt'])){
                $item->alt_text = $validated[$role.'_image_alt'];
            }
            
            if($order!=1){
                $question->answer->order = $order;
                $question->answer->update();
            }

        }
        else if($type === 'App\Models\QuestionMap'){
            $order = 1;
            //Part about description, if exists, update, else create
            if($role === 'question'){
                if(isset($validated['question_map_text'])){
                    if($description!=NULL){
                        $descriptionText = $description->component;
                        $descriptionText->text = $validated['question_map_text'];
                        $descriptionText->save();
                    }
                    else{
                        $descriptionText = QuestionText::create(['text' => $validated['question_map_text']]);
                        $question->components()->create([
                            'role' => 'description',
                            'component_type' => QuestionText::class,
                            'component_id' => $descriptionText->id,
                            'order' => $order,
                        ]);
                        $order++;
                    }
                    
                }
            }
            if(isset($validated[$role.'_map_target'])){
                $item->target_region = $validated[$role.'_map_target'];
            }
            
            if($order!=1){
                $question->answer->order = $order;
                $question->answer->update();
            }

        }
        
        $item->update();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Question $question)
    {
        $user = $request->user();
        if ($user->can('delete', $question)){
            $question->delete();

            if (request()->ajax()) {
                return response()->json(['success' => true, 'id' => $question->id]);
            }
        
            return redirect()->route('home');
        }

        return redirect()->route('home');
    }

    public function addToBank(Request $request, Question $question, Bank $bank){
        $user = $request->user();
        if ($user->can('addQuestionToBank', [$question, $bank])){
            
            $question->banks()->syncWithoutDetaching([$bank->id]);
            
            $mode="manage";
            $target_id = $bank->id;
            $type = "bank";

            if ($request->ajax()) {
                $mode = "manage";
                $html = view('banks.details', compact('bank', 'mode', 'target_id', 'type'))->render();

                return response()->json([
                    'success' => true,
                    'html'    => $html
                ]);
            }

            return view('banks.show', compact('bank', 'mode', 'target_id', 'type'));
        }
        return redirect()->route('home');
    }

    public function removeFromBank(Request $request, Question $question, Bank $bank){
        $user = $request->user();
        if ($user->can('removeQuestionFromBank', [$question, $bank])){
            
            $question->banks()->detach([$bank->id]);
            
            $mode="manage";
            $target_id = $bank->id;
            $type = "bank";

            if ($request->ajax()) {
                $mode = "manage";
                $html = view('banks.details', compact('bank', 'mode', 'target_id', 'type'))->render();

                return response()->json([
                    'success' => true,
                    'html'    => $html
                ]);
            }

            return view('banks.show', compact('bank', 'mode', 'target_id', 'type'));
        }
        return redirect()->route('home');
    }

    public function addToTest(Request $request, Question $question, Test $test){
        $user = $request->user();
        if ($user->can('addQuestionToTest', [$question, $test])){
            
            $question->tests()->syncWithoutDetaching([$test->id]);
            
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

    public function removeFromTest(Request $request, Question $question, Test $test){
        $user = $request->user();
        if ($user->can('removeQuestionFromTest', [$question, $test])){
            
            $question->tests()->detach([$test->id]);
            
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
}
