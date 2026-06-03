@extends('layouts.admin')

@section('title', 'Activity Logs')
@section('page-title', 'Activity Logs')
@section('page-icon', 'bi-clock-history')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="section-title mb-0">
            <i class="bi bi-clock-history"></i>
            All Activities
            <span class="text-muted fw-normal small ms-2">({{ $logs->total() }})</span>
        </h5>
    </div>
    <div class="card-body p-4">
        {{-- Filters --}}
        <form method="GET" class="row g-3 mb-3">
            <div class="col-md-3">
                <select name="action" class="form-select">
                    <option value="">All Actions</option>
                    <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>Created</option>
                    <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>Updated</option>
                    <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>Deleted</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="model" class="form-select">
                    <option value="">All Models</option>
                    @foreach($modelTypes as $type)
                        <option value="{{ $type }}" {{ request('model') === $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control" placeholder="From" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control" placeholder="To" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <a href="{{ route('activity-logs.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>Model</th>
                        <th>Date &amp; Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $i => $log)
                        <tr>
                            <td class="text-muted">{{ $logs->firstItem() + $i }}</td>
                            <td>
                                @if($log->user)
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="user-avatar" style="width:30px;height:30px;font-size:0.65rem;">
                                            {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                        </div>
                                        <span class="fw-semibold small">{{ $log->user->name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                @switch($log->action)
                                    @case('created')
                                        <span class="badge bg-success rounded-pill">
                                            <i class="bi bi-plus-circle me-1" style="font-size:0.65rem;"></i> Created
                                        </span>
                                        @break
                                    @case('updated')
                                        <span class="badge bg-primary rounded-pill">
                                            <i class="bi bi-pencil me-1" style="font-size:0.65rem;"></i> Updated
                                        </span>
                                        @break
                                    @case('deleted')
                                        <span class="badge bg-danger rounded-pill">
                                            <i class="bi bi-trash me-1" style="font-size:0.65rem;"></i> Deleted
                                        </span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary rounded-pill">{{ $log->action }}</span>
                                @endswitch
                            </td>
                            <td class="small">{{ $log->description }}</td>
                            <td>
                                <span class="badge bg-info-custom rounded-pill">{{ $log->model_type }}</span>
                            </td>
                            <td class="text-muted small">
                                <span title="{{ $log->created_at->format('F j, Y g:i A') }}">
                                    {{ $log->created_at->diffForHumans() }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-clock-history empty-icon"></i>
                                    <p class="empty-text">No activity logs found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3 d-flex justify-content-center">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-submit on filter change for action and model selects
    document.querySelectorAll('select[name="action"], select[name="model"]').forEach(el => {
        el.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
</script>
@endpush
