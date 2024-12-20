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

namespace App\Traits;

use App\Models\Torrent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

trait GroupedLastScope
{
    /**
     * Get the latest entry for each group.
     *
     * Each group is composed of one or more columns that make a unique combination to return the
     * last entry for.
     *
     * @param Builder<Torrent>   $query
     * @param array<string>|null $fields A list of fields that's considered as a unique entry by the query.
     */
    public function scopeLastPerGroup(Builder $query, ?array $fields = null): void
    {
        $query->whereIn('id', fn (QueryBuilder $query) => $query->from('torrents')
            ->selectRaw('max("id")')
            ->groupBy($fields));
    }
}
