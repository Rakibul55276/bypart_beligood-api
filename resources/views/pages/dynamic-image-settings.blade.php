@extends('layouts.app')

@section('content')
	<div class="col-md-12">

        <div class="row" style="padding:10px;">
          <div class="col-md-10">
            
          </div>
          <div class="col-md-2">
            <button class="btn btn-primary" data-toggle="modal" data-target="#add_setting_modal"> Add Image Setting</button>
          </div>
          
        </div>

        <div class="card">
            <div class="card-header bg-info text-white"><b>Dynamic Image Settings</b></div>

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
                            <td><b>Page Name</b></td>
                            <td><b>Image Type</b></td>
                            <td><b>Image Position</b></td>
                            <td><b>Image URL</b></td>
                            <td><b>Redirect URL</b></td>
                            <td><b>Action</b></td>
                        </tr>
                    </thead>
                    <tbody>
                      @foreach($page_settings as $key=>$list)
                        <?php $id=$key+1; ?>
                        <tr>
                          <td>{{$id}}</td>
                          <td>
                          		<select class="form-control" id="page_name_{{$list->id}}">
                          			@foreach($page_name_array as $k_pn=>$v_pn)
                          				@if($k_pn==$list->page_name)
                          					<option value="{{$k_pn}}" selected>{{$v_pn}}</option>
                          				@else
                          					<option value="{{$k_pn}}">{{$v_pn}}</option>
                          				@endif
                          			@endforeach
                          		</select>
                          </td>
                          <td>
                          		<select class="form-control" id="image_type_{{$list->id}}">
                          			@foreach($image_type_array as $k_it=>$v_it)
                          				@if($k_it==$list->image_type)
                          					<option value="{{$k_it}}" selected>{{$v_it}}</option>
                          				@else
                          					<option value="{{$k_it}}">{{$v_it}}</option>
                          				@endif
                          			@endforeach
                          		</select>
                          </td>

                          <td>
                          		<select class="form-control" id="image_position_{{$list->id}}">
                          			@foreach($image_position_array as $k_ip=>$v_ip)
                          				@if($k_ip==$list->image_position)
                          					<option value="{{$k_ip}}" selected>{{$v_ip}}</option>
                          				@else
                          					<option value="{{$k_ip}}">{{$v_ip}}</option>
                          				@endif
                          			@endforeach
                          		</select>
                          </td>

                          <td>
                          	<a href="{{$list->image_url}}" target="_blank">View Image</a>
                          </td>

                          <td>
                          	<input type="text" class="form-control" id="image_redirect_url_{{$list->id}}" value="{{$list->image_redirect_url}}">
                          </td>

                          <td>
                            <button class="btn btn-primary" onclick="editSettings('{{$list->id}}')">
                            	SAVE EDIT
                            </button>
                            
                            <button class="btn btn-secondary" onclick="editIcon('{{$list->id}}')">
	                            EDIT IMAGE
	                        </button>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add_setting_modal" tabindex="-1" role="dialog" aria-labelledby="add_setting_modalLabel" aria-hidden="true" style="overflow-y: scroll;">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="add_setting_modalLabel">Add Settings</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	          <form>
	              <div class="form-group">
	                <label for="page_name" class="col-form-label">Page Name</label>
	                <select class="form-control" id="page_name">
              			@foreach($page_name_array as $k_pn=>$v_pn)
              				<option value="{{$k_pn}}">{{$v_pn}}</option>
              			@endforeach
              		</select>
              		<label for="image_type" class="col-form-label">Image Type</label>
	                <select class="form-control" id="image_type">
              			@foreach($image_type_array as $k_it=>$v_it)
              				<option value="{{$k_it}}">{{$v_it}}</option>
              			@endforeach
              		</select>
              		<label for="image_position" class="col-form-label">Image Position</label>
	                <select class="form-control" id="image_position">
              			@foreach($image_position_array as $k_ip=>$v_ip)
              				<option value="{{$k_ip}}">{{$v_ip}}</option>
              			@endforeach
              		</select>

              		<label for="image_url" class="col-form-label">Image File</label>
              		<input type="file" class="form-control" name="image_url" id="image_url">

              		<label for="redirect_url" class="col-form-label">Redirect URL</label>
              		<input type="text" class="form-control" name="redirect_url" id="redirect_url">
	              </div>
	            
	        </form>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	        <button type="button" class="btn btn-primary" onclick="addSettings();">Add Settings</button>
	      </div>
	    </div>
	  </div>
	</div>

	<div class="modal fade" id="edit_icon_modal" tabindex="-1" role="dialog" aria-labelledby="edit_icon_modalLabel" aria-hidden="true" style="overflow-y: scroll;">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="edit_icon_modalLabel"><b>Edit Image</b></h5>
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
            <input type="hidden" id="image_settings_id">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="uploadIcon();">Update Image</button>
          </div>
        </div>
      </div>
    </div>
@endsection

@section('javascript')
	<script type="text/javascript">

		function editIcon(id){

            $("#image_settings_id").val(id);

            $("#edit_icon_modal").modal("show");

        }

        function uploadIcon(){

            var id=$("#image_settings_id").val();

            var formData = new FormData();
            formData.append('id', id);
            if($('#upload_icon')[0].files[0] !== undefined) {
                formData.append('upload_icon', $('#upload_icon')[0].files[0]);
            }

           $.ajax({
              type: 'post',
              processData: false,
              contentType: false,
              data: formData,
              url: "{{ url('updateImage') }}",
              success: function (data) {
                  $("#edit_icon_modal").modal("hide");
                  Swal.fire('Image Updated. Reload to View new image.');
                  // location.reload();
              }
           });
        }

		function addSettings(){

				var page_name=$("#page_name").val();
				var image_type=$("#image_type").val();
				var image_position=$("#image_position").val();
				var redirect_url=$("#redirect_url").val();

	            var formData = new FormData();
	            formData.append('page_name', page_name);
	            formData.append('image_type', image_type);
	            formData.append('image_position', image_position);
	            formData.append('redirect_url', redirect_url);

	            if($('#image_url')[0].files[0] !== undefined) {
	                formData.append('image_url', $('#image_url')[0].files[0]);
	            }

	           $.ajax({
	              type: 'post',
	              processData: false,
	              contentType: false,
	              data: formData,
	              url: "{{ url('addSettings') }}",
	              success: function (data) {
	                  location.reload();
	              }
	           });

		}

		function editSettings(id){

			var data={
				id:id,
				page_name:$("#page_name_"+id).val(),
				image_type:$("#image_type_"+id).val(),
				image_position:$("#image_position_"+id).val(),
				image_redirect_url:$("#image_redirect_url_"+id).val()
			}

			$.ajax({
	              type: 'post',
	              data: data,
	              url: "{{ url('editSettings') }}",
	              success: function (data) {
	                  Swal.fire("Settings Updated.")
	              }
	           });
		}
	</script>
@endsection