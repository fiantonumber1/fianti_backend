
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="MnTJdiM4AdS8ocqdbH19EE8njvxNh3LAbSAU55hM">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="https://e-office.inka.co.id/logo/favicon.png">
    <title>INKA office | Manajemen Rapat </title>
    <style>
        #map {
            margin: 10px;
            width: 600px;
            height: 300px;  
            padding: 10px;
          }
    </style>

    <link href="https://e-office.inka.co.id/elaadmin/css/lib/toastr/toastr.min.css" rel="stylesheet">
    <link  href="https://e-office.inka.co.id/elaadmin/css/lib/html5-editor/bootstrap-wysihtml5.css" rel="stylesheet" />
    <link  href="https://e-office.inka.co.id/elaadmin/css/lib/html5-editor/wysiwyg-color.css" rel="stylesheet" />
    <!-- Bootstrap Core CSS -->
    <link href="https://e-office.inka.co.id/elaadmin/css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="https://e-office.inka.co.id/elaadmin/css/helper.css" rel="stylesheet">
    <link href="https://e-office.inka.co.id/elaadmin/css/style.css" rel="stylesheet">

    <link media="all" type="text/css" rel="stylesheet" href="https://e-office.inka.co.id/bsbmd/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css">

    <link media="all" type="text/css" rel="stylesheet" href="https://e-office.inka.co.id/bsbmd/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css">


    <link media="all" type="text/css" rel="stylesheet" href="https://e-office.inka.co.id/bsbmd/plugins/dropzone/dropzone.css">


    <!-- Multi Select Css -->
    <link media="all" type="text/css" rel="stylesheet" href="https://e-office.inka.co.id/bsbmd/plugins/multi-select/css/multi-select.css">


    <!-- Bootstrap Spinner Css -->
    <link media="all" type="text/css" rel="stylesheet" href="https://e-office.inka.co.id/bsbmd/plugins/jquery-spinner/css/bootstrap-spinner.css">


    <!-- Bootstrap Tagsinput Css -->
    <link media="all" type="text/css" rel="stylesheet" href="https://e-office.inka.co.id/bsbmd/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css">


    <!-- Bootstrap Select Css -->
    <link media="all" type="text/css" rel="stylesheet" href="https://e-office.inka.co.id/bsbmd/plugins/bootstrap-select/css/bootstrap-select.css">


    <!-- noUISlider Css -->
    <link media="all" type="text/css" rel="stylesheet" href="https://e-office.inka.co.id/bsbmd/plugins/nouislider/nouislider.min.css">

    
    <link media="all" type="text/css" rel="stylesheet" href="https://e-office.inka.co.id/bsbmd/plugins/node-waves/waves.css">

    <link media="all" type="text/css" rel="stylesheet" href="https://e-office.inka.co.id/bsbmd/plugins/animate-css/animate.css">

    <link media="all" type="text/css" rel="stylesheet" href="https://e-office.inka.co.id/bsbmd/plugins/morrisjs/morris.css">

  
    <link href="https://e-office.inka.co.id/css/style.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="https://e-office.inka.co.id/datatables/datatables.min.css"/>
    <link rel="stylesheet" href="https://e-office.inka.co.id/css/lib/colorbox.css"></link>
    <link href="https://e-office.inka.co.id/css/jAlert.css" rel="stylesheet">

    
    <script src="https://e-office.inka.co.id/elaadmin/js/lib/jquery/jquery.min.js"></script>

        <script src="https://e-office.inka.co.id/js/lib/jquery.colorbox-min.js"></script>
</head>

