/**
 * Admin JavaScript functionality
 *
 * @package Opencode_Plugin_Example
 */

(function($) {
    'use strict';

    var Admin = {
        init: function() {
            this.bindEvents();
            this.initTabs();
            this.initTooltips();
        },

        bindEvents: function() {
            $(document).on('click', '.opencode-notice .notice-dismiss', this.dismissNotice);
            $(document).on('click', '.opencode-ajax-button', this.handleAjaxRequest);
            $(document).on('submit', '.opencode-admin-form', this.handleFormSubmit);
            $(document).on('change', '.opencode-toggle-setting', this.toggleSetting);
        },

        initTabs: function() {
            $('.nav-tab-wrapper a').on('click', function(e) {
                e.preventDefault();
                var target = $(this).attr('href');
                
                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                
                $('.tab-content').hide();
                $(target).show();
            });
        },

        initTooltips: function() {
            $('.opencode-tooltip').each(function() {
                var $element = $(this);
                var title = $element.attr('title');
                
                if (title) {
                    $element.removeAttr('title');
                    $element.hover(
                        function() {
                            $('<div class="opencode-tooltip-content">' + title + '</div>')
                                .appendTo('body')
                                .css({
                                    position: 'absolute',
                                    top: ($element.offset().top - 30) + 'px',
                                    left: $element.offset().left + 'px',
                                    backgroundColor: '#333',
                                    color: '#fff',
                                    padding: '5px 10px',
                                    borderRadius: '3px',
                                    fontSize: '12px',
                                    zIndex: 10000
                                })
                                .fadeIn(200);
                        },
                        function() {
                            $('.opencode-tooltip-content').remove();
                        }
                    );
                }
            });
        },

        dismissNotice: function(e) {
            e.preventDefault();
            var $notice = $(this).closest('.opencode-notice');
            var noticeId = $notice.data('notice-id');

            if (noticeId) {
                $.ajax({
                    url: opencodePluginAdmin.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'opencode_dismiss_notice',
                        notice_id: noticeId,
                        nonce: opencodePluginAdmin.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            $notice.fadeOut(300, function() {
                                $(this).remove();
                            });
                        }
                    }
                });
            } else {
                $notice.fadeOut(300, function() {
                    $(this).remove();
                });
            }
        },

        handleAjaxRequest: function(e) {
            e.preventDefault();
            var $button = $(this);
            var action = $button.data('action');
            var confirmMessage = $button.data('confirm');

            if (confirmMessage && !confirm(confirmMessage)) {
                return;
            }

            $button.prop('disabled', true).text(opencodePluginAdmin.i18n.loading || 'Loading...');

            $.ajax({
                url: opencodePluginAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: action,
                    nonce: opencodePluginAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data.message) {
                            Admin.showNotice(response.data.message, 'success');
                        }
                        if (response.data.redirect) {
                            window.location.href = response.data.redirect;
                        } else {
                            location.reload();
                        }
                    } else {
                        Admin.showNotice(response.data.message || 'An error occurred', 'error');
                        $button.prop('disabled', false).text($button.data('original-text'));
                    }
                },
                error: function(xhr, status, error) {
                    Admin.showNotice(opencodePluginAdmin.i18n.error || 'An error occurred', 'error');
                    $button.prop('disabled', false).text($button.data('original-text'));
                }
            });
        },

        handleFormSubmit: function(e) {
            e.preventDefault();
            var $form = $(this);
            var $submitBtn = $form.find('input[type="submit"]');
            var originalText = $submitBtn.val();

            $submitBtn.prop('disabled', true).val(opencodePluginAdmin.i18n.saving || 'Saving...');

            $.ajax({
                url: $form.attr('action') || opencodePluginAdmin.ajaxUrl,
                type: $form.attr('method') || 'POST',
                data: $form.serialize() + '&nonce=' + opencodePluginAdmin.nonce,
                success: function(response) {
                    if (response.success) {
                        Admin.showNotice(response.data.message || 'Settings saved', 'success');
                        if (response.data.redirect) {
                            window.location.href = response.data.redirect;
                        }
                    } else {
                        Admin.showNotice(response.data.message || 'An error occurred', 'error');
                        $submitBtn.prop('disabled', false).val(originalText);
                    }
                },
                error: function() {
                    Admin.showNotice(opencodePluginAdmin.i18n.error || 'An error occurred', 'error');
                    $submitBtn.prop('disabled', false).val(originalText);
                }
            });
        },

        toggleSetting: function(e) {
            var $checkbox = $(this);
            var settingKey = $checkbox.data('setting');
            var settingValue = $checkbox.is(':checked') ? 1 : 0;

            $.ajax({
                url: opencodePluginAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'opencode_toggle_setting',
                    setting_key: settingKey,
                    setting_value: settingValue,
                    nonce: opencodePluginAdmin.nonce
                },
                success: function(response) {
                    if (!response.success) {
                        Admin.showNotice(response.data.message || 'Failed to update setting', 'error');
                        $checkbox.prop('checked', !settingValue);
                    }
                },
                error: function() {
                    Admin.showNotice(opencodePluginAdmin.i18n.error || 'An error occurred', 'error');
                    $checkbox.prop('checked', !settingValue);
                }
            });
        },

        showNotice: function(message, type) {
            var noticeClass = 'opencode-notice ' + type;
            var $notice = $('<div class="' + noticeClass + '"><p>' + message + '</p></div>');
            
            $('.opencode-plugin-admin-wrap h1').after($notice);
            
            setTimeout(function() {
                $notice.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        }
    };

    $(document).ready(function() {
        Admin.init();
    });

})(jQuery);