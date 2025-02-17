<!-- resources/views/auth/all_users.blade.php -->

@extends('layouts.table1')

@php
  $categoryprojectbaru = json_decode($category, true)[0];
  $categoryproject = trim($categoryprojectbaru, '"'); // Remove the extra double quotes
  $listpic = json_decode($categoryproject, true);
  $listpic[] = "superuser";
@endphp

@section('container1') 
<div id="encoded-data" data-listprogressnodokumen="{{ $encodedFlattenedActivityData }}"></div>
<div class="col-sm-6">
  <h1>Anggota</h1>
</div>
<div class="col-sm-6">
  <ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{ url("") }}">Home</a></li>
    <li class="breadcrumb-item active">Anggota</li>
  </ol>
</div>
@endsection
@section('container2')
<h3 class="card-title">Page: All Users</h3>
@endsection


@section('container3')
<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item" role="presentation">
    <a class="nav-link active" id="progress-tab" data-toggle="tab" href="#progress" role="tab" aria-controls="progress"
      aria-selected="true">Progress</a>
  </li>
  <li class="nav-item" role="presentation">
    <a class="nav-link" id="history-tab" data-toggle="tab" href="#history" role="tab" aria-controls="history"
      aria-selected="false">History</a>
  </li>
</ul>
<div class="tab-content" id="myTabContent">

  <!-- Progress Tab Content -->
  <div class="tab-pane fade show active" id="progress" role="tabpanel" aria-labelledby="progress-tab">
    <div class="row">
      <div class="col-12">
        <div class="card card-outline card-danger">

          <div class="card-body">
            <table id="example2" class="table table-bordered">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nama</th>
                  <th>Username</th>
                  <th>Email</th>
                  <th>No Hp</th>
                  <th>Peran</th>
                  <th>Profile</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($users as $user)
          <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->username }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->waphonenumber }}</td>
            <td>{{ ucfirst($user->rule) }}</td>
            <td>
            <a href="{{ route('user.logs', $user->id) }}" class="btn btn-info btn-sm"><i
              class="bi bi-file-earmark-text"></i> Profile</a>
            </td>
            <td>
            @if(auth()->user()->rule == "superuser")
        <form action="{{ route('update-role', $user->id) }}" method="POST">
          @csrf
          @method('PUT')
          <select name="role" class="form-select">
          @foreach($listpic as $role)
        <option value="{{ $role }}" {{ $user->rule == $role ? 'selected' : '' }}>{{ ucfirst($role) }}
        </option>
      @endforeach
          </select>
          <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i> Ubah
          Peran</button>
        </form>
        <form action="{{ route('delete-user', $user->id) }}" method="POST" style="display: inline-block;">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger btn-sm"
          onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')"><i
          class="bi bi-trash"></i> Hapus</button>
        </form>
      @endif

            </td>
          </tr>
        @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- History Tab Content -->
  <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
    <div class="row">
      <div class="col-12">
        <div class="card card-outline card-danger">
          <div class="card-header">History</div>
          <div class="card-body">
            <!-- Activity Heatmap -->
            <div id="chartdiv"></div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Add amCharts Library -->
<style>
  #chartdiv {
    width: 100%;
    height: 500px;
  }
</style>

<!-- Resources -->
<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

<!-- Chart code -->
<script>
  am5.ready(function () {

    // Create root element
    // https://www.amcharts.com/docs/v5/getting-started/#Root_element
    var root = am5.Root.new("chartdiv");


    // Set themes
    // https://www.amcharts.com/docs/v5/concepts/themes/
    root.setThemes([
      am5themes_Animated.new(root)
    ]);


    // Create chart
    // https://www.amcharts.com/docs/v5/charts/xy-chart/
    var chart = root.container.children.push(am5xy.XYChart.new(root, {
      panX: false,
      panY: false,
      wheelX: "none",
      wheelY: "none",
      paddingLeft: 0,
      layout: root.verticalLayout
    }));


    // Create axes and their renderers
    var yRenderer = am5xy.AxisRendererY.new(root, {
      visible: false,
      minGridDistance: 20,
      inversed: true,
      minorGridEnabled: true
    });

    yRenderer.grid.template.set("visible", false);

    var yAxis = chart.yAxes.push(am5xy.CategoryAxis.new(root, {
      maxDeviation: 0,
      renderer: yRenderer,
      categoryField: "weekday"
    }));

    var xRenderer = am5xy.AxisRendererX.new(root, {
      visible: false,
      minGridDistance: 30,
      opposite: true,
      minorGridEnabled: true
    });

    xRenderer.grid.template.set("visible", false);

    var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
      renderer: xRenderer,
      categoryField: "hour"
    }));


    // Create series
    // https://www.amcharts.com/docs/v5/charts/xy-chart/#Adding_series
    var series = chart.series.push(am5xy.ColumnSeries.new(root, {
      calculateAggregates: true,
      stroke: am5.color(0xffffff),
      clustered: false,
      xAxis: xAxis,
      yAxis: yAxis,
      categoryXField: "hour",
      categoryYField: "weekday",
      valueField: "value"
    }));

    series.columns.template.setAll({
      tooltipText: "{value}",
      strokeOpacity: 1,
      strokeWidth: 2,
      width: am5.percent(100),
      height: am5.percent(100)
    });

    series.columns.template.events.on("pointerover", function (event) {
      var di = event.target.dataItem;
      if (di) {
        heatLegend.showValue(di.get("value", 0));
      }
    });

    series.events.on("datavalidated", function () {
      heatLegend.set("startValue", series.getPrivate("valueHigh"));
      heatLegend.set("endValue", series.getPrivate("valueLow"));
    });


    // Set up heat rules
    // https://www.amcharts.com/docs/v5/concepts/settings/heat-rules/
    series.set("heatRules", [{
      target: series.columns.template,
      min: am5.color(0xfffb77),
      max: am5.color(0xfe131a),
      dataField: "value",
      key: "fill"
    }]);


    // Add heat legend
    // https://www.amcharts.com/docs/v5/concepts/legend/heat-legend/
    var heatLegend = chart.bottomAxesContainer.children.push(am5.HeatLegend.new(root, {
      orientation: "horizontal",
      endColor: am5.color(0xfffb77),
      startColor: am5.color(0xfe131a)
    }));


    // Set data
    // https://www.amcharts.com/docs/v5/charts/xy-chart/#Setting_data
    var encodedDataElement = document.getElementById('encoded-data');
    var data = JSON.parse(encodedDataElement.dataset.listprogressnodokumen);

    series.data.setAll(data);

    // Auto-populate X and Y axis category data
    var weekdays = [];
    var hours = [];
    am5.array.each(data, function (row) {
      if (weekdays.indexOf(row.weekday) == -1) {
        weekdays.push(row.weekday);
      }
      if (hours.indexOf(row.hour) == -1) {
        hours.push(row.hour);
      }
    });


    yAxis.data.setAll(weekdays.map(function (item) {
      return { weekday: item }
    }));


    xAxis.data.setAll(hours.map(function (item) {
      return { hour: item }
    }));


    // Make stuff animate on load
    // https://www.amcharts.com/docs/v5/concepts/animations/#Initial_animation
    chart.appear(1000, 100);

  }); // end am5.ready()
</script>


@endsection