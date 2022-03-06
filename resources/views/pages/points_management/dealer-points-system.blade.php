@extends('layouts.app')

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-info text-white"><b>Dealer Point System</b></div>
            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <table id='pointTable' class="table table-striped table-bordered" width='100%' border="1" style='border-collapse: collapse;'>
                    <thead style="background-color: grey;">
                        <tr>
                            <td><b>#</b></td>
                            <td><b>Transaction Type</b></td>
                            <td><b>Point Deduction (Pts)</b></td>
                            <td><b>Action</b></td>
                        </tr>
                    </thead>
                    <tbody>
                      <?php $i=0; ?>
                      @foreach($point_system_list as $point_list)
                        <?php $i++; ?>
                        <tr>
                          <td>
                            {{$i}}
                          </td>
                          <td>
                            <?php $transaction_type=ucwords(str_replace("_", " ", $point_list->transaction_type)); ?>
                            {{$transaction_type}}
                          </td>
                          <td>
                            <input type="number" class="form-control" id="point_list_{{$point_list->id}}" value="{{$point_list->deduction_point}}">
                          </td>

                          <td>
                            <button class="btn btn-primary" onclick="editPoint('{{$point_list->id}}')">
                               Save
                            </button>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
  <script type="text/javascript">
    function editPoint(point_id){
      var new_point=$("#point_list_"+point_id).val();

      var data={
          id:point_id,
          point:new_point
      };

      $.ajax({
          type: 'post',
          data: data,
          url: "{{ url('editPoint') }}",
          success: function (data) {
            Swal.fire('Point Updated.');
          }
      });
    }
  </script>
@endsection

