@extends('layouts.admin')

@section('title', 'Users')
@section('page-title', 'Users')
@section('page-icon', 'bi-people')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="section-title mb-0">
            <i class="bi bi-people"></i>
            All Users
        </h5>
        @can('create')
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add User
        </a>
        @endcan
    </div>
    <div class="card-body p-4">
        {{-- Search Bar --}}
        <div class="mb-4">
            <form method="GET" action="{{ route('users.index') }}" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label for="search" class="form-label">Search Users</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="search" name="search"
                            class="form-control"
                            value="{{ request('search') }}"
                            placeholder="Search by name, email, or username...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="role_filter" class="form-label">Role</label>
                    <select id="role_filter" name="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    @if(request('search') || request('role'))
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary flex-fill">
                            <i class="bi bi-x-circle me-1"></i> Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Registered</th>
                        @can('edit')<th>Actions</th>@endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $i => $user)
                        <tr>
                            <td class="text-muted">{{ $users->firstItem() + $i }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar" style="width:36px;height:36px;font-size:0.8rem;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="fw-semibold">{{ $user->name }}</span>
                                        @if($user->id === auth()->id())
                                            <span class="badge bg-info-custom ms-1" style="font-size:0.6rem;">YOU</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted">{{ $user->email }}</td>
                            <td>
                                @if($user->username)
                                    <code style="color:#a78bfa;font-size:0.85rem;">{{ '@' . $user->username }}</code>
                                @else
                                    <span class="text-muted" style="font-size:0.85rem;">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="role-badge {{ $user->role }}">
                                    {{ $user->role === 'admin' ? 'Admin' : 'Staff' }}
                                </span>
                            </td>
                            <td class="text-muted small">{{ $user->created_at->format('M d, Y') }}</td>
                            @can('edit')
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                    <button onclick="confirmDelete('{{ route('users.destroy', $user) }}', 'Delete user &ldquo;{{ $user->name }}&rdquo;?')"
                                        class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-people empty-icon"></i>
                                <p class="empty-text">
                                    @if(request('search') || request('role'))
                                        No users match your search criteria.
                                    @else
                                        No users found.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3 d-flex justify-content-center">
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
