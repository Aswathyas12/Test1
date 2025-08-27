<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>eMist 2.0 - TERMS | Office Profile</title>

    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link href="{{ asset('css/bootstrap-glyphicons.css') }}" rel="stylesheet">

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" media="all" rel="stylesheet" type="text/css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('css/ionicons.min.css') }}" media="all" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap.min.css') }}" media="all" rel="stylesheet" type="text/css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('css/AdminLTE.min.css') }}" media="all" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="{{ asset('css/jquery.ui.autocomplete.css') }}" media="all" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="{{ asset('css/_all-skins.min.css') }}" media="all" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">

    <script src="{{ asset('js/jquery.js')}}"></script>

    <script type="text/javascript"> var APP_URL = '{{ URL::to('/') }}';
    </script>
    <script src="{{ asset('js/naac.js') }}"></script>

    <!-- Google Font -->
    <link href="{{ asset('css/font_new_css.css') }}" rel="stylesheet">
    
    <style type="text/css">

    .required:after
    {
      content:'*';
      color:red;
      padding-left:5px; 
    }
    
    </style>
  </head>
  <body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
      @include('admin.layout.header')
      @include('admin.layout.sidenav')
      <div class="content-wrapper">
        <section class="content">
          <div class="panel panel-primary">
            <div class="panel panel-heading">Tally Report</div>
            <div class="panel-body">
              <div class='row'>
     <div class="form-group col-md-12">
    <div class="row">
  <div class="form-group col-md-6">
    {{ Form::label('category', 'Report Category', ['class' => 'control-label required']) }}
    {!! Form::select('category', ['' => '--Select--', '1' => 'Supply', '2' => 'Purchase'], null, ['class' => 'form-control', 'id' => 'rprt_cat', 'onblur' => 'checkField(rprt_cat,"select")']) !!}
    <div id="rprt_cat_msg" class="text-danger small"></div>
  </div>

  <div class="form-group col-md-6">
    {{ Form::label('month', 'Month', ['class' => 'control-label required']) }}
    {!! Form::select('month', ['' => '--Select--'] + $months, null, ['class' => 'form-control', 'id' => 'month_id', 'onblur' => 'checkField(month_id,"select")']) !!}
    <div id="month_id_msg" class="text-danger small"></div>
  </div>
</div>

<div class="row">
  <div class="form-group col-md-6">
    {{ Form::label('year', 'Year', ['class' => 'control-label required']) }}
    {!! Form::text('year', null, ['class' => 'form-control', 'id' => 'year_id', 'placeholder' => 'Enter year', 'maxlength' => 4, 'onblur' => 'checkField(year_id,"null")']) !!}
    <div id="year_id_msg" class="text-danger small"></div>
  </div>

  <div class="form-group col-md-6">
    {{ Form::label('type', 'Type', ['class' => 'control-label required']) }}
    {!! Form::select('type', ['' => '--Select--', '0' => 'All', '1' => 'Payment', '2' => 'Normal'], null, ['class' => 'form-control', 'id' => 'type_id', 'onblur' => 'checkField(type_id,"select")']) !!}
    <div id="type_id_msg" class="text-danger small"></div>
  </div>
</div>

<div class="row">
  <div class="form-group col-md-12 text-center">
    {!! Form::button('Show Excel', ['type' => 'button', 'class' => 'btn btn-primary', 'id' => 'show_btn', 'onclick' => 'Showexcel_details()']) !!}
  </div>
</div>



              {!!Form::close()!!}
            </div>
          </div>
        </section>
      </div>
    </div>
  </body>

    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/fastclick.js') }}"></script>
    <script src="{{ asset('js/adminlte.min.js') }}"></script>
    <script src="{{ asset('js/jquery-ui.min.js')}}"></script>
    <script src="{{ asset('js/select2.full.min.js') }}"></script>

    @include('admin.layout.footer')

    <script type="text/javascript">

      
      $('#tallyreport-main-list').addClass('menu-open');
      $('#tallyreport-main-list').addClass('active');
      $('#tallyitemwise-sublist').addClass('active');

      function Showexcel_details()
      {
        // var officeid=$('#office_id').val();
        var category=$('#rprt_cat').val();
        var month=$('#month_id').val();
        var year=$('#year_id').val();
        var type=$('#type_id').val();
        


        $.ajax({

                url: APP_URL+"/purchase/Showexcel_details",
                type: 'GET',
                dataType:"json",
                 data:{category:category,month:month,year:year,type:type},
                success: function(result)
                { 
                  var html_data=result[0];
                  var fileno=result[1];
                  let file = new Blob([html_data], {type:"application/vnd.ms-excel"});
                  let url = URL.createObjectURL(file);
                  let a = $("<a />",{
                                    href: url,
                                    download: fileno+".xls"
                                    })
                  .appendTo("body")
                  .get(0)
                  .click();

                }
        });

      }


    </script>
