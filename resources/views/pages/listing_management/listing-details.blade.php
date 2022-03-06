@extends('layouts.app')

@section('content')
	@if(isset($listing_details[0]))
		<div class="card" style="min-height: 1000px;min-width: 700px; border-radius: 5px;border-color: #e9e9e9; margin: 50px;">

			<!-- Section For Car Name & Listing Date -->
			<div class="row" style="background-color: #e9e9e9; margin: 0px;">
				<div class="col-md-6 pull-left">
					<b>{{$listing_details[0]->car_make_name}} {{$listing_details[0]->model}}</b>
				</div>
				<div class="col-md-6">
					<div style="float: right; margin-right: 10px;">
						<i>{{$currentdate}}</i>
					</div>

				</div>
			</div>

			<!-- Section For User Info & Status -->
			<div class="row" style="margin: 5px;">
				<div class="col-md-4">
					<div class="card" style="border-radius: 5px; background-color: #e9e9e9;">
						<div class="row">

							<div class="col-md-4">
								<img src="{{$listing_details[0]->avatar}}" width="100px"style="cursor: pointer;border-radius: 50%;">
							</div>
							<div class="col-md-4">
								<div style="padding-top: 20px;">
									<div><b>{{$listing_details[0]->first_name}} {{$listing_details[0]->last_name}}</b> <br></div>
									<div><i><?php echo ucfirst($listing_details[0]->user_type); ?></i></div>
									<div><?php echo ucwords(str_replace("_", " ", $listing_details[0]->status_admin));?></div>
								</div>
							</div>
							<div class="col-md-4" style="display: flex; flex-wrap: wrap; align-content: center;">
								<button class="btn btn-primary" onclick="viewUser('{{$listing_details[0]->user_id}}');">
									View
								</button>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-8">
					<div style="float: right; margin-right: 10px;">
						<b>Status: </b> <span style="color: #ffed4a;"><?php echo ucwords(str_replace("_", " ", $listing_details[0]->listing_status));?></span><br>
						<i>Submitted on: {{$listing_details[0]->created_at}}</i>

						@if($listing_details[0]->listing_type=='Auction')
							<br><i>Auction End: {{$listing_details[0]->end_date}}</i>
							<br><i>Auction Duration: {{$listing_details[0]->duration_of_auction}} Days</i>
						@endif
					</div>
				</div>
			</div>

			<div style="margin:20px;">
				<!-- Image Section -->
				<span><b>Images </b> (<?php echo count($listing_details[0]->images);?> images)</span>
				<div>
					<div class="row">

						<div class="col-md-12" style="margin:0px;">
							<div class="row">
								@if(!empty($listing_details[0]->images))
									@foreach($listing_details[0]->images as $image)
											<div class="col-md-2" style="padding-bottom: 10px;">
												<img src="{{$image}}" width="180px"style="cursor: pointer;">
											</div>
									@endforeach
								@endif
							</div>
						</div>
					</div>
				</div>

				<!-- Carplate Details Section -->
				<div style="padding-top: 20px;">
					<span><b>Carplate Photo </b></span>
					<div class="row">
						<div class="col-md-3">
							<img src="{{$listing_details[0]->car_plate_verification_image}}" width="150px"style="cursor: pointer;">
						</div>
					</div>

					<div class="row">
						<div class="col-md-3">
							<label>Carplate Number</label>
							<input class="form-control" type="text" value="{{$listing_details[0]->car_plate_number}}" readonly>
						</div>
					</div>
				</div>

				<!-- Listing Details Section -->
				<div style="padding-top: 20px;">
					<div class="row">
						<div class="col-md-4">
							<label>Listing Type</label>
							<input class="form-control" type="text" value="{{$listing_details[0]->listing_type}}" readonly>
						</div>
						<div class="col-md-4">
							<label>Condition</label>
							<input class="form-control" type="text" value="{{$listing_details[0]->car_condition}}" readonly>
						</div>
						<div class="col-md-4">
							<label>Ownership Document</label>
							<div class="row">
								<div class="col-md-10">
									<input class="form-control" type="text" value="Document File" readonly>
								</div>
								<div class="col-md-2">
									<a href="{{$listing_details[0]->car_ownership_document}}" target="_blank">
										  <span class="material-icons">preview</span>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Vehicale Details Section -->
				<div style="padding-top: 20px;">
					<h5><b>Vehicle Details</b></h5>
					<div class="row">
						<div class="col-md-2">
							<label>Body Type</label>
							<input class="form-control" type="text" value="{{$listing_details[0]->car_body_type}}" readonly>
						</div>
						<div class="col-md-2">
							<label>Make</label>
							<input class="form-control" type="text" value="{{$listing_details[0]->car_make_name}}" readonly>
						</div>
						<div class="col-md-2">
							<label>Model</label>
							<input class="form-control" type="text" value="{{$listing_details[0]->model}}" readonly>
						</div>
						<div class="col-md-2">
							<label>Manufacture Year</label>
							<input class="form-control" type="text" value="{{$listing_details[0]->manufacture_year}}" readonly>
						</div>
						<div class="col-md-2">
							<label>Fuel Type</label>
							<input class="form-control" type="text" value="{{$listing_details[0]->fuel_type}}" readonly>
						</div>
						<div class="col-md-2">
							<label>Varient</label>
							<input class="form-control" type="text" value="{{$listing_details[0]->variant}}" readonly>
						</div>
						<div class="col-md-2">
							<label>Mileage</label>
							<input class="form-control" type="text" value="{{$listing_details[0]->mileage}}" readonly>
						</div>
						<div class="col-md-2">
							<label>Door</label>
							<input class="form-control" type="text" value="{{$listing_details[0]->doors}}" readonly>
						</div>
						<!-- <div class="col-md-2">
							<label>Door</label>
							<input class="form-control" type="text" value="{{$listing_details[0]->seats}}" readonly>
						</div> -->
						<div class="col-md-2">
							<label>Transmission</label>
							<input class="form-control" type="text" value="{{$listing_details[0]->transmission}}" readonly>
						</div>
						<div class="col-md-2">
							<label>Engine Size</label>
							<input class="form-control" type="text" value="{{$listing_details[0]->engine_size}}" readonly>
						</div>
						<div class="col-md-2">
							<label>State</label>
							<input class="form-control" type="text" value="{{$listing_details[0]->state}}" readonly>
						</div>
						<div class="col-md-2">
							<label>Area</label>
							<input class="form-control" type="text" value="{{$listing_details[0]->area}}" readonly>
						</div>
						<div class="col-md-2">
							<label>Color</label>
							<input class="form-control" type="text" value="{{$listing_details[0]->color}}" readonly>
						</div>
					</div>
				</div>

				<!-- Ad Details Section -->
				<div style="padding-top: 20px;">
					<h5><b>Ad Details</b></h5>
					<div class="row">
						<div class="col-md-6">
							<label>Ad Title</label>
							<input type="text" class="form-control" value="{{$listing_details[0]->ad_title}}" readonly>
						</div>
					</div>

					<div class="row" style="padding-top: 20px;">
						<div class="col-md-12">
							<label>Ad Description</label>
							<textarea class="form-control" style="min-height: 200px;" readonly>{{$listing_details[0]->ad_description}}</textarea>
						</div>
					</div>

					<div class="row" style="padding-top: 20px;">
						<div class="col-md-3">
							<label>Asking Price(RM)</label>
							<input type="text" class="form-control" value="{{$listing_details[0]->asking_price}}" readonly>
						</div>

						@if($listing_details[0]->listing_type=='Auction')
							<div class="col-md-3">
								<label>Starting Price(RM)</label>
								<input type="text" class="form-control" value="{{$listing_details[0]->starting_price}}" readonly>
							</div>

							<div class="col-md-3">
								<label>Reserve Price(RM)</label>
								<input type="text" class="form-control" value="{{$listing_details[0]->reserve_price}}" readonly>
							</div>

							<div class="col-md-3">
								<label>Buy Now Price(RM)</label>
								<input type="text" class="form-control" value="{{$listing_details[0]->buy_now_price}}" readonly>
							</div>
						@endif
					</div>
				</div>

				<!-- My Details Section -->
				<div style="padding-top: 20px;">
					<h5><b>My Details</b></h5>
					<div class="row">
						<div class="col-md-6">

							<label>Phone</label>
							<input type="text" class="form-control" value="{{$listing_details[0]->mobile_no}}" readonly>

						</div>
						<div class="col-md-6">

							<label>Email</label>
							<input type="text" class="form-control" value="{{$listing_details[0]->email}}" readonly>

						</div>
					</div>
				</div>
				<!-- Inspection Report Section -->
				<div style="padding-top: 20px;">
					<h5><b>Upload Inspection Report</b></h5>
                    @if (!empty( $listing_details[0]->inspection_report))
                        <a href="{{ lcfirst($listing_details[0]->inspection_report) }}" /> Current Inspection Report </a>
                    @else
                        <p> No Inspection Report uploaded </p>
                    @endif
					<div class="row">
						<div class="col-md-6">
							<input type="file" name="inspectionreport" class="form-control" id="inspection_report">
						</div>

						<div class="col-md-6">
							@if ($listing_details[0]->listing_status == 'Rejected')
								Reject Reason: <i style="color:red;">{{$listing_details[0]->rejection_reason}}</i>
							@endif
						</div>

					</div>
				</div>
			</div>

			<!-- Footer Section -->
			<div style="background-color: #e9e9e9;">
                    <div style="float: right; margin-right: 10px;">
                        @if ($listing_details[0]->listing_status == 'Pending_approval')
                            <button class="btn btn-danger" onclick="rejectListing();">
                                Reject
                            </button>
                            @if ($listing_details[0]->status_admin == 'Approve')
	                            <button class="btn btn-success" onclick="updateListingStatus('{{$listing_details[0]->id}}','published');">
	                                Approve
	                            </button>
                            @endif
                        @elseif ($listing_details[0]->listing_status == 'Published')
                            <button class="btn btn-success" onclick="updateListingStatus('{{$listing_details[0]->id}}','published');">
                                Update
                            </button>
                        @else
                            <div style="float: right; padding: 15px">
                                <p>You are not allowed to approve or rejrect this listing, current status is <span style="color: green"> {{$listing_details[0]->listing_status}}</span> </p>
                            </div>
                        @endif
                    </div>
			</div>

		</div>
	@else
		<div><b>No Details Found.</b></div>
	@endif

	<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Rejection Reason</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        <input class="form-control" type="text" id="rejection_reason">
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-primary" onclick="updateListingStatus('{{$listing_details[0]->id}}','rejected');">Reject Listing</button>
	        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	      </div>
	    </div>
	  </div>
	</div>

@endsection

@section('javascript')
	<script type="text/javascript">
		function updateListingStatus(listing_id,status){

			var rejection_reason=$("#rejection_reason").val();

            var formData = new FormData();
            formData.append('listing_id', listing_id);
            formData.append('status', status);
            formData.append('rejection_reason', rejection_reason);
            if($('#inspection_report')[0].files[0] !== undefined) {
                formData.append('inspectionreport', $('#inspection_report')[0].files[0]);
            }

		     $.ajax({
		        type: 'post',
                processData: false,
                contentType: false,
		        data: formData,
		        url: "{{ url('updateListingStatus') }}",
		        success: function (data) {
		            Swal.fire('Status Updated.');
		            location.reload();
		        }
		     });

		}

		function rejectListing(){

			$("#rejectModal").modal('show');

		}

		function viewUser(user_id){
			var url='userprofile?id='+user_id;
			window.open(url,'_blank');
		}
	</script>
@endsection
