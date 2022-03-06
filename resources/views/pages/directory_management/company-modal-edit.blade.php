<div class="modal fade" id="edit_company_modal" tabindex="-1" role="dialog" aria-labelledby="edit_company_modalLabel" aria-hidden="true" style="overflow-y: scroll;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="edit_company_modalLabel">Modify Company</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        @if(!empty($company_info))
          <form>
              <div class="form-group">
                <label for="company_name" class="col-form-label">Company Name</label>
                <input type="text" class="form-control" id="company_name" value="{{$company_info[0]->company_name}}">
              </div>

              <div class="form-group">
                <label for="company_description" class="col-form-label">Company Description</label>
                <input type="text" class="form-control" id="company_description" value="{{$company_info[0]->company_description}}">
              </div>

              <div class="form-group">
                <label for="company_email" class="col-form-label">Company Email</label>
                <input type="text" class="form-control" id="company_email" value="{{$company_info[0]->company_email}}">
              </div>

              <div class="form-group">
                <label for="company_phone_number" class="col-form-label">Company Phone</label>
                <input type="text" class="form-control" id="company_phone_number" value="{{$company_info[0]->company_phone_number}}">
              </div>

              <div class="form-group">
                <label for="address" class="col-form-label">Company Address</label>
                <input type="text" class="form-control" id="address" value="{{$company_info[0]->address}}">
              </div>

              <div class="form-group">
                <label for="state" class="col-form-label">State</label>
                <input type="text" class="form-control" id="state" value="{{$company_info[0]->state}}">
              </div>

              <div class="form-group">
                <label for="city" class="col-form-label">City</label>
                <input type="text" class="form-control" id="city" value="{{$company_info[0]->city}}">
              </div>

              <div class="form-group">
                <label for="company_url" class="col-form-label">Company URL</label>
                <input type="text" class="form-control" id="company_url" value="{{$company_info[0]->company_url}}">
              </div>

              <?php
                $checked_premium=($company_info[0]->is_premium==1)?"checked":"";
                $checked_recom=($company_info[0]->is_recommended==1)?"checked":"";
              ?>
              <div class="form-group">
                <input type="checkbox" id="is_premium" name="is_premium" {{$checked_premium}}>
                <label for="is_premium">Company Premium</label><br>
              </div>

              <div class="form-group">
                <input type="checkbox" id="is_recommended" name="is_recommended" {{$checked_recom}}>
                <label for="is_recommended">Company Recommended</label><br>
              </div>
            
        </form>
        @endif
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="editCompany('{{$company_id}}');">Update Company</button>
      </div>
    </div>
  </div>
</div>