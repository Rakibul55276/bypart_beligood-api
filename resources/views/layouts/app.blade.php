<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Beligood Admin</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"rel="stylesheet">

    <!-- Datatables -->

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">

    <!-- Icons -->
    <link href="https://unpkg.com/ionicons@4.5.10-0/dist/css/ionicons.min.css" rel="stylesheet">

</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div id="wrapper">
         <!-- Navbar -->
         @include('layouts.nav')
         <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @include('layouts.sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            @yield('content')
        </div>

        <!-- Footer -->
        @include('layouts.footer')
    </div>


    <!-- REQUIRED SCRIPTS -->
    <script src="{{  asset('js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

    <!-- Datatables JS CDN -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function(){

            $.ajax({
              type: 'post',
              url: "{{ url('notifications') }}",
              success: function (data) {
                var data=data.data;
                var count=data.count;
                var noti_list=data.data;

                $("#notification_count_top").html(count);

                var list_html='';

                for (var i = 0; i < noti_list.length; i++) {
                    var content=noti_list[i].notification_content;
                    var url=noti_list[i].url;

                    list_html=list_html+'<div class="dropdown-divider"></div><a href="'+url+'" target="_blank" class="dropdown-item"><div style="white-space: pre-wrap;">'+content+'</div></a>';
                }

                list_html=list_html+'<div class="dropdown-divider"></div><div class="dropdown-divider"></div><a href="{{ url('allnotifications') }}" class="dropdown-item dropdown-footer" target="_blank">See All Notifications</a>';
                
                $("#notification_box").html(list_html);
              }
            });
        });
    </script>
    @yield('javascript')
</body>

</html>
