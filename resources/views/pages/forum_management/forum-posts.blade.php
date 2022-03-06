@extends('layouts.app')

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-info text-white"><b>Forum Post List</b></div>

            <div class="row" style="padding: 20px;">
              <div class="col-md-6">
                  <label>Category Selection:</label>
              </div>
              <div class="col-md-6">
                  <select class="form-control" id="forum_category" onchange="fetchByCategory();">
                      <option value="" selected>All</option>
                      @foreach($forum_category_list as $f_cat)
                        <option value="{{$f_cat->id}}">{{$f_cat->name}}</option>
                      @endforeach
                  </select>
              </div>
            </div>
            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <table id='forumTable' class="table table-striped table-bordered" width='100%' border="1" style='border-collapse: collapse;'>
                    <thead style="background-color: grey;">
                        <tr>
                            <td><b>#</b></td>
                            <td><b>Category</b></td>
                            <td><b>Subject</b></td>
                            <td><b>Author</b></td>
                            <td><b>Report Trace</b></td>
                            <td><b>Action</b></td>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div id="edit_modal_append">
      
    </div>
@endsection

@section('javascript')
    <script src="{{  asset('js/forummanagement.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function(){

          // DataTable
          $('#forumTable').DataTable({
             processing: true,
             serverSide: true,
             ajax: {
                url: "{{route('getPosts')}}",
                data: function(data) {
                     data.category = '';
                 }
              },
             dom: "lBfrtip",
             buttons : [{
                  extend : 'excelHtml5',
                  text: '<span class="material-icons" style="padding-left:10px;">file_download</span>',
                  titleAttr: 'Export to Excel',
                  title: 'Forum Post List',
                  exportOptions : {
                      columns: ':not(:last-child)',
                  }
              }],
             columnDefs: [ 
                {"targets": 5,"orderable": false}
              ],
             columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'subject' },
                { data: 'first_name' },
                { data: 'report_text' },
                { data: 'action_html' },
             ]
          });

          $('select[name="forumTable_length"]').append($('<option>', {
              value: 500,
              text: '500'
          }));

        });


        function fetchByCategory(){

              var category=$("#forum_category").val();

              $('#forumTable').DataTable().clear().destroy();

              $('#forumTable').DataTable({
             processing: true,
             serverSide: true,
             ajax: {
                url: "{{route('getPosts')}}",
                data: function(data) {
                     data.category = category;
                 }
              },
             dom: "lBfrtip",
             buttons : [{
                  extend : 'excelHtml5',
                  text: '<span class="material-icons" style="padding-left:10px;">file_download</span>',
                  titleAttr: 'Export to Excel',
                  title: 'Forum Post List',
                  exportOptions : {
                      columns: ':not(:last-child)',
                  }
              }],
              columnDefs: [ 
                {"targets": 0,"orderable": false}
              ],
             columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'subject' },
                { data: 'first_name' },
                { data: 'action_html' },
             ]
          });

          $('select[name="forumTable_length"]').append($('<option>', {
              value: 500,
              text: '500'
          }));

        }
    </script>

@endsection
