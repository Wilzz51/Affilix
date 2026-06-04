<?php

namespace App\Addons\Affiliation;

use App\Extensions\BaseAddonServiceProvider;
use App\Addons\Affiliation\Http\Controllers\Admin\AdminAffiliateController;
use App\Addons\Affiliation\Console\ProcessAutoPayments;
use App\Addons\Affiliation\Listeners\TrackReferralRegistration;
use App\Addons\Affiliation\Listeners\CreateAffiliateCommission;
use App\Core\Menu\FrontMenuItem;
use Illuminate\Auth\Events\Registered;
use App\Events\Core\Invoice\InvoiceCompleted;
use Illuminate\Console\Scheduling\Schedule;

class AffiliationServiceProvider extends BaseAddonServiceProvider
{
    protected string $uuid = 'Affilix';

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (!is_installed()) {
            return;
        }

        $this->loadViews();
        $this->loadTranslations();
        $this->loadMigrations();
        $this->loadViewsFrom(addon_path($this->uuid, 'views/emails'), 'affilix_mail');

        // Routes admin
        \Route::middleware(['web', 'admin'])
            ->prefix(admin_prefix() . '/affiliation')
            ->name('affiliation.admin.')
            ->group(function () {
                require addon_path($this->uuid, 'routes/admin.php');
            });

        // Routes client
        \Route::middleware(['web'])
            ->name('affiliation.')
            ->group(function () {
                require addon_path($this->uuid, 'routes/web.php');
            });

        // Commande artisan + scheduler
        $this->commands([ProcessAutoPayments::class]);

        $this->app->booted(function () {
            $this->app->make(Schedule::class)
                ->command('affilix:auto-pay')
                ->dailyAt('06:00');
        });

        // Listeners d'événements
        $this->app['events']->listen(Registered::class, TrackReferralRegistration::class);
        $this->app['events']->listen(InvoiceCompleted::class, CreateAffiliateCommission::class);

        // Lien dans la navigation client
        $this->app['extension']->addFrontMenuItem(
            new FrontMenuItem(
                'Affilix',
                'affiliation.dashboard',
                'bi bi-people-fill',
                'Affilix::affiliation.title',
                10
            )
        );

        // Carte dans /admin/settings
        $this->app['settings']->addCard(
            'Affilix',
            'Affilix::affiliation.title',
            'Affilix::affiliation.admin.manage_affiliates',
            5,
            null,
            true
        );

        $this->app['settings']->addCardItem(
            'Affilix',
            'affiliation_settings',
            'Affilix::affiliation.settings',
            'Affilix::affiliation.admin.settings_description',
            'bi bi-sliders',
            url(admin_prefix() . '/affiliation/settings'),
            'admin.manage_settings'
        );

        $this->app['settings']->addCardItem(
            'Affilix',
            'affiliation_affiliates',
            'Affilix::affiliation.admin.affiliates',
            'Affilix::affiliation.admin.affiliates_description',
            'bi bi-people-fill',
            url(admin_prefix() . '/affiliation'),
            'admin.show_customers'
        );

        $this->app['settings']->addCardItem(
            'Affilix',
            'affiliation_commissions',
            'Affilix::affiliation.commissions',
            'Affilix::affiliation.admin.commissions_description',
            'bi bi-cash-stack',
            url(admin_prefix() . '/affiliation/commissions'),
            'admin.show_customers'
        );
    }
}
