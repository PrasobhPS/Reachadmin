@extends('layouts.app')
@section('content')
<form method="POST" id="changePasswordForm" action="{{ route('update_password') }}" enctype="multipart/form-data">
    @csrf
    <h1 class="fs-2x text-dark mb-3 common-head">Change Password</h1>
    @if(session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
    @endif

    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card w-100 p-5">
                <div class="align-items-center d-flex justify-content-between">
                    <div>
                        <h2>Password Details</h2>
                    </div>
                </div>

                <!-- Current Password -->
                <div class="row">
                    <div class="col-md-12">
                        <label class="col-lg-12 col-form-label required fw-semibold fs-6">Current Password</label>
                        <div class="col-lg-12 fv-row fv-plugins-icon-container">
                            <input type="password" name="current_password" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Enter Current Password" value="" required>
                            @if($errors->has('current_password'))
                            <div class="text-danger">
                                {{ $errors->first('current_password') }}
                            </div>
                            @endif
                            <div class="fv-plugins-message-container invalid-feedback"></div>
                        </div>
                    </div>
                </div>

                <!-- New Password -->
                <div class="row">
                    <div class="col-md-12">
                        <label class="col-lg-12 col-form-label required fw-semibold fs-6">New Password</label>
                        <div class="col-lg-12 fv-row fv-plugins-icon-container">
                            <input type="password" id="new_password" name="new_password" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Enter New Password" value="">
                            <div class="fv-plugins-message-container invalid-feedback"></div>
                        </div>
                    </div>
                    @error('new_password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <!-- Confirm New Password -->
                <div class="row">
                    <div class="col-md-12">
                        <label class="col-lg-12 col-form-label required fw-semibold fs-6">Confirm New Password</label>
                        <div class="col-lg-12 fv-row fv-plugins-icon-container">
                            <input type="password" name="new_password_confirmation" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Confirm New Password" value="" required>
                            <div class="fv-plugins-message-container invalid-feedback"></div>
                        </div>
                    </div>
                    @error('new_password_confirmation')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end px-0 py-3">
                <button type="submit" class="btn btn-primary">Change Password</button>
            </div>
        </div>

    </div>
</form>
<script>
    $(document).ready(function() {
        $("#changePasswordForm").validate({
            rules: {
                current_password: {
                    required: true,
                },
                new_password: {
                    required: true,
                    minlength: 8
                },
                new_password_confirmation: {
                    required: true,
                    equalTo: "#new_password"
                }
            },
            messages: {
                current_password: {
                    required: "Please enter your current password.",
                },
                new_password: {
                    required: "Please enter a new password.",
                    minlength: "Your password must be at least 8 characters long."
                },
                new_password_confirmation: {
                    required: "Please confirm your new password.",
                    equalTo: "Passwords do not match."
                }
            },
            errorElement: "div",
            errorPlacement: function(error, element) {
                error.insertAfter(element);
            },

        });
    });
</script>
@endsection