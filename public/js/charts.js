//== Class definition

let xChart = function () {

    //== Private functions

    let gThemes = function(theme){
        switch (theme) {
            case 'dark': return am4themes_dark;
            case 'material': return am4themes_material;
            case 'kelly': return am4themes_kelly;
            case 'moonrise': return am4themes_moonrisekingdom;
        }

        return null;
    }

    // basic demo
    let gRadar = function (id, data, category, value, name) {
        if (!KTUtil.getByID(id)) {
            return;
        }

        am4core.useTheme(am4themes_animated);

        let chart = am4core.create(id, am4charts.RadarChart);
        chart.exporting.menu = new am4core.ExportMenu();
        chart.data = data;

        /* Create axes */
        let categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = category;

        let valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.strictMinMax = true;
        valueAxis.min = 0;
        valueAxis.max = 10;

        /* Create and configure series */
        let series = chart.series.push(new am4charts.RadarSeries());
        series.dataFields.valueY = value;
        series.dataFields.categoryX = category;
        series.name = name;
        series.strokeWidth = 2;
        //series.zIndex = 2;
        //series.tooltipText = '[bold]YEAR {categoryX}[/]';

        series.bullets.push(new am4charts.CircleBullet());

        /*var series2 = chart.series.push(new am4charts.RadarColumnSeries());
        series2.dataFields.valueY = "valor";
        series2.dataFields.categoryX = "atributo";
        series2.name = "Units";
        series2.strokeWidth = 0;
        series2.columns.template.fill = am4core.color("#CDA2AB");
        series2.columns.template.tooltipText = "Series: {name}\nCategory: {categoryX}\nValue: {valueY}";*/

        chart.legend = new am4charts.Legend();
        //chart.cursor = new am4charts.XYCursor();
        //chart.cursor= new am4charts.RadarCursor();
    }

    let gRadarMultiple = function (id, data, category, value, names) {
        if (!KTUtil.getByID(id)) {
            return;
        }

        am4core.useTheme(am4themes_animated);

        let chart = am4core.create(id, am4charts.RadarChart);
        chart.exporting.menu = new am4core.ExportMenu();
        chart.legend = new am4charts.Legend();
        chart.data = data;

        let categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = category;

        let valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.strictMinMax = true;
        valueAxis.min = 0;
        valueAxis.max = 10;

        let total = names.length;
        for (let i = 1; i <= total; i++) {
            let series = chart.series.push(new am4charts.RadarSeries());
            series.dataFields.valueY = value + i;
            series.dataFields.categoryX = category;
            series.name = names[i - 1];
            series.strokeWidth = 2;
            series.bullets.push(new am4charts.CircleBullet());
        }
    }

    let gSeriesFecha = function (id, data, category, value, name='', theme='') {
        if (!KTUtil.getByID(id)) {
            return;
        }

        am4core.useTheme(am4themes_animated);
        //am4core.useTheme(am4themes_dark);

        var chart = am4core.create(id, am4charts.XYChart);
        chart.data = data;

        chart.dateFormatter.inputDateFormat = "yyyy-MM-dd";
        //chart.numberFormatter.numberFormat = "#,###.##";

        chart.exporting.menu = new am4core.ExportMenu();
        chart.responsive.enabled = true;

        var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
        dateAxis.renderer.grid.template.location = 0;
        // dateAxis.dateFormatter = new am4core.DateFormatter();
        // dateAxis.dateFormatter.dateFormat = "M/d/yyyy";

        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.title.text = name;
        valueAxis.renderer.grid.template.disabled = true;

        var series = chart.series.push(new am4charts.LineSeries());
        series.dataFields.valueY = value;
        series.dataFields.dateX = category;
        series.tooltipText = "{" + value + ".formatNumber('#,###.##')}";
        series.strokeWidth = 3;
        // series.fillOpacity = 0.5; //relleno
        series.strokeOpacity = 0.3;
        series.minBulletDistance = 10;

        series.tooltip.background.cornerRadius = 20;
        series.tooltip.background.strokeOpacity = 0;
        series.tooltip.pointerOrientation = "vertical";
        series.tooltip.label.minWidth = 40;
        series.tooltip.label.minHeight = 40;
        series.tooltip.label.textAlign = "middle";
        series.tooltip.label.textValign = "middle";


        var bullet = series.bullets.push(new am4charts.CircleBullet());
        bullet.circle.strokeWidth = 2;
        bullet.circle.radius = 3;
        bullet.circle.fill = am4core.color("#fff");

        var bullethover = bullet.states.create("hover");
        bullethover.properties.scale = 1.3;

        chart.cursor = new am4charts.XYCursor();
        chart.cursor.behavior = "panXY";
        chart.cursor.xAxis = dateAxis;
        chart.cursor.snapToSeries = series;

        // Scrollbar Y
        // chart.scrollbarY = new am4core.Scrollbar();
        // chart.scrollbarY.parent = chart.rightAxesContainer; //.leftAxesContainer;
        // chart.scrollbarY.toBack();

        chart.scrollbarX = new am4charts.XYChartScrollbar();
        chart.scrollbarX.series.push(series);
        chart.scrollbarX.parent = chart.bottomAxesContainer;

        //Zoom and preview
        // chart.events.on("ready", function () {
        //     dateAxis.zoom({start:0.4, end:1});
        // });
    };

    let gCylinder = function (id, data, category, value, name) {
        if (!KTUtil.getByID(id)) {
            return;
        }

        am4core.useTheme(am4themes_animated);

        var chart = am4core.create(id, am4charts.XYChart3D);

        //chart.titles.create().text = name;
        chart.data = data;

        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "certificacion";
        categoryAxis.renderer.grid.template.location = 0;
        categoryAxis.renderer.grid.template.strokeOpacity = 0;

        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.renderer.grid.template.strokeOpacity = 0;
        valueAxis.min = -10;
        valueAxis.max = 110;
        valueAxis.strictMinMax = true;
        valueAxis.renderer.baseGrid.disabled = true;
        valueAxis.renderer.labels.template.adapter.add("text", function(text) {
            if ((text > 100) || (text < 0)) {
                return "";
            }
            else {
                return text + "%";
            }
        })

        var series1 = chart.series.push(new am4charts.ConeSeries());
        series1.dataFields.valueY = "salidas_porcentaje";
        series1.dataFields.categoryX = "certificacion";
        series1.columns.template.width = am4core.percent(80);
        series1.columns.template.fillOpacity = 0.9;
        series1.columns.template.strokeOpacity = 1;
        series1.columns.template.strokeWidth = 2;
        series1.legendSettings.labelText = "Salidas";

        var series2 = chart.series.push(new am4charts.ConeSeries());
        series2.dataFields.valueY = "saldo_porcentaje";
        series2.dataFields.categoryX = "certificacion";
        series2.stacked = true;
        series2.columns.template.width = am4core.percent(80);
        series2.columns.template.fill = am4core.color("#000");
        series2.columns.template.fillOpacity = 0.1;
        series2.columns.template.stroke = am4core.color("#000");
        series2.columns.template.strokeOpacity = 0.2;
        series2.columns.template.strokeWidth = 2;
        series2.legendSettings.labelText = "Saldo";

        chart.legend = new am4charts.Legend();
    };

    return {
        radar: function (id, data, category, value, name) {
            gRadar(id, data, category, value, name);
        },
        radarMultiple: function (id, data, category, value, names) {
            gRadarMultiple(id, data, category, value, names);
        },
        seriesFecha: function (id, data, category, value, name) {
            gSeriesFecha(id, data, category, value, name);
        },
        cilindro: function (id, data, category, value, name) {
            gCylinder(id, data, category, value, name);
        },
    };
}();

let xChartMorris = function () {
    let gDonut = function (id, data, colors) {
        if (!KTUtil.getByID(id)) {
            return;
        }

        Morris.Donut({
            element: id,
            data: data,
            colors: colors,
        });
    }

    return {
        dona: function (id, data, colors) {
            gDonut(id, data, colors);
        },
    };
}();
