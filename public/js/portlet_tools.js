"use strict";

let xPortletTools = function () {
    // Toastr
    let initToastr = function() {
        toastr.options.showDuration = 1000;
    }

    // Tools 1
    let tools1 = function(id) {
        // This portlet is lazy initialized using data-portlet="true" attribute. You can access to the portlet object as shown below and override its behavior
        let portlet = new KTPortlet(id);

        // Toggle event handlers
        // portlet.on('beforeCollapse', function(portlet) {
        //     setTimeout(function() {
        //         toastr.info('Before collapse event fired!');
        //     }, 100);
        // });
        //
        // portlet.on('afterCollapse', function(portlet) {
        //     setTimeout(function() {
        //         toastr.warning('Before collapse event fired!');
        //     }, 2000);
        // });
        //
        // portlet.on('beforeExpand', function(portlet) {
        //     setTimeout(function() {
        //         toastr.info('Before expand event fired!');
        //     }, 100);
        // });
        //
        // portlet.on('afterExpand', function(portlet) {
        //     setTimeout(function() {
        //         toastr.warning('After expand event fired!');
        //     }, 2000);
        // });

        // Remove event handlers
        portlet.on('beforeRemove', function(portlet) {
            return confirm('Esta seguro de cerra esta sección ?');  // remove portlet after user confirmation
        });

        portlet.on('afterRemove', function(portlet) {
            setTimeout(function() {
                toastr.warning('Ha cerrado una sección');
            }, 2000);
        });

        // Reload event handlers
        portlet.on('reload', function(portlet) {
            toastr.info('Cargando nuevamente!');

            KTApp.block(portlet.getSelf(), {
                overlayColor: '#ffffff',
                type: 'loader',
                state: 'success',
                opacity: 0.3,
                size: 'lg'
            });

            // update the content here

            setTimeout(function() {
                KTApp.unblock(portlet.getSelf());
            }, 2000);
        });

        // Reload event handlers
        portlet.on('afterFullscreenOn', function(portlet) {
            toastr.warning('After fullscreen on event fired!');
            let scrollable = $(portlet.getBody()).find('> .kt-scroll');

            if (scrollable) {
                scrollable.data('original-height', scrollable.css('height'));
                scrollable.css('height', '100%');

                KTUtil.scrollUpdate(scrollable[0]);
            }
        });

        portlet.on('afterFullscreenOff', function(portlet) {
            toastr.warning('After fullscreen off event fired!');
            let scrollable = $(portlet.getBody()).find('> .kt-scroll');

            if (scrollable) {
                let scrollable = $(portlet.getBody()).find('> .kt-scroll');
                scrollable.css('height', scrollable.data('original-height'));

                KTUtil.scrollUpdate(scrollable[0]);
            }
        });
    }

    return {
        //main function to initiate the module
        init: function (id) {
            initToastr();
            tools1(id);
        }
    };
}();