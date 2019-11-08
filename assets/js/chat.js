$(document).ready(function () {
    // init chat size
    var height = $(window).height() - 150;
    $('.left-chat').css('height', (height - 163) + 'px');
    $('.right-header-contentChat').css('height', (height - 163) + 'px');

    loadListUserChat();
    setInterval(function () {
        loadListUserChat();
    }, 3000);

    setInterval(function () {
        loadChatMsgDetail();
    }, 3000);

    $('.left-chat').on('click', 'li:not(.active)', function () {
        $('.left-chat li').removeClass('active');
        $(this).find('.chat-left-unread-count').addClass('hidden');
        $(this).addClass('active');
        // $(this).hide().parent().prepend($(this).slideDown());
        CURRENT_USER_ID = $(this).attr('data-id');
        SENDER_ID = $(this).attr('data-sender-id');
        SENDER_NAME = $(this).find('.chat-left-detail p').text();
        CURRENT_LIST_MSG_PAGE = 1;
        $('.right-header-contentChat ul li').remove();
        $('.right-header-detail p').text(SENDER_NAME);
        loadChatMsgDetail();
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
});
$(window).on('resize',function () {
    var height = $(window).height() - 150;
    $('.left-chat').css('height', (height - 163) + 'px');
    $('.right-header-contentChat').css('height', (height - 163) + 'px');
});
