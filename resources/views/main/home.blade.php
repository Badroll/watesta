@extends('master')

@section('style')
<style type="text/css">
</style>
@stop

@php
    $monthNow = date("Y-m");
    $runningMonth = date("Y-m");
    $runningYear = date("Y");
@endphp

@section('breadcrumb')
@stop

@section('content')
<input type="hidden" name="_token" class="_token" value="{{ csrf_token() }}" />

<div class="panel panel-flat">
    <div class="panel-heading">
        <h4 class="panel-titletext-bold"><b>HOME</b></h4>
        <br>
        <div class="form-group">
            <label class="control-label col-lg-1">Periode</label>
            <div class="col-lg-10">
                <div class="row">
                    <div class="col-md-4">
                        <input class="form-control month-picker" id="periode" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel-body">
    	<div class="chart-container">
			<h5 align="center" style="color: #008ACD;">DETENI</h5>
			<div class="chart has-fixed-height" id="basic_columns"></div>
		</div>
		<br>
		<div class="chart-container">
			<div class="chart-container has-scroll">
				<div class="chart has-fixed-height has-minimum-width" id="rose_diagram_visible"></div>
			</div>
		</div>
    </div>

</div>	

@stop

@section('script')
<script type="text/javascript">
	var gArrDeteniJenis_ = ["Deteni", "Terdeportasi"];
	var gArrDeteniLaki = [0, 0];
	var gArrDeteniPerempuan = [0, 0];

	var gArrLogJenis_ = ["RAP", "PERKES", "KAMTIB"];
	var gArrLogJumlah = [
							{value : 0, name : "RAP"},
							{value : 0, name : "PERKES"},
							{value : 0, name : "KAMTIB"}
						];

    $(document).ready(function(){

    	$("#periode").datepicker('update', "{{ $ctl_periode }}");
        $("#periode").on('changeDate', function(selected){
            filter();
        });
        
    	// if(localStorage.getItem("selectedPeriode") == null){
    	// 	localStorage.setItem("selectedPeriode", $("#periode").val());
    	// }

    	var dataJSON = "{{ $ctl_deteni }}";
        var dataJSON2 = parseToString(dataJSON);
        var data = JSON.parse(dataJSON2);
        console.log(data);
        for (var i = 0; i < data.length; i++){
        	if(data[i]["DTN_DEPORTASI"] == "N"){
        		if(data[i]["DTN_JENIS_KELAMIN"] == "JK_LAKI"){
        			gArrDeteniLaki[0]++;
        		}else if(data[i]["DTN_JENIS_KELAMIN"] == "JK_PEREMPUAN"){
        			gArrDeteniPerempuan[0]++;
        		}
        	}else if(data[i]["DTN_DEPORTASI"] == "Y"){
        		if(data[i]["DTN_JENIS_KELAMIN"] == "JK_LAKI"){
        			gArrDeteniLaki[1]++;
        		}else if(data[i]["DTN_JENIS_KELAMIN"] == "JK_PEREMPUAN"){
        			gArrDeteniPerempuan[1]++;
        		}
        	}
        }

        dataJSON = "{{ $ctl_log }}";
        dataJSON2 = parseToString(dataJSON);
        data = JSON.parse(dataJSON2);
        for (var i = 0; i < data.length; i++){
        	if(data[i]["DLOG_JENIS"] == "JBERKAS_RAP"){
        		gArrLogJumlah[0].value++;
        	}else if(data[i]["DLOG_JENIS"] == "JBERKAS_PERKES"){
        		gArrLogJumlah[1].value++;
        	}else if(data[i]["DLOG_JENIS"] == "JBERKAS_KAMTIB"){
        		gArrLogJumlah[2].value++;
        	}
        }

    });


    function filter(){
        var periode = $("#periode").val();
        window.location = "{{ url('main/'.Helper::uri2()) }}?periode="+periode
    }


    $(function () {
	    require.config({
	        paths: {
	            echarts: gEchartResources
	        }
	    });
	    require(
	        [
	            'echarts',
	            'echarts/theme/limitless',
	            'echarts/chart/pie',
	            'echarts/chart/funnel',
	            'echarts/chart/bar',
	            'echarts/chart/line'
	        ],


	        // Charts setup
	        function (ec, limitless) {
	            var rose_diagram_visible = ec.init(document.getElementById('rose_diagram_visible'), limitless);
	            rose_diagram_visible_options = {

	                // Add title
	                title: {
	                    text: 'AKTIFITAS DETENI',
	                    // subtext: 'Senior front end developer',
	                    x: 'center'
	                },

	                // Add tooltip
	                tooltip: {
	                    trigger: 'item',
	                    formatter: "{a} <br/>{b}: {c} ({d}%)"
	                },

	                // Add legend
	                legend: {
	                    x: 'left',
	                    y: 'top',
	                    orient: 'vertical',
	                    data: gArrLogJenis_
	                },

	                // Display toolbox
	                toolbox: {
	                    show: true,
	                    orient: 'vertical',
	                    feature: {
	                        mark: {
	                            show: true,
	                            title: {
	                                mark: 'Markline switch',
	                                markUndo: 'Undo markline',
	                                markClear: 'Clear markline'
	                            }
	                        },
	                        dataView: {
	                            show: true,
	                            readOnly: false,
	                            title: 'View data',
	                            lang: ['View chart data', 'Close', 'Update']
	                        },
	                        magicType: {
	                            show: true,
	                            title: {
	                                pie: 'Switch to pies',
	                                funnel: 'Switch to funnel',
	                            },
	                            type: ['pie', 'funnel']
	                        },
	                        restore: {
	                            show: true,
	                            title: 'Restore'
	                        },
	                        saveAsImage: {
	                            show: true,
	                            title: 'Same as image',
	                            lang: ['Save']
	                        }
	                    }
	                },

	                // Enable drag recalculate
	                calculable: true,

	                // Add series
	                series: [
	                    {
	                        name: 'Jenis Berkas',
	                        type: 'pie',
	                        radius: ['15%', '73%'],
	                        center: ['50%', '57%'],
	                        roseType: 'area',

	                        // Funnel
	                        width: '40%',
	                        height: '78%',
	                        x: '30%',
	                        y: '17.5%',
	                        max: 450,
	                        sort: 'ascending',

	                        data: gArrLogJumlah
	                    }
	                ]
	            };
	            rose_diagram_visible.setOption(rose_diagram_visible_options);

	            var basic_columns = ec.init(document.getElementById('basic_columns'), limitless);
	            basic_columns_options = {

	                // Setup grid
	                grid: echartGrid,

	                // Add tooltip
	                tooltip: {
	                    trigger: 'axis'
	                },

	                // Add legend
	                legend: {
	                    data: ['Laki-laki', 'Perempuan']
	                },

	                // Enable drag recalculate
	                calculable: true,

	                // Horizontal axis
	                xAxis: [{
	                    type: 'category',
	                    data: ["Deteni", "Terdeportasi"]
	                }],

	                // Vertical axis
	                yAxis: [{
	                    type: 'value'
	                }],

	                // Add series
	                series: [
	                    {
	                        name: 'Laki-laki',
	                        type: 'bar',
	                        data: gArrDeteniLaki,
	                        itemStyle: {
	                            normal: {
	                                label: {
	                                    show: true,
	                                    textStyle: {
	                                        fontWeight: 500
	                                    }
	                                }
	                            }
	                        },
	                        markLine: {
	                            data: [{type: 'average', name: 'Average'}]
	                        }
	                    },
	                    {
	                        name: 'Perempuan',
	                        type: 'bar',
	                        data: gArrDeteniPerempuan,
	                        itemStyle: {
	                            normal: {
	                                label: {
	                                    show: true,
	                                    textStyle: {
	                                        fontWeight: 500
	                                    }
	                                }
	                            }
	                        },
	                        markLine: {
	                            data: [{type: 'average', name: 'Average'}]
	                        }
	                    }
	                ]
	            };
	            basic_columns.setOption(basic_columns_options);

	            window.onresize = function () {
	                setTimeout(function () {
	                    rose_diagram_visible.resize();
	                }, 200);
	            }
	        }
	    );
	});

</script>
@stop
