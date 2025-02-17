<?php

namespace App\Providers;
use Illuminate\Support\Facades\Gate;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('adminsetting', function ($user) {
            return $user->rule === 'superuser';
        });
        Gate::define('superuser', function ($user) {
            return $user->rule === 'superuser';
        });
        Gate::define('superuser_MTPR', function ($user) {
            return $user->rule === 'superuser' || $user->rule === 'MTPR';
        });
        Gate::define('superuser_PE', function ($user) {
            return $user->rule === 'superuser' || $user->rule === 'Product Engineering';
        });
        Gate::define('UploadProgressDocument', function ($user) {
            return $user->rule === 'superuser' || $user->rule === 'Product Engineering' || $user->rule === 'MTPR';
        });
        Gate::define('Hazardlog', function ($user) {
            return $user->rule === 'superuser' || $user->rule === 'MTPR';
        });

        Gate::define('MTPR', function ($user) {
            return $user->rule === 'superuser' || $user->rule === 'MTPR';
        });

        Gate::define('Memo', function ($user) {
            $allowedRoles = [
                'superuser',
                'Logistik',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ]
            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });

        Gate::define('Justimemo', function ($user) {
            $allowedRoles = [
                'superuser',
            ];

            $allowedRolesStaff = [];

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });

        Gate::define('Jobticket', function ($user) {
            $allowedRoles = [
                'superuser',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];
            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ]
            );



            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });

        Gate::define('Ramsdocument', function ($user) {
            $allowedRoles = [
                'superuser',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ]
            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });

        Gate::define('Newbom', function ($user) {
            $allowedRoles = [
                'superuser',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ]
            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });

        Gate::define('Progress', function ($user) {
            $allowedRoles = [
                'superuser',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ],
                [
                    'QC FAB',
                    'QC FIN',
                    'QC FAB',
                    'QC FIN',
                    'QC INC',
                    'Fabrikasi',
                    'PPC',
                    'QC Banyuwangi',
                    'Pabrik Banyuwangi',
                    'Fabrikasi',
                    'PPC',
                    'QC Banyuwangi'
                ]
            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });

        Gate::define('Library', function ($user) {
            $allowedRoles = [
                'superuser',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ],
            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });

        Gate::define('KatalogKomat', function ($user) {
            $allowedRoles = [
                'superuser',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ],
            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });

        Gate::define('Rapat', function ($user) {
            $allowedRoles = [
                'superuser',
                'MTPR',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering'
            ];

            $allowedRolesStaff = array_merge(
                [
                    'Desain Mekanik & Interior',
                    'Desain Bogie & Wagon',
                    'Desain Carbody',
                    'Desain Elektrik'
                ],
                [
                    'Preparation & Support',
                    'Welding Technology',
                    'Shop Drawing',
                    'Teknologi Proses'
                ],
                [
                    'Product Engineering',
                    'Mechanical Engineering System',
                    'Electrical Engineering System',
                    'Quality Engineering',
                    'RAMS'
                ],
                [
                    'Manager Desain Mekanik & Interior',
                    'Manager Desain Bogie & Wagon',
                    'Manager Desain Carbody',
                    'Manager Desain Elektrik'
                ],
                [
                    'Manager Preparation & Support',
                    'Manager Welding Technology',
                    'Manager Shop Drawing',
                    'Manager Teknologi Proses'
                ],
                [
                    'Manager Product Engineering',
                    'Manager Mechanical Engineering System',
                    'Manager Electrical Engineering System',
                    'Manager Quality Engineering',
                    'Manager RAMS'
                ]
            );

            $mergedRoles = array_merge($allowedRoles, $allowedRolesStaff);
            return in_array($user->rule, $mergedRoles);
        });




    }
}
