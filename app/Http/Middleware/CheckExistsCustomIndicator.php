<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Smart2\CommandModel\Eloquent\MemberOriginalDiv;
use Switchm\SmartApi\Queries\Dao\Rdb\DivisionDao;

class CheckExistsCustomIndicator
{
    public function __construct(DivisionDao $divisionDao)
    {
        $this->divisionDao = $divisionDao;
    }

    public function handle($request, Closure $next)
    {
        $memberOriginalDivs = MemberOriginalDiv
            ::where('member_id', Auth::id())->where('division', $request->division)->where('region_id', $request->regionId)->get();

        if ($memberOriginalDivs->count() === 0) {
            return $next($request);
        }

        $originalDivisions = $this->divisionDao->findOriginalDiv([$request->division], Auth::id(), $request->regionId);

        $expecteds = [];

        foreach ($originalDivisions as $division) {
            $expecteds[] = $division->code;
        }

        $codes = $request->codes;

        if ($codes === null) {
            $codes = $request->code;
        }

        if (!is_array($codes)) {
            $codes = [$codes];
        }

        $actuals = array_diff($codes, ['personal', 'household']);

        if (!empty(array_diff($actuals, $expecteds))) {
            abort(412, 'custom_indicator_search_error');
        }

        return $next($request);
    }
}
