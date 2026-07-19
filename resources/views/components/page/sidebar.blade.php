<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

  <div data-simplebar class="h-100">

    <!--- Sidemenu -->
    <div id="sidebar-menu">
      <!-- Left Menu Start -->
      <ul class="metismenu list-unstyled" id="side-menu">
        <li class="menu-title" data-key="t-menu">Menu</li>

        <li>
          <a href="{{ route('dashboard') }}">
            <i data-feather="home"></i>
            <span data-key="t-dashboard">Dashboard</span>
          </a>
        </li>
        <li>
          <a href="{{ route('invoices.index') }}">
            <i data-feather="file-text"></i>
            <span data-key="t-horizontal">Invoices</span>
          </a>
        </li>

        <li>
          <a href="{{route('payments.index')}}">
            <i data-feather="cpu"></i>
            <span data-key="t-horizontal">Payments</span>
          </a>
        </li>
        <li>
          <a href="{{ route('crops.index') }}">
            <i data-feather="box"></i>
            <span data-key="t-horizontal">Crops</span>
          </a>
        </li>
        <li>
          <a href="{{ route('units.index') }}">
            <i data-feather="layout"></i>
            <span data-key="t-horizontal">Units</span>
          </a>
        </li>


        <li>
          <a href="javascript: void(0);" class="has-arrow">
            <i data-feather="users"></i>
            <span data-key="t-components">People</span>
          </a>
          <ul class="sub-menu" aria-expanded="false">
            <li><a href="{{ route('members.index') }}" data-key="t-alerts">Members</a></li>
            <li><a href="{{ route('staffs.index') }}" data-key="t-buttons">Staff</a></li>
          </ul>
        </li>

        <li>
          <a href="javascript: void(0);" class="has-arrow">
            <i data-feather="pie-chart"></i>
            <span data-key="t-reports">Reports</span>
          </a>
          <ul class="sub-menu" aria-expanded="false">
            <li><a href="{{ route('reports.collection') }}" data-key="t-form-elements">Collection Report</a></li>
            {{-- <li><a href="{{ route('reports.payments') }}" data-key="t-form-validation">Payments Report</a></li> --}}
            <li><a href="{{ route('reports.crop_performance-report') }}" data-key="t-form-advanced">Crop Performance Report</a></li>
            {{-- <li><a href="{{ route('reports.crops') }}" data-key="t-form-editors">Crops</a></li> --}}
          </ul>
        </li>
      </ul>
    </div>
    <!-- Sidebar -->
  </div>
</div>
<div class="main-content">
