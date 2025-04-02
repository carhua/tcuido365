let DatatablesAjax = function() {

    $.fn.dataTable.Api.register('column().title()', function() {
        return $(this.header()).text().trim();
    });

    let initTable = function(id, name, path_ajax, columns, columnsDefs, action_links=null) {
        if(action_links != null){
            let actions =
                {
                    targets: -1,
                    title: '',
                    orderable: false,
                    render: function(data, type, full, meta) {
                        let action = '', url;
                        let dropdown = '<span class="dropdown d-block d-sm-none"><a href="#" class="btn m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown" aria-expanded="true"><i class="la la-ellipsis-h"></i></a><div class="dropdown-menu dropdown-menu-right">';
                        if (typeof action_links.show != 'undefined') {
                            url = action_links.show;
                            url = url.replace("my_id", full.id);
                            action = action + '<a href="' + url + '" class="d-none d-sm-inline-block m-portlet__nav-link btn m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill" title="Mostrar">';
                            action = action + '<i class="la la-search-plus"></i></a>';
                            dropdown = dropdown + '<a class="dropdown-item" href="' + url + '"><i class="la la-search-plus"></i> Mostrar</a>';
                        }

                        if (typeof action_links.edit != 'undefined') {
                            url = action_links.edit;
                            url = url.replace("my_id", full.id);
                            action = action + '<a href="' + url + '" class="d-none d-sm-inline-block m-portlet__nav-link btn m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill" title="Editar">';
                            action = action + '<i class="la la-edit"></i></a>';
                            dropdown = dropdown + '<a class="dropdown-item" href="' + url + '"><i class="la la-edit"></i> Editar</a>';
                        }

                        if (typeof action_links.print != 'undefined') {
                            url = action_links.print;
                            url = url.replace("my_id", full.id);
                            action = action + '<a href="' + url + '" class="d-none d-sm-inline-block m-portlet__nav-link btn m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill" title="Imprimir" target="_blank">';
                            action = action + '<i class="la la-print"></i></a>';
                            dropdown = dropdown + '<a class="dropdown-item" href="' + url + '"><i class="la la-print"></i> Imprimir</a>';
                        }

                        if (typeof action_links.pay != 'undefined') {
                            url = action_links.pay;
                            url = url.replace("my_id", full.id);
                            action = action + '<a href="' + url + '" class="d-none d-sm-inline-block m-portlet__nav-link btn m-btn m-btn--hover-primary m-btn--icon m-btn--icon-only m-btn--pill" title="Pagar">';
                            action = action + '<i class="la la-star"></i></a>';
                            dropdown = dropdown + '<a class="dropdown-item" href="' + url + '"><i class="la la-send"></i> Pagar</a>';
                        }

                        dropdown = dropdown + '</div></span>';

                        return dropdown + action;
                    },
                };
            columnsDefs.push(actions);
        }

        let table = $(id).DataTable({
            //bStateSave: true,
            /*fnStateSave: function (oSettings, oData) {
                sessionStorage.setItem( name + '_storage_sispro', JSON.stringify(oData) );
            },
            fnStateLoad: function (oSettings) {
                return JSON.parse( sessionStorage.getItem(name + '_storage_sispro') );
            },*/
            responsive: true,
            pagingType: 'full_numbers',
            dom: `<'row'<'col-sm-12'tr>>
			        <'row'<'col-sm-12 col-md-5'li><'col-sm-12 col-md-7 dataTables_pager'p>>`,
            language: {
                //"url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json",
                "sProcessing":     "Procesando...",
                "sLengthMenu":     "Mostrar _MENU_ registros",
                "sZeroRecords":    "No se encontraron resultados",
                "sEmptyTable":     "Ningún dato disponible en esta tabla",
                "sInfo":           "Registros del _START_ al _END_ de un total de _TOTAL_",
                "sInfoEmpty":      "No hay registros",
                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix":    "",
                "sSearch":         "Buscar:",
                "sUrl":            "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst":    '<i class="la la-angle-double-left"></i>',
                    "sLast":     '<i class="la la-angle-double-right"></i>',
                    "sNext":     '<i class="la la-angle-left"></i>',
                    "sPrevious": '<i class="la la-angle-right"></i>'
                },
                "oAria": {
                    "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            },
            processing: true,
            serverSide: true,
            ajax: path_ajax,
            columns: columns,
            columnDefs: columnsDefs,
            order: [[0, "desc"]],
            initComplete: function() {
                this.api().columns().every(function() {
                    // let column = this;
                    //
                    // switch (column.title()) {
                    //     case 'Status':
                    //         var status = {
                    //             1: {'title': 'Pending', 'class': 'm-badge--brand'},
                    //             2: {'title': 'Delivered', 'class': ' m-badge--metal'},
                    //             3: {'title': 'Canceled', 'class': ' m-badge--primary'},
                    //             4: {'title': 'Success', 'class': ' m-badge--success'},
                    //             5: {'title': 'Info', 'class': ' m-badge--info'},
                    //             6: {'title': 'Danger', 'class': ' m-badge--danger'},
                    //             7: {'title': 'Warning', 'class': ' m-badge--warning'},
                    //         };
                    //         column.data().unique().sort().each(function(d, j) {
                    //             $('.m-input[data-col-index="6"]').append('<option value="' + d + '">' + status[d].title + '</option>');
                    //         });
                    //         break;
                    // }
                });
            },
        });

        /*var filter = function() {
            var val = $.fn.dataTable.util.escapeRegex($(this).val());
            table.column($(this).data('col-index')).search(val ? val : '', false, false).draw();
        };

        var asdasd = function(value, index) {
            var val = $.fn.dataTable.util.escapeRegex(value);
            table.column(index).search(val ? val : '', false, true);
        };*/

        $('#m_search').on('click', function(e) {
            e.preventDefault();
            let params = {};
            $('.m-input').each(function() {
                let i = $(this).data('col-index');
                if (params[i]) {
                    params[i] += '|' + $(this).val();
                }
                else {
                    params[i] = $(this).val();
                }
            });
            $.each(params, function(i, val) {
                // apply search params to datatable
                table.column(i).search(val ? val : '', false, false);
            });
            table.table().draw();
        });

        $('#m_reset').on('click', function(e) {
            e.preventDefault();
            $('.m-input').each(function() {
                $(this).val('');
                table.column($(this).data('col-index')).search('', false, false);
            });
            table.table().draw();
        });

        return table;
    };

    return {
        init: function(id, name, path_ajax, columns, columnsDefs, action_links=null) {
            return initTable(id, name, path_ajax, columns, columnsDefs, action_links);
        },
    };

}();