<div class="space-y-6 p-4">
    <!-- Approval History Section -->
    <h3 class="text-lg font-semibold text-gray-900 mb-6">{{ __('gatekeeper::gatekeeper.approval_timeline_action.approval_history_title') }}</h3>
    @if($pendingApprovals->isEmpty() && $approvalHistory->isEmpty())
        <div class="text-center text-gray-500 py-4">
            <p>{{ __('gatekeeper::gatekeeper.approval_timeline_action.no_approval_history') }}</p>
        </div>
    @else
        <ul class="relative border-l border-gray-200 dark:border-gray-700">
            <!-- Pending Approvals -->
            @foreach($pendingApprovals as $approval)
                <li class="mb-6 ml-6">
                    <span class="absolute flex items-center justify-center w-6 h-6 bg-yellow-100 rounded-full -left-3 ring-8 ring-white dark:ring-gray-900 dark:bg-yellow-900">
                        <svg class="w-3 h-3 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v4a1 1 0 00.293.707l3 3a1 1 0 001.414-1.414L11 9.586V5z" clip-rule="evenodd"></path>
                        </svg>
                    </span>
                    <h3 class="flex items-center mb-1 text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __('gatekeeper::gatekeeper.approval.Pending') }}
                        <span class="bg-yellow-100 text-yellow-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-yellow-200 dark:text-yellow-800 ml-3">
                            {{ $approval->created_at->diffForHumans() }}
                        </span>
                    </h3>
                    <time class="block mb-2 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
                        {{ $approval->created_at->toFormattedDateString() }} {{ __('gatekeeper::gatekeeper.approval_timeline_action.at') }} {{ $approval->created_at->toTimeString() }}
                    </time>
                    <p class="mb-2 text-base font-normal text-gray-500 dark:text-gray-400">
                        {{ __('gatekeeper::gatekeeper.approval_timeline_action.pending_description', [
                            'model' => $approval->model_name,
                            'id' => $approval->approvable_id,
                        ]) }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        {{ __('gatekeeper::gatekeeper.approval.Requested by:') }} {{ $approval->requested_by }}
                    </p>
                    @if($approval->comment)
                        <p class="text-sm text-gray-500 mt-1">{{ __('gatekeeper::gatekeeper.approval.Comment:') }} {{ $approval->comment }}</p>
                    @endif
                </li>
            @endforeach

            <!-- Approval History -->
            @foreach($approvalHistory as $history)
                @php
                    $color = match ($history->status) {
                        'approved' => 'green',
                        'rejected' => 'red',
                        default => 'gray',
                    };
                @endphp
                <li class="mb-6 ml-6">
                    <span class="absolute flex items-center justify-center w-6 h-6 bg-{{ $color }}-100 rounded-full -left-3 ring-8 ring-white dark:ring-gray-900 dark:bg-{{ $color }}-900">
                        <svg class="w-3 h-3 text-{{ $color }}-600 dark:text-{{ $color }}-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            @switch($history->status)
                                @case('approved')
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    @break
                                @case('rejected')
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    @break
                                @default
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v4a1 1 0 00.293.707l3 3a1 1 0 001.414-1.414L11 9.586V5z" clip-rule="evenodd"></path>
                            @endswitch
                        </svg>
                    </span>
                    <h3 class="flex items-center mb-1 text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __("gatekeeper::gatekeeper.approval." . ucfirst($history->status)) }}
                        <span class="bg-{{ $color }}-100 text-{{ $color }}-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-{{ $color }}-200 dark:text-{{ $color }}-800 ml-3">
                            {{ $history->created_at->diffForHumans() }}
                        </span>
                    </h3>
                    <time class="block mb-2 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
                        {{ $history->created_at->toFormattedDateString() }} {{ __('gatekeeper::gatekeeper.approval_timeline_action.at') }} {{ $history->created_at->toTimeString() }}
                    </time>
                    <p class="mb-2 text-base font-normal text-gray-500 dark:text-gray-400">
                        {{ __("gatekeeper::gatekeeper.approval_timeline_action.{$history->status}_description", [
                            'model' => $history->model_name,
                            'id' => $history->approvable_id,
                        ]) }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        {{ __('gatekeeper::gatekeeper.approval.Reviewed by:') }} {{ $history->reviewed_by }}
                    </p>
                    @if($history->comment)
                        <p class="text-sm text-gray-500 mt-1">{{ __('gatekeeper::gatekeeper.approval.Comment:') }} {{ $history->comment }}</p>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</div>
