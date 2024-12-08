<?php

declare(strict_types=1);

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Controllers\Staff;

use App\Helpers\SystemInformation;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Services\Unit3dAnnounce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\SslCertificate\SslCertificate;
use Exception;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\Staff\HomeControllerTest
 */
class HomeController extends Controller
{
    /**
     * Display Staff Dashboard.
     *
     * @throws Exception
     */
    public function index(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        // User Info
        $bannedGroup = cache()->rememberForever('banned_group', fn () => Group::where('slug', '=', 'banned')->pluck('id'));
        $validatingGroup = cache()->rememberForever('validating_group', fn () => Group::where('slug', '=', 'validating')->pluck('id'));

        // SSL Info
        //try {
        //    $certificate = $request->secure() ? SslCertificate::createForHostName(config('app.url')) : '';
        //} catch (Exception) {
            $certificate = '';
        //}

        // System Information
        $systemInformation = new SystemInformation();

        return view('Staff.dashboard.index', [
            'users' => cache()->remember('dashboard_users', 300, fn () => DB::table('users')
                ->selectRaw('COUNT(*) AS total')
                ->selectRaw('SUM(group_id = ?) AS banned', [$bannedGroup[0]])
                ->selectRaw('SUM(group_id = ?) AS validating', [$validatingGroup[0]])
                ->first()),
            'torrents' => cache()->remember('dashboard_torrents', 300, fn () => DB::table('torrents')
                ->selectRaw('COUNT(*) AS total')
                ->selectRaw('SUM(status = 0) AS pending')
                ->selectRaw('SUM(status = 1) AS approved')
                ->selectRaw('SUM(status = 2) AS rejected')
                ->selectRaw('SUM(status = 3) AS postponed')
                ->first()),
            'peers' => cache()->remember('dashboard_peers', 300, fn () => DB::table('peers')
                ->selectRaw('COUNT(*) AS total')
                ->selectRaw('SUM(active = TRUE) AS active')
                ->selectRaw('SUM(active = FALSE) AS inactive')
                ->selectRaw('SUM(seeder = FALSE AND active = TRUE) AS leechers')
                ->selectRaw('SUM(seeder = TRUE AND active = TRUE) AS seeders')
                ->first()),
            'unsolvedReportsCount'     => DB::table('reports')->whereNull('snoozed_until')->where('solved', '=', false)->count(),
            'pendingApplicationsCount' => DB::table('applications')->where('status', '=', 0)->count(),
            'certificate'              => $certificate,
            'uptime'                   => $systemInformation->uptime(),
            'ram'                      => $systemInformation->memory(),
            'disk'                     => $systemInformation->disk(),
            'avg'                      => $systemInformation->avg(),
            'basic'                    => $systemInformation->basic(),
            'file_permissions'         => $systemInformation->directoryPermissions(),
            'externalTrackerStats'     => Unit3dAnnounce::getStats(),
        ]);
    }
}
