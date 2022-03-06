@extends('layouts.app')

@section('content')
    <div class="col-md-12" style="margin-top:20px">
        <div class="card">
            <div class="row" style="margin-bottom: 10px;">
              <div style="margin-left: 20px; padding: 20px">
                <button class="btn btn-primary" data-toggle="modal" data-target="#addModal">Add Make</button>
              </div>

            </div>
            <div class="card-header bg-info text-white"><b>Makes & Model Listing</b></div>
            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <table id='makeTable' class="table table-striped table-bordered" width='100%' border="1" style='border-collapse: collapse;'>
                    <thead style="background-color: grey;">
                        <tr>
                            <td><b>#</b></td>
                            <td><b>MAKE</b></td>
                            <td><b>Model Name</b></td>
                            <td><b>Variant</b></td>
                            <td><b>Generation</b></td>
                            <td><b>Min. Year</b></td>
                            <td><b>Max. Year</b></td>
                            <td><b>Fuel Type</b></td>
                            <td><b>Car Body Type</b></td>
                            <td><b>Door</b></td>
                            <td><b>Seat</b></td>
                            <td><b>Engine Size</b></td>
                            <td><b>Engine Code</b></td>
                            <td><b>Action</b></td>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Make Details</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <label>Make Name</label>
            <input type="text" class="form-control editvalues" id="make" required>
            <label>Model Name</label>
            <input type="text" class="form-control editvalues" id="model_name" required>
            <label>Variant</label>
            <input type="text" class="form-control editvalues" id="variant">
            <label>Generation</label>
            <input type="text" class="form-control editvalues" id="generation">
            <label>Min. Year</label>
            <input type="number" class="form-control editvalues" id="min_year" required>
            <label>Max. Year</label>
            <input type="number" class="form-control editvalues" id="max_year">
            <label>Fuel Type</label>
            <input type="text" class="form-control editvalues" id="fuel_type" required>
            <label>Car Body Type</label>
            <input type="text" class="form-control editvalues" id="car_body_type" required>
            <label>Door</label>
            <input type="number" class="form-control editvalues" id="door" required>
            <label>Seat</label>
            <input type="number" class="form-control editvalues" id="seat">
            <label>Engine Size</label>
            <input type="text" class="form-control editvalues" id="engine_size">
            <label>Engine Code</label>
            <input type="text" class="form-control editvalues" id="engine_code">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="editMake();">SAVE</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="addModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Make Details</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <label>Make Name</label>
            <input type="text" class="form-control addvalues" id="make_new" required>
            <label>Model Name</label>
            <input type="text" class="form-control addvalues" id="model_name_new" required>
            <label>Variant</label>
            <input type="text" class="form-control addvalues" id="variant_new">
            <label>Generation</label>
            <input type="text" class="form-control addvalues" id="generation_new">
            <label>Min. Year</label>
            <input type="number" class="form-control addvalues" id="min_year_new" required>
            <label>Max. Year</label>
            <input type="number" class="form-control addvalues" id="max_year_new">
            <label>Fuel Type</label>
            <input type="text" class="form-control addvalues" id="fuel_type_new" required>
            <label>Car Body Type</label>
            <input type="text" class="form-control addvalues" id="car_body_type_new" required>
            <label>Door</label>
            <input type="number" class="form-control addvalues" id="door_new" required>
            <label>Seat</label>
            <input type="number" class="form-control addvalues" id="seat_new" required>
            <label>Engine Size</label>
            <input type="text" class="form-control addvalues" id="engine_size_new">
            <label>Engine Code</label>
            <input type="text" class="form-control addvalues" id="engine_code_new">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="addMake();">SAVE</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function(){

          // DataTable
          $('#makeTable').DataTable({
             processing: true,
             serverSide: true,
             ajax: {
                url: "{{route('getmakesmodels')}}"
              },
             dom: "lBfrtip",
             buttons : [
               {
                    extend : 'excelHtml5',
                    text: '<span class="material-icons" style="padding-left:10px;">file_download</span>',
                    titleAttr: 'Export to Excel',
                    title: 'Dealers List',
                    exportOptions : {
                        columns: ':not(:last-child)',
                    }
                }
              ],

             columnDefs: [
                {"targets": 13,"orderable": false}
              ],
             columns: [
                { data: 'id' },
                { data: 'make' },
                { data: 'model_name' },
                { data: 'variant' },
                { data: 'generation' },
                { data: 'min_year' },
                { data: 'max_year' },
                { data: 'fuel_type' },
                { data: 'car_body_type' },
                { data: 'door' },
                { data: 'seat' },
                { data: 'engine_size' },
                { data: 'engine_code' },
                { data: 'action_html' },
             ]
          });

          $('select[name="makeTable_length"]').append($('<option>', {
              value: 500,
              text: '500'
          }));

        });

        var edit_id='';
        function editModal(make_id){
          edit_id=make_id;
          var data={
              id:make_id
          };

          $.ajax({
              type: 'post',
              data: data,
              url: "{{ url('editmodal') }}",
              success: function (data) {
                data=JSON.parse(data);
                data=data[0];
                console.log(data);
                $.each(data,function(idx,val){
                  $("#"+idx).val(val);
                });
                $("#editModal").modal("show");
              }
          });

        }

        function editMake(){
          var data={
              id:edit_id
          };
          $(".editvalues").each(function(){
              var index=this.id;
              var value=$("#"+index).val();

              data[index]=value;
          });

          $.ajax({
              type: 'post',
              data: data,
              url: "{{ url('editmakes') }}",
              success: function (data) {
                $("#editModal").modal("hide");

                Swal.fire('Make Updated. Reload to see Latest.');
              }
          });


        }

        function addMake(){
          var data={};

          $(".addvalues").each(function(){
              var index=this.id;
              var value=$("#"+index).val();

              var col_name=index.slice(0,-4);
              data[col_name]=value;
          });

          $.ajax({
              type: 'post',
              data: data,
              url: "{{ url('addmakes') }}",
              success: function (data) {
                $("#addModal").modal("hide");

                Swal.fire('Make Added. Reload to see Latest.');
              }
          });


        }

    </script>

@endsection
