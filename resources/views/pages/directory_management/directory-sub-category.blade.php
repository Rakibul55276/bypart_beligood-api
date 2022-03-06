@extends('layouts.app')

@section('content')
    <div class="col-md-12">

      <div class="row" style="padding:10px;">
          <div class="col-md-10">
            
          </div>
          <div class="col-md-2">
            <button class="btn btn-primary" data-toggle="modal" data-target="#add_category_modal"> Add Sub Category</button>
          </div>
          
        </div>
        <div class="card">
            <div class="card-header bg-info text-white"><b>{{$category_name}} - Sub Category List</b></div>

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
                            <td><b>Sub Category Name</b></td>
                            <td><b>Sub Category Icon</b></td>
                            <td><b>Action</b></td>
                        </tr>
                    </thead>
                    <tbody>
                      @foreach($list_of_sub_categories as $key=>$list)
                        <?php $id=$key+1; ?>
                        <tr>
                          <td>{{$id}}</td>
                          <td><input type="text" class="form-control" id="sub_category_name_{{$list->id}}" value="{{$list->sub_category_name}}"></td>
                          <td>
                            @if(!empty($list->sub_category_icon))
                              <img src="{{$list->sub_category_icon}}" style="width: 80px;height: 80px;">
                            @else
                              -
                            @endif

                            <br><p style="padding-top: 7px;"><button class="btn btn-secondary" onclick="editIcon('{{$list->id}}','{{$list->sub_category_name}}');">EDIT ICON</button></p>
                          </td>
                          <td>
                            <button class="btn btn-primary" onclick="editCategory('{{$list->id}}')">SAVE EDIT</button>
                            <!-- <button class="btn btn-danger">DELETE</button> -->
                            <button class="btn btn-secondary">
                            <a href="{{config('bypart.admin_url')}}/companies?ref={{$list->id}}" target="_blank">VIEW COMPANY LIST</a>
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
            <h5 class="modal-title" id="add_category_modalLabel">Add Sub Category</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
              <form>
                  <div class="form-group">
                    <label for="category_name" class="col-form-label">Sub Category Name</label>
                    <input type="text" class="form-control" id="category_name">
                  </div>
                
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="addCategory();">Add Sub Category</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="edit_icon_modal" tabindex="-1" role="dialog" aria-labelledby="edit_icon_modalLabel" aria-hidden="true" style="overflow-y: scroll;">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="edit_icon_modalLabel"><b>Edit Icon</b>  <span><i><p id="sub_category_name_in_modal"></p></i></span></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
              <form>
                  <div class="form-group">
                      <input type="file" name="uploadicon" class="form-control" id="upload_icon">
                  </div>
                
            </form>
          </div>
          <div class="modal-footer">
            <input type="hidden" id="icon_edit_category_id">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="uploadIcon();">Update Icon</button>
          </div>
        </div>
      </div>
    </div>
@endsection

@section('javascript')

    <script type="text/javascript">

        function editIcon(id,name){
            $("#sub_category_name_in_modal").html(name);
            $("#icon_edit_category_id").val(id);

            $("#edit_icon_modal").modal("show");

        }

        function uploadIcon(){

            var sub_id=$("#icon_edit_category_id").val();

            var formData = new FormData();
            formData.append('sub_category_id', sub_id);
            if($('#upload_icon')[0].files[0] !== undefined) {
                formData.append('upload_icon', $('#upload_icon')[0].files[0]);
            }

           $.ajax({
              type: 'post',
              processData: false,
              contentType: false,
              data: formData,
              url: "{{ url('updateIcon') }}",
              success: function (data) {
                  $("#edit_icon_modal").modal("hide");
                  Swal.fire('Icon Updated. Reload to View new icon.');
                  // location.reload();
              }
           });
        }

        function editCategory(id){

              var sub_category_name=$("#sub_category_name_"+id).val();

              var data={
                  sub_category_id:id,
                  sub_category_name:sub_category_name
              };

              $.ajax({
                  type: 'post',
                  data: data,
                  url: "{{ url('editSubCategory') }}",
                  success: function (data) {
                    Swal.fire('Sub Category Name Updated.');
                  }
              });
        }

        function addCategory(){

              var category_name=$("#category_name").val();

              var data={
                  category_name:category_name,
                  directory_category_id:'{{$category_id}}'
              };

              $.ajax({
                  type: 'post',
                  data: data,
                  url: "{{ url('addSubCategory') }}",
                  success: function (data) {
                    $("#add_category_modal").modal("hide");
                    Swal.fire('Sub Category Added.');
                  }
              });
        }
      
    </script>

@endsection