<body class="fix-header fix-sidebar">
    <!-- Preloader - style you can find in spinners.css -->
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
			<circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> </svg>
    </div>
    <!-- Main wrapper  -->
    <div id="main-wrapper">
        
        <div class="header">
            <nav class="navbar top-navbar navbar-expand-md navbar-light">
                <!-- Logo -->
                <div class="navbar-header">
                    <a class="navbar-brand" href="https://e-office.inka.co.id">
                        <!-- Logo icon -->
                        <b><img src="https://e-office.inka.co.id/logo/logo-sm-icon.png" alt="homepage" class="dark-logo" /></b>
                        <!--End Logo icon -->
                        <!-- Logo text -->
                        <span><img src="https://e-office.inka.co.id/logo/logo-sm-text.png" alt="homepage" class="dark-logo" /></span>
                    </a>
                </div>
                <!-- End Logo -->
                <div class="navbar-collapse">
                    <!-- toggle and nav items -->
                    <ul class="navbar-nav mr-auto mt-md-0">
                        <!-- This is  -->
                        <li class="nav-item"> <a class="nav-link nav-toggler hidden-md-up text-muted  " href="javascript:void(0)"><i class="mdi mdi-menu"></i></a> </li>
                        <li class="nav-item m-l-10"> <a class="nav-link sidebartoggler hidden-sm-down text-muted  " href="javascript:void(0)"><i class="ti-menu"></i></a> </li>
                        
                       

                        
                                                <li class="nav-item nav-menu hidden-md-down ">
                            <a class="nav-link hidden-md-down" href="https://e-office.inka.co.id/office/absensi/dashboard">Absensi</a>
                        </li>
                        
                                                <li class="nav-item nav-menu hidden-md-down ">
                            <a class="nav-link hidden-md-down" href="https://e-office.inka.co.id/office/ess/sppd">ESS</a>
                        </li>
                        
                                                <li class="nav-item nav-menu hidden-md-down ">
                            <a class="nav-link hidden-md-down" href="https://e-office.inka.co.id/office/penilaian/semester">Penilaian</a>
                        </li>
                        
                                                <li class="nav-item nav-menu hidden-md-down active">
                            
                            <a class="nav-link hidden-md-down" href="https://e-office.inka.co.id/office/surat/filter-surat-masuk">Persuratan</a>
                        </li>
                                                
                                                <li class="nav-item nav-menu hidden-md-down ">
                            <a class="nav-link hidden-md-down" href="https://e-office.inka.co.id/office/lain/izin-barang">Permintaan</a>
                        </li>
                                                
                                                
                        
                                                
                                                      
                        <li class="nav-item nav-menu dropdown hidden show-md main-nav">
                            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Menu </a>
                            <div class="dropdown-menu dropdown-menu-left animated zoomIn" style="height:400%;width:40%">
                                <ul class="dropdown-user"  style="height:10%;">
                                    
                                                                        <li class="nav-item nav-menu  ">
                                        <a class="nav-link " href="https://e-office.inka.co.id/office/absensi/dashboard">Absensi</a>
                                    </li>
                                    
                                                                        <li class="nav-item nav-menu  ">
                                        <a class="nav-link " href="https://e-office.inka.co.id/office/ess/sppd">ESS</a>
                                    </li>
                                                                        
                                                                        <li class="nav-item nav-menu ">
                                        <a class="nav-link " href="https://e-office.inka.co.id/office/penilaian/semester">Penilaian</a>
                                    </li>
                                    
                                                                        <li class="nav-item nav-menu  active">
                                        <a class="nav-link " href="https://e-office.inka.co.id/office/surat/surat-masuk">Persuratan</a>
                                    </li>
                                                                        
                                                                        <li class="nav-item nav-menu  ">
                                        <a class="nav-link " href="https://e-office.inka.co.id/office/lain/izin-barang">Permintaan</a>
                                    </li>
                                                                                                        </ul>
                            </div>
                        </li>
                       
                    </ul>
                    <!-- User profile and search -->
                    <ul class="navbar-nav my-lg-0">
                        <!-- Comment -->
                        
                        <!-- End Comment -->
                        <!-- Messages -->
               
                        <!-- End Messages -->
                        <!-- Profile -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-muted  " href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="https://e-office.inka.co.id/elaadmin/images/users/images.png" alt="user" class="profile-pic" />  
                             KHARISMA CAHAYA AQLI
                            </a>
                            <div class="dropdown-menu dropdown-menu-right animated zoomIn">
                                <ul class="dropdown-user">
                                    <li><a href="https://e-office.inka.co.id/profil"><i class="ti-user"></i> Profile</a></li>
                                    <!-- <li><a href="#"><i class="ti-wallet"></i> Ubah Password</a></li> -->
                                    <li><a href="#" href="https://e-office.inka.co.id/logout" onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();"><i class="fa fa-power-off"></i> Logout</a></li>
                                    <form id="logout-form" action="https://e-office.inka.co.id/logout" method="POST" style="display: none;">
                                        <input type="hidden" name="_token" value="MnTJdiM4AdS8ocqdbH19EE8njvxNh3LAbSAU55hM">
                                    </form>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
        <!-- End header header -->        <!-- Left Sidebar  -->
