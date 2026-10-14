@extends('layout.app')

@section('content')
    <style>
        .branch-card {
            max-width: 1300px;
            background: white;
            border-radius: 20px;
            padding: 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .page-wrapper>.content {
            padding: 30px;
            background-color: rgb(248 246 245);
            height: 100vh;
        }

        .branch-header {
            color: black;
            background-color: #87ceb0;
            padding: 16px 20px;
            font-weight: 600;
            font-size: 1.5rem;
        }

        .branch-body {
            padding: 20px;
        }

        .branch-field {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #c8e6c9;
        }

        .branch-field:last-child {
            border-bottom: none;
        }

        .branch-field i {
            width: 25px;
            color: #87ceb0;
            margin-right: 12px;
            font-size: 1.1rem;
        }

        .branch-label {
            font-weight: 500;
            min-width: 150px;
        }

        .branch-value {
            flex: 1;
            word-break: break-word;
        }

        .action-buttons {
            text-align: right;
            padding: 20px;
            background-color: white;
        }

        .action-buttons .btn {
            margin-left: 8px;
            min-width: 80px;
        }

        .icon-style2 {
            color: white
        }

        .card-footer {
            background-color: #87ceb0;
        }

        .float-left {
            float: left;
        }

        .justify-end {
            justify-content: end;
        }

        .delete-branch {
            border-radius: 50px;
        }
    </style>

    <div class="page-wrapper">
        <div class="content">

            <div class="branch-card mt-2">
                <div class="card-footer text-right">
                    <h3 class="float-left text-dark">
                        <i class="fa fa-info-circle icon-style2 text-white"></i>
                        Branch Details
                    </h3>
                    <div class="d-flex text-right justify-end">
                        @if (app('hasPermission')(34, 'view'))
                            <div class="  m-b-2">
                                <a href="{{ route('branch.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                                    <i class="fa fa-arrow-left"></i> <span class="hdr-btn-text">Back</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="branch-body">
                    <div class="branch-field">
                        <i class="fa fa-hashtag"></i>
                        <div class="branch-label">Branch ID:</div>
                        <div class="branch-value" id="branch_id">--</div>
                    </div>

                    <div class="branch-field">
                        <i class="fa fa-building"></i>
                        <div class="branch-label">Branch Name:</div>
                        <div class="branch-value" id="branch_name">--</div>
                    </div>

                    <div class="branch-field">
                        <i class="fa fa-map-marker-alt"></i>
                        <div class="branch-label">Address:</div>
                        <div class="branch-value" id="branch_address">--</div>
                    </div>

                    <div class="branch-field">
                        <i class="fa fa-city"></i>
                        <div class="branch-label">City:</div>
                        <div class="branch-value" id="branch_city">--</div>
                    </div>

                    <div class="branch-field">
                        <i class="fa fa-flag"></i>
                        <div class="branch-label">State:</div>
                        <div class="branch-value" id="branch_state">--</div>
                    </div>

                    <div class="branch-field">
                        <i class="fa fa-globe"></i>
                        <div class="branch-label">Country:</div>
                        <div class="branch-value" id="branch_country">--</div>
                    </div>

                    {{-- <div class="branch-field">
                        <i class="fa fa-calendar"></i>
                        <div class="branch-label">Created At:</div>
                        <div class="branch-value" id="branch_created">--</div>
                    </div> --}}
                </div>

                <div class="action-buttons">
                    <a href="{{ url('/branch/' . $id . '/edit') }}" class="btn btn-primary">
                        <i class="fa fa-edit"></i> <span class="hdr-btn-text">Edit</span>
                    </a>
                    <button class="btn btn-danger delete-branch" data-id="{{ $id }}">
                        <i class="fa fa-trash"></i> <span class="hdr-btn-text">Delete</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let branchId = "{{ $id }}";

            $.ajax({
                url: '/api/branches/' + branchId,
                type: 'GET',
                success: function(branch) {
                    $('#branch_id').text(branch.id ?? '--');
                    $('#branch_name').text(branch.name ?? '--');
                    $('#branch_address').text(branch.address ?? '--');
                    $('#branch_city').text(branch.city ?? '--');
                    $('#branch_state').text(branch.state ?? '--');
                    $('#branch_country').text(branch.country ?? '--');
                    // $('#branch_created').text(branch.created_at ?? '--');
                },
                error: function(xhr) {
                    console.error("Failed to fetch Branch:", xhr.responseText);
                }
            });

            $(document).on('click', '.delete-branch', function() {
                if (!confirm('Are you sure you want to delete this Branch?')) return;

                $.ajax({
                    url: '/api/branches/' + branchId,
                    type: 'DELETE',
                    success: function() {
                        alert('Branch deleted successfully!');
                        window.location.href = "{{ route('branches.index') }}";
                    },
                    error: function(xhr) {
                        alert('Failed to delete Branch.');
                    }
                });
            });
        });
    </script>
@endsection
