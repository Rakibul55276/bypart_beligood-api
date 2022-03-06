@extends('layouts.app')

@section('content')
    <div class="col-md-12">

        <div class="row" style="padding:10px;">
          <div class="col-md-10">
            
          </div>
          <div class="col-md-2">
            <button class="btn btn-primary" data-toggle="modal" data-target="#add_category_modal"> Add Category</button>
          </div>
          
        </div>

        <div class="card">
            <div class="card-header bg-info text-white"><b>Directory Category List</b></div>

            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <table id='catTable' class="table table-striped table-bordered" width='100%' border="1" style='border-collapse: collapse;'>
                    <thead style="background-color: grey;">
                        <tr>
                            <td><b>#</b></td>
                            <td><b>Category Name</b></td>
                            <td><b>Action</b></td>
                        </tr>
                    </thead>
                    <tbody>
                      @foreach($list_of_categories as $key=>$list)
                        <?php $id=$key+1; ?>
                        <tr>
                          <td>{{$id}}</td>
                          <td><input type="text" class="form-control" id="category_name_{{$list->id}}" value="{{$list->category_name}}"></td>
                          <td>
                            <button class="btn btn-primary" onclick="editCategory('{{$list->id}}')">SAVE EDIT</button>
                            <!-- <button class="btn btn-danger">DELETE</button> -->
                            <button class="btn btn-secondary">
                            <a href="{{config('bypart.admin_url')}}/subcategories?ref={{$list->id}}" target="_blank">VIEW SUB LIST</a>
                        </button>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add_category_modal" tabindex="-1" role="dialog" aria-labelledby="add_category_modalLabel" aria-hidden="true" style="overflow-y: scroll;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="add_category_modalLabel">Add Category</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form>
              <div class="form-group">
                <label for="category_name" class="col-form-label">Category Name</label>
                <input type="text" class="form-control" id="category_name">
              </div>
            
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="addCategory();">Add Category</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('javascript')

    <script type="text/javascript">

        function editCategory(id){

              var category_name=$("#category_name_"+id).val();

              var data={
                  category_id:id,
                  category_name:category_name
              };

              $.ajax({
                  type: 'post',
                  data: data,
                  url: "{{ url('editCategory') }}",
                  success: function (data) {
                    Swal.fire('Category Name Updated.');
                  }
              });
        }

        function addCategory(){

              var category_name=$("#category_name").val();

              var data={
                  category_name:category_name
              };

              $.ajax({
                  type: 'post',
                  data: data,
                  url: "{{ url('addCategory') }}",
                  success: function (data) {
                    $("#add_category_modal").modal("hide");
                    Swal.fire('Category Added.');
                  }
              });
        }
      
    </script>

@endsection
