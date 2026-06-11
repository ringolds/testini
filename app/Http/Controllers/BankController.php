<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Bank;


class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $banks = $user->banks;
        $mode = "manage";
        $target_id = null;
        return view('banks.index', compact('banks', 'mode', 'target_id'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('banks.create');
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
                Rule::unique('banks')->where(fn ($query) => 
                    $query->where('user_id', Auth::id())->whereNull('deleted_at')
                ),
            ],
            'description' => 'required|min:5|max:500'
        );    

        $validated = $request->validate($rules);
    
        Bank::create([ 
            'name' => $validated['name'], 
            'description' => $validated['description'], 
            'user_id'=> Auth::id(),
            'public'=> FALSE,
            'collaborative'=> FALSE,
            'default'=> FALSE
        ]); 

        return redirect()->route('bank.index')->with('success', __('banks.succesfulCreation'));     
    }

    /**
     * Display the specified resource.
     */
    public function show(Bank $bank)
    {
        $user = request()->user();
        if ($user->cannot('view', $bank)){
            return redirect()->route('home');
        }

        $bank->load('questions.banks');

        $mode = request()->query('mode', 'manage');
        $target_id = request()->query('target-id', $bank->id);
        $type = request()->query('type', "bank");

        if (request()->ajax()) {
            return view('banks.details', compact('bank', 'mode', 'target_id', 'type'))->render();
        }

        return view('banks.show', compact('bank', 'mode', 'target_id', 'type'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bank $bank)
    {
        $user = request()->user();
        if ($user->cannot('update', $bank)){
            return redirect()->route('home');
        }

        if (request()->ajax()) {
            return view('banks.details_edit', compact('bank'))->render();
        }

        return view('banks.edit', compact('bank'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bank $bank)
    {

        if($request->user()->cannot('update', $bank)){
            return redirect()->route('home');
        }

        $rules = array(
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('banks')->where(fn ($query) => 
                    $query->where('user_id', Auth::id())->whereNull('deleted_at')
                )->ignore($bank->id),
            ],
            'description' => 'required|min:5|max:500'
        );    

        $validated = $request->validate($rules);

        $request->merge([
            'public' => $request->has('public'),
            'collaborative' => $request->has('collaborative'),
        ]);

        $bank->name = $validated['name'];
        $bank->description = $validated['description'];
        $bank->public = $request->public;
        $bank->collaborative = $request->collaborative;

        $bank->save();

        if(request()->ajax()){
                return response()->json([
                'id' => $bank->id,
                'name' => $bank->name,
                'success' => __('banks.successfulUpdate')
            ]);
        }

        return redirect()->route('bank.index')->with('success', __('banks.successfulUpdate'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bank $bank)
    {
        $user = request()->user();
        if ($user->can('delete', $bank)){
            $bank->delete();

            if (request()->ajax()) {
                return response()->json(['success' => true, 'id' => $bank->id]);
            }

            return redirect()->route('bank.index');
        }
    }

    public function addQuestion(Bank $bank){
        $user = request()->user();
        $banks = $user->banks()->where('id', '!=', $bank->id)->get();
        $mode = "add";
        $target_id = $bank->id;
        if (request()->ajax()) {
                return view('banks.index_details', compact('banks', 'mode', 'target_id'))->render();
            }
        return view('banks.index', compact('banks', 'mode', 'target_id'));
    }
}
