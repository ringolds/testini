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
use APP\Models\Test;

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
        $banks = $user->banks->where('hidden', 0);
        $tests = $user->tests->where('hidden', 0);
        return view('questions.create', compact('banks', 'tests'));
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
            return response()->json(['success' => true, 'message' => 'Question created!']);
        }
        return redirect()->back()->with('success', 'Question created successfully!');


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

            $html = view('components.question_block', compact('item', 'mode'))->render();

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
}
