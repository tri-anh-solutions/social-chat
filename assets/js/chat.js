(function ($) {
    $.Chat = function (options) {
        var CURRENT_USER_ID = 0;
        var CURRENT_LIST_USER_PAGE = 1;
        var CURRENT_LIST_MSG_PAGE = 1;
        var SENDER_ID = '';
        var SENDER_NAME = '';
        var LIST_USER = [];
        var CURRENT_CHAT_ID = null;

        var settings = $.extend({
            'chatId': '#chatApp',
            'urls': {
                'conversation': '',
                'conversationDetail': '',
                'sendTicket': '',
                'sendMsg': '',
                'lock': '',
                'unlock': '',
                'transfer': '',
                'searchCustomer': '',
                'setCustomer': ''
            },
            'chatType': {
                'facebook': 1,
                'viber': 2,
                'zalo': 3,
                'lhc': 4
            },
            'msgType': {
                'text': 1,
                'img': 2,
                'link': 3
            },
            'logo': {
                'facebook': '',
                'viber': '',
                'zalo': '',
                'lhc': ''
            },
            'modal': '#modal',
            'debug': true,
            'updateTime': {
                'chat': 5000,
                'user': 5000
            }
        }, options);

        var user_template = $("#user-template>li:first");
        var msg_template = $("#msg-template>li:first");
        var chatApp = $(settings.chatId);
        var userPanel = $('.left-chat', chatApp);
        var contentPanel = $('.right-header-contentChat', chatApp);
        var msgPanel = $('.right-chat-textbox', chatApp);

        function init() {
            $(userPanel).on('click', '.chat-left-set-customer-id', function () {
                CURRENT_CHAT_ID = $(this).attr('data-id');
                debug('CURRENT_CHAT_ID ==> ' + CURRENT_CHAT_ID);
                $.ajax({url: settings.urls.searchCustomer})
                    .done(function (response) {
                        if (response.result && response.result === true && response.view) {
                            $('#messenger-modal-content').html(response.view);
                            $('#messenger-modal-title').html('Select customer');
                            $('#messenger-modal').modal('show');
                        } else {
                            alert(response.message);
                        }
                    })
                    .fail(function () {
                    });
            });

            $(userPanel).on('click', '.chat-left-transfer', function () {
                CURRENT_CHAT_ID = $(this).attr('data-id');
                debug('CURRENT_CHAT_ID ==> ' + CURRENT_CHAT_ID);
                $.ajax({url: settings.urls.transfer})
                    .done(function (response) {
                        if (response.result && response.result === true && response.view) {
                            $('#messenger-modal-content').html(response.view);
                            $('#messenger-modal-title').html('Select customer');
                            $('#messenger-modal').modal('show');
                        } else {
                            alert(response.message);
                        }
                    })
                    .fail(function () {
                    });
            });

            $(userPanel).on('click', '.chat-left-lock', function () {
                var data = {'conversation_id': $(this).attr('data-id')};
                var item = this;
                $.ajax({url: settings.urls.lock, data: data, method: 'POST'})
                    .done(function (response) {
                        if (response.result && response.result === true) {
                            alert(response.message);
                            $(item).hide();
                        } else {
                            alert(response.message);
                        }
                    })
                    .fail(function () {
                    });
            });

            $(userPanel).on('click', '.chat-left-unlock', function () {
                var data = {'conversation_id': $(this).attr('data-id')};
                var item = this;
                $.ajax({url: settings.urls.unlock, data: data, method: 'POST'})
                    .done(function (response) {
                        if (response.result && response.result === true) {
                            alert(response.message);
                            $(item).hide();
                        } else {
                            alert(response.message);
                        }
                    })
                    .fail(function () {
                    });
            });

            $('#chat-send-ticket').on('click', function () {
                var checkedMessages = $('.msg-checkbox:checked');
                if (checkedMessages.length > 0) {
                    var data = [];
                    for (var i = 0; i < checkedMessages.length; i++) {
                        var checkBox = checkedMessages[i];
                        console.log(checkBox);
                        if (checkBox !== null && checkBox !== undefined) {
                            data.push(checkBox.getAttribute('data-id'));
                        }
                    }
                    if (data.length > 0) {
                        var params = encodeURIComponent(JSON.stringify(data));
                        var url = settings.urls.sendTicket + '?conversation_detail_ids=' + params;
                        var win = window.open(url, '_blank');
                        win.focus();
                    }
                } else {
                    alert("Please select messages.");
                }
            });

            $('#messenger-modal-content').on('click', '.pagination a', function () {
                $.ajax({url: $(this).attr('href')})
                    .done(function (response) {
                        if (response.result && response.result === true && response.view) {
                            $('#messenger-modal-content').html(response.view);
                        } else {
                            alert(response.message);
                        }
                    })
                    .fail(function () {
                    });
                return false;
            });

            $('#messenger-modal-content').on('beforeSubmit', '#messenger-customer-search-form', function () {
                var form = $(this);
                $.ajax({url: settings.urls.searchCustomer, data: form.serialize()})
                    .done(function (response) {
                        if (response.result && response.result === true && response.view) {
                            debug(response.view);
                            $('#messenger-modal-content').html(response.view);
                        } else {
                            alert(response.message);
                        }
                    })
                    .fail(function () {
                    });
                return false;
            });

            $('#messenger-modal-content').on('click', '.select-customer', function () {
                var cusId = $(this).attr('data-id');
                var fullName = $(this).attr('data-full_name');
                setCustomer(cusId, fullName);
            });

            $('.left-chat').on('click', 'li:not(.active)', function () {
                changeSelectedUser(this);
            });

            $("#msg-content").keypress(function (e) {
                if (e.keyCode == 13) {
                    e.preventDefault();
                    sendMsg();
                }
            });

            $("#btn-send-msg").click(function (e) {
                e.preventDefault();
                sendMsg();
            });

            $('#srch-term').keyup(function (e) {
                e.preventDefault();
                var filter = $(this).val().toLowerCase();
                filter = change_alias(filter);
                LIST_USER.each(function (index, item) {
                    var name = $('.chat-left-detail p', item).text().toLowerCase();
                    name = change_alias(name);
                    if (name.indexOf(filter) > -1) {
                        $(item).show();
                    } else {
                        $(item).hide();
                    }
                })
            });

            $('#load-more-msg').click(function () {
                $('.load-more-text').hide();
                $('.load-more-loading').show();
                CURRENT_LIST_MSG_PAGE++;
                loadChatOldMsgDetail();
            });

            $('.right-header-detail-control').on('click', '#chat-select', function () {
                $('#chat-select').addClass('hidden');
                $('#chat-cancel').removeClass('hidden');
                $('#chat-send-ticket').removeClass('hidden');
                $('.right-header-contentChat').addClass('chat-selection');
            });

            $('.right-header-detail-control').on('click', '#chat-cancel', function () {
                $('#chat-select').removeClass('hidden');
                $('#chat-cancel').addClass('hidden');
                $('#chat-send-ticket').addClass('hidden');
                $('.right-header-contentChat').removeClass('chat-selection');
                $(".msg-checkbox:checkbox").prop('checked', false);
            });
//init chat size
            var height = $(window).height() - 150;
            $('.left-chat').css('height', (height - 163) + 'px');
            $('.right-header-contentChat').css('height', (height - 163) + 'px');

            $(window).on('resize', function () {
                var height = $(window).height() - 150;
                $('.left-chat').css('height', (height - 163) + 'px');
                $('.right-header-contentChat').css('height', (height - 163) + 'px');
            });
        }

        function changeSelectedUser(obj) {
            $('.left-chat li').removeClass('active');
            $(obj).find('.chat-left-unread-count').addClass('hidden');
            $(obj).addClass('active');
            // $(this).hide().parent().prepend($(this).slideDown());
            debug(obj);
            CURRENT_USER_ID = $(obj).attr('data-id');
            SENDER_ID = $(obj).attr('data-sender-id');
            SENDER_NAME = $(obj).find('.chat-left-detail p').text();
            CURRENT_LIST_MSG_PAGE = 1;
            debug('SET CURRENT_USER_ID ==> ' + CURRENT_USER_ID);
            $('.right-header-contentChat ul li').remove();
            $('.right-header-detail p').text(SENDER_NAME);
            loadChatMsgDetail();
        }

        function loadListUserChat() {
            $.ajax({
                url: settings.urls.conversation,
                success: function (data) {
                    for (var i in data.data) {
                        renderUserItem(data.data[i], 'old');
                    }
                    LIST_USER = $('.left-chat ul li');
                }
            });
        }

        function loadChatMsgDetail() {
            debug('CURRENT_USER_ID ==> ' + CURRENT_USER_ID);
            $.ajax({
                url: settings.urls.conversationDetail,
                data: {'id': CURRENT_USER_ID},
                success: function (data) {
                    for (var i in data.data) {
                        renderMsgItem(data.data[i], 'new');
                    }
                    if (CURRENT_LIST_MSG_PAGE < data.total_page) {
                        $('.load-more-text').show();
                    }

                },
                dataFormat: 'json'
            });
        }

        function loadChatOldMsgDetail() {
            $.ajax({
                url: settings.urls.conversationDetail,
                data: {'id': CURRENT_USER_ID, 'page': CURRENT_LIST_MSG_PAGE},
                success: function (data) {
                    for (var i in data.data) {
                        renderMsgItem(data.data[i], 'old');
                    }

                    $('.load-more-loading').hide();
                    if (CURRENT_LIST_MSG_PAGE < data.total_page) {
                        $('.load-more-text').show();
                    }
                },
                dataFormat: 'json'
            });
        }


        function renderUserItem(item, type) {
            var html = user_template.clone();
            var row = $('#chat_item_' + item.conversation_id);
            if (CURRENT_USER_ID == 0) {
                CURRENT_USER_ID = item.conversation_id;
                SENDER_ID = item.sender_id;
                SENDER_NAME = item.sender_name;
                $('.right-header-detail p').text(SENDER_NAME);
                loadChatMsgDetail();
            }
            if (row.length > 0) {
                html = row;
                $('.chat-left-detail .chat-left-updated-at', html).text(item.updated_at);
                $('.chat-left-detail .chat-left-customer-id', html).text(item.customer_name);
            } else {
                html = $(html).attr('id', 'chat_item_' + item.conversation_id);
                if (item.conversation_id == CURRENT_USER_ID) {
                    $(html).addClass('active');
                }
                $(html).attr('data-id', item.conversation_id).attr('data-sender-id', item.sender_id);
                $('.chat-left-detail .chat-left-updated-at', html).text(item.updated_at);


                $('.chat-left-detail p', html).text(item.sender_name);

                switch (item.type) {
                    case settings.chatType.zalo:
                        $('.chat-left-img img', html).attr('src', settings.logo.zalo);
                        break;
                    case settings.chatType.viber:
                        $('.chat-left-img img', html).attr('src', settings.logo.viber);
                        break;
                    case settings.chatType.facebook:
                        $('.chat-left-img img', html).attr('src', settings.logo.facebook);
                        break;
                    case settings.chatType.lhc:
                        $('.chat-left-img img', html).attr('src', settings.logo.lhc);
                        break;
                }

                if (type == 'new') {
                    $('.left-chat ul', chatApp).prepend(html);
                } else {
                    $('.left-chat ul', chatApp).append(html);
                }
            }
            if (item.unread_count > 0) {
                $('.chat-left-unread-count', html).text(item.unread_count);
            }
            $('.chat-left-set-customer-id', html).attr('data-id', item.conversation_id);
            $('.chat-left-lock', html).attr('data-id', item.conversation_id);
            $('.chat-left-unlock', html).attr('data-id', item.conversation_id);
            $('.chat-left-transfer', html).attr('data-id', item.conversation_id);
            if (item.customer_name.length > 0) {
                $('.chat-left-set-customer-id', html).hide();
            }
            $('.chat-left-detail .chat-left-customer-name', html).text(item.customer_name);
            $('.chat-left-detail .chat-left-locked-by', html).text(item.locked_name);

            if (item.allow_transfer) {
                $('.chat-left-detail .chat-left-transfer', html).show();
                $('.chat-left-detail .chat-left-unlock', html).show();
            } else {
                $('.chat-left-detail .chat-left-transfer', html).hide();
                $('.chat-left-detail .chat-left-unlock', html).hide();
            }
            if (item.locked_by) {
                $('.chat-left-detail .chat-left-lock', html).hide();
            } else {
                $('.chat-left-detail .chat-left-lock', html).show();
            }
        }

        function renderMsgItem(item, type) {
            var html = msg_template.clone();
            var row = $('#msg_item_' + item.conversation_detail_id);
            if (row.length == 0) {
                html = $(html).attr('id', 'msg_item_' + item.conversation_detail_id);
                var class_name = ((SENDER_ID != item.sender_id) ? 'rightside-left-chat' : 'rightside-right-chat');
                var display_name = ((SENDER_ID != item.sender_id) ? item.sender_name : SENDER_NAME);
                $(html).attr('data-id', item.conversation_detail_id);
                $('>div', html).addClass(class_name);
                $('.name', html).text(display_name);
                $('small', html).text(item.created_at);
                $('.msg-checkbox-container .msg-checkbox', html).attr('data-id', item.conversation_detail_id)

                switch (item.type) {
                    case settings.msgType.img:
                        $('p', html).text(item.content);
                        var link = $('<a href="#" target="_blank" class="link"></a>');
                        var img = $('<img src="" />');
                        $(img).attr('src', item.thumb);
                        $(link).attr('href', item.href);
                        link.html(img);
                        $('p', html).append('<br/>');
                        $('p', html).append(link);
                        break;
                    case  settings.msgType.link:
                        break;
                    case settings.msgType.text:
                    default:
                        $('p', html).text(item.content);
                }
                // console.log(type);
                if (type == 'new') {
                    $('.right-header-contentChat ul').append(html);
                    $('.right-header-contentChat').scrollTop($('.right-header-contentChat')[0].scrollHeight);
                } else {
                    $('.right-header-contentChat ul').prepend(html);
                }
            }
        }

        function sendMsg() {
            $.ajax({
                url: settings.urls.sendMsg,
                data: {'id': CURRENT_USER_ID, 'msg': $('#msg-content').val()},
                success: function (data) {
                    console.log(data);
                    if (data.success) {
                        $('#msg-content').val('');
                        renderMsgItem(data.data, 'new');
                    } else {
                        alert(data.error);
                    }
                },
                dataFormat: 'json'
            });
        }

        function setCustomer(id_customer, name) {
            var data = {'conversation_id': CURRENT_CHAT_ID, 'id_customer': id_customer};
            debug('Customer Name => ' + name);
            $.ajax({url: settings.urls.setCustomer, data: data, method: 'POST'})
                .done(function (response) {
                    if (response.result && response.result === true) {
                        var item = $('.left-chat').find('li[data-id="' + CURRENT_CHAT_ID + '"]').find('.chat-left-customer-name:first');
                        $('.left-chat').find('li[data-id="' + CURRENT_CHAT_ID + '"]').find('.chat-left-set-customer-id').hide();
                        if (item !== undefined) {
                            item.html(name);
                            $('#messenger-modal').modal('hide');
                        }
                    } else {
                        alert(response.message);
                    }
                })
                .fail(function () {
                });
        }

        function debug(msg) {
            if (settings.debug) {
                console.log(msg);
            }
        }

        init();

        loadListUserChat();
        setInterval(function () {
            loadListUserChat();
        }, settings.updateTime.user);

        setInterval(function () {
            loadChatMsgDetail();
        }, settings.updateTime.chat);
    };
}(jQuery));