@extends('layouts.app')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">

            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <!-- <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Starter Page</li> -->


                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<div class="row">
    <div class="col-8 container bypart-profile">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true">Profile</a>
    </li>
    @if($user_details[0]->user_type=='agent'||$user_details[0]->user_type=='dealer')
    <li class="nav-item">
        <a class="nav-link" id="company-tab" data-toggle="tab" href="#company" role="tab" aria-controls="company" aria-selected="false">Company Details</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="document-tab" data-toggle="tab" href="#document" role="tab" aria-controls="document" aria-selected="false">Document</a>
    </li>
    @endif
    </ul>
    </div>
</div>
<div class="tab-content" id="myTabContent">
      <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="container bypart-profile" id="profile">

                <div class="row gutters">

                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="row gutters">
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <h6 class="mb-2 text-primary">Personal Details</h6>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <img src="{{$user_details[0]->avatar}}" width="100px"style="cursor: pointer;border-radius: 50%;">
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="first_name">First Name</label>
                                            <input type="text" class="form-control" id="first_name" value="{{$user_details[0]->first_name}}">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="last_name">Last Name</label>
                                            <input type="text" class="form-control" id="last_name" value="{{$user_details[0]->last_name}}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row gutters">
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <h6 class="mb-2 text-primary">Contact Informations</h6>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" id="email" value="{{$user_details[0]->email}}">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="mobile_no">Mobile No</label>
                                            <input type="text" class="form-control" id="mobile_no" value="{{$user_details[0]->mobile_no}}">
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="user_ic_number">NRIC Number</label>
                                            <input type="text" class="form-control" id="user_ic_number" value="{{$user_details[0]->user_ic_number}}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="user_ic_photo">NRIC Photo</label>
                                            <div class="form-group">
                                                @if(!empty($user_details[0]->user_ic_photo))
                                                    <img src="{{$user_details[0]->user_ic_photo}}" width="400px" height="200px">
                                                @else
                                                <i>No NRIC Photo</i>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row gutters">
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <h6 class="mt-3 mb-2 text-primary">Address Details</h6>
                                    </div>
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <div class="form-group">
                                            <label for="address_text">Address</label>
                                            <input type="name" class="form-control" id="address_text" value="{{$user_details[0]->address}}">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="city">City</label>
                                            <input type="name" class="form-control" id="city" value="{{$user_details[0]->city}}">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="state">State</label>
                                            <input type="text" class="form-control" id="state" value="{{$user_details[0]->state}}">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="country">State</label>
                                            <input type="text" class="form-control" id="country" value="{{$user_details[0]->country}}">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="postal_code">Postal Code</label>
                                            <input type="text" class="form-control" id="postal_code" value="{{$user_details[0]->postal_code}}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row gutters">
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <h6 class="mt-3 mb-2 text-primary">Status Details</h6>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="status">User Status</label>
                                            <select class="form-control" id="status">

                                                @foreach($user_status as $k_s=>$u_s)
                                                    @if($k_s==$user_details[0]->status)
                                                        <option value="{{$k_s}}" selected>{{$u_s}}</option>
                                                    @else
                                                        <option value="{{$k_s}}">{{$u_s}}</option>
                                                    @endif
                                                @endforeach

                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="status_admin">Status By Admin</label>
                                            <select class="form-control" id="status_admin">

                                                @foreach($admin_status as $k_stat=>$a_stat)
                                                    @if($k_stat==$user_details[0]->status_admin)
                                                        <option value="{{$k_stat}}" selected>{{$a_stat}}</option>
                                                    @else
                                                        <option value="{{$k_stat}}">{{$a_stat}}</option>
                                                    @endif
                                                @endforeach

                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="status">Update BP Point</label>
                                            <input type="text" class="form-control" id="bp_point" value="{{$user_details[0]->bp_point}}">
                                        </div>
                                    </div>

                                </div>
                                <div class="row gutters">
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <div class="text-right">
                                            <button type="button" id="submit" name="submit" class="btn btn-primary" onclick="editUserData();">Update Profile</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
      </div>

      <div class="tab-pane fade" id="company" role="tabpanel" aria-labelledby="company-tab">
        <div class="container bypart-profile" id="profile">

            <div class="row gutters">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="row gutters">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <h6 class="mb-2 text-primary">Company Infos</h6>
                                </div>
                                @if(isset($company_info[0]))
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <img src="{{$company_info[0]->company_logo}}" width="100px"style="cursor: pointer;border-radius: 50%;">
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="first_name">Company Name</label>
                                            <input type="text" id="company_name" class="form-control" value="{{$company_info[0]->company_name}}">
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="first_name">Company Email</label>
                                            <input type="text" id="company_email" class="form-control" value="{{$company_info[0]->company_email}}">
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="first_name">Company Phone</label>
                                            <input type="text" id="company_phone_number" class="form-control" value="{{$company_info[0]->company_phone_number}}">
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="first_name">Company Address</label>
                                            <input type="text" id="company_address" class="form-control" value="{{$company_info[0]->address}}">
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="first_name">City</label>
                                            <input type="text" id="company_city" class="form-control" value="{{$company_info[0]->city}}">
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="first_name">State</label>
                                            <input type="text" id="company_state" class="form-control" value="{{$company_info[0]->state}}">
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="first_name">Zip Code</label>
                                            <input type="text" id="zip_code" class="form-control" value="{{$company_info[0]->zip_code}}">
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="first_name">Country</label>
                                            <input type="text" id="company_country" class="form-control" value="{{$company_info[0]->country}}">
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="first_name">New Company Reg. No</label>
                                            <input type="text" id="new_company_registration_no" class="form-control" value="{{$company_info[0]->new_company_registration_no}}">
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="first_name">Old Company Reg. No</label>
                                            <input type="text" id="old_cpompany_registration_no" class="form-control" value="{{$company_info[0]->old_cpompany_registration_no}}">
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="first_name">Company URL</label>
                                            <input type="text" id="company_url" class="form-control" value="{{$company_info[0]->company_url}}">
                                        </div>
                                    </div>

                                    <input type="hidden" id="company_id" value="{{$company_info[0]->id}}">
                                @endif
                            </div>
                            <div class="row gutters">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="text-right">
                                        <button type="button" id="submit" name="submit" class="btn btn-primary" onclick="editCompanyData();">Update Company</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>

      <div class="tab-pane fade" id="document" role="tabpanel" aria-labelledby="document-tab">
        <div class="container bypart-profile" id="profile">

            <div class="row gutters">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="row gutters">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <h6 class="mb-2 text-primary">Preview Documents</h6>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label for="first_name">SSM Certs</label>
                                        <div class="row">
                                            @foreach($ssm_files as $k=>$ssm)
                                                <div class="col-md-2">
                                                    <a href="{{$ssm}}" target="_blank">File {{$k+1}}</a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label for="first_name">Name Card Files</label>
                                        <div class="row">
                                            @foreach($name_cards as $kn=>$name_card)
                                                <div class="col-md-2">
                                                    <a href="{{$name_card}}" target="_blank">File {{$kn+1}}</a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label for="first_name">Premise Images</label>
                                        <div class="row">
                                            @foreach($premise_images as $kp=>$premise_image)
                                                <div class="col-md-2">
                                                    <a href="{{$premise_image}}" target="_blank">Image {{$kp+1}}</a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label for="first_name">Other Supporting Files</label>
                                        <div class="row">
                                            @foreach($other_supprting_docs as $ko=>$other_supprting_doc)
                                                <div class="col-md-2">
                                                    <a href="{{$other_supprting_doc}}" target="_blank">File {{$ko+1}}</a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
</div>

<input type="hidden" id="user_id" value="{{$user_details[0]->id}}">

<div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="passwordModalLabel">Change Password</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <label for="current_password" class="col-form-label">Current Password:</label>
            <input type="text" class="form-control" id="current_password">
          </div>
          <div class="form-group">
            <label for="new_password" class="col-form-label">New Password:</label>
            <input type="text" class="form-control" id="new_password">
          </div>
          <div class="form-group">
            <label for="confirm_password" class="col-form-label">Confirm Password:</label>
            <input type="text" class="form-control" id="confirm_password">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Update Password</button>
      </div>
    </div>
  </div>
</div>

<br />
@endsection

@section('javascript')
    <script src="{{  asset('js/usermanagement.js') }}"></script>
@endsection
