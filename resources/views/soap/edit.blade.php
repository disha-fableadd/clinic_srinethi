@extends('layout.app')

@section('content')
    <style>
        /* Center card styling */
        .soap-card {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .heading {
            max-width: 900px;
            margin: 40px auto;
        }
        .form-container {
        width: 60% !important;
        padding-bottom: 60px !important;
    }

    </style>

    <div class="page-wrapper">
        <div class="content">

            <div class="heading">
                <div class="row">
                    <div class="col-sm-6 col-6">
                        <h4 class="page-title">Edit SOAP</h4>
                    </div>
                    <div class="col-sm-6 col-6 text-right">
                        <a href="{{ route('soap.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="soap-card">
                <form id="editSoapForm">
                    @csrf

                    <input type="hidden" name="soap_id" id="soap_id">

                    {{-- Patient --}}
                    <div class="form-group">
                        <label for="patient_id">Select Patient <span
                                                        class="text-danger">*</span></label>
                        <select class="form-control select patient-id" id="patient_id" name="patient_id"
                            style="width: 100%;">
                            <option value="">-- Select Patient --</option>
                        </select>
                    </div>


                    {{-- Date --}}
                    <div class="form-group">
                        <label>Date <span
                                                        class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="date" id="date" required>
                    </div>

                    {{-- Subjective --}}

                    @php
                        $userRole = auth()->user()->role->name ?? '';
                    @endphp

                    @if ($userRole === 'Admin')
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label>Subjective <span
                                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="subjective_title" id="subjective_title"
                                    required>
                            </div>
                            <div class="col-md-8">
                                <label>Description</label>
                                <textarea class="form-control" name="subjective_description" id="subjective_description" rows="1"></textarea>
                            </div>
                        </div>
                    @endif


                    {{-- Objective --}}
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label>Objective <span
                                                        class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="objective_title" id="objective_title" required>
                        </div>
                        <div class="col-md-8">
                            <label>Description</label>
                            <textarea class="form-control" name="objective_description" id="objective_description" rows="1"></textarea>
                        </div>
                    </div>

                    {{-- Assessment --}}
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label>Assessment <span
                                                        class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="assessment_title" id="assessment_title"
                                required>
                        </div>
                        <div class="col-md-8">
                            <label>Description</label>
                            <textarea class="form-control" name="assessment_description" id="assessment_description" rows="1"></textarea>
                        </div>
                    </div>

                    {{-- Plan --}}
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label>Plan <span
                                                        class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="plan_title" id="plan_title" required>
                        </div>
                        <div class="col-md-8">
                            <label>Description</label>
                            <textarea class="form-control" name="plan_description" id="plan_description" rows="1"></textarea>
                        </div>
                    </div>

                    {{-- Messages --}}
                    <div id="soapSuccessMessage" class="alert alert-success" style="display:none;"></div>
                    {{-- <div id="soapErrorMessage" class="alert alert-danger" style="display:none;"></div> --}}

                    {{-- Submit --}}
                    <div class="m-t-20 text-center">
                        <button type="submit" class="btn btn-primary submit-btn">Update SOAP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- jQuery Validate -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- jQuery (already in your project, skip if included) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>var soapId = "{{ $soap_id }}";</script>
    <script src="{{ asset(env('IMAGE_PATH').'admin/assets/js/soap-edit.js') }}"></script>
@endsection
