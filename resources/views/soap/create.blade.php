@extends('layout.app')

@section('content')
    <style>
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
                        <h4 class="page-title">Add SOAP</h4>
                    </div>
                    <div class="col-sm-6 col-6 text-right">
                        <a href="{{ route('soap.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="soap-card">
                <form id="createSoapForm">
                    @csrf
                    <input type="hidden" name="branch_id" id="branch_id">
                    {{-- Patient Dropdown --}}
                    <div class="form-group">
                        <label><i class="fas fa-user icon-style"></i> Select Patient <span
                                                        class="text-danger">*</span></label>
                        <select class="form-control select2" name="patient_id" id="patientDropdown" required>
                            <option value="">Select Patient</option>

                        </select>
                    </div>


                    {{-- Date --}}
                    <div class="form-group">
                        <label>Date <span
                                                        class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="date" required>
                    </div>

                    {{-- Subjective + Description --}}
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label>Subjective <span
                                                        class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="subjective_title" required>
                        </div>
                        <div class="col-md-8">
                            <label>Description</label>
                            <textarea class="form-control" name="subjective_description" rows="1"></textarea>
                        </div>
                    </div>

                    {{-- Objective + Description --}}
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label>Objective <span
                                                        class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="objective_title" required>
                        </div>
                        <div class="col-md-8">
                            <label>Description</label>
                            <textarea class="form-control" name="objective_description" rows="1"></textarea>
                        </div>
                    </div>

                    {{-- Assessment + Description --}}
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label>Assessment <span
                                                        class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="assessment_title" required>
                        </div>
                        <div class="col-md-8">
                            <label>Description</label>
                            <textarea class="form-control" name="assessment_description" rows="1"></textarea>
                        </div>
                    </div>

                    {{-- Plan + Description --}}
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label>Plan <span
                                                        class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="plan_title" required>
                        </div>
                        <div class="col-md-8">
                            <label>Description</label>
                            <textarea class="form-control" name="plan_description" rows="1"></textarea>
                        </div>
                    </div>

                    {{-- Messages --}}
                    <div id="soapSuccessMessage" class="alert alert-success" style="display:none;"></div>
                    <div id="soapErrorMessage" class="alert alert-danger" style="display:none;"></div>

                    {{-- Submit --}}
                    <div class="m-t-20 text-center">
                        <button type="submit" class="btn btn-primary submit-btn">Create SOAP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- jQuery Validate -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    <script src="{{ asset(env('IMAGE_PATH').'admin/assets/js/soap-create.js') }}"></script>
@endsection
