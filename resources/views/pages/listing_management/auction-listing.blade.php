@extends('layouts.app')

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-info text-white"><b>Auction Listing</b></div>
            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <table id='listingTable' class="table table-striped table-bordered" width='100%' border="1" style='border-collapse: collapse;'>
                    <thead style="background-color: grey;">
                        <tr>
                            <td><b>#</b></td>
                            <td><b>Listing Id</b></td>
                            <td><b>Condition</b></td>
                            <td><b>Car Make</b></td>
                            <td><b>Car Model</b></td>
                            <td><b>State</b></td>
                            <td><b>Starting Price (RM)</b></td>
                            <td><b>Reserve Price (RM)</b></td>
                            <td><b>Buy Now Price (RM)</b></td>
                            <td><b>User</b></td>
                            <td><b>Status</b></td>
                            <td><b>Datetime</b></td>
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
          $('#listingTable').DataTable({
             order: [ [0, 'desc'] ],
             processing: true,
             serverSide: true,
             ajax: {
                url: "{{route('getListing')}}",
                data: function(data) {
                     data.listing_type = 'auction';
                 }
              },
             dom: "lBfrtip",
             buttons : [{
                  extend : 'excelHtml5',
                  text: '<span class="material-icons" style="padding-left:10px;">file_download</span>',
                  titleAttr: 'Export to Excel',
                  title: 'Auction Listing',
                  exportOptions : {
                      // columns: ':not(:last-child)',
                  }
              }],
              columnDefs: [
                {"targets": 0,"orderable": false},
                {"targets": 1,"orderable": false},
                {"targets": 12,"orderable": false}
              ],
             columns: [
                { data: 'id' },
                { data: 'listing_id' },
                { data: 'car_condition' },
                { data: 'car_make_name' },
                { data: 'model' },
                { data: 'state' },
                { data: 'starting_price' },
                { data: 'reserve_price' },
                { data: 'buy_now_price' },
                { data: 'first_name' },
                { data: 'listing_status' },
                { data: 'created_at' },
                { data: 'view_more_button' },
             ]
          });

          $('select[name="listingTable_length"]').append($('<option>', {
              value: 500,
              text: '500'
          }));

        });
    </script>

@endsection
