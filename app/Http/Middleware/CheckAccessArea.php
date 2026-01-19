<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckAccessArea
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $areaCode   <-- the code we will pass in routes ("2", "5", etc.)
     */
    public function handle(Request $request, Closure $next, $areaCode)
    {
        $return_value = false;
        $accessArea = Session::get('USER_ACCESS_AREA', []);


        $modules = explode("||", $areaCode);

        foreach ($modules as $mod_id) {
            if (in_array($mod_id, $accessArea)) {
                $return_value = true;
            }
        }

        if ($return_value == false) {
            return redirect('/Dashboard')->with('error', 'You do not have access to this area.');
        }

        return $next($request);
    }
}
