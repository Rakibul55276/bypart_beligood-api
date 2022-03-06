@extends('layouts.app')

@section('content')
    <div class="col-md-12" style="margin-top:20px">
        <div class="card">
            <div class="row" style="margin-bottom: 10px;">

            </div>
            <div class="card-header bg-info text-white"><b>Makes Request Listing</b></div>
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
                            <td><b>Fuel Type</b></td>
                            <td><b>Manufactured Year</b></td>
                            <td><b>Transmission</b></td>
                            <td><b>Engine Capacity</b></td>
                            <td><b>Condition</b></td>
                            <td><b>Supporting Document</b></td>
                            <td><b>Action</b></td>
                        </tr>
                    </thead>
                </table>
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
                url: "{{route('getmakerequests')}}"
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
             columns: [
                { data: 'id' },
                { data: 'make' },
                { data: 'model' },
                { data: 'variant' },
                { data: 'fuel_type' },
                { data: 'manufactured_year' },
                { data: 'transmission' },
                { data: 'engine_capacity' },
                { data: 'condition' },
                { data: 'supporting_document' },
                { data: 'action' }
             ]
          });

          $('select[name="makeTable_length"]').append($('<option>', {
              value: 500,
              text: '500'
          }));

        });

        function deleteRequest(make_id){

            var data={
                make_id:make_id
                
            };

            Swal.fire({
              title: "Are you sure want to Delete?",
              icon: "warning",
              buttons: true,
              dangerMode: true,
            })
            .then((willDelete) => {
              if (willDelete.isConfirmed===true) {
                $.ajax({
                    type: 'post',
                    data: data,
                    url: 'deleteRequest',
                    success: function (data) {
                        location.reload();
                    }
                });
              }
            });
        }

    </script>

@endsection
