<?php include 'includes/session.php'; ?>
<?php include 'includes/status.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header content-page-title">
      <h1>
        Voters List
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Manage</a></li>
        <li class="active">Voters</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <?php
        if(isset($_SESSION['error'])){
          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> Error!</h4>
              ".$_SESSION['error']."
            </div>
          ";
          unset($_SESSION['error']);
        }
        if(isset($_SESSION['success'])){
          echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              ".$_SESSION['success']."
            </div>
          ";
          unset($_SESSION['success']);
        }
      ?>
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
              <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat"><i class="fa fa-plus"></i> New</a>
              <a href="#importcsv" data-toggle="modal" class="btn btn-primary btn-sm btn-flat"><i class="fa fa-plus"></i> Import CSV</a>
              <button id="bulk-delete-btn" class="btn btn-danger btn-sm btn-flat" data-toggle="modal" data-target="#bulk-delete" style="display: none;"><i class="fa fa-trash"></i> Delete Selected</button>
            </div>
            <div class="box-body">
              <table id="example1" class="table table-bordered">
                <thead>
                  <th><input type="checkbox" id="select-all"></th>
                  <th>Lastname</th>
                  <th>Firstname</th>
                  <th>Course</th>
                  <th>Student Number</th>
                  <th>Tools</th>
                </thead>
                <tbody>
              <?php
                $sql = "SELECT voters.*, courses.description AS course_description 
                        FROM voters 
                        JOIN courses ON voters.course_id = courses.id";
                $query = $conn->query($sql);
                
                while($row = $query->fetch_assoc()){
                  $image = (!empty($row['photo'])) ? '../images/'.$row['photo'] : '../images/profile.jpg';
                  echo "
                    <tr>
                      <td><input type='checkbox' class='select-item' value='".$row['id']."'></td>
                      <td>".$row['lastname']."</td>
                      <td>".$row['firstname']."</td>
                      <td>".$row['course_description']."</td>
                      <td>".$row['voters_id']."</td>
                      <td>
                        <button class='btn btn-success btn-sm edit btn-flat' data-id='".$row['id']."'><i class='fa fa-edit'></i> Edit</button>
                        <button class='btn btn-danger btn-sm delete btn-flat' data-id='".$row['id']."'><i class='fa fa-trash'></i> Delete</button>
                      </td>
                    </tr>
                  ";
                }
              ?>
            </tbody> 

              </table>
            </div>
          </div>
        </div>
      </div>
    </section>   
  </div>
    
  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/voters_modal.php'; ?>
</div>
<?php include 'includes/scripts.php'; ?>
<script>
$(function(){
  // Show/hide bulk delete button
  $('.select-item').on('change', function() {
    if ($('.select-item:checked').length > 0) {
      $('#bulk-delete-btn').show();
    } else {
      $('#bulk-delete-btn').hide();
    }
  });

  // Select/Deselect all checkboxes
  $('#select-all').click(function() {
    if(this.checked) {
      $('.select-item').each(function() {
        this.checked = true;
      });
    } else {
      $('.select-item').each(function() {
        this.checked = false;
      });
    }
    $('.select-item').trigger('change');
  });

  // Bulk delete action
  $('#confirm-bulk-delete-btn').click(function() {
    var selected = [];
    $('.select-item:checked').each(function() {
      selected.push($(this).val());
    });

    if(selected.length > 0) {
      $.ajax({
        type: 'POST',
        url: 'voters_delete.php',
        data: { ids: selected, bulk_delete: true },
        success: function(response) {
          location.reload();
        },
        error: function(xhr, status, error) {
          console.error(xhr.responseText);
        }
      });
    }
  });

  $(document).on('click', '.edit', function(e){
    e.preventDefault();
    $('#edit').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  $(document).on('click', '.delete', function(e){
    e.preventDefault();
    $('#delete').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  $(document).on('click', '.photo', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    getRow(id);
  });

});

function getRow(id){
  $.ajax({
    type: 'POST',
    url: 'voters_row.php',
    data: {id:id},
    dataType: 'json',
    success: function(response){
      $('.id').val(response.id);
      $('#edit_firstname').val(response.firstname);
      $('#edit_lastname').val(response.lastname);
      $('#edit_studentNumber').val(response.voters_id);
      $('#edit_course').val(response.course_id);
      $('#edit_password').val(response.password);
      $('.fullname').html(response.firstname+' '+response.lastname);
    }
  });
}
</script>
</body>
</html>