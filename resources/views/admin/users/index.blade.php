@extends('admin.layouts.admin')

@section('title', 'Quản lý Người dùng')

@section('content')
    <div class="page-header">
        <h2>👤 Quản lý Người dùng</h2>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>SĐT</th>
                        <th>Địa chỉ</th>
                        <th>Admin</th>
                        <th>Ngày tạo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td style="font-weight: 500;">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? '—' }}</td>
                            <td style="font-size: 13px; max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                                {{ $user->address ?? '—' }}</td>
                            <td>
                                <span class="badge badge-{{ $user->is_admin ? 'green' : 'yellow' }}">
                                    {{ $user->is_admin ? 'Admin' : 'User' }}
                                </span>
                            </td>
                            <td style="font-size: 13px; color: #666;">{{ $user->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $users->links() }}</div>
    </div>
@endsection