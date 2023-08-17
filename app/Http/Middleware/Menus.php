<?php

namespace App\Http\Middleware;

use Closure;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Establishment;
use Illuminate\Support\Facades\Auth;

class Menus
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {

    $guest_mode = $request->cookie('guest_mode') ? true : false;

    if (!Auth::user()) {
      return $next($request);
    }

    /* Prospect menu */

    $menus_prospect = [];

    $menus_prospect[] = [
      "icon" => "columns",
      "title" => "Tableau de bord",
      "link" => "dashboard",
    ];



    // $menus_prospect[] = [
    //     "icon" => "columns",
    //     "title" => "Plannings des centres",
    //     "link" => "dashboard",
    // ];

    $menus_prospect[] = [
      "icon" => "envelope",
      "title" => "Contact",
      "link" => "contact.create",
    ];

    /* Customer menu */

    $menus_customer = [];

    $menus_customer[] = [
      "icon" => "columns",
      "title" => "Tableau de bord",
      "link" => "dashboard",
      "active_routes" => [
        'dashboard'
      ],
    ];

    /*** customer Center planning submenus */
    $planning_submenus = [];
    $establishments = session()->get('establishments_list');

    foreach ($establishments as $key => $establishment) {
      $title = $establishment->name;
      $planning_submenus[] = [
        "icon" => $establishment->relaxation_center ? "hot-tub" : "calendar-day",
        "title" => $title,
        "link" => "customer.plannings.establishment",
        "params" => [
          "establishment_id" => $establishment->id,
        ],
        "active" => [
          ['customer.plannings.establishment', ['establishment_id' => $establishment->id]]
        ],
      ];
    }

    if (!$guest_mode) {
      $menus_customer[] = [
        "icon" => "calendar-alt",
        "title" => "Plannings des centres",
        "submenus" => $planning_submenus,
      ];
    }

    if (!$guest_mode) {
      $menus_customer[] = [
        "icon" => "calendar-day",
        "title" => "Mon planning",
        "link" => "customer.plannings.customer",
      ];
    } else {
      $menus_customer[] = [
        "icon" => "calendar-day",
        "title" => "Ses planning",
        "link" => "customer.plannings.customer",
      ];
    }

    $menus_customer[] = [
      "icon" => "user-alt-slash",
      "title" => "Absences prévenues",
      "link" => "absences.index",
    ];

    $menus_customer[] = [
      "icon" => "user-clock",
      "title" => "Mes séances à récupérer",
      "link" => "queues.index",
    ];

    $menus_customer[] = [
      "icon" => "clipboard-check",
      "title" => "Boutique",
      "link" => "subscription.index",
    ];

    $menus_customer[] = [
      "icon" => "file-invoice",
      "title" => "Mes factures",
      "link" => "subscription.customer.facture",
    ];

    if (!$guest_mode) {
      $menus_customer[] = [
        "icon" => "people-arrows",
        "title" => "Clients suivi",
        "link" => "folowed.user.index",
      ];
    }

    $menus_customer[] = [
      "icon" => "hot-tub",
      "title" => "Soins",
      // "link" => "dashboard",
    ];

    if (!$guest_mode) {
      $menus_customer[] = [
        "icon" => "envelope",
        "title" => "Contact",
        "link" => "contact.create",
      ];
    }

    /* End - Customer menu */

    /* Coach menu */

    $menus_coach = [];

    $menus_coach[] = [
      "icon" => "columns",
      "title" => "Tableau de bord",
      "link" => "dashboard",
    ];

    /*** coach Center planning submenus */
    $planning_submenus = [];
    $establishments = session()->get('establishments_list');

    foreach ($establishments as $key => $establishment) {
      $planning_submenus[] = [
        "icon" => $establishment->relaxation_center ? "hot-tub" : "calendar-day",
        "title" => $establishment->name,
        "link" => "coach.plannings.establishment",
        "params" => [
          "establishment_id" => $establishment->id,
        ],
      ];
    }

    $menus_coach[] = [
      "icon" => "calendar-alt",
      "title" => "Plannings des centres",
      "link" => "dashboard",
      "submenus" => $planning_submenus,
    ];

    $menus_coach[] = [
      "icon" => "calendar-day",
      "title" => "Mon planning",
      "link" => "coach.plannings.coach",
    ];

    $menus_coach[] = [
      "icon" => "user-alt-slash",
      "title" => "Absences prévenues",
      "link" => "absences.index",
    ];


    $menus_coach[] = [
      "icon" => "envelope",
      "title" => "Contact",
      "link" => "contact.create",
    ];
    /*** End - Coach menus */

    /** Intervenant menu */

    $menus_intervenant = [];

    $menus_intervenant[] = [
      "icon" => "columns",
      "title" => "Tableau de bord",
      "link" => "dashboard",
    ];

    /*** coach Center planning submenus */

    $planning_submenus = [];
    $establishments = session()->get('establishments_list');

    foreach ($establishments as $key => $establishment) {
      $planning_submenus[] = [
        "icon" => $establishment->relaxation_center ? "hot-tub" : "calendar-day",
        "title" => $establishment->name,
        "link" => "intervenant.plannings.establishment",
        "params" => [
          "establishment_id" => $establishment->id,
        ],
      ];
    }

    $menus_intervenant[] = [
      "icon" => "calendar-alt",
      "title" => "Plannings des centres",
      "link" => "dashboard",
      "submenus" => $planning_submenus,
    ];

    $menus_intervenant[] = [
      "icon" => "envelop",
      "title" => "Contact",
      "link" => "contact.create",
    ];

    /** End - Intervenant menu */

    /* Admin menus */

    $menus_admin = [];

    $menus_admin[] = [
      "icon" => "columns",
      "title" => "Tableau de bord",
      "link" => "dashboard",
    ];

    $menus_admin[] = [
      "icon" => "chart-area",
      "title" => "Statistique",
      "link" => "statistics.index",
    ];

    $planning_submenus = [];

    $establishments = session()->get('establishments_list');

    foreach ($establishments as $key => $establishment) {
      $planning_submenus[] = [
        "icon" => $establishment->relaxation_center ? "hot-tub" : "calendar-day",
        "title" => $establishment->name,
        "link" => "establishments.plannings.sessions.index",
        "params" => [
          "establishment" => $establishment->id,
        ],
      ];
    }

    $menus_admin[] = [
      "icon" => "calendar-alt",
      "title" => "Planning des centres",
      // "link" => "dashboard",
      "submenus" => $planning_submenus,
    ];

    $menus_admin[] = [
      "icon" => "users",
      "title" => "Clients",
      "link" => "customers",

      "submenus" => [
        [
          "icon" => "user-plus",
          "title" => "Ajouter un client",
          "link" => "customers.create",
        ],
        [
          "icon" => "list-alt",
          "title" => "Liste",
          "link" => "customers.index",
        ],
        [
          "icon" => "list-alt",
          "title" => "Liste d'attente renouvelement",
          "link" => "attente.renouvellement",
        ],
        [
          "icon" => "birthday-cake",
          "title" => "Anniversaires",
          "link" => "customers.index",
          "params" => [
            'birthdate' => \date('Y-m-d'),
          ],
        ],
        [
          "icon" => "users-slash",
          "title" => "Absences",
          "link" => "absences.notified",
        ],
        [
          "icon" => "user-clock",
          "title" => "File d’attente",
          "link" => "queues.index",
        ],
        [
          "icon" => "people-arrows",
          "title" => "Demande de suivi",
          "link" => "followings.requests",
        ],
        [
          "icon" => "rocket",
          "title" => "Relance",
          "link" => "relaunchs.index",
        ],
        [
          "icon" => "upload",
          "title" => "Import",
          "link" => "customer.import",
        ],
        [
          "icon" => "file-export",
          "title" => "Fichier exporté",
          "link" => "export.index",
        ],
      ],
    ];

    $menus_admin[] = [
      "icon" => "user-plus",
      "title" => "Prospects",
      "link" => "customers.index",
      "params" => [
        'account_type' => 'prospect',
      ],
      "badgeSync" => [
        "tooltip" => "Nouveau prospect",
        "id" => "prospect_count",
      ],
    ];

    $menus_admin[] = [
      "icon" => "users-cog",
      "title" => "Utilisateurs",
      "submenus" => [
        [
          "icon" => "chalkboard-teacher",
          "title" => "Coachs",
          "link" => "coach.index",
        ],
        [
          "icon" => "user-cog",
          "title" => "Administrative",
          "link" => "admins.index",
        ],
        [
          "icon" => "user-friends",
          "title" => "Assistantes commerciales",
          "link" => "assistants.index",
        ],
        [
          "icon" => "street-view",
          "title" => "Intervenant exterieurs",
          "link" => "intervenants.index",
        ],
      ],
    ];

    $menus_admin[] = [
      "icon" => "clipboard-check",
      "title" => "Souscription",
      "link" => "dashboard",
      "submenus" => [
        [
          "icon" => "cart-plus",
          "title" => "Nouvelle souscription",
          "link" => "subscriptions.create",
        ],
        [
          "icon" => "list-alt",
          "title" => "Liste des souscriptions",
          "link" => "subscriptions.index",
        ],
        [
          "icon" => "undo-alt",
          "title" => "Renouvelement",
          "link" => "renewals.index",
        ],
        // [
        //     "icon" => "calendar-week",
        //     "title" => "Periode de souscription",
        //     "link" => "subscriptions.periodes.index",
        // ],
      ]
    ];

    $menus_admin[] = [
      "icon" => "euro-sign",
      "title" => "Paiement",
      "submenus" => [
        [
          "icon" => "check",
          "title" => "Facture réglé",
          "link" => "payments.index",
        ],
        [
          "icon" => "file-invoice",
          "title" => "Facture impayée",
          "link" => "invoice.unpaid.index",
        ],
      ],
    ];

    $menus_admin[] = [
      "icon" => "newspaper",
      "title" => "Actualités",
      "link" => "posts.index",
    ];

    $menus_admin[] = [
      "icon" => "envelope",
      "title" => "Contact",
      "link" => "contact.index",
    ];


    /* End - Admin menus */

    Inertia::share('menus_role', function () use ($menus_prospect, $menus_customer, $menus_coach, $menus_intervenant, $menus_admin) {
      if (Auth::user()->isProspect()) {
        return $menus_prospect;
      } elseif (Auth::user()->isCustomer()) {
        return $menus_customer;
      } elseif (Auth::user()->isAdmin() || Auth::user()->isAssistant()) {
        if (Auth::user()->isAdmin()) {
          $menus_admin[] = [
            "icon" => "cogs",
            "title" => "Paramètres",
            // "link" => "dashboard",
            "submenus" => [
              [
                "icon" => "swimming-pool",
                "title" => "Activités",
                "link" => "activities.index",
              ],
              [
                "icon" => "ticket-alt",
                "title" => "Pass",
                "link" => "passes.index",
              ],
              [
                "icon" => "door-open",
                "title" => "Centres",
                "link" => "establishments.index",
              ],
              [
                "icon" => "question",
                "title" => "Questionnaires",
                "link" => "questions.index",
              ],
              // [
              //     "icon" => "columns",
              //     "title" => "Roles utilisateur",
              //     "link" => "roles.index",
              // ],
            ]
          ];

          $menus_admin[] = [
            "icon" => "exclamation-circle",
            "title" => "Bug signalées",
            "link" => "bugs.index",
          ];
        }
        return $menus_admin;
      } elseif (Auth::user()->isCoach()) {
        return $menus_coach;
      } elseif (Auth::user()->isIntervenant()) {
        return $menus_intervenant;
      } else {
        return [];
      }
    });

    return $next($request);
  }
}
