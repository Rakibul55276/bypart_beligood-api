<div class="modal fade" id="add_company_modal" tabindex="-1" role="dialog" aria-labelledby="add_company_modalLabel" aria-hidden="true" style="overflow-y: scroll;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="add_company_modalLabel">Add Company</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form>
              <div class="form-group">
                <label for="company_name" class="col-form-label">Company Name</label>
                <input type="text" class="form-control" id="company_name">
              </div>

              <div class="form-group">
                <label for="company_description" class="col-form-label">Company Description</label>
                <input type="text" class="form-control" id="company_description">
              </div>

              <div class="form-group">
                <label for="company_email" class="col-form-label">Company Email</label>
                <input type="text" class="form-control" id="company_email">
              </div>

              <div class="form-group">
                <label for="company_phone_number" class="col-form-label">Company Phone</label>
                <input type="text" class="form-control" id="company_phone_number">
              </div>

              <div class="form-group">
                <label for="address" class="col-form-label">Company Address</label>
                <input type="text" class="form-control" id="address">
              </div>

              <div class="form-group">
                <label for="state" class="col-form-label">State</label>
                <input type="text" class="form-control" id="state">
              </div>

              <div class="form-group">
                <label for="city" class="col-form-label">City</label>
                <input type="text" class="form-control" id="city">
              </div>

              <div class="form-group">
                <label for="company_url" class="col-form-label">Company URL</label>
                <input type="text" class="form-control" id="company_url">
              </div>

              <div class="form-group">
                <input type="checkbox" id="is_premium" name="is_premium">
                <label for="is_premium">Company Premium</label><br>
              </div>

              <div class="form-group">
                <input type="checkbox" id="is_recommended" name="is_recommended">
                <label for="is_recommended">Company Recommended</label><br>
              </div>
            
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="addCompany();">Add Company</button>
      </div>
    </div>
  </div>
</div>