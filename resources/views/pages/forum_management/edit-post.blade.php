<div class="modal fade" id="postModal" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true" style="overflow-y: scroll;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="postModalLabel">Modify Post</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
              <div class="form-group">
                <label for="cover_image" class="col-form-label">Cover Image:</label>
                <img src="{{$post_details[0]->cover_image}}" alt="Cover Image" style="width: 100%;height: 100%;">
              </div>
              <div class="form-group">
                <label for="post_category" class="col-form-label">Category:</label>
                <select class="form-control" id="post_category">
                    @foreach($forum_category_list as $f_cat)
                      @if($f_cat->id==$post_details[0]->category)
                        <option value="{{$f_cat->id}}" selected>{{$f_cat->name}}</option>
                      @else
                        <option value="{{$f_cat->id}}">{{$f_cat->name}}</option>
                      @endif
                      
                    @endforeach
                </select>
              </div>
              <div class="form-group">
                <label for="post_subject" class="col-form-label">Subject:</label>
                <input type="text" class="form-control" id="post_subject" value="{{$post_details[0]->subject}}">
              </div>
              <div class="form-group">
                <label for="post_author" class="col-form-label">Author:</label>
                <input type="text" class="form-control" id="post_author" value="{{$post_details[0]->first_name}} {{$post_details[0]->last_name}}" readonly>
              </div>
              <div class="form-group">
                <label for="post_datetime" class="col-form-label">Datetime:</label>
                <input type="text" class="form-control" id="post_datetime" value="{{$post_details[0]->created_at}}" readonly>
              </div>
            
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="modifyPostDetails('{{$post_details[0]->id}}');">Update post</button>
      </div>
    </div>
  </div>
</div>