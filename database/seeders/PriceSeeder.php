<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Pass;
use App\Models\Price;
use App\Models\Season;
use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $prices = [];

    $prices[] = [
      "activity" => 'bébés nageurs',
      "passes" => [
        'ESSAI' => 15,
        'COUP PAR COUP' => 15,
      ],
      "price" => 12,
      "reduced_price" => 0,
    ];

    $prices[] = [
      "activity" => 'jardin aquatique',
      "passes" => [
        'ESSAI' => 15,
        'COUP PAR COUP' => 15,
      ],
      "price" => 12,
      "reduced_price" => 0,
    ];

    $prices[] = [
      "activity" => 'école de natation',
      "passes" => [
        'ESSAI' => 15,
        'COUP PAR COUP' => 15,
      ],
      "price" => 12,
      "reduced_price" => 0,
    ];

    $prices[] = [
      "activity" => 'aquagym',
      "passes" => [
        'ESSAI' => 15,
        'COUP PAR COUP' => 15,
      ],
      "price" => 13,
      "reduced_price" => 10,
    ];

    $prices[] = [
      "activity" => 'aquabike',
      "passes" => [
        'ESSAI' => 18,
        'COUP PAR COUP' => 18,
      ],
      "price" => 15,
      "reduced_price" => 12,
    ];

    $prices[] = [
      "activity" => 'aquajump',
      "passes" => [
        'ESSAI' => 18,
        'COUP PAR COUP' => 18,
      ],
      "price" => 15,
      "reduced_price" => 12,
    ];

    $prices[] = [
      "activity" => 'aqua stand up paddle',
      "passes" => [
        'ESSAI' => 18,
        'COUP PAR COUP' => 18,
      ],
      "price" => 15,
      "reduced_price" => 12,
    ];

    $prices[] = [
      "activity" => 'natation adulte',
      "passes" => [
        'ESSAI' => 15,
        'COUP PAR COUP' => 15,
        // 'STAGE VAINCRE' => 179,
      ],
    ];

    $prices[] = [
      "activity" => 'aquagym prénatale',
      "passes" => [
        'ESSAI' => 15,
        'COUP PAR COUP' => 15,
      ],
      "price" => 13,
      "reduced_price" => 0,
    ];

    $prices[] = [
      "activity" => 'stage aquaphobie',
      "passes" => [
        'ESSAI' => 20,
        'COUP PAR COUP' => 20,
      ],
      "price" => 12,
      "reduced_price" => 0,
    ];

    /* PASS */

    $prices[] = [
      "pass" => 'PASS DECOUVERTE',
      "price" => 169,
      "reduced_price" => 0,
    ];

    $prices[] = [
      "pass" => 'PASS SPORT',
      "price" => 39,
      "reduced_price" => 0,
    ];

    $prices[] = [
      "pass" => 'PASS DETENTE',
      "price" => 39,
      "reduced_price" => 0,
    ];

    $prices[] = [
      "pass" => 'PASS BIEN ETRE',
      "price" => 39,
      "reduced_price" => 0,
    ];

    /* Soins */

    $prices[] = [
      "pass" => '1 MEDYJET',
      "price" => 19,
      "reduced_price" => 0,
    ];

    $prices[] = [
      "pass" => '1 LAGON',
      "price" => 19,
      "reduced_price" => 0,
    ];

    $prices[] = [
      "pass" => '10 SOINS HYDROTHERAPIE',
      "price" => 120,
      "reduced_price" => 0,
    ];

    $prices[] = [
      "pass" => '1 BAIN',
      "price" => 15,
      "reduced_price" => 12,
    ];

    $prices[] = [
      "pass" => '10 BAINS',
      "price" => 120,
      "reduced_price" => 0,
    ];

    $prices[] = [
      "pass" => 'BAINS + 1 SOINS',
      "price" => 29,
      "reduced_price" => 0,
    ];

    $prices[] = [
      "pass" => 'BAINS + 2 SOINS',
      "price" => 45,
      "reduced_price" => 0,
    ];

    $prices[] = [
      "pass" => 'DBE + 1 SOIN',
      "price" => 44,
      "reduced_price" => 0,
    ];

    $seasons = Season::with('trimesters')->with('passes')->get();

    /* get/set activities id */
    $prices = array_map(function ($price) {
      if (isset($price['activity'])) {
        $price['activity_id'] = Activity::where('name', $price['activity'])->first()->id;
      } elseif (isset($price['pass'])) {
        $price['pass_id'] = PASS::where('name', $price['pass'])->first()->id;
      }

      if (isset($price['passes'])) {
        foreach ($price['passes'] as $ip => $pass) {
          $price['passes'][$ip] = [
            'pass_id' => Pass::where('name', $ip)->first()->id,
            'price' => $price['passes'][$ip]
          ];
        }
      }

      return $price;
    }, $prices);

    foreach ($prices as $i => $price) {
      foreach ($seasons as $j => $season) {

        if (isset($price['activity_id'])) {
          foreach ($season->trimesters as $j => $trimester) {
            $priceData = [];

            if (!isset($price['price'])) {
              continue;
            }
            $priceData['price'] = $price['price'];
            $priceData['reduced_price'] = $price['reduced_price'];
            $priceData['trimester_id'] = $trimester->id;
            $priceData['establishment_id'] = $season->establishment_id;

            $priceData['activity_id'] = $price['activity_id'];
          }

          Price::create($priceData);
        } else {
          $priceData = [];
          $priceData['price'] = $price['price'];
          $priceData['reduced_price'] = $price['reduced_price'];
          $priceData['pass_id'] = $price['pass_id'];
          $priceData['establishment_id'] = $season->establishment_id;

          Price::create($priceData);
        }

        if (isset($price['passes'])) {
          foreach ($price['passes'] as $j => $pass) {
            $priceData = [];

            $priceData['price'] = $pass['price'];
            $priceData['reduced_price'] = null;
            $priceData['pass_id'] = $pass['pass_id'];
            $priceData['activity_id'] = $price['activity_id'];
            $priceData['establishment_id'] = $season->establishment_id;

            Price::create($priceData);
          }
        }
      }
    }
  }
}
