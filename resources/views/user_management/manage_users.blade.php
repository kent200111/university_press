@extends('layouts.app')
@section('content')
<html>

<head>
    <title>Manage Users</title>
    <link rel="stylesheet" href="admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="admin/plugins/toastr/toastr.min.css">
    <link rel="icon" href="images/cmu_press_logo.png" type="image/png">
    <script src="admin/plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="admin/plugins/toastr/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <style>
    #UsersTable th,
    #UsersTable td {
        white-space: nowrap;
    }
    .dataTables_paginate .paginate_button {
        display: none;
    }
    .dataTables_paginate .paginate_button.previous,
    .dataTables_paginate .paginate_button.next {
        display: inline-block;
    }
    </style>
</head>

<body class="hold-transition sidebar-mini" style="font-family: Roboto, sans-serif;">
    <div class="wrapper">
        <div class="container-fluid">
            <br>
            <div class="card">
                <div class="card-header" style="background: #E9ECEF;">
                    <h3 class="card-title">Manage Users</h3>
                    <div class="text-right">
                        <a class="btn btn-primary" onClick="showAddUserModal()" href="javascript:void(0)"
                            style="background-color: #00491E; border-color: #00491E;">
                            <i class="fas fa-plus"></i>&nbsp;&nbsp;Add
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- USERS TABLE -->
                    <table class="table table-bordered table-striped" id="UsersTable" style="font-size: 14px;">
                        <thead class="text-center">
                            <tr>
                                <th class="text-center">Actions</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Account Type</th>
                                <th>Email Verified At</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                    <!-- USERS TABLE -->
                </div>
            </div>
        </div>
        <br>
        <!-- ADD USER MODAL -->
        <div class="modal fade" id="AddUserModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- ADD USER FORM -->
                    <form id="AddUserForm" method="POST">
                        @csrf
                        <div class="modal-header" style="background: #E9ECEF;">
                            <h4 class="modal-title">Add User</h4>
                            <button type="button" class="close" onClick="hideAddUserModal()">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" style="background: #02681E;">
                            <div class="container-fluid">
                                <div class="card card-default">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="name">Username</label>
                                                    <input type="text" class="form-control" name="name" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="email" class="form-control" name="email" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="account_type">Account Type</label>
                                                    <select class="select2 form-control" name="account_type"
                                                        style="width: 100%;" required>
                                                        <option value="" disabled selected></option>
                                                        <option>Admin</option>
                                                        <option>Employee</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="background: #E9ECEF;">
                            <button type="button" class="btn btn-danger" onClick="hideAddUserModal()"
                                href="javascript:void(0)"><i class="fas fa-times"></i>&nbsp;&nbsp;Cancel</button>
                            <button type="submit" class="btn btn-primary"
                                style="background-color: #00491E; border-color: #00491E;"><i
                                    class="fas fa-plus"></i>&nbsp;&nbsp;Add</button>
                        </div>
                    </form>
                    <!-- ADD USER FORM -->
                </div>
            </div>
        </div>
        <!-- ADD USER MODAL -->
        <!-- EDIT USER MODAL -->
        <div class="modal fade" id="EditUserModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- EDIT USER FORM -->
                    <form id="EditUserForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header" style="background: #E9ECEF;">
                            <h4 class="modal-title">Edit User</h4>
                            <button type="button" class="close" onClick="hideEditUserModal()">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" style="background: #02681E;">
                            <input type="hidden" id="UserId" name="user_id">
                            <div class="container-fluid">
                                <div class="card card-default">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="name">Username</label>
                                                    <input type="text" class="form-control" id="EditName" name="name"
                                                        required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="email" class="form-control" id="EditEmail" name="email"
                                                        required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="account_type">Account Type</label>
                                                    <select class="select2 form-control" id="EditAccountType"
                                                        name="account_type" style="width: 100%;" required>
                                                        <option value="" disabled selected></option>
                                                        <option>Admin</option>
                                                        <option>Employee</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="background: #E9ECEF;">
                            <button type="button" class="btn btn-danger" onClick="hideEditUserModal()"
                                href="javascript:void(0)"><i class="fas fa-times"></i>&nbsp;&nbsp;Cancel</button>
                            <button type="submit" class="btn btn-primary"
                                style="background-color: #00491E; border-color: #00491E;"><i
                                    class="fas fa-check"></i>&nbsp;&nbsp;Update</button>
                        </div>
                    </form>
                    <!-- EDIT USER FORM -->
                </div>
            </div>
        </div>
        <!-- EDIT USER MODAL -->
        <!-- DELETE USER MODAL -->
        <div class="modal fade" id="DeleteUserModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background: #E9ECEF;">
                        <h4 class="modal-title">Delete User</h4>
                        <button type="button" class="close" onClick="hideDeleteUserModal()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="background: #E9ECEF;">
                        Are you sure you want to delete this user?
                    </div>
                    <div class="modal-footer" style="background: #E9ECEF;">
                        <button type="button" class="btn btn-primary" onClick="hideDeleteUserModal()"
                            href="javascript:void(0)" style="background-color: #00491E; border-color: #00491E;"><i
                                class="fas fa-times"></i>&nbsp;&nbsp;Cancel</button>
                        <button type="button" class="btn btn-danger" id="DeleteUser"><i
                                class="fas fa-trash"></i>&nbsp;&nbsp;Delete</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- DELETE USER MODAL -->
    </div>
    <script>
    function showAddUserModal() {
        $('#AddUserModal').modal('show');
    }
    function hideAddUserModal() {
        $('#AddUserModal').modal('hide');
    }
    function showEditUserModal(userId) {
        $.ajax({
            url: "{{ route('users.edit', ':id') }}".replace(':id', userId),
            type: 'GET',
            dataType: 'json',
            success: function(user) {
                $('#UserId').val(user.id);
                $('#EditName').val(user.name);
                $('#EditEmail').val(user.email);
                $('#EditAccountType').val(user.account_type).trigger('change');
                $('#EditUserModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }
    function hideEditUserModal() {
        $('#EditUserModal').modal('hide');
    }
    function showDeleteUserModal() {
        $('#DeleteUserModal').modal('show');
    }
    function hideDeleteUserModal() {
        $('#DeleteUserModal').modal('hide');
    }
    $('#AddUserForm').submit(function(event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: "{{ route('users.store') }}",
            type: 'POST',
            data: formData,
            success: function(response) {
                var successMessage = response.success;
                console.log(successMessage);
                hideAddUserModal()
                toastr.success(successMessage);
                refreshUsersTable();
            },
            error: function(xhr, status, error) {
                var errorMessage = JSON.parse(xhr.responseText).error;
                console.error(errorMessage);
                toastr.error(errorMessage);
            }
        });
    });
    $('#EditUserForm').submit(function(event) {
        event.preventDefault();
        var formData = $(this).serialize();
        var userId = $('#UserId').val();
        $.ajax({
            url: "{{ route('users.update', ':id') }}".replace(':id',
                userId),
            type: 'POST',
            data: formData,
            success: function(response) {
                var successMessage = response.success;
                console.log(successMessage);
                hideEditUserModal();
                toastr.success(successMessage);
                refreshUsersTable();
            },
            error: function(xhr, status, error) {
                var errorMessage = JSON.parse(xhr.responseText).error;
                console.error(errorMessage);
                toastr.error(errorMessage);
            }
        });
    });
    $('#UsersTable').on('click', '.delete', function(event) {
        event.preventDefault();
        var userId = $(this).data('id');
        showDeleteUserModal();
        $('#DeleteUser').off().on('click', function() {
            $.ajax({
                url: "{{ route('users.destroy', ':id') }}".replace(':id', userId),
                type: 'DELETE',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    var successMessage = response.success;
                    console.log(successMessage);
                    hideDeleteUserModal();
                    toastr.success(successMessage);
                    refreshUsersTable();
                },
                error: function(xhr, status, error) {
                    var errorMessage = JSON.parse(xhr.responseText).error;
                    console.error(errorMessage);
                    toastr.error(errorMessage);
                }
            });
        });
    });
    function refreshUsersTable() {
        $.ajax({
            url: "{{ route('users.index') }}",
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var table = $('#UsersTable').DataTable();
                var existingRows = table.rows().remove().draw(false);
                data.forEach(function(user) {
                    var formattedEmailVerifiedAtString = 'Not Verified';
                    if (user.email_verified_at) {
                        var formattedEmailVerifiedAt = new Date(user.email_verified_at);
                        var options = {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                            hour: 'numeric',
                            minute: 'numeric'
                        };
                        formattedEmailVerifiedAtString = formattedEmailVerifiedAt
                            .toLocaleDateString('en-US', options);
                    }
                    table.row.add([
                        '<div class="text-center">' +
                        '<a href="#" class="edit" title="Edit" data-toggle="tooltip" data-id="' +
                        user.id + '" onclick="showEditUserModal(' + user.id +
                        ')"><i class="material-icons">&#xE254;</i></a>' +
                        '<a href="#" class="delete" title="Delete" data-toggle="tooltip" data-id="' +
                        user.id + '"><i class="material-icons">&#xE872;</i></a>' +
                        '</div>',
                        user.name,
                        user.email,
                        user.account_type,
                        formattedEmailVerifiedAtString
                    ]);
                });
                table.draw();
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }
    $(document).ready(function() {
        $('#UsersTable').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": false,
            "info": true,
            "autoWidth": false,
            "responsive": false,
            "scrollX": true,
            "scrollY": true,
            "scrollCollapse": false,
            "buttons": ["copy", "excel", "pdf"],
            "pageLength": 10
        }).buttons().container().appendTo('#UsersTable_wrapper .col-md-6:eq(0)');
        refreshUsersTable();
        setInterval(refreshUsersTable, 60000);
        $('#AddUserModal').on('hidden.bs.modal', function(e) {
            $('#AddUserForm')[0].reset();
            $('#AddUserModal select').val(null).trigger('change');
        });
        var previousWidth = $(window).width();
        $(window).on('resize', function() {
            var currentWidth = $(window).width();
            if (currentWidth !== previousWidth) {
                refreshUsersTable();
                previousWidth = currentWidth;
            }
        });
    });
    </script>
</body>

</html>
@endsection