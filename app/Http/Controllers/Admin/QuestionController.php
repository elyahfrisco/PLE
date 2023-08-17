<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveActivitySubscriptionQuestionnaireRequest;
use App\Models\ActivitySubscriptionQuestionAnswer;
use App\Models\ActivitySubscriptionQuestionnaire;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuestionController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $questions =  ActivitySubscriptionQuestionnaire::with('answers')->orderBy('content')->get();
    return Inertia::render('Admin/Question/index', compact('questions'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return Inertia::render('Admin/Question/create');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(SaveActivitySubscriptionQuestionnaireRequest $request)
  {
    $question = ActivitySubscriptionQuestionnaire::create($request->validated());
    $question->answers()->createMany($request->answers);
    return back()->with('success', "La question a été créée");
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit(ActivitySubscriptionQuestionnaire $question)
  {
    $question->load('answers');
    return Inertia::render('Admin/Question/edit', compact('question'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(SaveActivitySubscriptionQuestionnaireRequest $request, ActivitySubscriptionQuestionnaire $question)
  {
    $question->update($request->validated());

    foreach ($request->answers as $answer) {
      if (isset($answer['id'])) {
        if (isset($answer['deleted']))
          ActivitySubscriptionQuestionAnswer::whereId($answer['id'])->delete();
        else
          ActivitySubscriptionQuestionAnswer::whereId($answer['id'])->update([
            'content' => $answer['content']
          ]);
      } else
        $question->answers()->create($answer);
    }

    return back()->with('success', "La question a été mise à jour");
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(ActivitySubscriptionQuestionnaire $question)
  {
    $question->answers()->delete();
    $question->delete();
    return back()->with('success', "La question a été supprimée");
  }
}
