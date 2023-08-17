<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
  use Exportable;
  public $data = [];
  public $account_type = "customer";

  /**
   * @return \Illuminate\Support\Collection
   */
  public function collection()
  {
    if (in_array($this->account_type, ['attente', 'prospect'])) {
      $this->data->load([
        'wishes' => function ($query_activity_sessions) {
          $query_activity_sessions->queryExport();
        },
      ]);
    } else {
      $this->data->load([
        'subscription_activities' => function ($query_activity_sessions) {
          $query_activity_sessions->whereDate('subscription_activities.time_end', '>=', now()->subMonths(1))
            ->JoinEstablishment()
            ->JoinActivity()
            ->groupByRaw('establishment_id, planning_id, activity_id');
        },
      ]);
    }

    return $this->data;
  }

  public function setAccountType($account_type)
  {
    $this->account_type = $account_type;
    return $this;
  }

  public function setData($data)
  {
    $this->data = $data;
    return $this;
  }

  public function headings(): array
  {
    return [
      'Code',
      'Date de création',
      'Prènoms et Nom',
      'Enfant',
      'Email',
      'Email secondaire',
      'Centre',
      'Activités',
    ];
  }

  public function map($user): array
  {
    $email = $user['is_child'] ? $user['mail1'] : $user['email'];
    $email2 = $user['is_child'] ? $user['mail2'] : '';
    $child = $user['is_child'] ? 'OUI' : 'NON';

    $data = [
      $user['id'],
      date_format(date_create($user['created_at']), 'd/m/Y'),
      trim($user['full_name']),
      $child,
      return_real_mail($email),
      return_real_mail($email2),
    ];

    if (in_array($this->account_type, ['attente', 'prospect'])) {
      return array_merge($data, $this->mapProspect($user));
    } else {
      return array_merge($data, $this->mapCustomer($user));
    }
  }

  public function mapCustomer($user)
  {
    return [
      $user->subscription_activities
        ->map(function ($item) {
          return $item->establishment_name;
        })
        ->unique()
        ->implode(PHP_EOL),
      $user->subscription_activities
        ->map(function ($item) {
          return ucfirst("{$item->activity_name} {$item->group_name}");
        })
        ->implode(PHP_EOL),
    ];
  }

  public function mapProspect($user)
  {
    return [
      $user->wishes
        ->map(function ($item) {
          return $item->establishment_name;
        })
        ->unique()
        ->implode(PHP_EOL),
      $user->wishes
        ->sortBy('day_of_week')
        ->map(function ($item) {
          return ucfirst($item->activity_name . " " . (explode(' ', trim($item->search_key))[0] ?? ''));
        })
        ->implode(PHP_EOL),
    ];
  }

  public function styles(Worksheet $sheet)
  {
    $sheet->getStyle('F')->getAlignment()->setWrapText(true);
    $sheet->getStyle('A:F')->getAlignment()->setVertical('top');
  }
}
