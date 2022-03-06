@extends('layouts.app')

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-info text-white"><b>Dealers List</b></div>
            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <table id='userTable' class="table table-striped table-bordered" width='100%' border="1" style='border-collapse: collapse;'>
                    <thead style="background-color: grey;">
                        <tr>
                            <td><b>#</b></td>
                            <td><b>User Name</b></td>
                            <td><b>Contact Number</b></td>
                            <td><b>Address</b></td>
                            <td><b>Email</b></td>
                            <td><b>Status Admin</b></td>
                            <td><b>Action</b></td>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="{{  asset('js/usermanagement.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function(){

          // DataTable
          $('#userTable').DataTable({
             processing: true,
             serverSide: true,
             ajax: {
                url: "{{route('getUsers')}}",
                data: function(data) {
                     data.user_type = 'dealer';
                 }
              },
             dom: "lBfrtip",
             buttons : [{
                  extend : 'excelHtml5',
                  text: '<span class="material-icons" style="padding-left:10px;">file_download</span>',
                  titleAttr: 'Export to Excel',
                  title: 'Dealers List',
                  exportOptions : {
                      columns: ':not(:last-child)',
                  }
              }],
              columnDefs: [
                {"targets": 6,"orderable": false}
              ],
             columns: [
                { data: 'id' },
                { data: 'first_name' },
                { data: 'mobile_no' },
                { data: 'address' },
                { data: 'email' },
                { data: 'status_admin' },
                { data: 'action_html' },
             ]
          });

          $('select[name="userTable_length"]').append($('<option>', {
              value: 500,
              text: '500'
          }));

        });
    </script>

@endsection
