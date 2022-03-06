<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
      </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge"><p id="notification_count_top"></p></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right" id="notification_box" style="min-width: 400px!important;">
                
            </div>
        </li>

        <li class="nav-item d-none d-sm-inline-block">
            <a class="nav-link" href="{{ route('profile') }}">
                <i class="fas fa-user-circle"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a class="nav-link" data-toggle="dropdown" href="javascript:void" onclick="$('#logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </li>
    </ul>
</nav>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
