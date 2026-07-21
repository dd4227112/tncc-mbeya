<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

  <meta charset="utf-8" />
  <title>{{ config('app.name', 'Tncc-Mbeya') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta content="Tanzania National Chamber of Commerce (TNCC)" name="description" />
  <meta
    content="Tanzania National Chamber of Commerce was established in 1988.The establishment of the TNCC was an important step in moving on from a centralized, planned economy towards a more open, mixed economy giving full scope to privately owned enterprises and farms."
    name="description">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- App favicon -->
  <link rel="shortcut icon" href="{{ asset('assets/images/tncc-logo.png') }}">

  <!-- flatpickr css -->
  <link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css">

  <!-- Sweet Alert-->
  <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

  <!-- DataTables -->
  <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
    type="text/css" />

  <!-- Responsive datatable examples -->
  <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}"
    rel="stylesheet" type="text/css" />
  <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">


  <!-- Bootstrap Css -->
  <link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
  <!-- Icons Css -->
  <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
  <!-- Toastr -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <!-- App Css-->
  <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
  <style>
    .search-box {
      position: relative;
    }

    .search-box .search-icon {
      position: absolute;
      top: 12px;
      left: 14px;
      font-size: 16px;
      color: var(--minia-text-muted);
    }

    .search-box input {
      padding-left: 40px;
    }

    .form-control:focus {
      border-color: #a6b0cf;
      box-shadow: none;
    }

    .search-results {
      position: absolute;
      z-index: 20;
      top: 100%;
      left: 0;
      right: 0;
      margin-top: 4px;
      background: #fff;
      border: 1px solid var(--minia-card-border);
      border-radius: 0.25rem;
      box-shadow: 0 4px 16px rgba(33, 37, 41, 0.12);
      max-height: 260px;
      overflow-y: auto;
      display: none;
    }

    .search-results.show {
      display: block;
    }

    .search-result-item {
      padding: 10px 14px;
      cursor: pointer;
      border-bottom: 1px solid var(--minia-card-border);
    }

    .search-result-item:last-child {
      border-bottom: none;
    }

    .search-result-item:hover,
    .search-result-item.active {
      background-color: rgba(var(--minia-primary-rgb), 0.08);
    }

    .search-result-item .item-name {
      font-weight: 600;
      font-size: 14px;
      color: #495057;
    }

    .search-result-item .item-sub {
      font-size: 12.5px;
      color: var(--minia-text-muted);
    }

    .search-result-empty {
      padding: 14px;
      font-size: 13px;
      color: var(--minia-text-muted);
      text-align: center;
    }

    .member-card {
      border: 1px dashed #ced4da;
      border-radius: 0.25rem;
      padding: 1.25rem;
      min-height: 140px;
    }

    .member-card.has-member {
      border-style: solid;
      border-color: var(--minia-card-border);
      background-color: #fbfbfd;
    }

    .member-placeholder {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100%;
      min-height: 92px;
      color: var(--minia-text-muted);
      font-size: 13.5px;
      text-align: center;
    }

    .avatar-title {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background-color: rgba(var(--minia-primary-rgb), 0.15);
      color: var(--minia-primary);
      font-weight: 600;
    }

    .avatar-sm {
      height: 3rem;
      width: 3rem;
      border-radius: 50%;
      font-size: 18px;
    }

    .badge-soft-success {
      background-color: rgba(52, 195, 143, 0.12);
      color: var(--minia-success);
      font-weight: 500;
    }

    table.crop-table th {
      font-size: 12.5px;
      text-transform: uppercase;
      letter-spacing: 0.02em;
      color: var(--minia-text-muted);
      font-weight: 600;
      border-top: none;
      border-bottom: 1px solid var(--minia-card-border);
    }

    table.crop-table td {
      vertical-align: middle;
      border-color: var(--minia-card-border);
    }

    .qty-input {
      width: 80px;
    }

    .empty-row td {
      text-align: center;
      color: var(--minia-text-muted);
      padding: 2.25rem 0;
      font-size: 13.5px;
    }

    .btn-remove-row {
      border: none;
      background: transparent;
      color: var(--minia-danger);
      font-size: 18px;
      line-height: 1;
      padding: 4px 6px;
    }

    .btn-remove-row:hover {
      color: #d84a4a;
    }

    .summary-table td {
      padding: 0.4rem 0;
      font-size: 14px;
    }

    .summary-table tr.total-row td {
      border-top: 1px solid var(--minia-card-border);
      padding-top: 0.75rem;
      font-size: 15px;
      font-weight: 600;
      color: #343a40;
    }

    .section-label {
      font-size: 15px;
      font-weight: 600;
      color: #343a40;
      margin-bottom: 0.75rem;
    }

    .logo-txt {
      font-size: 18px;
      font-weight: 600;
      color: var(--minia-primary);
      vertical-align: middle;
      margin-left: 6px;
    }

    @media (max-width: 575.98px) {
      .qty-input {
        width: 70px;
      }
    }
  </style>

</head>

<body>
  <!-- Begin page -->
  <div id="layout-wrapper">


    <header id="page-topbar">
      <div class="navbar-header">
        <div class="d-flex">
          <!-- LOGO -->
          <div class="navbar-brand-box">
            <a href="{{ route('dashboard') }}" class="logo logo-dark">
              <span class="logo-sm">
                <img src="{{ asset('assets/images/tncc-logo.png') }}" alt="" height="24">
              </span>
              <span class="logo-lg">
                <img src="{{ asset('assets/images/tncc-logo.png') }}" alt="" height="24"> <span
                  class="logo-txt">Tncc- Mbeya</span>
              </span>
            </a>

            <a href="{{ route('dashboard') }}" class="logo logo-light">
              <span class="logo-sm">
                <img src="{{ asset('assets/images/tncc-logo.png') }}" alt="" height="24">
              </span>
              <span class="logo-lg">
                <img src="{{ asset('assets/images/tncc-logo.png') }}" alt="" height="24"> <span
                  class="logo-txt">Tncc- Mbeya</span>
              </span>
            </a>
          </div>

          <button type="button" class="btn btn-sm px-3 font-size-16 header-item" id="vertical-menu-btn">
            <i class="fa fa-fw fa-bars"></i>
          </button>
        </div>

        <div class="d-flex">
          <div class="dropdown d-none d-sm-inline-block">
            <button type="button" class="btn header-item" data-bs-toggle="dropdown" aria-haspopup="true"
              aria-expanded="false">
              <img id="header-lang-img" src="{{ asset('assets/images/flags/us.jpg') }}" alt="Header Language"
                height="16">
            </button>
            <div class="dropdown-menu dropdown-menu-end">

              <!-- item-->
              <a href="javascript:void(0);" class="dropdown-item notify-item language" data-lang="en">
                <img src="{{ asset('assets/images/flags/us.jpg') }}" alt="user-image" class="me-1"
                  height="12">
                <span class="align-middle">English</span>
              </a>
              <!-- item-->
              <a href="javascript:void(0);" class="dropdown-item notify-item language" data-lang="sp">
                <img src="{{ asset('assets/images/flags/tanzania.png') }}" alt="user-image" class="me-1"
                  height="12"> <span class="align-middle">Kiswahili</span>
              </a>
            </div>
          </div>

          <div class="dropdown d-none d-sm-inline-block">
            <button type="button" class="btn header-item" id="mode-setting-btn">
              <i data-feather="moon" class="icon-lg layout-mode-dark"></i>
              <i data-feather="sun" class="icon-lg layout-mode-light"></i>
            </button>
          </div>

          <div class="dropdown d-inline-block">
            <button type="button" class="btn header-item bg-soft-light border-start border-end"
              id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <img class="rounded-circle header-profile-user" src="{{ asset('assets/images/tncc-logo.png') }}"
                alt="Header Avatar">
              <span
                class="d-none d-xl-inline-block ms-1 fw-medium">{{ Auth::user()->first_name . ' ' . Auth::user()->last_name }}</span>
              <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end">
              <!-- item-->
              <a class="dropdown-item show-profile" href="#"><i
                  class="mdi mdi-face-profile font-size-16 align-middle me-1"></i> Profile</a>
              <a class="dropdown-item" href="#" data-bs-toggle="modal"
                data-bs-target=".change-password-modal"><i class="mdi mdi-lock font-size-16 align-middle me-1"></i>
                Change Password</a>
              <div class="dropdown-divider"></div>
              <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                @csrf
                <button type="submit" class="dropdown-item text-start border-0 bg-transparent w-100">
                  <i class="mdi mdi-logout font-size-16 align-middle me-1"></i> Logout
                </button>
              </form>
            </div>
          </div>

        </div>
      </div>
    </header>
    @include('auth.change-password')
    @include('pages.users.profile')
