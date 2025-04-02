let amChart = function () {
    let chartReg = {};

    function maybeDisposeChart(chartdiv) {
        if (chartReg[chartdiv]) {
            chartReg[chartdiv].dispose();
            delete chartReg[chartdiv];
        }
    }

    let gClusterColumn = function (id, category, values, series, titleY, titleChart) {
        maybeDisposeChart(id);
        // Create root element
        var root = am5.Root.new(id);

        chartReg[id] = root;

        root.setThemes([
            am5themes_Animated.new(root),
            am5themes_Kelly.new(root)
        ]);

        let chart = root.container.children.push(am5xy.XYChart.new(root, {
            panX: false,
            panY: false,
            wheelX: "panX",
            wheelY: "zoomX",
            layout: root.verticalLayout
        }));

        // Add scrollbar
        // https://www.amcharts.com/docs/v5/charts/xy-chart/scrollbars/

        let scrollbarX = am5.Scrollbar.new(root, {
            orientation: "horizontal"
        });

        chart.set("scrollbarX", scrollbarX);
        chart.bottomAxesContainer.children.push(scrollbarX);

        var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));
        cursor.lineY.set("visible", false);

        let legend = chart.children.push(
            am5.Legend.new(root, {
                centerX: am5.p50,
                x: am5.p50
            })
        );

        let data = values;

        let xRenderer = am5xy.AxisRendererX.new(root, { minGridDistance: 30 });
        xRenderer.labels.template.setAll({
            // rotation: 270,
            centerY: am5.p50,
            centerX: am5.p100,
            paddingRight: 0.1
        });

        let xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
            categoryField: category,
            renderer: xRenderer,
            tooltip: am5.Tooltip.new(root, {})
        }));

        xAxis.data.setAll(data);

        let yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
            renderer: am5xy.AxisRendererY.new(root, {})
        }));

        yAxis.children.push(
            am5.Label.new(root, {
                text: titleY,
                y: 25,
                centerX:am5.p50,
                rotation: 270,
            })
        );

        function makeSeries(name, fieldName) {
            let series = chart.series.push(am5xy.ColumnSeries.new(root, {
                name: name,
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: fieldName,
                categoryXField: category
            }));

            series.columns.template.setAll({
                tooltipText: "{name}: {valueY}",
                width: am5.percent(90),
                tooltipY: 0
            });

            series.data.setAll(data);

            series.appear();

            series.bullets.push(function () {
                return am5.Bullet.new(root, {
                    locationY: 1,
                    sprite: am5.Label.new(root, {
                        text: "{valueY}",
                        fill: root.interfaceColors.get("alternativeText"), //alternativeText
                        centerY: 0,
                        centerX: am5.p50,
                        populateText: true
                    })
                });
            });

            legend.data.push(series);
        }

        for(let i in series) {
            makeSeries(series[i], series[i]);
        }

        // EXPORT MENU
        let exporting = am5plugins_exporting.Exporting.new(root, {
            menu: am5plugins_exporting.ExportingMenu.new(root, {}),
            dataSource: values,
            title: titleChart,
            filePrefix: titleChart,
            pdfOptions: {
                includeData: true,
            }
        });

        //ADD TITLE CHART
        chart.children.unshift(am5.Label.new(root, {
            text: titleChart,
            fontSize: 20,
            textAlign: "center",
            x: am5.percent(50),
            centerX: am5.percent(50)
        }));

        chart.appear(1000, 100);

        return chart;
    }

    let gSimpleColumn = function (id, categoryX, valueY, values, titleChart, titleY = '') {

        maybeDisposeChart(id);
        // Create root element
        var root = am5.Root.new(id);

        chartReg[id] = root;

        root.setThemes([
            am5themes_Animated.new(root),
            am5themes_Kelly.new(root)
        ]);

        var chart = root.container.children.push(am5xy.XYChart.new(root, {
            panY: false,
            wheelY: "zoomX",
            layout: root.verticalLayout
        }));

        let scrollbarX = am5.Scrollbar.new(root, {
            orientation: "horizontal"
        });

        chart.set("scrollbarX", scrollbarX);
        chart.bottomAxesContainer.children.push(scrollbarX);

        var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));
        cursor.lineY.set("visible", false);


        var xRenderer = am5xy.AxisRendererX.new(root, { minGridDistance: 30 });
        xRenderer.labels.template.setAll({
            rotation: -90,
            centerY: am5.p50,
            centerX: am5.p100,
            paddingRight: 15
        });

        var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
            maxDeviation: 0.3,
            categoryField: categoryX,
            renderer: xRenderer,
            tooltip: am5.Tooltip.new(root, {})
        }));

        var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
            maxDeviation: 0.3,
            renderer: am5xy.AxisRendererY.new(root, {})
        }));

        var series = chart.series.push(am5xy.ColumnSeries.new(root, {
            name: "Series 1",
            xAxis: xAxis,
            yAxis: yAxis,
            valueYField: valueY,
            sequencedInterpolation: true,
            categoryXField: categoryX,
            tooltip: am5.Tooltip.new(root, {
                labelText:"{valueY}"
            })

        }));

        series.columns.template.setAll({ cornerRadiusTL: 5, cornerRadiusTR: 5 });
        series.columns.template.adapters.add("fill", function(fill, target) {
            return chart.get("colors").getIndex(series.columns.indexOf(target));
        });

        series.columns.template.adapters.add("stroke", function(stroke, target) {
            return chart.get("colors").getIndex(series.columns.indexOf(target));
        });

        xAxis.data.setAll(values);
        series.data.setAll(values);

        // Add titles
        chart.children.unshift(am5.Label.new(root, {
            text: titleChart,
            fontSize: 20,
            textAlign: "center",
            x: am5.percent(50),
            centerX: am5.percent(50)
        }));


        let exporting = am5plugins_exporting.Exporting.new(root, {
            menu: am5plugins_exporting.ExportingMenu.new(root, {}),
            dataSource: values,
            title: titleChart,
            filePrefix: titleChart,
            pdfOptions: {
                includeData: true,
            }
        });

        series.appear(1000);
        chart.appear(1000, 100);

        return chart;
    }

    let gLineMultiple = function (id, values, campoGlobal, subCam, titleChart){

        maybeDisposeChart(id);
        // Create root element
        var root = am5.Root.new(id);

        chartReg[id] = root;

        root.setThemes([
            am5themes_Animated.new(root),
            am5themes_Kelly.new(root)
        ]);

        var chart = root.container.children.push(
            am5xy.XYChart.new(root, {
                panX: true,
                panY: true,
                wheelX: "panX",
                wheelY: "zoomX",
                layout: root.verticalLayout,
                pinchZoomX:true
            })
        );

        var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
            behavior: "none"
        }));

        cursor.lineY.set("visible", false);

        var data = values;

        var xRenderer = am5xy.AxisRendererX.new(root, {});
        xRenderer.grid.template.set("location", 0.5);
        xRenderer.labels.template.setAll({
            location: 0.5,
            multiLocation: 0.5
        });

        var xAxis = chart.xAxes.push(
            am5xy.CategoryAxis.new(root, {
                categoryField: campoGlobal,
                renderer: xRenderer,
                tooltip: am5.Tooltip.new(root, {})
            })
        );

        xAxis.data.setAll(data);

        var yAxis = chart.yAxes.push(
            am5xy.ValueAxis.new(root, {
                maxPrecision: 0,
                renderer: am5xy.AxisRendererY.new(root, {
                    //inversed: true
                })
            })
        );

        function createSeries(name, field) {
            var series = chart.series.push(
                am5xy.LineSeries.new(root, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: field,
                    categoryXField: campoGlobal,
                    tooltip: am5.Tooltip.new(root, {
                        pointerOrientation: "horizontal",
                        labelText: "[bold]{name}[/]\n{categoryX}: {valueY}"
                    })
                })
            );

            series.bullets.push(function() {
                return am5.Bullet.new(root, {
                    sprite: am5.Circle.new(root, {
                        radius: 5,
                        fill: series.get("fill")
                    })
                });
            });

            series.set("setStateOnChildren", true);
            series.states.create("hover", {});

            series.mainContainer.set("setStateOnChildren", true);
            series.mainContainer.states.create("hover", {});

            series.strokes.template.states.create("hover", {
                strokeWidth: 4
            });

            series.data.setAll(data);
            series.appear(1000);
        }

        chart.set("scrollbarX", am5.Scrollbar.new(root, {
            orientation: "horizontal",
            marginBottom: 20
        }));

        subCam.forEach(
            element => createSeries(element, element)
        )

        // EXPORT MENU
        let exporting = am5plugins_exporting.Exporting.new(root, {
            menu: am5plugins_exporting.ExportingMenu.new(root, {}),
            dataSource: values,
            title: titleChart,
            filePrefix: titleChart,
            pdfOptions: {
                includeData: true,
            }
        });

        chart.children.unshift(am5.Label.new(root, {
            text: titleChart,
            fontSize: 20,
            textAlign: "center",
            x: am5.percent(50),
            centerX: am5.percent(50)
        }));

        var legend = chart.children.push(
            am5.Legend.new(root, {
                centerX: am5.p50,
                x: am5.p50
            })
        );

        legend.itemContainers.template.states.create("hover", {});

        legend.itemContainers.template.events.on("pointerover", function(e) {
            e.target.dataItem.dataContext.hover();
        });
        legend.itemContainers.template.events.on("pointerout", function(e) {
            e.target.dataItem.dataContext.unhover();
        });

        legend.data.setAll(chart.series.values);

        chart.appear(1000, 100);
        return chart;
    }

    let gCircularMultiple = function (id, values, nameCategoria, nameValue, titleChart, unidadMedida) {

        maybeDisposeChart(id);
        if (values.length > 0){
            // Create root element
            var root = am5.Root.new(id);

            chartReg[id] = root;
            // Set themes
            root.setThemes([
                am5themes_Animated.new(root),
                am5themes_Kelly.new(root)
            ]);

            // Create chart
            var chart = root.container.children.push(am5percent.PieChart.new(root, {
                layout: root.verticalLayout,
                innerRadius: am5.percent(50)
            }));

            // Create series
            var series = chart.series.push(am5percent.PieSeries.new(root, {
                valueField: nameValue,
                categoryField: nameCategoria,
                alignLabels: false,
            }));

            series.labels.template.setAll({
                textType: "circular",
                centerX: 0,
                centerY: 0
            });

            series.labels.template.set("text",  `[bold]{valuePercentTotal.formatNumber('0.00')}%[/] ({value} ${unidadMedida}[/])`);

            // Set data
            series.data.setAll(values);

            // Create legend
            var legend = chart.children.push(am5.Legend.new(root, {
                centerX: am5.percent(50),
                x: am5.percent(50),
                marginTop: 15,
                marginBottom: 15,
            }));

            legend.data.setAll(series.dataItems);

            // EXPORT MENU
            let exporting = am5plugins_exporting.Exporting.new(root, {
                menu: am5plugins_exporting.ExportingMenu.new(root, {}),
                dataSource: values,
                title: titleChart,
                filePrefix: titleChart,
                pdfOptions: {
                    includeData: true,
                }
            });

            // TITULO CHAR
            chart.children.unshift(am5.Label.new(root, {
                text: titleChart,
                fontSize: 20,
                textAlign: "center",
                x: am5.percent(50),
                centerX: am5.percent(50)
            }));

            series.appear(1000, 100);

            return chart;
        }
    }

    return {
        simpleColumn: function (id, categoryX, valueY, values, titleY = '') {
            return gSimpleColumn(id, categoryX, valueY, values, titleY);
        },
        clusterColumn: function (id, category, values, series, titleY = '',  titleChart ='') {
            return gClusterColumn(id, category, values, series, titleY,  titleChart);
        },
        lineGeneral: function (id, values, campoGlobal, subCam, titleChart= '') {
            return gLineMultiple(id, values, campoGlobal, subCam, titleChart);
        },
        circleColumn: function (id, values, nameCategoria, nameValue, titleChart = '', unidadMedida = '') {
            return gCircularMultiple (id, values, nameCategoria, nameValue, titleChart, unidadMedida);
        },
    };
}();
