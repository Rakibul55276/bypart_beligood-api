function deleteUsers(user_id){

    var data={
        id:user_id

    };

    Swal.fire({
      title: "Are you sure want to Delete?",
      icon: "warning",
      buttons: true,
      dangerMode: true,
    })
    .then((willDelete) => {
      if (willDelete.isConfirmed===true) {
        $.ajax({
            type: 'post',
            data: data,
            url: 'deleteUsers',
            success: function (data) {
                location.reload();
            }
        });
      }
    });

}

function restoreUsers(user_id){

    var data={
        id:user_id
    };

    Swal.fire({
      title: "Are you sure want to Restore?",
      icon: "warning",
      buttons: true,
      dangerMode: true,
    }).then((willDelete) => {
      if (willDelete.isConfirmed===true) {
        $.ajax({
            type: 'post',
            data: data,
            url: 'restoreUsers',
            success: function (data) {
                location.reload();
            }
        });
      }
    });

}

function editUser(user_id){
	var url='userprofile?id='+user_id;
	window.location.href = url;
}

function changePassword(){
    var current_password=$("#current_password").val();
    var new_password=$("#new_password").val();
    var confirm_password=$("#confirm_password").val();
    var user_id=$("#user_id").val();

    if(new_password!=''){
        if(new_password!=confirm_password){
            swal("Passwrod Does not Match.")
        }else{
            var data={
                current_password:current_password,
                new_password:new_password,
                user_id:user_id

            };

            $.ajax({
                type: 'post',
                data: data,
                url: 'changePassword',
                success: function (data) {
                    if(typeof data =='object'){

                    }else{
                        data=JSON.Parse(data);
                    }

                    var status=data.status;

                    if(status==200){
                        swal("Password Successfully Updated.");
                        $("#passwordModal").modal('hide');
                    }else{
                        swal("Current Password Doesn't Match.");
                    }
                }
            });
        }
    }else{
        swal("Password cannot be empty.");
    }

}

function editUserData(){
    var data={
        user_id:$("#user_id").val(),
        first_name:$("#first_name").val(),
        last_name:$("#last_name").val(),
        mobile_no:$("#mobile_no").val(),
        email:$("#email").val(),
        address:$("#address").val(),
        city:$("#city").val(),
        state:$("#state").val(),
        postal_code:$("#postal_code").val(),
        country:$("#country").val(),
        status:$("#status").val(),
        status_admin:$("#status_admin").val(),
        bp_point:$("#bp_point").val()
    };

    $.ajax({
        type: 'post',
        data: data,
        url: 'updateUser',
        success: function (data) {
            // location.reload();
            Swal.fire("Account Info Successfully Updated.");
        }
    });

}

function editCompanyData(){
    var data={
        id:$("#company_id").val(),
        company_name:$("#company_name").val(),
        company_email:$("#company_email").val(),
        company_phone_number:$("#company_phone_number").val(),
        address:$("#company_address").val(),
        city:$("#company_city").val(),
        state:$("#company_state").val(),
        zip_code:$("#zip_code").val(),
        country:$("#company_country").val(),
        old_cpompany_registration_no:$("#old_cpompany_registration_no").val(),
        new_company_registration_no:$("#new_company_registration_no").val(),
        company_url:$("#company_url").val()

    };

    $.ajax({
        type: 'post',
        data: data,
        url: 'updateCompany',
        success: function (data) {
            // location.reload();
            Swal.fire("Company Info Successfully Updated.");
        }
    });

}

function addUserData(){
    var data={
        user_id:$("#user_id").val(),
        first_name:$("#first_name").val(),
        last_name:$("#last_name").val(),
        mobile_no:$("#mobile_no").val(),
        email:$("#email").val(),
        password:$("#password").val(),
        address:$("#address").val(),
        city:$("#city").val(),
        state:$("#state").val(),
        postal_code:$("#postal_code").val(),
        country:$("#country").val()

    };

    $.ajax({
        type: 'post',
        data: data,
        url: 'addNewUser',
        success: function (data) {
            Swal.fire("Admin Succesfully Added. Reload to Manage.");
        }
    });

}
