<aside class="main-sidebar sidebar-dark-primary elevation-1">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <img src="{{ asset('images/beligood-logo.png') }}" alt="Logo" style="width: 235px;">
      <!-- <span class="brand-text font-weight-light">Beligood</span> -->
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{ asset('images/profile.png') }}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

          <!-- <li class="nav-item">
            <a href="pages/widgets.html" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Dashboard
                <span class="right badge badge-danger">New</span>
              </p>
            </a>
          </li> -->
          <li class="nav-item {{ request()->is('forumposts') || request()->is('admin') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tasks"></i>
              <p>
                Manage
                <i class="fas fa-angle-left right"></i>
                <!-- <span class="badge badge-info right">5</span> -->
              </p>
            </a>
            <ul class="nav nav-treeview bypart-style">

              <li class="nav-item">
                <a href="{{ url('forumposts') }}" class="nav-link {{ request()->is('forumposts') ? 'active' : '' }}">
                  <i class="fas fa-comments nav-icon"></i>
                  <p>Forum Management</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ url('admin') }}" class="nav-link {{ request()->is('admin') ? 'active' : '' }}">
                  <i class="fas fa-user-shield nav-icon"></i>
                  <p>Admin Management</p>
                </a>
              </li>

            </ul>
          </li>
          <li class="nav-item {{ request()->is('classified') || request()->is('auction')?'menu-open' : '' }}">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tasks"></i>
              <p>
                Listing Management
                <i class="fas fa-angle-left right"></i>
                <!-- <span class="badge badge-info right">2</span> -->
              </p>
            </a>
            <ul class="nav nav-treeview bypart-style">
              <li class="nav-item">
                <a href="{{ url('classified') }}" class="nav-link {{ request()->is('classified') ? 'active' : '' }}">
                  <i class="fas fa-tasks nav-icon"></i>
                  <p>Classified Listing</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ url('auction') }}" class="nav-link" class="nav-link {{ request()->is('auction') ? 'active' : '' }}">
                  <i class="fas fa-tasks nav-icon"></i>
                  <p>Auction Listing</p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item {{ request()->is('userpointsystem') || request()->is('agentpointsystem') || request()->is('dealerpointsystem') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tasks"></i>
              <p>
                Point Management
                <i class="fas fa-angle-left right"></i>
                <!-- <span class="badge badge-info right">3</span> -->
              </p>
            </a>
            <ul class="nav nav-treeview bypart-style">
                <li class="nav-item">
                  <a href="{{ url('userpointsystem') }}"  class="nav-link {{ request()->is('userpointsystem') ? 'active' : '' }}">
                    <i class="fas fa-hand-holding-usd nav-icon"></i>
                    <p>Private</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('agentpointsystem') }}" class="nav-link {{ request()->is('agentpointsystem') ? 'active' : '' }}">
                    <i class="fas fa-hand-holding-usd nav-icon"></i>
                    <p>Agent/Broker</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ url('dealerpointsystem') }}" class="nav-link {{ request()->is('dealerpointsystem') ? 'active' : '' }}">
                    <i class="fas fa-hand-holding-usd nav-icon"></i>
                    <p>Dealer</p>
                  </a>
                </li>
            </ul>
          </li>

          <li class="nav-item {{ request()->is('users') || request()->is('agents') || request()->is('dealer') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tasks"></i>
              <p>
                User Management
                <i class="fas fa-angle-left right"></i>
                <!-- <span class="badge badge-info right">3</span> -->
              </p>
            </a>
            <ul class="nav nav-treeview bypart-style">
              <li class="nav-item">
                <a href="{{ url('users') }}" class="nav-link {{ request()->is('users') ? 'active' : '' }}">
                  <i class="fas fa-users nav-icon"></i>
                  <p>Private Users</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ url('agents') }}" class="nav-link {{ request()->is('agents') ? 'active' : '' }}">
                  <i class="fas fa-users nav-icon"></i>
                  <p>Agent/Broker</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ url('dealer') }}" class="nav-link {{ request()->is('dealer') ? 'active' : '' }}">
                  <i class="fas fa-users nav-icon"></i>
                  <p>Dealers</p>
                </a>
              </li>
            </ul>
          </li>
          <!-- <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-chart-pie"></i>
              <p>
                Tracking
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview bypart-style">
              <li class="nav-item">
                <a href="pages/charts/chartjs.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Classified</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages/charts/flot.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Auction</p>
                </a>
              </li>

            </ul>
          </li> -->
          <li class="nav-item">
            <a href="{{ url('makes') }}" class="nav-link {{ request()->is('makes') ? 'active' : '' }}">
                <i class="nav-icon fas fa-car"></i>
              <p>
                Makes & Model
              </p>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{ url('makerequests') }}" class="nav-link {{ request()->is('makerequests') ? 'active' : '' }}">
                <i class="nav-icon fas fa-car"></i>
              <p>
                Makes Requests
              </p>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{ url('categories') }}" class="nav-link {{ request()->is('categories') ? 'active' : '' }}">
                <i class="nav-icon fas fa-car"></i>
              <p>
                Directory Categories
              </p>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{ url('dynamicimagessettings') }}" class="nav-link {{ request()->is('dynamicimagessettings') ? 'active' : '' }}">
                <i class="nav-icon fas fa-car"></i>
              <p>
                Dynamic Image Settings
              </p>
            </a>
          </li>
          <!-- <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-file-invoice-dollar"></i>
              <p>
                Reports
              </p>
            </a>
          </li> -->
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
