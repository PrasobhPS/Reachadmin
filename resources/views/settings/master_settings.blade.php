@extends('layouts.app')

@section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
	<!--begin::Content wrapper-->

	<div class="d-flex flex-column flex-column-fluid">

		<!--begin::Toolbar-->
		<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
			<!--begin::Toolbar container-->
			<div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">

				<div class="event-outer w-100">
					@if ($errors->any())
					<div style="color:red">
						<ul>
							@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
					@endif

					@if(session('success'))
					<div class="alert alert-success">
						{{ session('success') }}
					</div>
					@endif

					<form method="POST" action="{{ route('settings.update') }}" id="settingsForm" enctype="multipart/form-data">
						@csrf

						<h1 class="fs-2x text-dark mb-3 common-head">Master Settings</h1>
						<div class="row">
							<div class="col-md-6">
								<div class="card p-5 w-100">
									<div class="row">
										<label class="col-lg-12 col-form-label required fw-semibold fs-6">Annual Membership Fee</label>
										<div class="col-lg-6 fv-row fv-plugins-icon-container d-flex align-items-center">
											<span>£</span>
											<input type="number" name="full_membership_fee" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Membership Fee (£)" value="{{ number_format($feeSettings->full_membership_fee, 2) }}">
										</div>
										<div class="col-lg-6 fv-row fv-plugins-icon-container d-flex align-items-center">
											<span>Є</span>
											<input type="number" name="full_membership_fee_euro" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Membership Fee (Є)" value="{{ number_format($feeSettings->full_membership_fee_euro, 2) }}">
										</div>
										<div class="col-lg-6 fv-row fv-plugins-icon-container d-flex align-items-center">
											<span>$</span>
											<input type="number" name="full_membership_fee_dollar" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Membership Fee ($)" value="{{ number_format($feeSettings->full_membership_fee_dollar, 2) }}">
										</div>
										<label class="col-lg-12 col-form-label required fw-semibold fs-6">Monthly Membership Fee</label>
										<div class="col-lg-6 fv-row fv-plugins-icon-container d-flex align-items-center">
											<span>£</span>
											<input type="number" name="monthly_membership_fee" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Membership Fee (£)" value="{{ number_format($feeSettings->monthly_membership_fee, 2) }}">
										</div>
										<div class="col-lg-6 fv-row fv-plugins-icon-container d-flex align-items-center">
											<span>Є</span>
											<input type="number" name="monthly_membership_fee_euro" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Membership Fee (Є)" value="{{ number_format($feeSettings->monthly_membership_fee_euro, 2) }}">
										</div>
										<div class="col-lg-6 fv-row fv-plugins-icon-container d-flex align-items-center">
											<span>$</span>
											<input type="number" name="monthly_membership_fee_dollar" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Membership Fee ($)" value="{{ number_format($feeSettings->monthly_membership_fee_dollar, 2) }}">
										</div>
									</div>
									<div class="row">
										<label class="col-lg-12 col-form-label  fw-bold fs-6">Experts Booking Fee</label>
										<!--<div class="col-lg-6 fv-row fv-plugins-icon-container">
		                                <input type="number" name="specialist_booking_fee" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Specialist Booking Fee" value="{{ $feeSettings->specialist_booking_fee }}">
		                            </div>-->
									</div>


									<!--for exchnage rate convertion-->
									<div class="row">
										<label class="col-lg-12 col-form-label  fw-bold fs-6">Exchange Rate</label>

									</div>
									<!--for exchnage rate convertion-->
									<div class="table-responsive">
										<table class="table table-bordered">
											<thead>
												<tr>
													<th class="fw-semibold fs-6">Currency</th>
													<th class="fw-semibold fs-6">Rate to USD</th>
													<th class="fw-semibold fs-6">Rate to GBP</th>
													<th class="fw-semibold fs-6">Rate to EUR</th>
												</tr>
											</thead>
											<tbody>
												<!-- USD Row -->
												@foreach ($currencyExchangeRates as $rate)
												<tr>
													<td>{{$rate->currency_code}}</td>
													<td>
														<input type="number" name="exchange_rate_to_usd[{{ $rate->id }}]" class="form-control form-control-lg form-control-solid" placeholder="" value="{{ number_format($rate->exchange_rate_to_usd, 2) }}">
													</td>
													<td>
														<input type="number" name="exchange_rate_to_gbp[{{ $rate->id }}]" class="form-control form-control-lg form-control-solid" placeholder="" value="{{ number_format($rate->exchange_rate_to_gbp, 2) }}">
													</td>
													<td>
														<input type="number" name="exchange_rate_to_eur[{{ $rate->id }}]" class="form-control form-control-lg form-control-solid" placeholder="" value="{{ number_format($rate->exchange_rate_to_eur, 2) }}">
													</td>
												</tr>
												@endforeach
												<!-- GBP Row -->
												<!--<tr>
                <td>GBP</td>
                <td>
                    <input type="number" name="exchange_rate_gbp_to_usd" class="form-control form-control-lg form-control-solid" placeholder="" value="{{ number_format($feeSettings->specialist_booking_fee, 2) }}">
                </td>
                <td>
                    <input type="number" name="exchange_rate_gbp_to_gbp" class="form-control form-control-lg form-control-solid" placeholder="" value="{{ number_format($feeSettings->specialist_booking_fee_half_hour, 2) }}">
                </td>
                <td>
                    <input type="number" name="exchange_rate_gbp_to_eur" class="form-control form-control-lg form-control-solid" placeholder="" value="{{ number_format($feeSettings->specialist_booking_fee_extra, 2) }}">
                </td>
            </tr>
            
            <tr>
                <td>EUR</td>
                <td>
                    <input type="number" name="exchange_rate_eur_to_usd" class="form-control form-control-lg form-control-solid" placeholder="" value="{{ number_format($feeSettings->specialist_booking_fee_dollar, 2) }}">
                </td>
                <td>
                    <input type="number" name="exchange_rate_eur_to_gbp" class="form-control form-control-lg form-control-solid" placeholder="" value="{{ number_format($feeSettings->specialist_booking_fee_half_hour_dollar, 2) }}">
                </td>
                <td>
                    <input type="number" name="exchange_rate_eur_to_eur" class="form-control form-control-lg form-control-solid" placeholder="" value="{{ number_format($feeSettings->specialist_booking_fee_extra_dollar, 2) }}">
                </td>
            </tr>-->
											</tbody>
										</table>
									</div>

									<!--end for exchange rate convertion-->

									<!--end for exchange rate convertion-->





									<!-- <div class="row">
		                            <label class="col-lg-12 col-form-label required fw-semibold fs-6">Experts Cancellation Fee (%)</label>
		                            <div class="col-lg-6 fv-row fv-plugins-icon-container">
		                                <input type="number" name="specialist_cancel_fee" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Specialist Cancellation Fee" value="{{ $feeSettings->specialist_cancel_fee }}">
		                            </div>
		                        </div> -->
									<div class="row">
										<label class="col-lg-12 col-form-label required fw-semibold fs-6">Member Cancellation Fee (%)</label>
										<div class="col-lg-6 fv-row fv-plugins-icon-container">
											<input type="number" name="member_cancel_fee" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Member Cancellation Fee" value="{{ $feeSettings->member_cancel_fee }}">
										</div>
									</div>
									<div class="row">
										<label class="col-lg-12 col-form-label required fw-semibold fs-6">Service Fee (%)</label>
										<div class="col-lg-6 fv-row fv-plugins-icon-container">
											<input type="number" name="reach_fee" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Service Fee" value="{{ $feeSettings->reach_fee }}">
										</div>
									</div>

									<div class="row" id="referralTypesContainer">

										@foreach($referaalTypes as $index => $referral)
										<div class="row mb-3 referral-type-entry">
											<input type="hidden" name="referral_types[{{ $index }}][id]" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" required value="{{$referral->id}}">
											<div class="col-md-5">

												<label class="col-lg-12 col-form-label required fw-semibold fs-6">Referral Type </label>
												<input type="text" name="referral_types[{{ $index }}][type]" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Referral Type" required value="{{$referral->referral_type}}" @if($index===0) readonly @endif>
											</div>
											<div class="col-md-5">
												<label class="col-lg-12 col-form-label required fw-semibold fs-6">Referral Bonus (%)</label>
												<input type="number" name="referral_types[{{ $index }}][rate]" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Referral Rate (%)" step="0.01" required value="{{$referral->referral_rate}}">
											</div>
											<div class="col-md-2">
												<label class="col-lg-12 col-form-label fw-semibold fs-6"></label>
												@if ($loop->last)
												<button type="button" class="btn btn-primary add-referral-type">+</button>
												@endif
											</div>
										</div>
										@endforeach
									</div>
									<div class="row" id="referralTypesContainer">
										<div class="row mb-3 referral-type-entry">
											<div class="col-md-4">
												<label class="col-lg-12 col-form-label required fw-semibold fs-6">1 Hour </label>
												<div class="d-flex align-items-center">
													<span>£</span>
													<input type="number" name="specialist_booking_fee" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Specialist Booking Fee (£)" value="{{ number_format($feeSettings->specialist_booking_fee,2) }}">
												</div>
											</div>
											<div class="col-md-4">
												<label class="col-lg-12 col-form-label required fw-semibold fs-6">30 Minutes</label>
												<div class="d-flex align-items-center">
													<span>£</span>
													<input type="number" name="specialist_booking_fee_half_hour" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Specialist Booking Fee (£)" value="{{ number_format($feeSettings->specialist_booking_fee_half_hour,2) }}">
												</div>
											</div>
											<div class="col-md-4">
												<label class="col-lg-12 col-form-label required fw-semibold fs-6">Extra</label>
												<div class="d-flex align-items-center">
													<span>£</span>
													<input type="number" name="specialist_booking_fee_extra" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Specialist Booking Fee (£)" value="{{ number_format($feeSettings->specialist_booking_fee_extra,2) }}">
												</div>
											</div>
										</div>
										<div class="row mb-3 referral-type-entry">
											<div class="col-md-4">
												<div class="d-flex align-items-center">
													<span>Є</span>
													<input type="number" name="specialist_booking_fee_euro" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Specialist Booking Fee (Є)" value="{{ number_format($feeSettings->specialist_booking_fee_euro,2) }}">
												</div>
											</div>
											<div class="col-md-4">
												<div class="d-flex align-items-center">
													<span>Є</span>
													<input type="number" name="specialist_booking_fee_half_hour_euro" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Specialist Booking Fee (Є)" value="{{ number_format($feeSettings->specialist_booking_fee_half_hour_euro,2) }}">
												</div>
											</div>
											<div class="col-md-4">
												<div class="d-flex align-items-center">
													<span>Є</span>
													<input type="number" name="specialist_booking_fee_extra_euro" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Specialist Booking Fee (Є)" value="{{ number_format($feeSettings->specialist_booking_fee_extra_euro,2) }}">
												</div>
											</div>
										</div>

										<div class="row mb-3 referral-type-entry">
											<div class="col-md-4">
												<div class="d-flex align-items-center">
													<span>$</span>
													<input type="number" name="specialist_booking_fee_dollar" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Specialist Booking Fee ($)" value="{{ number_format($feeSettings->specialist_booking_fee_dollar,2) }}">
												</div>
											</div>
											<div class="col-md-4">
												<div class="d-flex align-items-center">
													<span>$</span>
													<input type="number" name="specialist_booking_fee_half_hour_dollar" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Specialist Booking Fee ($)" value="{{ number_format($feeSettings->specialist_booking_fee_half_hour_dollar,2) }}">
												</div>
											</div>
											<div class="col-md-4">
												<div class="d-flex align-items-center">
													<span>$</span>
													<input type="number" name="specialist_booking_fee_extra_dollar" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Specialist Booking Fee ($)" value="{{ number_format($feeSettings->specialist_booking_fee_extra_dollar,2) }}">
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<label class="col-lg-12 col-form-label required fw-semibold fs-6">Payment Info</label>

										<textarea rows="6" name="payment_info" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="payment_info">{{ $feeSettings->payment_info }}</textarea>

									</div>
									<div class="card-footer d-flex justify-content-end px-0 py-3 mt-5">
										<button type="submit" class="btn btn-primary">Update</button>
									</div>
								</div>
							</div>
						</div>

					</form>

				</div>
			</div>
			<!--end::Toolbar container-->
		</div>
	</div>
	<!--end::Content wrapper-->

	@include('scripts.settings')
	@include('layouts.dashboard_footer')
</div>

@endsection