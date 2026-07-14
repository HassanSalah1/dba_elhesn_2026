<!-- BEGIN: Vendor CSS-->
@if ($configData['direction'] === 'rtl' && isset($configData['direction']))
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/vendors-rtl.min.css')) }}"/>
@else
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/vendors.min.css')) }}"/>
@endif
<link rel="stylesheet" type="text/css"
      href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    #toast-container>div {
        opacity: unset;
    }
    #toast-container>.toast-success {
        background-color: #28c76f;
    }

    #toast-container>.toast-error {
        background-color: #e05a63;
    }
</style>


@yield('vendor-style')
<!-- END: Vendor CSS-->

<!-- BEGIN: Theme CSS-->
<link rel="stylesheet" href="{{ asset(mix('css/core.css')) }}"/>
<link rel="stylesheet" href="{{ asset(mix('css/base/themes/dark-layout.css')) }}"/>
<link rel="stylesheet" href="{{ asset(mix('css/base/themes/bordered-layout.css')) }}"/>
<link rel="stylesheet" href="{{ asset(mix('css/base/themes/semi-dark-layout.css')) }}"/>

@php $configData = Helper::applClasses(); @endphp

<!-- BEGIN: Page CSS-->
@if ($configData['mainLayoutType'] === 'horizontal')
    <link rel="stylesheet" href="{{ asset(mix('css/base/core/menu/menu-types/horizontal-menu.css')) }}"/>
@else
    <link rel="stylesheet" href="{{ asset(mix('css/base/core/menu/menu-types/vertical-menu.css')) }}"/>
@endif

{{-- Page Styles --}}
@yield('page-style')

<!-- laravel style -->
<link rel="stylesheet" href="{{ asset(mix('css/overrides.css')) }}"/>

<!-- BEGIN: Custom CSS-->

@if ($configData['direction'] === 'rtl' && isset($configData['direction']))
    <link rel="stylesheet" href="{{ asset(mix('css-rtl/custom-rtl.css')) }}"/>
    <link rel="stylesheet" href="{{ asset(mix('css-rtl/style-rtl.css')) }}"/>

@else
    {{-- user custom styles --}}
    <link rel="stylesheet" href="{{ asset(mix('css/style.css')) }}"/>
@endif

<style>
    /* ================================================================
       Dba Elhesn — Global Theme Overrides from dh_logo.png
       Primary:   #1B7340  (Green - shield & palms)
       Secondary: #6B3427  (Brown - tower)
       Accent:    #3B5998  (Navy - waves)
       ================================================================ */

    /* Primary color utilities */
    .text-primary {
        color: #1B7340 !important;
    }
    .bg-primary {
        background-color: #1B7340 !important;
    }
    .bg-light-primary {
        background-color: rgba(27, 115, 64, 0.12) !important;
        color: #1B7340 !important;
    }
    .border-primary {
        border-color: #1B7340 !important;
    }

    /* Primary buttons */
    .btn-primary {
        border-color: #1B7340 !important;
        background-color: #1B7340 !important;
        color: #ffffff !important;
    }
    .btn-primary:hover,
    .btn-primary:focus,
    .btn-primary:active,
    .btn-primary.active {
        border-color: #22905a !important;
        background-color: #22905a !important;
        color: #ffffff !important;
    }
    .btn-outline-primary {
        border-color: #1B7340 !important;
        color: #1B7340 !important;
    }
    .btn-outline-primary:hover,
    .btn-outline-primary:active,
    .btn-outline-primary:focus {
        background-color: #1B7340 !important;
        color: #ffffff !important;
    }

    /* Sidebar Active Menu highlight override */
    .vertical-layout.vertical-menu-modern .main-menu .navigation .active,
    .main-menu.menu-light .navigation > li.active > a,
    .main-menu.menu-light .navigation .navigation-main .active a,
    .main-menu.menu-dark .navigation > li.active > a,
    .main-menu.menu-light .navigation > li.active {
        background: linear-gradient(118deg, #1B7340, #22905a) !important;
        box-shadow: 0 4px 10px 0 rgba(27, 115, 64, 0.3) !important;
        color: #ffffff !important;
    }

    .main-menu.menu-light .navigation > li.active > a i,
    .main-menu.menu-light .navigation > li.active > a svg,
    .main-menu.menu-light .navigation > li.active > a span,
    .navigation-main .active a span,
    .navigation-main .active a i,
    .navigation-main .active a svg,
    .main-menu.menu-light .navigation > li.active i,
    .main-menu.menu-light .navigation > li.active svg,
    .main-menu.menu-light .navigation > li.active span {
        color: #ffffff !important;
        stroke: #ffffff !important;
        fill: transparent !important;
    }

    /* Brand text in sidebar header */
    .main-menu .brand-text {
        color: #1B7340 !important;
    }

    /* Nav toggle icon */
    .modern-nav-toggle svg,
    .modern-nav-toggle i,
    .nav-link.modern-nav-toggle .text-primary {
        color: #1B7340 !important;
        stroke: #1B7340 !important;
    }

    /* Badges */
    .badge-light-primary {
        background-color: rgba(27, 115, 64, 0.12) !important;
        color: #1B7340 !important;
    }

    /* Form focus state */
    .form-control:focus {
        border-color: #1B7340 !important;
        box-shadow: 0 3px 8px 0 rgba(27, 115, 64, 0.15) !important;
    }
    .form-check-input:checked {
        background-color: #1B7340 !important;
        border-color: #1B7340 !important;
    }
    .form-check-input:focus {
        border-color: #1B7340 !important;
        box-shadow: 0 0 0 0.2rem rgba(27, 115, 64, 0.25) !important;
    }

    /* Nav pills / tabs */
    .nav-pills .nav-link.active {
        background-color: #1B7340 !important;
        box-shadow: 0 4px 10px rgba(27, 115, 64, 0.25) !important;
    }
    .nav-tabs .nav-link.active {
        border-color: #1B7340 !important;
        color: #1B7340 !important;
    }

    /* Dropdowns */
    .dropdown-item:hover,
    .dropdown-item:focus {
        color: #1B7340 !important;
        background-color: rgba(27, 115, 64, 0.08) !important;
    }
    .dropdown-item.active,
    .dropdown-item:active {
        background-color: #1B7340 !important;
    }

    /* Pagination */
    .page-item.active .page-link {
        background-color: #1B7340 !important;
        border-color: #1B7340 !important;
    }
    .page-link:hover {
        color: #1B7340 !important;
    }

    /* Breadcrumb */
    .breadcrumb-item a:hover {
        color: #1B7340 !important;
    }

    /* Toast */
    #toast-container > .toast-success {
        background-color: #1B7340 !important;
    }

    /* Links */
    a:hover {
        color: #1B7340;
    }

    /* Selection highlight */
    ::selection {
        background-color: rgba(27, 115, 64, 0.15);
    }
</style>

