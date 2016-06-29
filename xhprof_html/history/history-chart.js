google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback(lineChart);

function lineChart() {
    var data = new google.visualization.DataTable();
    data.addColumn(xAxisType, xAxisLabel);
    data.addColumn('number', 'Load Time (s)');
    data.addColumn('number', 'Total Function calls');

    data.addRows(chartRows);

    var options = {
        pointSize: 5,
        hAxis: {
            title: xAxisLabel
        },
        vAxes: {
            0: {
                title: "Load Time (s)",
                logScale: false
            },
            1: {
                title: "Function Calls",
                logScale: false,
                maxValue: 2
            }
        },
        series:{
            0:{targetAxisIndex:0},
            1:{targetAxisIndex:1},
            2:{targetAxisIndex:1}
        },
        backgroundColor: '#f1f8e9'
    };

    var chart = new google.visualization.LineChart(document.getElementById('chart'));
    chart.draw(data, options);
}

$(document).ready(function(){
    var checked = 0;
    $('.chk_compare').on('change', function() {
        checked = $('.chk_compare:checked').length;
        if(checked == 3) {
            $(this).prop('checked', false);
            return false;
        }

        if(checked == 2) {
            var input_num = 1;
            $('.chk_compare:checked').each(function() {
                $('input[name=run' + input_num + ']').val($(this).val()); 
                input_num++;
            });

            $('.submit_compare').prop('disabled', false);
        } else {
            $('.submit_compare').prop('disabled', true);
        }
    });

    $('.td_compare').on('click', function(e) {
        if ($(e.target).is('.td_compare')) {
            $(this).find('input').click();
        }
    });
});

