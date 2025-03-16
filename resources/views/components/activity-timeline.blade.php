<div class="space-y-6">
    <!-- Persetujuan Tertunda -->
    <div>
        <h2 class="text-xl font-semibold">{{ __('gatekeeper::approval.Pending Approvals') }}</h2>
        <ul class="mt-4 space-y-2">
            @foreach($pendingApprovals as $approval)
                <li class="p-4 bg-yellow-100 border-l-4 border-yellow-500 rounded">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium">{{ $approval->model_name }} - {{ $approval->model_id }}</p>
                            <p class="text-xs text-gray-600">{{ __('gatekeeper::approval.Requested by:') }} {{ $approval->requested_by }}</p>
                        </div>
                        <span class="text-yellow-600 text-sm">{{ __('gatekeeper::approval.Pending') }}</span>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Riwayat Persetujuan -->
    <div>
        <h2 class="text-xl font-semibold">{{ __('gatekeeper::approval.Approval History') }}</h2>
        <div class="mt-4 border-l-4 border-gray-300 space-y-4 pl-4">
            @foreach($approvalHistory as $history)
                <div class="relative">
                    <div class="absolute -left-3 w-6 h-6 rounded-full flex items-center justify-center
                        @if($history->status === 'approved') bg-green-500 text-white
                        @elseif($history->status === 'rejected') bg-red-500 text-white
                        @else bg-gray-500 text-white @endif">
                        @if($history->status === 'approved')
                            ✓
                        @elseif($history->status === 'rejected')
                            ✗
                        @else
                            ⏳
                        @endif
                    </div>
                    <div class="ml-6 p-4 bg-gray-100 rounded shadow">
                        <p class="text-sm font-medium">{{ $history->model_name }} - {{ $history->model_id }}</p>
                        <p class="text-xs text-gray-600">
                            {{ __('gatekeeper::approval.Reviewed by:') }} {{ $history->reviewed_by }} -
                            <span class="font-bold">{{ __("gatekeeper::approval." . ucfirst($history->status)) }}</span>
                        </p>
                        <p class="text-xs text-gray-500">{{ $history->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
