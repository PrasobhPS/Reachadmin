<div class="d-flex flex-column flex-root app-root" id="kt_app_root">
	<!--begin::Page-->
	<div class="app-page flex-column flex-column-fluid" id="kt_app_page">
		@if(auth()->check())
			@include('layouts.dashboard_header')
		@endif
		@guest
			<main class="py-4 login-container">
            	@yield('content')
        	</main>
		@else
		<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
			
			<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
				@if(auth()->check())
				@include('layouts.logo')

				@include('layouts.dashboard_sidebar')
				@endif
			</div>
			
			<main class="py-4">
            	@yield('content')
        	</main>

		</div>
		@endguest
	</div>
</div>