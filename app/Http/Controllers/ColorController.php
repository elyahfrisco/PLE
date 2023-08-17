<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ColorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $colors  = Color::all();
        return Inertia::render('Admin/Color/index', compact('colors'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'background' => [
                'required',
                Rule::unique('colors')->where(function ($query) {
                    return $query->where(function ($query) {
                        $query->where('background', strtoupper(request()->background))->where('font', strtoupper(request()->font));
                    })->orWhere(function ($query) {
                        $query->where('background', strtolower(request()->background))->where('font', strtolower(request()->font));
                    });
                })
            ],
            'font' => 'nullable|different:background',
        ]);

        Color::create($request->all());
        return back()->with(
            'success',
            'Couleur ajouté'
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'background' => [
                'required',
                Rule::unique('colors')->where(function ($query) {
                    return $query->where(function ($query) {
                        $query->where('background', strtoupper(request()->background))->where('font', strtoupper(request()->font));
                    })->orWhere(function ($query) {
                        $query->where('background', strtolower(request()->background))->where('font', strtolower(request()->font));
                    })->whereNot('id', request()->id);
                })
            ],
            'font' => 'nullable|different:background',
        ]);

        Color::find($id)->update($request->all());

        return back()->with(
            'success',
            'Couleur modifié'
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
