<?php 
function getNotificationClassName($n) {
	switch($n->getSystemNotificationTypeID()) {
		case SystemNotification::SN_TYPE_CORE_MESSAGE_HELP:
			return 'ccm-dashboard-notification-core-message-help';
			break;
		case SystemNotification::SN_TYPE_CORE_MESSAGE_NEWS:
			return 'ccm-dashboard-notification-core-message-news';
			break;
		case SystemNotification::SN_TYPE_ADDON_UPDATE:
			return 'ccm-dashboard-notification-addon-update';
			break;
		case SystemNotification::SN_TYPE_CORE_UPDATE_CRITICAL:
		case SystemNotification::SN_TYPE_ADDON_UPDATE_CRITICAL:
			return 'ccm-dashboard-notification-critical';
			break;
		case SystemNotification::SN_TYPE_ADDON_MESSAGE:
			return 'ccm-dashboard-notification-addon-message';
			break;
		case SystemNotification::SN_TYPE_CORE_UPDATE:
			return 'ccm-dashboard-notification-core-update';
			break;
		case SystemNotification::SN_TYPE_CORE_MESSAGE_OTHER:
		default:
			return 'ccm-dashboard-notification-generic';
			break;
	}
}

?>


<ul data-role="listview" data-inset="true" class="ui-listview">
<?php  
$lastDate = false;
$txt = Loader::helper('text');
foreach($notifications as $n) { 
	$date = date('Y-m-d', strtotime($n->getSystemNotificationDateTime()));
	$time = date('g:i A', strtotime($n->getSystemNotificationDateTime()));
	
	if ($date != $lastDate) { ?>
		<li class="ui-li ui-li-divider ui-btn ui-bar-a ui-li-has-count ui-btn-up-a" data-role="list-divider"><h2><?php  
			if (date('Y-m-d') == $date) { 
				print t('Today');
			} else if (date('Y-m-d', strtotime('-1 days')) == $date) { 
				print t('Yesterday');
			} else {
				print date('F jS', strtotime($date));
			}
		?></h2></li>
	<?php  } ?>
	
	<li class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-btn-up-c">
	
	<h3><?php echo $n->getSystemNotificationTitle()?> <span><?php echo $time?></span></h3>
	<?php  if ($isDashboardModule && in_array($n->getSystemNotificationTypeID(), array(
		SystemNotification::SN_TYPE_CORE_MESSAGE_HELP,
		SystemNotification::SN_TYPE_CORE_MESSAGE_NEWS,
		SystemNotification::SN_TYPE_CORE_MESSAGE_OTHER
	))) { ?>
		<p><?php echo $txt->shorten(strip_tags($n->getSystemNotificationDescription()), 64)?></p>
	<?php  } else { ?>
		<p><?php echo $n->getSystemNotificationDescription()?></p>
	<?php  } ?>

	</li>
	
<?php  } ?>
</ul>
