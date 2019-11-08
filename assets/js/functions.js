function change_alias(alias) {
    var str = alias;
    str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
    str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
    str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
    str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
    str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
    str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
    str = str.replace(/đ/g, "d");
    str = str.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/g, "A");
    str = str.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/g, "E");
    str = str.replace(/Ì|Í|Ị|Ỉ|Ĩ/g, "I");
    str = str.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/g, "O");
    str = str.replace(/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/g, "U");
    str = str.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/g, "Y");
    str = str.replace(/Đ/g, "D");
    return str;
}

function loadListUserChat() {
    $.ajax({
        url: CONVERSATION_URL,
        success: function (data) {
            for (i in data.data) {
                renderUserItem(data.data[i], 'old');
            }
            LIST_USER = $('.left-chat ul li');
        }
    });
}

function loadChatMsgDetail() {
    $.ajax({
        url: CONVERSATION_DETAIL_URL,
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
        url: CONVERSATION_DETAIL_URL,
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

    // console.log(item);

    if (row.length > 0) {
        html = row;
        $('.chat-left-detail .chat-left-updated-at', html).text(item.updated_at);
    } else {
        html = $(html).attr('id', 'chat_item_' + item.conversation_id);
        if (item.conversation_id == CURRENT_USER_ID) {
            $(html).addClass('active');
        }
        $(html).attr('data-id', item.conversation_id);
        $(html).attr('data-sender-id', item.sender_id);
        $('.chat-left-detail .chat-left-updated-at', html).text(item.updated_at);
        $('.chat-left-detail .chat-left-customer-id', html).text(item.customer_name);
        $('.chat-left-detail p', html).text(item.sender_name);
        if (item.unread_count > 0) {
            $('.chat-left-unread-count', html).text(item.unread_count);
        }
        switch (item.type) {
            case TYPE_ZALO:
                $('.chat-left-img img', html).attr('src', ZALO_LOGO);
                break;
            case TYPE_VIBER:
                $('.chat-left-img img', html).attr('src', VIBER_LOGO);
                break;
            case TYPE_FB:
                $('.chat-left-img img', html).attr('src', FACEBOOK_LOGO);
                break;
        }

        if (type == 'new') {
            $('.left-chat ul').prepend(html);
        } else {
            $('.left-chat ul').append(html);
        }
    }
}

function renderMsgItem(item, type) {
    var html = msg_template.clone();
    var row = $('#msg_item_' + item.conversation_detail_id);
    if (row.length == 0) {
        html = $(html).attr('id', 'msg_item_' + item.conversation_detail_id);
        var class_name = ((SENDER_ID.valueOf() != item.sender_id.valueOf()) ? 'rightside-left-chat' : 'rightside-right-chat');
        var display_name = ((SENDER_ID.valueOf() != item.sender_id.valueOf()) ? 'Agent' : SENDER_NAME);
        $(html).attr('data-id', item.conversation_detail_id);
        $('>div', html).addClass(class_name);
        $('.name', html).text(display_name);
        $('small', html).text(item.created_at);
        $('.msg-checkbox-container .msg-checkbox', html).attr('data-id', item.conversation_detail_id)

        switch (item.type) {
            case MSG_TYPE_IMG:
                $('p', html).text(item.content);
                var link = $('<a href="#" target="_blank" class="link"></a>');
                var img = $('<img src="" />');
                $(img).attr('src', item.thumb);
                $(link).attr('href', item.href);
                link.html(img);
                $('p', html).append('<br/>');
                $('p', html).append(link);
                break;
            case MSG_TYPE_LINK:
                break;
            case MSG_TYPE_TEXT:
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
        url: SEND_MSG_URL,
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
