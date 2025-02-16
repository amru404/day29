@extends('admin.layouts.admin')

@section('content')



<table class="table table-striped table-bordered" id="datatable">
    <thead>
        <tr>
            <th>nama</th>
            <th>harga</th>
            <th>stock</th>
            <th>tanggal_input</th>
        </tr>
    </thead>


    <tbody>
        @foreach ($product as $p )
        <tr>
            <td>{{$p->nama}}</td>
            <td>{{$p->harga}}</td>
            <td>{{$p->stock}}</td>
            <td>{{$p->created_at}}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="row">
    <div class="col-md-6 mt-3">
        <input name="dates">
    </div>
    <div class="col-md-6 mt-3">
        <select class="js-example-basic-single" name="state" multiple="multiple">
            @foreach ($product as $p)
            <option value="{{$p->id}}">{{$p->nama}}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mt-3">
        <div id="product_price_range">
            <canvas class="canvasChartProduct"></canvas>
        </div>

    </div>
    <div class="col-md-6 mt-3">
        <div class="div" id="output">

        </div>
    </div>
</div>






@endsection

@section('scripts')
@parent
{{ Html::script(mix('assets/admin/js/dashboard.js')) }}
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript" src="https://pivottable.js.org/dist/pivot.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function () {
        $('#datatable').DataTable();
    });

</script>
<script>
    $('input[name="dates"]').daterangepicker();
    $('.js-example-basic-single').select2();
    $.ajax({
        url: 'allProduct',
        success: function (response) {
            console.log(response, "<<<<<<")

            $("#output").pivot(
                response, {
                    rows: ["created_range"],
                    cols: ["price_range"]
                }
            );
        }
    })

    var productPriceRange = {
        _defaults: {
            type: 'doughnut',
            tooltipFillColor: "rgba(51, 51, 51, 0.55)",
            data: {
                labels: [
                    '< 50000',
                    '50000 - 99999',
                    '100000 - 999999',
                    '>= 1000000'
                ],
                datasets: [{
                    data: [],
                    backgroundColor: [
                        "#3498DB",
                        "#3498DB",
                        "#9B59B6",
                        "#E74C3C",
                    ],
                    hoverBackgroundColor: [
                        "#36CAAB",
                        "#49A9EA",
                        "#B370CF",
                        "#E95E4F",
                    ]
                }]
            },
            options: {
                legend: false,
                responsive: false
            }
        },
        init: function ($el) {
            var self = this;
            $el = $($el);

            $.ajax({
                url: 'chartProduct',
                success: function (response) {

                    self._defaults.data.datasets[0].data = [
                        response.less_50000,
                        response._50000_99999,
                        response._100000_999999,
                        response.more_1000000
                    ];
                    console.log("huh??????")
                    new Chart($el.find('.canvasChartProduct'), self._defaults);
                }
            });
        }
    };

    productPriceRange.init($('#product_price_range'));

</script>
@endsection

@section('styles')
@parent
{{ Html::style(mix('assets/admin/css/dashboard.css')) }}
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="https://pivottable.js.org/dist/pivot.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
@endsection
