<?php

namespace XBigDaddyx\Gatekeeper\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalController extends Controller
{
    /**
     * Handle the approval action via signed URL without requiring login.
     */
    public function approve(Request $request, $approvable_type, $approvable_id, $user_id, $tenant_slug)
    {


        if (!class_exists($approvable_type) || !in_array(\XBigDaddyx\Gatekeeper\Traits\Approvable::class, class_uses_recursive($approvable_type))) {

            return response()->json(['error' => 'Invalid resource type'], 404);
        }

        $tenant = \App\Models\Tenant::where('slug', $tenant_slug)->first();
        if (!$tenant) {

            return response()->json(['error' => 'Tenant not found'], 404);
        }

        $result = $tenant->run(function () use ($approvable_type, $approvable_id, $user_id, $request) {
            $approvable = $approvable_type::findOrFail($approvable_id);

            $userModel = config('gatekeeper.user_model', \App\Models\User::class);
            $user = $userModel::findOrFail($user_id);

            if (!$approvable->canBeApprovedBy($user)) {
                return response()->json(['error' => 'You are not authorized to approve this resource'], 403);
            }

            $approvable->approve($user, $request->input('comment'), false);

            return view('gatekeeper::success.approved', [
                'resourceName' => class_basename($approvable),
                'resourceId' => $approvable->id,
                'po' => $approvable->purchase_order_number,
                'status' => $approvable->management_approval_status->value,
                'currentStep' => $approvable->current_step,
            ]);
        });

        return $result;
    }

    /**
     * Handle the rejection action via signed URL without requiring login.
     */
    public function reject(Request $request, $approvable_type, $approvable_id, $user_id, $tenant_slug)
    {
        if (!class_exists($approvable_type) || !in_array(\XBigDaddyx\Gatekeeper\Traits\Approvable::class, class_uses_recursive($approvable_type))) {
            return response()->json(['error' => 'Invalid resource type'], 404);
        }
        $tenant = \App\Models\Tenant::where('slug', $tenant_slug)->first();
        $result = $tenant->run(function ($tenant) use ($approvable_type, $approvable_id, $user_id, $request) {
            $approvable = $approvable_type::findOrFail($approvable_id);
            $userModel = config('gatekeeper.user_model', \App\Models\User::class);
            $user = $userModel::findOrFail($user_id);

            if (!$approvable->canBeApprovedBy($user)) {
                return response()->json(['error' => 'You are not authorized to reject this resource'], 403);
            }

            $approvable->reject($user, $request->input('comment'), false);
            return view('gatekeeper::success.rejected', [
                'resourceName' => class_basename($approvable),
                'resourceId' => $approvable->id,
                'po' => $approvable->purchase_order_number,
                'status' => $approvable->management_approval_status->value,
                'currentStep' => $approvable->current_step,
            ]);
        });
        return $result;
    }
}
