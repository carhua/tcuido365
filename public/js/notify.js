//== Class definition

let Notify = function () {

    // basic demo
    let demo = function () {
        // init bootstrap switch
        $('[data-switch=true]').bootstrapSwitch();

        // handle the demo
        $('#m_notify_btn').click(function() {
            let content = {};

            content.message = 'New order has been placed';
            if ($('#m_notify_title').prop('checked')) {
                content.title = 'Notification Title';
            }
            if ($('#m_notify_icon').val() != '') {
                content.icon = 'icon ' + $('#m_notify_icon').val();
            }
            if ($('#m_notify_url').prop('checked')) {
                content.url = 'www.keenthemes.com';
                content.target = '_blank';
            }

            let notify = $.notify(content, {
                type: $('#m_notify_state').val(),
                allow_dismiss: $('#m_notify_dismiss').prop('checked'),
                newest_on_top: $('#m_notify_top').prop('checked'),
                mouse_over:  $('#m_notify_pause').prop('checked'),
                showProgressbar:  $('#m_notify_progress').prop('checked'),
                spacing: $('#m_notify_spacing').val(),
                timer: $('#m_notify_timer').val(),
                placement: {
                    from: $('#m_notify_placement_from').val(),
                    align: $('#m_notify_placement_align').val()
                },
                offset: {
                    x: $('#m_notify_offset_x').val(),
                    y: $('#m_notify_offset_y').val()
                },
                delay: $('#m_notify_delay').val(),
                z_index: $('#m_notify_zindex').val(),
                animate: {
                    enter: 'animated ' + $('#m_notify_animate_enter').val(),
                    exit: 'animated ' + $('#m_notify_animate_exit').val()
                }
            });

            if ($('#m_notify_progress').prop('checked')) {
                setTimeout(function() {
                    notify.update('message', '<strong>Saving</strong> Page Data.');
                    notify.update('type', 'primary');
                    notify.update('progress', 20);
                }, 1000);

                setTimeout(function() {
                    notify.update('message', '<strong>Saving</strong> User Data.');
                    notify.update('type', 'warning');
                    notify.update('progress', 40);
                }, 2000);

                setTimeout(function() {
                    notify.update('message', '<strong>Saving</strong> Profile Data.');
                    notify.update('type', 'danger');
                    notify.update('progress', 65);
                }, 3000);

                setTimeout(function() {
                    notify.update('message', '<strong>Checking</strong> for errors.');
                    notify.update('type', 'success');
                    notify.update('progress', 100);
                }, 4000);
            }
        });
    }

    let basic = function (message, state='success', title=false, icon=false, timer=3000, url=false) {
        let content = {};

        content.message = message;

        if (title) {
            content.title = title;
        }
        if (icon) {
            content.icon = 'icon ' + icon;
        }
        if (url) {
            content.url = url;
            content.target = '_blank';
        }

        $.notify(content, {
            type: state,
            allow_dismiss: true,
            newest_on_top: false,
            mouse_over:  false,
            //showProgressbar:  true,
            spacing: 10,
            timer: timer,
            placement: {
                from: 'top',
                align: 'right'
            },
            offset: {
                x: 30,
                y: 30
            },
            delay: 1000,
            z_index: 10000,
            animate: {
                enter: 'animated bounceInUp',
                exit: 'animated bounce'
            }
        });
    }

    let basic2 = function (message, state='success', title=false, icon=false) {
        let content = {};

        content.text = message;

        if (title) {
            content.title = title;
        }
        if (state) {
            content.type = state;
        }
        if (icon) {
            content.icon = icon;
        }
        content.confirmButtonText = "Aceptar!";
        content.confirmButtonClass = "btn m-btn--pill m-btn--air btn-metal";
        swal(content);
    }

    return {
        // public functions
        init: function() {
            demo();
        },
        show: function(message) {
            basic(message);
        },
        success: function(message, title=false) {
            basic(message, 'success',title,'flaticon-information', 5000);
        },
        warning: function(message, title=false) {
            basic(message, 'warning',title,'flaticon-warning-sign', 5000);
        },
        danger: function(message, title=false) {
            basic(message, 'danger',title,'flaticon-close',5000);
        },
        success2: function(message, title=false) {
            basic2(message, 'success',title,'success');
        },
        warning2: function(message, title=false) {
            basic2(message, 'warning',title, 'warning');
        },
        danger2: function(message, title=false) {
            basic2(message, 'error',title, 'error');
        }
    };
}();