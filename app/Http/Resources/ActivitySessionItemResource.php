<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivitySessionItemResource extends JsonResource
{

  protected $days = [
    'monday' => 'lundi',
    'tuesday' => 'mardi',
    'wednesday' => 'mercredi',
    'thursday' => 'jeudi',
    'friday' => 'vendredi',
    'saturday' => 'samedi',
    'sunday' => 'dimanche',
  ];

  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array
   */
  public function toArray($request)
  {
    $activity = $this->activity;

    if (false) {
      $participants_pass_trimester_count = $this->max_effective;
      $participants_count = $this->max_effective;
    } else {
      $participants_pass_trimester_count = $this->participants_pass_trimester_count;
      $participants_count = $this->participants_count;
    }

    $participants_count = $participants_count - intval($this->absence_count);

    $coachs =  [];
    if (!in_array(request()->view, ['years', 'year', 'month']) && auth()->user()->role_name != 'customer' && request()->with_coachs) {
      $coachs =  $this->coachs;
    }

    return [
      "id" => $this->id,
      "activity_id" => $this->activity_id,
      "planning_id" => $this->planning_id,
      "day" => strtolower(Carbon::parse($this->date)->format('l')),
      "date" => Carbon::parse($this->date)->format('Y-m-d'),
      "date_timestamp" => strtotime($this->date),
      "dateFr" => Carbon::parse($this->date)->format('d/m/Y'),
      "weekStart" => Carbon::parse($this->time_start)->startOfWeek()->format('Y-m-d'),
      "start" => $this->time_start,
      "end" => $this->time_end,
      "time_start" => Carbon::parse($this->time_start)->format('H:i'),
      "timestart" => Carbon::parse($this->time_start)->format('H:i'),
      "time_end" => Carbon::parse($this->time_end)->format('H:i'),
      "timeend" => Carbon::parse($this->time_end)->format('H:i'),
      "elapseTime" => $this->elapseTime,
      "elapseTimePresence" => $this->elapseTimePresence,
      "TimeSpent" => $this->TimeSpent,
      "presence_checked_at" => $this->presence_checked_at,
      "title" => ($activity->name),
      "activity" => ($activity->name),
      "content" => $activity->description,
      "accomplished" => $this->accomplished,
      "establishment_id" => $this->establishment_id,
      "participants_count" => $participants_count,
      "participants_pass_trimester_count" => $participants_pass_trimester_count,
      "max_effective" => $this->max_effective,
      "super_pass" => $this->super_pass,
      "admin_max_effective" => $this->real_max_effective,
      "current_max_effective" => $this->max_effective - $participants_count,
      "real_max_effective" => $this->real_max_effective - $participants_count,
      "class" => 'd-flex shadow-sm session-activity session-activity-' . clear_accent($activity->name) . ' session-activity-' . $this->activity_id . ($this->hide_to_customer ? ' hide-to-customer' : ''),
      "background_color" => $activity->background_color,
      "bgcolor" => $activity->background_color,
      "font_color" => $activity->font_color,
      "presence" => array_merge((!request()->without_subscription_activity) && $this->relationLoaded('user_subscription_activity') ? $this->user_subscription_activity->presence ?? [] : [], [
        "absence_prevention" => (request()->with_price == 'true'
          || in_array(request()->view, ['years', 'year', 'month'])
          ||  request()->without_subscription_activity
          ? null : $this->absence_prevention),
      ]),
      "deletable" => "false",
      "resizable" => "false",
      "draggable" => "false",
      "form" => [
        "activity_session_id" => $this->id,
        "activity_id" => $this->activity_id,
        "planning_id" => $this->planning_id,
        "date" => Carbon::parse($this->date)->format('Y-m-d'),
      ],
      "display" => [
        "date" => Carbon::parse($this->date)->format('d/m/Y'),
        "day" => $this->days[strtolower(Carbon::parse($this->date)->format('l'))],
        "activity" => $activity->name,
        "start_at" => Carbon::parse($this->date)->format('d/m/Y'),
        "planning" => Carbon::parse($this->time_start)->format('H:i') . " à " . Carbon::parse($this->time_end)->format('H:i'),
      ],
      "price" => $this->price ?? [],
      "coachs" => $coachs,
      "absence_count" => $this->absence_count,
    ];
  }
}