<div class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <li class="nav-devider"></li>
                
                                                                                                                                                                                                                                                                    <li class="nav-label">PERSURATAN</li>

                                        
    <li> 
    	<a href="https://e-office.inka.co.id/office/surat/surat-masuk" aria-expanded="false"><i class="fa fa-inbox"></i><span class="hide-menu">Surat Masuk  <!--<span class="label label-rouded label-info pull-right">12</span></span>--></a>
    </li>
    <li>
    	<a href="https://e-office.inka.co.id/office/surat/surat-keluar" aria-expanded="false"><i class="fa fa-hdd-o"></i><span class="hide-menu">Surat Keluar  <!--<span class="label label-rouded label-danger pull-right">12</span></span>--></a>
    </li>

                            
    
    <li> 
    	<a href="https://e-office.inka.co.id/office/surat/disposisi-masuk" aria-expanded="false"><i class="fa fa-inbox"></i><span class="hide-menu">Disposisi Masuk<!--<span class="label label-rouded label-info pull-right">12</span></span>--></a>
    </li>
    <li> 
        <a href="https://e-office.inka.co.id/office/surat/disposisi-keluar" aria-expanded="false"><i class="fa fa-inbox"></i><span class="hide-menu">Disposisi Keluar<!--<span class="label label-rouded label-info pull-right">12</span></span>--></a>
    </li>
  
    
    
    <li>
    	<a href="https://e-office.inka.co.id/office/surat/manajemen-rapat" aria-expanded="false"><i class="fa fa-briefcase"></i><span class="hide-menu">Manajemen Rapat </span></a>
    </li>
    
        <li>
    	<a href="https://e-office.inka.co.id/office/surat/notulen" aria-expanded="false"><i class="fa fa-pencil"></i><span class="hide-menu">Notulen </span></a>
    </li>
    

    
        
                                                                            
                                                    </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</div>
<!-- End Left Sidebar  -->        
        <!-- Page wrapper  -->
        <div class="page-wrapper">
            <!-- Bread crumb -->
            <!-- <div class="row page-titles">
                <div class="col-md-5 align-self-center">
                    <h3 class="text-primary">Manajemen Rapat</h3> </div>
                <div class="col-md-7 align-self-center">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div> -->
            <!-- End Bread crumb -->
            <div class="main-content-wrapper">
                <!-- Container fluid  -->
                <div class="container-fluid">
                    <div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-title">
				<h4>List Rapat</h4>
            </div>
            <div class="card-top-button">
                <div class="row">
                    <div class="col-sm-8">
                       
                    </div>
                    <div class="col-sm-4">
                        
                        <a href="https://e-office.inka.co.id/office/surat/manajemen-rapat/create" class="btn btn-sm btn-outline-danger">Buat Baru</a>
                        
                    </div>
                </div>
            </div>
            <div class="card-body p-b-0">
                <ul class="nav nav-tabs customtab" role="tablist">
                    	
                    <li class="nav-item"> <a class="nav-link active show myrapat" data-toggle="tab"  role="tab" aria-selected="true"><span class="hidden-sm-up"><i class="ti-home"></i></span> <span class="hidden-xs-down">Rapat</span></a> </li>
                    
                            
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active show" id="myrapat" role="tabpanel">
                        <div class="table-responsive table-sm">
                            <table id="datatable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>No Undangan</th>
                                        <th>NIP</th>
                                        <th>Pengundang</th>
                                        <th>Tanggal</th>
                                        <th>Tempat</th>
                                        <th>Agenda</th>
                                        <th>Status</th>
                                        <th><div align="left">Aksi</div></th>
                                        <!-- <th></th> -->          
                                    </tr>
                                </thead>
                                <tbody>
                                        
                                </tbody>
                            </table>
                        </div>
                    </div>
                  
                    
                    
                </div>
            <!-- End of message list -->

            <!-- Message footer -->
	            <div class="row">
	               
	            </div>
            
			</div>
		</div>	
	</div>
