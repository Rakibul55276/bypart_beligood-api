@extends('layouts.app')

@section('content')
    <div class="col-md-12">
        <div class="row" style="padding:10px;">
          <div class="col-md-10">
            
          </div>
          <div class="col-md-2">
            <button class="btn btn-primary" onclick="addCompanyModal();"> Add Company</button>
          </div>
          
        </div>
        <div class="card">
            <div class="card-header bg-info text-white"><b>{{$sub_category_name}} - Company List</b></div>

            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <table id='companyTable' class="table table-striped table-bordered" width='100%' border="1" style='border-collapse: collapse;'>
                    <thead style="background-color: grey;">
                        <tr>
                            <td><b>#</b></td>
                            <td><b>Name</b></td>
                            <td><b>Description</b></td>
                            <td><b>Email</b></td>
                            <td><b>Phone</b></td>
                            <td><b>Address</b></td>
                            <td><b>City</b></td>
                            <td><b>State</b></td>
                            <td><b>Logo</b></td>
                            <td><b>URL</b></td>
                            <td><b>Action</b></td>
                        </tr>
                    </thead>
                    
                </table>
            </div>
        </div>
    </div>

    <div id="edit_modal_append">
      
    </div>

    <div class="modal fade" id="edit_logo_modal" tabindex="-1" role="dialog" aria-labelledby="edit_logo_modalLabel" aria-hidden="true" style="overflow-y: scroll;">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="edit_logo_modalLabel"><b>Edit Logo</b>  <span><i><p id="company_name_in_modal"></p></i></span></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
              <form>
                  <div class="form-group">
                      <input type="file" name="uploadlogo" class="form-control" id="upload_logo">
                  </div>
                
            </form>
          </div>
          <div class="modal-footer">
            <input type="hidden" id="icon_edit_company_id">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="uploadLogo();">Update Logo</button>
          </div>
        </div>
      </div>
    </div>
@endsection

@section('javascript')

    <script type="text/javascript">

       $(document).ready(function(){

          // DataTable
            $('#companyTable').DataTable({
               processing: true,
               serverSide: true,
               ajax: {
                  url: "{{route('getCompanies')}}",
                  data: function(data) {
                       data.sub_category_id = '{{$sub_category_id}}';
                   }
                },
               dom: "lBfrtip",
               buttons : [{
                    extend : 'excelHtml5',
                    text: '<span class="material-icons" style="padding-left:10px;">file_download</span>',
                    titleAttr: 'Export to Excel',
                    title: 'Agents List',
                    exportOptions : {
                        columns: ':not(:last-child)',
                    }
                }],
                columnDefs: [ 
                  {"targets": 8,"orderable": false},
                  {"targets": 9,"orderable": false},
                  {"targets": 10,"orderable": false},
                ],
               columns: [
                  { data: 'id' },
                  { data: 'company_name' },
                  { data: 'company_description' },
                  { data: 'company_email' },
                  { data: 'company_phone_number' },
                  { data: 'address' },
                  { data: 'state' },
                  { data: 'city'},
                  { data: 'logo' },
                  { data: 'url' },
                  { data: 'action_html' },
               ]
            });

            $('select[name="companyTable_length"]').append($('<option>', {
                value: 500,
                text: '500'
            }));

      });

      function editLogo(id,name){
            $("#company_name_in_modal").html(name);
            $("#icon_edit_company_id").val(id);

            $("#edit_logo_modal").modal("show");

        }

        function uploadLogo(){

            var id=$("#icon_edit_company_id").val();

            var formData = new FormData();
            formData.append('id', id);
            if($('#upload_logo')[0].files[0] !== undefined) {
                formData.append('upload_logo', $('#upload_logo')[0].files[0]);
            }

           $.ajax({
              type: 'post',
              processData: false,
              contentType: false,
              data: formData,
              url: "{{ url('uploadLogo') }}",
              success: function (data) {
                  $("#edit_logo_modal").modal("hide");
                  Swal.fire('Logo Updated. Reload to View new logo.');
                  // location.reload();
              }
           });
        }

      function editCompanyModal(company_id){

          var data={
            company_id:company_id
          };

          $.ajax({
              type: 'post',
              data: data,
              url: "{{ url('editCompanyModal')}}",
              success: function (data) {
                $("#edit_modal_append").html(data);

                $("#edit_company_modal").modal("show");
              }
          });
      }

      function editCompany(company_id){

            var premium=0;
            if ($('#is_premium').is(":checked")){
                premium=1;
            }

            var recommended=0;
            if ($('#is_recommended').is(":checked")){
                recommended=1;
            }

            var data={
              id:company_id,
              company_name:$("#company_name").val(),
              company_description:$("#company_description").val(),
              company_email:$("#company_email").val(),
              company_phone_number:$("#company_phone_number").val(),
              address:$("#address").val(),
              state:$("#state").val(),
              city:$("#city").val(),
              is_recommended:recommended,
              is_premium:premium,
              company_url:$("#company_url").val(),
            }

            $.ajax({
                type: 'post',
                data: data,
                url: "{{ url('editCompany')}}",
                success: function (data) {
                  $("#edit_company_modal").modal("hide");
                  Swal.fire('Company Info Updated. Reload to View.');
                }
            });

      }

      function addCompanyModal(){

          var data={
            
          };

          $.ajax({
              type: 'post',
              data: data,
              url: "{{ url('addCompanyModal')}}",
              success: function (data) {
                $("#edit_modal_append").html(data);

                $("#add_company_modal").modal("show");
              }
          });
      }
      function addCompany(){

            var premium=0;
            if ($('#is_premium').is(":checked")){
                premium=1;
            }

            var recommended=0;
            if ($('#is_recommended').is(":checked")){
                recommended=1;
            }

            var data={
              sub_category_id:'{{$sub_category_id}}',
              company_name:$("#company_name").val(),
              company_description:$("#company_description").val(),
              company_email:$("#company_email").val(),
              company_phone_number:$("#company_phone_number").val(),
              address:$("#address").val(),
              state:$("#state").val(),
              city:$("#city").val(),
              is_recommended:recommended,
              is_premium:premium,
              company_url:$("#company_url").val(),
            }

            $.ajax({
                type: 'post',
                data: data,
                url: "{{ url('addCompany')}}",
                success: function (data) {
                  $("#add_company_modal").modal("hide");
                  Swal.fire('Company Info Added.  Reload to View.');
                }
            });

      }

    </script>

@endsection
