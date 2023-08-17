<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AbsenceResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array
   */
  public function toArray($request)
  {
    $data = [
      "id" => $this->id,
      "date" => $this->date,
      "reason" => $this->reason,
      "created_at" => $this->created_at,
      "updated_at" => $this->updated_at,
      "activity_session_id" => $this->activity_session_id,
      "user_id" => $this->user_id,
      "motif" => $this->motif,
      "activity_session_time_start" => $this->activity_session_time_start,
      "establishment_id" => $this->establishment_id,
      "activity_id" => $this->activity_id,
      "pass_id" => $this->pass_id,
      "season_id" => $this->season_id,
      "prevention_time" => [
        "human" => $this->created_at->diffForHumans($this->activity_session_time_start),
        "notified_n_hours_before" => $this->created_at->diffInHours($this->activity_session_time_start),
      ],
      "user" => $this->user,
      "pass" => $this->pass,
      "can_catch_up_until" => $this->can_catch_up_until,
      "presence_status_txt" => $this->presence_status_txt,
      "session_status_txt" => $this->session_status_txt,
    ];

    if ($this->relationLoaded('activity_session')) {
      $data["activity_session"] = $this->activity_session;
      if (optional($this->activity_session)->relationLoaded('activity')) {
        $data["activity"] = $this->activity_session->activity;
      }
    }

    if ($this->relationLoaded('queue')) {
      $data["queue"] = $this->queue;
      if (optional($this->queue)->relationLoaded('recuperation_request')) {
        $data["recuperation_request"] = $this->queue->recuperation_request;
      }
    }

    return $data;
  }
}
