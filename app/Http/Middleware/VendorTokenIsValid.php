<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\CentralLogics\Helpers;
use App\Models\Vendor;
use App\Models\VendorDevice;

class VendorTokenIsValid
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
        Helpers::check_subscription_validity();

        $token=$request->bearerToken();
        if(strlen($token)<1)
        {
            return response()->json([
                'errors' => [
                    ['code' => 'auth-001', 'message' => 'Unauthorized.']
                ]
            ], 401);
        }
        // Each signed-in phone has its own token in vendor_devices. The single
        // vendors.auth_token column is still accepted so sessions issued before
        // vendor_devices existed keep working.
        $device = VendorDevice::with('vendor')->where('auth_token', $token)->first();
        $device?->markUsed();
        $vendor = $device?->vendor ?? Vendor::where('auth_token', $token)->first();
        if($vendor)
        {
            $request['vendor']=$vendor;
            return $next($request);
        }
        return response()->json([
            'errors' => [
                ['code' => 'auth-001', 'message' => 'Unauthorized.']
            ]
        ], 401);
    }
}
