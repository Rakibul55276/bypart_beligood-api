@extends('layouts.app')

@section('content')

	<div class="container bypart-profile" style="padding-top: 30px;">
	    <div class="row gutters">
	        
	        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
	            <div class="card h-100">
	                <div class="card-body">
	                    <div class="row gutters">
	                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
	                            <h6 class="mb-2 text-primary">Notification List</h6>
	                        </div>
	                        <div id="in_page_noti_list">
	                        	
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>

@endsection

@section('javascript')
	<script type="text/javascript">
		$(document).ready(function(){

            $.ajax({
              type: 'post',
              url: "{{ url('notifications') }}",
              success: function (data) {
                var data=data.data;
                var count=data.count;
                var noti_list=data.data;

                $("#notification_count_top").html(count);

                var list_html='';
                var in_page_list='';

                for (var i = 0; i < noti_list.length; i++) {
                    var content=noti_list[i].notification_content;
                    var url=noti_list[i].url;
                    var is_seen=noti_list[i].is_seen;

                    var color='';

                    if(is_seen==0){
                    	color='background-color:#F5F5F5;';
                    }

                    list_html=list_html+'<div class="dropdown-divider"></div><a href="'+url+'" target="_blank" class="dropdown-item"><div style="white-space: pre-wrap;'+color+'">'+content+'</div></a>';
                    in_page_list=in_page_list+'<a href="'+url+'" target="_blank" class="dropdown-item"><div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12" style="padding: 15px!important; white-space: pre-wrap;'+color+'">'+content+'</div></a>';
                }

                list_html=list_html+'<div class="dropdown-divider"></div><div class="dropdown-divider"></div><a href="{{ url('allnotifications') }}" class="dropdown-item dropdown-footer" target="_blank">See All Notifications</a>';
                
                $("#in_page_noti_list").html(in_page_list);
                $("#notification_box").html(list_html);
              }
            });
        });

        var page=2;

        $(function() {

		  $(window).scroll(function (){

		        paginationCall(page);
		  });
		   
		});

		function paginationCall(page_pass){

			var data={
				page:page_pass
			};

			$.ajax({
              type: 'post',
              data:data,
              url: "{{ url('notifications') }}",
              success: function (data) {
                var data=data.data;
                var count=data.count;
                var noti_list=data.data;

                $("#notification_count_top").html(count);

                var list_html='';
                var in_page_list='';

                for (var i = 0; i < noti_list.length; i++) {
                    var content=noti_list[i].notification_content;
                    var url=noti_list[i].url;
                    var is_seen=noti_list[i].is_seen;

                    var color='';

                    if(is_seen==0){
                    	color='background-color:#F5F5F5;';
                    }

                    in_page_list=in_page_list+'<a href="'+url+'" target="_blank" class="dropdown-item"><div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12" style="padding: 15px!important; white-space: pre-wrap;'+color+'">'+content+'</div></a>';
                }

                
                $("#in_page_noti_list").append(in_page_list);
              }
            });

			page++;
		}
	</script>
@endsection