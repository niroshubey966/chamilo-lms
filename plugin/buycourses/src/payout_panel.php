<?php
/**
 * User Panel
 * @package chamilo.plugin.buycourses
 */
/**
 * Initialization
 */

$cidReset = true;

require_once '../../../main/inc/global.inc.php';

$plugin = BuyCoursesPlugin::create();
$includeSessions = $plugin->get('include_sessions') === 'true';
$includeServices = $plugin->get('include_services') === 'true';

$userInfo = api_get_user_info();

if (!$userInfo) {
    api_not_allowed();
}

$payouts = $plugin->getPayouts(BuyCoursesPlugin::PAYOUT_STATUS_COMPLETED, false, $userInfo['user_id']);

$payoutList = [];

foreach ($payouts as $payout) {
    $payoutList[] = [
        'id' => $payout['id'],
        'sale_id' => $payout['sale_id'],
        'reference' => $payout['sale_reference'],
        'date' => api_format_date($payout['date'], DATE_TIME_FORMAT_LONG_24H),
        'payout_date' => ($payout['payout_date'] === '0000-00-00 00:00:00') ? '-' : api_format_date($payout['payout_date'], DATE_TIME_FORMAT_LONG_24H),
        'currency' => $payout['iso_code'],
        'price' => $payout['item_price'],
        'commission' => $payout['commission'],
        'paypal_account' => $payout['paypal_account'],
        'status' => $payout['status']
    ];
}
$interbreadcrumb[] = ['url' => '../index.php', 'name' => $plugin->get_lang('UserPanel')];

$templateName = get_lang('TabsDashboard');
$tpl = new Template($templateName);
$tpl->assign('showing_courses', true);
$tpl->assign('sessions_are_included', $includeSessions);
$tpl->assign('services_are_included', $includeServices);
$tpl->assign('payout_list', $payoutList);

$content = $tpl->fetch('buycourses/view/payout_panel.tpl');

$tpl->assign('header', $templateName);
$tpl->assign('content', $content);
$tpl->display_one_col_template();