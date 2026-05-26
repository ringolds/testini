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
        $banks = $user->banks->where('hidden', 0);
    
        return view('banks.index', compact('banks'));
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
                    $query->where('user_id', Auth::id())
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
            'hidden'=> FALSE,
            'default'=> FALSE
        ]); 

        return redirect()->route('bank.index')->with('success', 'Bank created 
        successfully!');     
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $bank = Bank::findOrFail($id);
        $user = request()->user();
        if ($user->cannot('view', $bank)){
            return redirect()->route('home');
        }

        if (request()->ajax()) {
            return view('banks.details', compact('bank'))->render();
        }

        return view('banks.show', compact('bank'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $bank = Bank::findOrFail($id);
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
    public function update(Request $request, string $id)
    {
        $bank = Bank::findOrFail($id);

        if($request->user()->cannot('update', $bank)){
            return redirect()->route('home');
        }

        $rules = array(
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('banks')->where(fn ($query) => 
                    $query->where('user_id', Auth::id())
                )->ignore($id),
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
                'success' => 'Bank updated successfully!'
            ]);
        }

        return redirect()->route('bank.index')->with('success', 'Bank edited 
        successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bank = Bank::findOrFail($id);
        $user = request()->user();
        if ($user->can('delete', $bank)){
            $bank->hidden = TRUE;
            $bank->save();

            if (request()->ajax()) {
                return response()->json(['success' => true, 'id' => $id]);
            }

            return redirect()->route('bank.index');
        }
    }
}
