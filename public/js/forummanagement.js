function deletePost(post_id){

    var data={
        id:post_id
        
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
            url: 'deletepost',
            success: function (data) {
                location.reload();
            }
        });
      }
    });
    
}

function deleteComment(comment_id){

    var data={
        comment_id:comment_id
        
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
            url: 'deletecomment',
            success: function (data) {
                location.reload();
            }
        });
      }
    });
    
}

function restoreComment(comment_id){

    var data={
        comment_id:comment_id
        
    };

    Swal.fire({
      title: "Are you sure want to Restore?",
      icon: "warning",
      buttons: true,
      dangerMode: true,
    })
    .then((willDelete) => {
      if (willDelete.isConfirmed===true) {
        $.ajax({
            type: 'post',
            data: data,
            url: 'restorecomment',
            success: function (data) {
                location.reload();
            }
        });
      }
    });
    
}

function restorePost(post_id){

    var data={
        id:post_id
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
            url: 'restorepost',
            success: function (data) {
                location.reload();
            }
        });
      }
    });
    
}

function editPost(post_id){
	
    var data={
        id:post_id
    };

    $.ajax({
        type: 'post',
        data: data,
        url: 'editpost',
        success: function (data) {
            $("#edit_modal_append").html(data);

            $("#postModal").modal("show");
        }
    });
}

function modifyPostDetails(post_id){
    
    var category=$("#post_category").val();
    var subject=$("#post_subject").val();

    var data={
        id:post_id,
        category:category,
        subject:subject

    };

    $.ajax({
        type: 'post',
        data: data,
        url: 'modifypostpetails',
        success: function (data) {
            location.reload();
        }
    });
}
