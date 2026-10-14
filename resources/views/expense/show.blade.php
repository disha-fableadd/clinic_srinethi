@extends('layout.app')

<style>
    .expense-button {
        /* padding-right: 65px !important; */
        text-align: right !important;
    }
    .card-footer{
        background-color:#87ceb0 !important;
    }

    @media screen and (max-width: 767px) {
        .page-title {
            font-size: 19px !important;
        }

        .expense-button {
            padding-right: 0 !important;
            text-align: center !important;
        }
    }
</style>

@section('content')
<div class="page-wrapper">
    <div class="content" style="height:100vh">
        <div class="row" style="padding-top:15px">
            <div class="col-sm-6 col-8">
                <h4 class="page-title" style="text-align:left;">
                    <i class="fa fa-cogs"></i> Expense Details
                </h4>
            </div>
            @if(app('hasPermission')(7, 'view'))
                <div class="col-sm-6 col-4 expense-button m-b-2">
                    <a href="{{ route('expense.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                        <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                    </a>
                </div>
            @endif
        </div>

        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-footer text-right">
                        <h3 style="float:left" class="text-dark">
                            <i class="fa fa-info-circle icon-style2"></i>
                            <span class="expense_service"></span> Details
                        </h3>
                    </div>

                    <div class="card-body mt-3">
                        <p class="text-dark">
                            <strong><i class="fa fa-user icon-style1"></i> Staff Name: </strong>
                            <span id="staff_name"></span>
                        </p>
                        <hr>

                        <p class="text-dark">
                            <strong><i class="fas fa-clipboard-list icon-style1"></i> Service: </strong>
                            <span class="expense_service"></span>
                        </p>
                        <hr>

                        <p class="text-dark">
                            <strong><i class="fa fa-dollar-sign icon-style1"></i> Amount: </strong>
                            <span id="amount"></span>
                        </p>
                        <hr>

                        @php
                            $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);
                        @endphp
                        @if($currentProjectTypeId !== 3)
                        <p class="text-dark">
                            <strong><i class="fa fa-comment icon-style1"></i> Comment: </strong>
                            <span id="comment"></span>
                        </p>
                        <hr>
                        @endif

                        <p class="text-dark">
                            <strong><i class="fa fa-calendar-plus icon-style1"></i> Date: </strong>
                            <span id="expense_date"></span>
                        </p>

                        <div class="button mb-4" style="display: flex; justify-content: end; margin: 0 5px;">
                            @if(app('hasPermission')(31, 'update'))
                                <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-expense-btn"
                                    style="color:black; margin-right:10px">
                                    <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span> 
                                </a>
                            @endif
                            @if(app('hasPermission')(31, 'delete'))
                                <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-expense"
                                    data-id="{{ $expense_id }}">
                                    <i class="fa fa-trash"></i> <span class="hdr-btn-text">Delete</span> 
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="successMessage" class="alert alert-success" style="display:none;"></div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
    </div>
</div>

<script>
    $(document).ready(function () {
        var pathParts = window.location.pathname.split('/');
        var expenseId = pathParts[pathParts.length - 1];

        console.log("Extracted Expense ID from URL:", expenseId);

        if (!expenseId || isNaN(expenseId)) {
            alert("Error: Expense ID is missing or invalid.");
            return;
        }

        let token = sessionStorage.getItem('token');

        $.ajax({
            url: "/api/expenses/" + expenseId,
            type: "GET",
            dataType: "json",
            headers: { "Authorization": "Bearer " + token },
            success: function (response) {
                console.log("Fetched Expense Data:", response);

                function ucfirst(str) {
                    return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
                }

                $(".expense_service").text(ucfirst(response.data.service));
                $("#staff_name").text(ucfirst(response.data.user.fullname || 'N/A'));
                $("#amount").text(response.data.amount);
                $("#comment").text(ucfirst(response.data.comment));
                $("#expense_date").text(response.data.date_time.split('T')[0]);

                // Update the edit button link
                $(".edit-expense-btn").attr("href", "/expense/edit/" + response.data.id);
            },
            error: function () {
                alert("Failed to fetch expense details.");
            }
        });
    });

    $(document).on('click', '.delete-expense', function () {
        var expenseId = $(this).data('id');
        let token = sessionStorage.getItem('token');

        if (!token) {
            window.location.href = "{{ route('login') }}";
        }

        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this expense!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/api/expenses/' + expenseId,
                    type: 'DELETE',
                    headers: { "Authorization": "Bearer " + token },
                    success: function (response) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Expense deleted successfully!',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function (xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Failed to delete expense. Please try again.', 'error');
                    }
                });
            }
        });
    });
</script>

<style>
    .icon-style1 {
        background-color: white;
        color: rgb(157 195 179);
        padding: 5px;
        font-size: 20px;
        border-radius: 50%;
    }

    .icon-style2 {
        color: white;
        padding: 5px;
        font-size: 20px;
        border-radius: 50%;
    }
</style>
@endsection
