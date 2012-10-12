<?php
//highchart 学习文件
?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Highcharts 学习（根据文档）</title>
<?php 
		echo load_static('jquery.min.js');
		echo load_static('highcharts.js');
?>

<script>
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'column',
            },
			<?php
					$d = array();
					$x = array();
					$p = array();
					//这里处理数据
					foreach ($data as $key=>$val) {
						$d[] = (float)$val['id'];
						$x[] = $val['username'];
						$p[] = array($val['username'], (float)$val['id']);
					}

			?>
            title: {
                text: '',
                x: -20 //center
            },
            subtitle: {
                text: '',
                x: 120
            },
            xAxis: {
                categories: <?php echo json_encode($x); ?>,
				labels:{
					align: 'right',
					rotation:-45
				}
            },
            yAxis: {
                title: {
                    text: 'id'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }],
				
            },
            tooltip: {
				//pointFormat: '{series.name}: <b>{point.percentage}%</b>',
				//percentageDecimals: 1
                
				formatter: function() {
                    return '<b>' + this.series.name + '</b><br/>' + this.x + ': ' + this.y;
					return '<b>' + this.point.name + '</b>: ' + this.percentage + ' %';
				}
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                x: -10,
                y: 100,
                borderWidth: 0
            },
            series: [

			{
                name: '',
                data: <?php echo json_encode($p); ?>,

            }
			]
        });
    });
    
});
		</script>
	</head>
	<body>

<div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>

	</body>
</html>
