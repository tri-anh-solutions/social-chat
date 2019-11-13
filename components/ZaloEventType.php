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
	const SEND_MSG          = 'user_send_text';
	const SEND_IMAGE_MSG    = 'user_send_image';
	const SEND_LINK_MSG     = 'user_send_link';
	const SEND_VOICE_MSG    = 'sendvoicemsg';
	const SEND_LOCATION_MSG = 'sendlocationmsg';
	const SEND_STICKER_MSG  = 'user_send_sticker';
	const SEND_GIF_MSG      = 'sendgifmsg';
	const FOLLOW            = 'follow';
	const UNFOLLOW          = 'unfollow';
	const MSG_DELIVERED     = 'msg_delivered';
	const OS_SEND_MSG       = 'os_send_msg';
}