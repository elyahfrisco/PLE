<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RenewalWaitingExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
  use Exportable;

  public function __construct($waiting_list)
  {
    $this->waiting_list = collect($waiting_list);
  }

  /**
   * @return \Illuminate\Support\Collection
   */
  public function collection()
  {
    return $this->waiting_list;
  }

  public function headings(): array
  {
    return [
      'Code',
      'Mise à jour',
      'Prènoms et Nom',
      'Email',
      'Centre',
      'Activités',
      'Status',
    ];
  }

  public function map($user): array
  {
    return [
      $user['customer_id'],
      Carbon::parse($user['updated_at'])->format('d/m/Y H:i'),
      $user['name'],
      return_real_mail($user['email']),
      $user['establishment'],
      collect($user['activities'])->map(function ($item) {
        return ucfirst(join(' ', $item));
      })->join(PHP_EOL),
      [
        'continue_change_time' => 'Continuer et changer horaire',
        'continue_change_time_else_stop' => 'Continuer et changer horaire sinon STOP',
      ][$user['renewal_status']] ?? $user['renewal_status'],
    ];
  }

  public function styles(Worksheet $sheet)
  {
    $sheet->getStyle('F')->getAlignment()->setWrapText(true);
    $sheet->getStyle('G')->getAlignment()->setWrapText(true);
    $sheet->getStyle('A:G')->getAlignment()->setVertical('top');
  }
}
