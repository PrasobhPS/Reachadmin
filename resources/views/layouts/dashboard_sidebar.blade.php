@if(auth()->check())
<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
    <!--begin::Menu wrapper-->
    <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper hover-scroll-overlay-y my-5" data-kt-scroll="true"
        data-kt-scroll-activate="true" data-kt-scroll-height="auto"
        data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
        data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
        <!--begin::Menu-->
        <div class="menu menu-column menu-rounded menu-sub-indention px-3" id="#kt_app_sidebar_menu" data-kt-menu="true"
            data-kt-menu-expand="false">
            <!--begin:Menu item-->
            <div data-kt-menu-trigger="click"
                class="menu-item here  menu-accordion {{ request()->routeIs('add-member', 'home', 'employee', 'member-edit', 'member-history', 'member-transaction', 'member-deleted') ? 'show' : '' }}">
                <!--begin:Menu link-->
                <span class="menu-link">
                    <span class="menu-icon">
                        <i class="fa-solid fa-user" title="Members"></i>
                    </span>
                    <span class="menu-title">Members</span>
                    <span class="menu-arrow"></span>
                </span>
                <!--end:Menu link-->
                <!--begin:Menu sub-->
                <div
                    class="menu-sub menu-sub-accordion  {{ request()->routeIs('add-member', 'home', 'employee', 'member-edit', 'member-history', 'member-transaction') ? 'show' : '' }}">
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('add-member') ? 'active' : '' }}"
                            href="{{ route('add-member') }}">
                            <span class="menu-bullet">
                                <span><i class="fas fa-plus p-0" title="Add Members"></i></span>
                            </span>
                            <span class="menu-title">Add</span>
                        </a>
                    </div>

                    <!--begin:Menu item-->
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('home', 'member-edit', 'member-history', 'member-transaction') ? 'active' : '' }}"
                            href="{{ route('home') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-table" title="List Members"></i></span>
                            </span>
                            <span class="menu-title">List</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('member-deleted') ? 'active' : '' }}"
                            href="{{ route('member-deleted') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-table" title="List Members"></i></span>
                            </span>
                            <span class="menu-title">Deleted List</span>
                        </a>
                    </div>

                    <!-- <div class="menu-item">
                                    <a class="menu-link" href="{{ route('employer') }}">
                                        <span class="menu-bullet">
                                            <span><i class="fal fa-table" title="List Employer"></i></span>
                                        </span>
                                        <span class="menu-title">Employer List</span>
                                    </a>
                                </div> -->
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('employee') ? 'active' : '' }}"
                            href="{{ route('employee') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-table" title="List Employee"></i></span>
                            </span>
                            <span class="menu-title">Employee List</span>
                        </a>
                    </div>

                </div>
                <!--end:Menu sub-->
            </div>

            <div data-kt-menu-trigger="click"
                class="menu-item here  menu-accordion {{ request()->routeIs('add-event', 'events') ? 'show' : '' }}"
                style="display: none;">
                <!--begin:Menu link-->
                <span class="menu-link">
                    <span class="menu-icon">
                        <i class="fad fa-calendar-alt" title="Events"></i>
                    </span>
                    <span class="menu-title">Events</span>
                    <span class="menu-arrow"></span>
                </span>
                <!--end:Menu link-->
                <!--begin:Menu sub-->
                <div class="menu-sub menu-sub-accordion {{ request()->routeIs('add-event', 'events') ? 'show' : '' }}">

                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('add-event') ? 'active' : '' }}"
                            href="{{ route('add-event') }}">
                            <span class="menu-bullet">
                                <span><i class="fas fa-plus p-0" title="Add Event"></i></span>
                            </span>
                            <span class="menu-title">Add</span>
                        </a>
                    </div>

                    <!--begin:Menu item-->
                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{ request()->routeIs('events') ? 'active' : '' }}"
                            href="{{ route('events') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-table" title="List Event"></i></span>
                            </span>
                            <span class="menu-title">List</span>
                        </a>
                        <!--end:Menu link-->
                    </div>


                </div>
                <!--end:Menu sub-->
            </div>

            <div data-kt-menu-trigger="click"
                class="menu-item here  menu-accordion {{ request()->routeIs('specialists', 'scheduled-call', 'member-edit', 'specialists-videos', 'specialists-history') ? 'show' : '' }}">
                <!--begin:Menu link-->
                <span class="menu-link">
                    <span class="menu-icon">
                        <i class="fas fa-volleyball-ball" title="Experts"></i>
                    </span>
                    <span class="menu-title">Experts</span>
                    <span class="menu-arrow"></span>
                </span>
                <!--end:Menu link-->
                <!--begin:Menu sub-->
                <div
                    class="menu-sub menu-sub-accordion {{ request()->routeIs('specialists', 'scheduled-call', 'member-edit', 'specialists-videos', 'specialists-history') ? 'show' : '' }}">

                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('specialists', 'member-edit', 'specialists-videos', 'specialists-history') ? 'active' : '' }}"
                            href="{{ route('specialists') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-table" title="List Experts"></i></span>
                            </span>
                            <span class="menu-title">List</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('scheduled-call') ? 'active' : '' }}"
                            href="{{ route('scheduled-call') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-phone-alt" title="Scheduled Call List"></i></span>
                            </span>
                            <span class="menu-title">Scheduled Call List</span>
                        </a>
                    </div>
                </div>
            </div>


            <div data-kt-menu-trigger="click"
                class="menu-item here  menu-accordion {{ request()->routeIs('add-job', 'jobs', 'job-edit') ? 'show' : '' }}">
                <!--begin:Menu link-->
                <span class="menu-link">
                    <span class="menu-icon">
                        <i class="fal fa-chalkboard-teacher" title="Jobs"></i>
                    </span>
                    <span class="menu-title">Jobs</span>
                    <span class="menu-arrow"></span>
                </span>
                <!--end:Menu link-->
                <!--begin:Menu sub-->
                <div
                    class="menu-sub menu-sub-accordion {{ request()->routeIs('add-job', 'jobs', 'job-edit') ? 'show' : '' }}">

                    <!-- <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('add-job') ? 'active' : '' }}" href="{{ route('add-job') }}">
                                        <span class="menu-bullet">
                                            <span><i class="fas fa-plus p-0" title="Add Job"></i></span>
                                        </span>
                                        <span class="menu-title">Add</span>
                                    </a>
                                </div> -->

                    <!--begin:Menu item-->
                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{ request()->routeIs('jobs', 'job-edit') ? 'active' : '' }}"
                            href="{{ route('jobs') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-table" title="List Jobs"></i></span>
                            </span>
                            <span class="menu-title">List</span>
                        </a>
                        <!--end:Menu link-->
                    </div>

                </div>
                <!--end:Menu sub-->
            </div>

            <div data-kt-menu-trigger="click"
                class="menu-item here  menu-accordion {{ request()->routeIs('add-partner', 'partners', 'partner-edit') ? 'show' : '' }}">
                <!--begin:Menu link-->
                <span class="menu-link">
                    <span class="menu-icon">
                        <i class="far fa-handshake" title="Partners"></i>
                    </span>
                    <span class="menu-title">Partners</span>
                    <span class="menu-arrow"></span>
                </span>
                <!--end:Menu link-->
                <!--begin:Menu sub-->
                <div
                    class="menu-sub menu-sub-accordion {{ request()->routeIs('add-partner', 'partners', 'partner-edit') ? 'show' : '' }}">

                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('add-partner') ? 'active' : '' }}"
                            href="{{ route('add-partner') }}">
                            <span class="menu-bullet">
                                <span><i class="fas fa-plus p-0" title="Add Partner"></i></span>
                            </span>
                            <span class="menu-title">Add</span>
                        </a>
                    </div>

                    <!--begin:Menu item-->
                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{ request()->routeIs('partners', 'partner-edit') ? 'active' : '' }}"
                            href="{{ route('partners') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-table" title="List Partner"></i></span>
                            </span>
                            <span class="menu-title">List</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                </div>
                <!--end:Menu sub-->
            </div>

            <div data-kt-menu-trigger="click"
                class="menu-item here  menu-accordion {{ request()->routeIs('add-chandlery', 'chandlery', 'chandlery-edit') ? 'show' : '' }}">
                <!--begin:Menu link-->
                <span class="menu-link">
                    <span class="menu-icon">
                        <i class="fa-solid fa-sailboat" title="Chandlery"></i>
                    </span>
                    <span class="menu-title">Chandlery</span>
                    <span class="menu-arrow"></span>
                </span>
                <!--end:Menu link-->
                <!--begin:Menu sub-->
                <div
                    class="menu-sub menu-sub-accordion {{ request()->routeIs('add-chandlery', 'chandlery', 'chandlery-edit') ? 'show' : '' }}">

                    <div class="menu-item">
                        <a class="menu-link" href="{{ route('add-chandlery') }}">
                            <span class="menu-bullet">
                                <span><i class="fas fa-plus p-0" title="Add Chandlery"></i></span>
                            </span>
                            <span class="menu-title">Add</span>
                        </a>
                    </div>

                    <!--begin:Menu item-->
                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{ request()->routeIs('chandlery', 'chandlery-edit') ? 'active' : '' }}"
                            href="{{ route('chandlery') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-table" title="List Chandlery"></i></span>
                            </span>
                            <span class="menu-title">List</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                </div>
                <!--end:Menu sub-->
            </div>

            <div data-kt-menu-trigger="click"
                class="menu-item here  menu-accordion  {{ request()->routeIs('add-club-house', 'club-house', 'club-house-edit') ? 'show' : '' }}">
                <!--begin:Menu link-->
                <span class="menu-link">
                    <span class="menu-icon">
                        <i class="fal fa-club" title="Club House"></i>
                    </span>
                    <span class="menu-title">Club House</span>
                    <span class="menu-arrow"></span>
                </span>
                <!--end:Menu link-->
                <!--begin:Menu sub-->
                <div
                    class="menu-sub menu-sub-accordion {{ request()->routeIs('add-club-house', 'club-house', 'club-house-edit') ? 'show' : '' }}">

                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('add-club-house') ? 'active' : '' }}"
                            href="{{ route('add-club-house') }}">
                            <span class="menu-bullet">
                                <span><i class="fas fa-plus p-0" title="Add Club House"></i></span>
                            </span>
                            <span class="menu-title">Add</span>
                        </a>
                    </div>

                    <!--begin:Menu item-->
                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{ request()->routeIs('club-house', 'club-house-edit') ? 'active' : '' }}"
                            href="{{ route('club-house') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-table" title="List Club House"></i></span>
                            </span>
                            <span class="menu-title">List</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                </div>
                <!--end:Menu sub-->
            </div>

            <div data-kt-menu-trigger="click"
                class="menu-item here  menu-accordion {{ request()->routeIs('add-site-pages', 'site-pages', 'other-pages', 'site-page-edit', 'other-page-edit', 'home-page', 'home-page-edit', 'app-pages', 'app-page-edit', 'reach-membership-page', 'membership-page-edit') ? 'show' : '' }}">
                <!--begin:Menu link-->
                <span class="menu-link">
                    <span class="menu-icon">
                        <i class="fal fa-file-alt" title="CMS pages"></i>
                    </span>
                    <span class="menu-title">CMS Pages</span>
                    <span class="menu-arrow"></span>
                </span>
                <!--end:Menu link-->
                <!--begin:Menu sub-->
                <div
                    class="menu-sub menu-sub-accordion {{ request()->routeIs('add-site-pages', 'site-pages', 'other-pages', 'site-page-edit', 'other-page-edit', 'home-page', 'home-page-edit', 'app-pages', 'app-page-edit', 'reach-membership-page', 'membership-page-edit') ? 'show' : '' }}">

                    <!-- <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('add-site-pages') ? 'active' : '' }}" href="{{ route('add-site-pages') }}">
                                        <span class="menu-bullet">
                                            <span><i class="fas fa-plus p-0" title="Add CMS pages"></i></span>
                                        </span>
                                        <span class="menu-title">Add</span>
                                    </a>
                                </div> -->
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('site-pages', 'site-page-edit') ? 'active' : '' }}"
                            href="{{ route('site-pages') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-table" title="List CMS pages"></i></span>
                            </span>
                            <span class="menu-title">List</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('home-page', 'home-page-edit') ? 'active' : '' }}"
                            href="{{ route('home-page') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-table" title="Home Page"></i></span>
                            </span>
                            <span class="menu-title">Home Page</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('app-pages', 'site-page-edit') ? 'active' : '' }}"
                            href="{{ route('app-pages') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-table" title="List CMS pages"></i></span>
                            </span>
                            <span class="menu-title">App Home</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('other-pages', 'other-page-edit') ? 'active' : '' }}"
                            href="{{ route('other-pages') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-table" title="Other pages"></i></span>
                            </span>
                            <span class="menu-title">Other Pages</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('reach-membership-page', 'reach-membership-page-edit') ? 'active' : '' }}"
                            href="{{ route('reach-membership-page') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-table" title="Reach Membership Page"></i></span>
                            </span>
                            <span class="menu-title">Reach Membership Page</span>
                        </a>
                    </div>

                </div>
                <!--end:Menu sub-->
            </div>

            <div data-kt-menu-trigger="click"
                class="menu-item here  menu-accordion {{ request()->routeIs('countries', 'job-role', 'boat-type', 'vessels', 'job-duration', 'boat-location', 'languages', 'qualifications', 'experience', 'availability', 'positions', 'salary-expectations', 'templates', 'job-role-edit', 'boat-type-edit', 'vessel-edit', 'job-duration-edit', 'boat-location-edit', 'language-edit', 'qualification-edit', 'experience-edit', 'availability-edit', 'positions-edit', 'salary-expectations-edit', 'edit-template', 'add-visa', 'visa', 'visa-edit') ? 'show' : '' }}">
                <!--begin:Menu link-->
                <span class="menu-link">
                    <span class="menu-icon">

                        <i class="fal fa-cog" title="Settings"></i>
                    </span>
                    <span class="menu-title">Settings</span>
                    <span class="menu-arrow"></span>
                </span>
                <!--end:Menu link-->
                <!--begin:Menu sub-->
                <div
                    class="menu-sub menu-sub-accordion {{ request()->routeIs('countries', 'visa', 'job-role', 'boat-type', 'vessels', 'job-duration', 'boat-location', 'languages', 'qualifications', 'experience', 'availability', 'positions', 'salary-expectations', 'templates', 'job-role-edit', 'boat-type-edit', 'vessel-edit', 'job-duration-edit', 'boat-location-edit', 'language-edit', 'qualification-edit', 'experience-edit', 'availability-edit', 'positions-edit', 'salary-expectations-edit', 'edit-template', 'add-visa', 'visa-edit') ? 'show' : '' }}">

                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('countries') ? 'active' : '' }}"
                            href="{{ route('countries') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-flag" title="Countries"></i></span>
                            </span>
                            <span class="menu-title">Countries</span>
                        </a>
                    </div>

                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('visa') ? 'active' : '' }}"
                            href="{{ route('visa') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-flag" title="Visa"></i></span>
                            </span>
                            <span class="menu-title">Visa</span>
                        </a>
                    </div>

                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('job-role', 'job-role-edit') ? 'active' : '' }}"
                            href="{{ route('job-role') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-user-md" title="Job Role"></i></span>
                            </span>
                            <span class="menu-title">Job Role</span>
                        </a>
                    </div>

                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('boat-type', 'boat-type-edit') ? 'active' : '' }}"
                            href="{{ route('boat-type') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-ship" title="Boat Type"></i></span>
                            </span>
                            <span class="menu-title">Boat Type</span>
                        </a>
                    </div>

                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('vessels', 'vessel-edit') ? 'active' : '' }}"
                            href="{{ route('vessels') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-ship" title="Vessel Type"></i></span>
                            </span>
                            <span class="menu-title">Vessel Type</span>
                        </a>
                    </div>

                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('job-duration', 'job-duration-edit') ? 'active' : '' }}"
                            href="{{ route('job-duration') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-user-clock" title="Job Duration"></i></span>
                            </span>
                            <span class="menu-title">Job Duration</span>
                        </a>
                    </div>

                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('boat-location', 'boat-location-edit') ? 'active' : '' }}"
                            href="{{ route('boat-location') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-map-marker-alt" title="Boat Location"></i></span>
                            </span>
                            <span class="menu-title">Boat Location</span>
                        </a>
                    </div>

                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('languages', 'language-edit') ? 'active' : '' }}"
                            href="{{ route('languages') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-book" title="Languages"></i></span>
                            </span>
                            <span class="menu-title">Languages</span>
                        </a>
                    </div>

                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('qualifications', 'qualification-edit') ? 'active' : '' }}"
                            href="{{ route('qualifications') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-award" title="Qualifications"></i></span>
                            </span>
                            <span class="menu-title">Qualifications</span>
                        </a>
                    </div>

                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('experience', 'experience-edit') ? 'active' : '' }}"
                            href="{{ route('experience') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-history" title="Experience"></i></span>
                            </span>
                            <span class="menu-title">Experience</span>
                        </a>
                    </div>

                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('availability', 'availability-edit') ? 'active' : '' }}"
                            href="{{ route('availability') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-check" title="Current Availability"></i></span>
                            </span>
                            <span class="menu-title">Current Availability</span>
                        </a>
                    </div>

                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('positions', 'positions-edit') ? 'active' : '' }}"
                            href="{{ route('positions') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-user" title="Positions"></i></span>
                            </span>
                            <span class="menu-title">Positions</span>
                        </a>
                    </div>

                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('salary-expectations', 'salary-expectations-edit') ? 'active' : '' }}"
                            href="{{ route('salary-expectations') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-money-check-dollar" title="Salary Expectations"></i></span>
                            </span>
                            <span class="menu-title">Salary Expectations</span>
                        </a>
                    </div>

                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('templates', 'edit-template') ? 'active' : '' }}"
                            href="{{ route('templates') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-envelope" title="Email Templates"></i></span>
                            </span>
                            <span class="menu-title">Email Templates</span>
                        </a>
                    </div>

                </div>
                <!--end:Menu sub-->
            </div>

            <div data-kt-menu-trigger="click"
                class="menu-item here  menu-accordion {{ request()->routeIs('settings.edit', 'payments', 'membership', 'transfers', 'transaction_history') ? 'show' : '' }}">
                <span class="menu-link">
                    <span class="menu-icon">

                        <i class="fal fa-university" title="Settings"></i>
                    </span>
                    <span class="menu-title">Transactions</span>
                    <span class="menu-arrow"></span>
                </span>
                <div
                    class="menu-sub menu-sub-accordion {{ request()->routeIs('settings.edit', 'payments', 'membership', 'transfers', 'transaction_history') ? 'show' : '' }}">

                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('settings.edit') ? 'active' : '' }}"
                            href="{{ route('settings.edit') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-money-check-dollar" title="Payment Settings"></i></span>
                            </span>
                            <span class="menu-title">Payment Settings</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('payments') ? 'active' : '' }}"
                            href="{{ route('payments') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-money-check-dollar" title="Booking Payments"></i></span>
                            </span>
                            <span class="menu-title">Booking Payments</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('membership') ? 'active' : '' }}"
                            href="{{ route('membership') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-money-check-dollar" title="Membership Payments"></i></span>
                            </span>
                            <span class="menu-title">Membership Payments</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('transfers') ? 'active' : '' }}"
                            href="{{ route('transfers') }}">
                            <span class="menu-bullet">
                                <span><i class="fa fa-exchange" title="Booking Transfers"></i></span>
                            </span>
                            <span class="menu-title">Booking Transfers</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('transaction_history') ? 'active' : '' }}"
                            href="{{ route('transaction_history') }}">
                            <span class="menu-bullet">
                                <span><i class="fa fa-exchange" title="Transaction History"></i></span>
                            </span>
                            <span class="menu-title">Transaction History</span>
                        </a>
                    </div>
                    <!-- <div class="menu-item">
                                    <a class="menu-link" href="">
                                        <span class="menu-bullet">
                                            <span><i class="fal fa-stripe" title="Stripe Settings"></i></span>
                                        </span>
                                        <span class="menu-title">Stripe Settings</span>
                                    </a>
                                </div> -->

                </div>
            </div>

            <div data-kt-menu-trigger="click"
                class="menu-item here  menu-accordion {{ request()->routeIs('reported_list') ? 'show' : '' }}">
                <!--begin:Menu link-->
                <span class="menu-link">
                    <span class="menu-icon">
                        <i class="fal fa-chalkboard-teacher" title="Jobs"></i>
                    </span>
                    <span class="menu-title">Reported List</span>
                    <span class="menu-arrow"></span>
                </span>
                <!--end:Menu link-->
                <!--begin:Menu sub-->
                <div class="menu-sub menu-sub-accordion {{ request()->routeIs('reported_list') ? 'show' : '' }}">


                    <!--begin:Menu item-->
                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{ request()->routeIs('reported_list') ? 'active' : '' }}"
                            href="{{ route('reported_list') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-table" title="Reported List"></i></span>
                            </span>
                            <span class="menu-title">Reported List</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                </div>
                <!--end:Menu sub-->
            </div>
            <div data-kt-menu-trigger="click"
                class="menu-item here  menu-accordion {{ request()->routeIs('announcement_list') ? 'show' : '' }}">
                <!--begin:Menu link-->
                <span class="menu-link">
                    <span class="menu-icon">
                        <i class="fal fa-bullhorn" title="Announcement"></i>
                    </span>
                    <span class="menu-title">Announcement </span>
                    <span class="menu-arrow"></span>
                </span>
                <!--end:Menu link-->
                <!--begin:Menu sub-->
                <div class="menu-sub menu-sub-accordion {{ request()->routeIs('announcement_list') ? 'show' : '' }}">


                    <!--begin:Menu item-->
                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{ request()->routeIs('announcement_list') ? 'active' : '' }}"
                            href="{{ route('announcement_list') }}">
                            <span class="menu-bullet">
                                <span><i class="fal fa-bullhorn" title="Announcement List"></i></span>
                            </span>
                            <span class="menu-title"> List</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                </div>
                <!--end:Menu sub-->
            </div>

        </div>
        <!--end::Menu-->
    </div>
    <!--end::Menu wrapper-->
</div>
@endif