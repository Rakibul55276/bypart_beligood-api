@extends('layouts.app')

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-info text-white"><b>Comment & Reply List</b></div>

            <div class="row" style="margin-top: 10px;">
              <div class="col-md-6">
                <label>Post Category</label>
                <input class="form-control" type="text" value="{{$post_details[0]->category_name}}" readonly>
              </div>

              <div class="col-md-6">
                <label>Post Subject</label>
                <input class="form-control" type="text" value="{{$post_details[0]->subject}}" readonly>
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
                            <td><b>Comment</b></td>
                            <td><b>Images</b></td>
                            <td><b>Commented By</b></td>
                            <td><b>Datetime</b></td>
                            <td><b>Report Trace</b></td>
                            <td><b>Delete Status</b></td>
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
                url: "{{route('getComments')}}",
                data: function(data) {
                     data.post_id = '{{$post_id}}';
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
                {"targets": 7,"orderable": false}
              ],
             columns: [
                { data: 'id' },
                { data: 'comment' },
                { data: 'images' },
                { data: 'first_name' },
                { data: 'created_at' },
                { data: 'report_text' },
                { data: 'is_deleted' },
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