</div>
    <div class="modal" id="detail-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog wide-modal" style="max-width: 50%" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    Detail Rapat
                </div>
                <div class="modal-body">
                    <div class="row detail-header m-b-20">
                        <div class="col-6">
                            <div class="detail-title">
                                <strong>Nomor Undangan </strong><br/>
                                <noundangan></noundangan>
                            </div>
                            <div class="detail-subtitle">
                                
                            </div>
                        </div>
                        
                    </div>
                    <div class="row detail-body m-b-20">
                        <div class="col-12 detail-content">
                            <strong>Rapat Ke</strong><br/>
                            <rapatke></rapatke>
                        </div>

                        <div class="col-12 detail-content">
                            <strong>Tanggal Rapat</strong><br/>
                            <tanggal></tanggal>
                        </div>

                        <div class="col-12 detail-content">
                            <strong>Waktu Pelaksanaan</strong><br/>
                            <waktu></waktu>
                        </div>

                        <div class="col-12 detail-content">
                            <strong>Tempat Rapat</strong><br/>
                            <tempat></tempat>
                        </div>

                        <div class="col-12 detail-content">
                            <strong>Agenda</strong><br/>
                            <agenda></agenda>
                        </div>

                        

                        
                        <div class="col-12 detail-content">
                                <strong>Pengundang Rapat</strong><br/>
                                <pengundang></pengundang>
                        </div>
                        <div class="col-12 detail-content">
                                <strong>Pemimpin Rapat</strong><br/>
                                <pemimpin></pemimpin>
                        </div>
                        <div class="col-12 detail-content">
                                <strong>Pembuat Notulen</strong><br/>
                                <pembuatnotulen></pembuatnotulen>
                        </div>
                        
                        
                        
                        <div class="col-12 detail-content">
                            <strong>Notulensi</strong><br/>
                            <file-notulen></file-notulen>
                        </div>
                        <hr>
                        <div class="col-12 detail-content">
                            <div class="table-responsive table-sm">
                                <strong>Peserta Rapat</strong><br/>
                                <table id="peserta" class="table table-striped">
                                    <tbody>
                                                
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr>
                        <div class="col-12 detail-content">
                        <div class="table-responsive table-sm">
                            <strong>Lampiran</strong><br/>
                            <table id="lampiran" class="table table-striped">
                                <tbody>
                                            
                                </tbody>
                            </table>
                        </div>
                        </div>
                        <hr>
                        <div class="col-12 detail-content">
                        <div class="table-responsive table-sm">
                            <strong>Tembusan Rapat</strong><br/>
                            <table id="tembusan" class="table table-striped">
                                <tbody>
                                            
                                </tbody>
                            </table>
                        </div>
                    </div>
                        <hr>
                        <div class="col-12 detail-timestamp">
                            &nbsp;&nbsp;&nbsp;&nbsp; <strong> Status:  </strong><br/>&nbsp;<status></status>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
								
                    <id></id>
                </div>
            </div>
        </div>
    </div>
   
                </div>
                <!-- End Container fluid  -->
            </div>
            <!-- footer -->
            <footer class="footer"> © 2018 All rights reserved</footer>
            <!-- End footer -->
        </div>
        <!-- End Page wrapper  -->
    </div>
    <!-- End Wrapper -->
    <!-- All Jquery -->
    
    <!-- Bootstrap tether Core JavaScript -->
    <script src="https://e-office.inka.co.id/elaadmin/js/lib/bootstrap/js/popper.min.js"></script>
    <script src="https://e-office.inka.co.id/elaadmin/js/lib/bootstrap/js/bootstrap.min.js"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="https://e-office.inka.co.id/elaadmin/js/jquery.slimscroll.js"></script>
    <!--Menu sidebar -->
    <script src="https://e-office.inka.co.id/elaadmin/js/sidebarmenu.js"></script>
    <!--stickey kit -->
    <script src="https://e-office.inka.co.id/elaadmin/js/lib/sticky-kit-master/dist/sticky-kit.min.js"></script>

    <script src="https://e-office.inka.co.id/elaadmin/js/lib/toastr/toastr.min.js"></script>
    <!-- scripit init-->
    <script src="https://e-office.inka.co.id/elaadmin/js/lib/toastr/toastr.init.js"></script>
    <script src="https://e-office.inka.co.id/elaadmin/js/lib/html5-editor/wysihtml5-0.3.0.js"></script>
    <script src="https://e-office.inka.co.id/elaadmin/js/lib/html5-editor/bootstrap-wysihtml5.js"></script>
    <script src="https://e-office.inka.co.id/elaadmin/js/lib/html5-editor/wysihtml5-init.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
    
    
    <!--Custom JavaScript -->
    <script src="https://e-office.inka.co.id/elaadmin/js/custom.min.js"></script>

    <script type="text/javascript" data-turbolinks-track="reload">
        $(".currency-format").on('keyup', function(evt){
            if (evt.which != 110 ){//not a fullstop
                var n = parseFloat($(this).val().replace(/\,/g,''),10);
                $(this).val(n.toLocaleString());
            }
        });

        function openStatusToast() {
                    }

        // document.addEventListener("turbolinks:load", function() {
        $(document).ready( function () {
            toastr.options.closeButton = true;
            toastr.clear();
            
            openStatusToast();
        });
    </script>
        <script type="text/javascript" src="https://e-office.inka.co.id/datatables/datatables.min.js"></script>
    <script src="https://e-office.inka.co.id/js/jAlert.js"></script>
	<script src="https://e-office.inka.co.id/js/jAlert-functions.js"></script>
	<script type="text/javascript">
		$(document).ready( function () {
            datatable();
            $(document).delegate('.delete','click', function(){
				let id = $(this).attr('data');
				let link = "https://e-office.inka.co.id/office/surat/rapat-delete";
				link = link+'?id='+id;
                
				$.jAlert({
					'title':'Konfirmasi Hapus Data Rapat',
					'content':'Apakah anda yakin akan menghapus data rapat ini  ?' ,
					'btns': [
						{'text':'Tidak', 'closeAlert':true, 'onClick': function(){console.log('Tidak Menghapus Data Rapat'); }},
						
						{'text': 'Ya, saya yakin', 'closeAlert':true, 'onClick': function(){ window.location.href = link; }}
					]

				});
			});
            $(document).delegate('.myrapat', 'click', function(){
				$('#myrapat').show();
                datatable();
            });
            $(document).delegate('.btn-detail', 'click', function(){
                $('#detail-modal').modal('show');
                detailUrl = "https://e-office.inka.co.id/office/surat/rapat-detail/:id";
				var id = $(this).attr('data');
				var toapprove = $(this).attr('isapprove') *1;
				console.log(toapprove);
				detailUrl = detailUrl.replace(':id', id);
				$.ajax({
		                url: detailUrl,
		                type: "GET",
		                dataType: "json",
		                success:function(data) {
                            var pemimpin="-";
                            var pengundang="-";
                            var pembuatnotulen="-";
                            var status=data.status *1;
                            if(data.pengundang_rapat.nama != ""){
                                pengundang= data.pengundang_rapat.nama +'( '+data.pengundang_rapat.nip+' )';
                            }
                            if(data.pemimpin_rapat.nama != ""){
                                pemimpin= data.pemimpin_rapat.nama +'( '+data.pemimpin_rapat.nip+' )';
                            }
                            if(data.pembuat_notulen.nama != ""){
                                pembuatnotulen= data.pembuat_notulen.nama +'( '+data.pembuat_notulen.nip+' )';
                            }
                            var status2="Belum Disetujui";
                            if(status==1){
                                status2="Sudah Disetujui";
                            }
                            var lampiran ="";
                            var tembusan ="";
                            var peserta="";
                            var i=0;
                            var btnapprove=' <div class="btn-group right">'+
                                            '<a approve="1" data="'+data.id+'" class="btn btn-danger  btn-approve"></i> Setujui</a>'+
                                          '</div>';
                            for(i=0; i<data.file.length; i++){
                                link="https://e-office.inka.co.id";
                                lampiran+='<tr>'+'<td><a href="'+link+data.file[i].link+'" >'+data.file[i].nama+'</a></td></tr>';
                            }
                            for(i=0; i<data.tembusan.length; i++){
                                tembusan+='<tr><td>'+data.tembusan[i].nama+'</td><td>'+data.tembusan[i].nip+'</td></tr>';
                            }
                            for(i=0; i<data.peserta_rapat.length; i++){
                                peserta+='<tr><td>'+data.peserta_rapat[i].nama+'</td><td>'+data.peserta_rapat[i].nip+'</td></tr>'; 
                            }
                            if(toapprove==1){
                                $('id').html(btnapprove);
                            }
                            let fileNotulen = data.file_notulen ? "https://e-office.inka.co.id"+data.file_notulen.link : null;
                            let linkNotulen = fileNotulen ? '<a target="_blank" href="'+fileNotulen+'">File Notulen </a>' : null;
                            $('#lampiran tbody').html(lampiran);
                            $('#tembusan tbody').html(tembusan);
                            $('#peserta tbody').html(peserta);
                            $('pembuat').html(data.creator.nama +'( '+data.creator.nip+' )');
                            $('pengundang').html(pengundang);
                            $('pemimpin').html(pemimpin);
                            $('pembuatnotulen').html(pembuatnotulen);
                            $('waktu').html(data.dari_jam+' '+data.sampai_jam);
                            $('rapatke').html(data.urutan_rapat);
                            $('noundangan').html(data.no_undangan);
                            $('tempat').html(data.tempat+'( '+data.kode_ruang+' )');
                            $('agenda').html(data.agenda);
                            $('tanggal').html(data.tanggal_rapat);
                            $('file-notulen').html(linkNotulen);
                            $('status').html(status2);
                        }
                    
		        });

            });
            $(document).delegate('.btn-approve', 'click', function(){
                $('#detail-modal').modal('hide');
                var id= $(this).attr('data');
                var status= $(this).attr('approve')*1;
                //alert("nip: " + data + "<br/>nilai : " +value);
                var formData = {
                        'id_rapat'              		: id,
                        'status'                        : status,
                    };
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
				$.ajax({
                    type        : 'POST',
                    url         : 'https://e-office.inka.co.id/office/surat/approve-rapat', 
                    data        : formData, 
					success:function(data2) {
						$('#detail-modal').modal('hide');
						
						datatable();
                        console.log('sukses');
					}
                    })
                    .done(function(data3) {
						
                    console.log('done');
                    })
                    .fail(function(jqXHR, textStatus, errorThrown){
                    console.log(jqXHR);
                });
            });
		});
    </script>
    <script>
        function datatable(){
            var url = "https://e-office.inka.co.id/office/surat/rapat";
			var table = $('#datatable').DataTable({
				// ordering: false,
                columnDefs: [
   				 				{ "orderable": false, "targets": 4 },
                                { "orderable": false, "targets": 6 },
                                { "orderable": false, "targets": 7 },
                                { "orderable": false, "targets": 8 },
							],
                 order: [[ 0, "desc" ]],
				destroy: true,
				processing: true,
				serverSide: true,
				ajax: url,
				columns: [
                        { 
					        data: 'id',
					    },
						{ 
					        data: 'no_undangan',
					    },
						{
							data: 'pegundang_rapat',
							
						},
                        {
							data: 'nama',
							
						},
						{
							data: 'tanggal_rapat',
							

						},
                        {
							data: 'ruangan.nama_ruang',
                            defaultContent: "--",
							

						},
                        {
							data: null,
                            render:function(data){
                                let agenda = '<p style="font-weight:bold;">'+data.agenda+'</p>';
                                return agenda;
                            }
							

						},
						
					
						{
							data:null,
							searchable:false,
							render:function(data){
								var status="";
								if(data.status3=="Belum Disetujui"){
									status+='<span class="label label-warning">'+data.status3+'</span>';
								}
								if(data.status3=="Sudah Disetujui"){
									status+='<span class="label label-success">'+data.status3+'</span>';
								}
								if(data.status3=="Ditolak"){
									status+='<span class="label label-danger">'+data.status3+'</span>';
								}
								return status;
								
							}
							

						},
						
					    { 
					        data: null,
							searchable: false,
					        render: function(data){
								
							
                                var login="991600011";
								var linkEdit="";
                                var linkDelete="";
                                var linkApprove="";
                                var linkDetail="";
                                var print = "https://e-office.inka.co.id/office/surat/rapat-print/:id";
                                var cetak = "https://e-office.inka.co.id/office/surat/rapat-print-pdf/:id";
                                    cetak = cetak.replace(':id', data.id);
								print = print.replace(':id', data.id);
								if(data.status==0 && data.creator != data.pegundang_rapat && data.pegundang_rapat==login ){
									 linkApprove='<div class="peer">'+
													'<a isapprove="1" data="'+data.id+'" class="btn  btn-sm btn-outline-danger btn-detail">Approve</a>'+
												'</div>';
								}
                                if(data.creator == login  && data.status==0){
                                    var Edit = "https://e-office.inka.co.id/office/surat/manajemen-rapat/:id/edit";
								    Edit = Edit.replace(':id', data.id);
                                    linkEdit='<div class="peer">'+
													'<a href="'+Edit+'" data="'+data.id+'" class="btn  btn-sm btn-outline-danger ">Edit</a>'+
												'</div>';
                                    var Delete = "https://e-office.inka.co.id/office/surat/rapat-delete";
								    Delete = Delete+'?id='+data.id;
                                    linkDelete='<div class="peer">'+
													'<a href="javascript:void(0)" data="'+data.id+'" class="btn  btn-sm btn-outline-danger delete">Hapus</a>'+
												'</div>';

                                }
								var aksi=  '<div class="peers">'+linkApprove+linkEdit+linkDelete+
                                                '<div class="peer">'+
													'<a  isapprove="0" data="'+data.id+'" class="btn  btn-sm btn-outline-danger btn-detail">Detail</a>'+
												'</div>'+
                                                '<div class="peer">'+
													'<a  href="'+print+'" isapprove="0" data="'+data.id+'" class="btn btn-sm btn-outline-info  btn-print cboxElement">Cetak</a>'+
												'</div>'+
                                                // '<div class="peer">'+
												// 	'<a  href="'+cetak+'" isapprove="0" data="'+data.id+'" class="btn btn-sm btn-outline-info">Cetak test</a>'+
												// '</div>'+
												
                                            '</div>';
                                $(".btn-print").colorbox({height:"750px", width:"742px"});
                                             
					        	return aksi;
					        }
					        
					    },
						
						
						
					]
			});
        }
    </script>
</body>

</html>