<?php
/**
 *
 * User: ThangDang
 * Date: 7/24/18
 * Time: 09:50
 *
 */

namespace tas\social\components;


class ZaloEventType
{
    const SENDMSG         = 'sendmsg';
    const SENDIMAGEMSG    = 'sendimagemsg';
    const SENDLINKMSG     = 'sendlinkmsg';
    const SENDVOICEMSG    = 'sendvoicemsg';
    const SENDLOCATIONMSG = 'sendlocationmsg';
    const SENDSTICKERMSG  = 'sendstickermsg';
    const SENDGIFMSG      = 'sendgifmsg';
    const FOLLOW          = 'follow';
    const UNFOLLOW        = 'unfollow';
    const MSG_DELIVERED   = 'msg_delivered';
    const OS_SEND_MSG     = 'os_send_msg';
}