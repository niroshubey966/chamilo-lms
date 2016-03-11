<?php

/* For licensing terms, see /chamilo_license.txt */
/**
 * Responses to AJAX calls
 * @package chamilo.plugin.buycourses
 */

$cidReset = true;

require_once '../../../main/inc/global.inc.php';

api_protect_admin_script(true, true);

$plugin = BuyCoursesPlugin::create();

$paypalEnable = $plugin->get('paypal_enable');
$commissionsEnable = $plugin->get('commissions_enable');

$action = isset($_GET['a']) ? $_GET['a'] : null;

switch ($action) {
    case 'verifyPaypal':
        if (api_is_anonymous()) {
            break;
        }
        
        $userId = isset($_POST['id']) ? intval($_POST['id']) : '';
        $isUserHavePaypalAccount = $plugin->verifyPaypalAccountByBeneficiary($userId);
        
        if ($isUserHavePaypalAccount) {
            echo '';
        } else {
            echo '<b style="color: red; font-size: 70%;">* '.$plugin->get_lang('NoPayPalAccountDetected').'</b>';
        }
        
        break;
        
    case 'saleInfo':
        if (api_is_anonymous()) {
            break;
        }
        
        $saleId = isset($_POST['id']) ? intval($_POST['id']) : '';
        $sale = $plugin->getSale($saleId);
        $productType = ($sale['product_type'] == 1) ? get_lang('Course') : get_lang('Session');
        $paymentType = ($sale['payment_type'] == 1) ? 'Paypal' : $plugin->get_lang('BankTransfer');
        $productInfo = ($sale['product_type'] == 1) ? api_get_course_info_by_id($sale['product_id']) : api_get_session_info($sale['product_id']);
        $currency = $plugin->getSelectedCurrency();
        if ($sale['product_type'] == 1) {
            $productImage = $productInfo['course_image_large'];
        } else {
            $productImage = ($productInfo['image']) ? $productInfo['image'] : Template::get_icon_path('session_default.png');
        }
        
        $userInfo = api_get_user_info($sale['user_id']);
        
        $html = '<h2>' . $sale['product_name'] .'</h2>';
        $html .= '<div class="row">';
        $html .= '<div class="col-sm-6 col-md-6">';
        $html .= '<ul>';
        $html .= '<li><b>'. $plugin->get_lang('OrderPrice') . ':</b> '. $sale['price'] . '</li>';
        $html .= '<li><b>'. $plugin->get_lang('CurrencyType') . ':</b> '. $currency['iso_code'] . '</li>';
        $html .= '<li><b>'. $plugin->get_lang('ProductType') . ':</b> '. $productType . '</li>';
        $html .= '<li><b>'. $plugin->get_lang('OrderDate') . ':</b> '. api_format_date($sale['date'], DATE_TIME_FORMAT_LONG_24H) . '</li>';
        $html .= '<li><b>'. $plugin->get_lang('Buyer') . ':</b> '. $userInfo['complete_name'] . '</li>';
        $html .= '<li><b>'. $plugin->get_lang('PaymentMethods') . ':</b> '. $paymentType . '</li>';
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '<div class="col-sm-6 col-md-6">';
        $html .= '<img class="thumbnail" src="'. $productImage .'" >';
        $html .= '</div>';
        $html .= '</div>';

        echo $html;
        
        break;
    
    case 'stats':
        if (api_is_anonymous()) {
            break;
        }
        
        $stats = [];
        $stats['completed_count'] = 0;
        $stats['completed_total_amount'] = 0;
        $stats['pending_count'] = 0;
        $stats['pending_total_amount'] = 0;
        $stats['canceled_count'] = 0;
        $stats['canceled_total_amount'] = 0;
        
        $completedPayouts = $plugin->getPayouts(BuyCoursesPlugin::PAYOUT_STATUS_COMPLETED);
        $pendingPayouts = $plugin->getPayouts(BuyCoursesPlugin::PAYOUT_STATUS_PENDING);
        $cancelledPayouts = $plugin->getPayouts(BuyCoursesPlugin::PAYOUT_STATUS_CANCELLED);
        $currency = $plugin->getSelectedCurrency();
        
        foreach ($completedPayouts as $completed) {
            $stats['completed_count'] = count($completedPayouts);
            $stats['completed_total_amount'] += $completed['commission'];
            $stats['completed_total_amount'] = number_format($stats['completed_total_amount'], 2);
        }
        
        foreach ($pendingPayouts as $pending) {
            $stats['pending_count'] = count($pendingPayouts);
            $stats['pending_total_amount'] += $pending['commission'];
            $stats['pending_total_amount'] = number_format($stats['pending_total_amount'], 2);
        }
        
        foreach ($cancelledPayouts as $cancelled) {
            $stats['canceled_count'] = count($cancelledPayouts);
            $stats['canceled_total_amount'] += $cancelled['commission'];
            $stats['canceled_total_amount'] = number_format($stats['canceled_total_amount'], 2);
        }
        
        
        $html = '<div class="row">'
        . '<p>'
            . '<ul>'
                . '<li>'. get_plugin_lang("PayoutsTotalCompleted", "BuyCoursesPlugin") .' <b>'. $stats['completed_count'] .'</b> - '. get_plugin_lang("TotalAmount", "BuyCoursesPlugin") .' <b>'. $stats['completed_total_amount'] .' '. $currency['iso_code'] . '</b></li>'
                . '<li>'. get_plugin_lang("PayoutsTotalPending", "BuyCoursesPlugin") .' <b>'. $stats['pending_count'] .'</b> - '. get_plugin_lang("TotalAmount", "BuyCoursesPlugin") .' <b>'. $stats['pending_total_amount'] .' '. $currency['iso_code'] . '</b></li>'
                . '<li>'. get_plugin_lang("PayoutsTotalCancelled", "BuyCoursesPlugin") .' <b>'. $stats['canceled_count'] .'</b> - '. get_plugin_lang("TotalAmount", "BuyCoursesPlugin") .' <b>'. $stats['canceled_total_amount'] .' '. $currency['iso_code'] . '</b></li>'
            . '</ul>'
        . '</p>';
        $html .= '</div>';
        echo $html;
        
        break;
    
    case 'processPayout':
        if (api_is_anonymous()) {
            break;
        }
        
        $html = '';
        $allPays = [];
        $totalAccounts = 0;
        $totalPayout = 0;
        
        $payouts = isset($_POST['payouts']) ? $_POST['payouts'] : '';
        
        if (!$payouts) {
            
            echo Display::return_message(get_plugin_lang("SelectOptionToProceed", "BuyCoursesPlugin"), 'error', false);
            
            break;
        }
        
        foreach ($payouts as $index => $id) {
            $allPays[] = $plugin->getPayouts(BuyCoursesPlugin::PAYOUT_STATUS_PENDING, $id);
        }
        
        foreach ($allPays as $payout) {
            $totalPayout += number_format($payout['commission'], 2);
            $totalAccounts++;
        }
        
        $currentCurrency = $plugin->getSelectedCurrency();
        
        $isoCode = $currentCurrency['iso_code'];
        
        $html .= '<p>'. get_plugin_lang("VerifyTotalAmountToProceedPayout", "BuyCoursesPlugin") .'</p>';
        $html .= ''
        . '<p>'
            . '<ul>'
                . '<li>'. get_plugin_lang("TotalAcounts", "BuyCoursesPlugin") .' <b>'. $totalAccounts .'</b></li>'
                . '<li>'. get_plugin_lang("TotalPayout", "BuyCoursesPlugin") .' <b>'. $isoCode .' '. $totalPayout .'</b></li>'
            . '</ul>'
        . '</p>';
        $html .= '<p>'. get_plugin_lang("CautionThisProcessCantBeCancelled", "BuyCoursesPlugin") .'</p>';
        $html .= '</br></br>';
        $html .= '<div id="spinner" class="text-center"></div>';
                
        echo $html;
        break;
    
    case 'proceedPayout':
        if (api_is_anonymous()) {
            break;
        }
        
        $paypalParams = $plugin->getPaypalParams();
        
        $pruebas = $paypalParams['sandbox'] == 1;
        $paypalUsername = $paypalParams['username'];
        $paypalPassword = $paypalParams['password'];
        $paypalSignature = $paypalParams['signature'];
        
        require_once("paypalfunctions.php");
        
        $allPayouts = [];
        $totalAccounts = 0;
        $totalPayout = 0;
        
        $payouts = isset($_POST['payouts']) ? $_POST['payouts'] : '';
        
        if (!$payouts) {
            
            echo Display::return_message(get_plugin_lang("SelectOptionToProceed", "BuyCoursesPlugin"), 'error', false);
            
            break;
        }
        
        foreach ($payouts as $index => $id) {
            $allPayouts[] = $plugin->getPayouts(BuyCoursesPlugin::PAYOUT_STATUS_PENDING, $id);
        }
        
        $currentCurrency = $plugin->getSelectedCurrency();
        
        $isoCode = $currentCurrency['iso_code'];
        
        
        $result = MassPayment($allPayouts, $isoCode);
        
        if ($result['ACK'] === 'Success') {
            foreach ($allPayouts as $payout) {
                $plugin->setStatusPayouts($payout['id'], BuyCoursesPlugin::PAYOUT_STATUS_COMPLETED);
            }
            
            echo Display::return_message(get_plugin_lang("PayoutSuccess", "BuyCoursesPlugin"), 'success', false);
            
        } else {

            echo Display::return_message('<b>'.$result['L_SEVERITYCODE0'].' '.$result['L_ERRORCODE0'].'</b> - '.$result['L_SHORTMESSAGE0'].'</br><ul><li>'. $result['L_LONGMESSAGE0'].'</li></ul>', 'error', false);
            
        }
        
        break;
        
    case 'cancelPayout':
        if (api_is_anonymous()) {
            break;
        }

        // $payoutId only gets used in setStatusPayout(), where it is filtered
        $payoutId = isset($_POST['id']) ? $_POST['id'] : '';
        $plugin->setStatusPayouts($payoutId, BuyCoursesPlugin::PAYOUT_STATUS_CANCELLED);
        
        echo '';
        
        break;
    case 'subscribe_user':
        
        if (api_is_anonymous()) {
            break;
        }
        
        $subscribeSlot = isset($_POST['subscribe_slot']) ? intval($_POST['subscribe_slot']) : null;
        $email = isset($_POST['email']) ? $_POST['email'] : null;
        $type = isset($_POST['type']) ? intval($_POST['type']): null;
        $typeId = isset($_POST['typeId']) ? intval($_POST['typeId']): null;
        
        if ($subscribeSlot && $email) {
            $userExist = api_get_user_info_from_email($email);
            if ($userExist) {
                $userId = $userExist['user_id'];                
            } else {
                $userId = UserManager::createUserByEmail($email);
            }
            
            $userInfo = api_get_user_info($userId);
            UserManager::subscribeUsersToUser(api_get_user_id(), [$userId], USER_RELATION_TYPE_RRHH);
            $result = $plugin->updateSubscriberUser($subscribeSlot, $userId, $type, $typeId);
            
            if ($result) {
                Display::addFlash(Display::return_message($userInfo['email'] . ': ' . get_lang('AddedToCourse'), 'success'));
            } else {
                Display::addFlash(Display::return_message(get_lang('CannotSubscribeUser'), 'error'));
            }
        } else {
            Display::addFlash(Display::return_message('Error: '.get_lang('ThisFieldIsRequired'), 'error'));
        }
        break;
    case 'unsubscribe_user';
        
        if (api_is_anonymous()) {
            break;
        }
        
        $subscribeSlot = isset($_POST['subscribe_slot']) ? intval($_POST['subscribe_slot']) : null;
        $type = isset($_POST['type']) ? intval($_POST['type']): null;
        $typeId = isset($_POST['typeId']) ? intval($_POST['typeId']): null;
        $groupId = isset($_POST['groupId']) ? intval($_POST['groupId']): null;
        $userId = isset($_POST['userId']) ? intval($_POST['userId']): null;
        
        if ($type == 1) {
            $courseInfo = api_get_course_info_by_id($typeId);
            $courseCode = $courseInfo['code'];
            CourseManager::unsubscribe_user($userId, $courseCode);
            
        } else if ($type == 2) {
            $sessionId = $typeId;
            SessionManager::unsubscribe_user_from_session($sessionId, $userId);
        }
        
        $usergroup = new UserGroup();
        $usergroup->delete_user_rel_group($userId, $groupId);
        UserManager::unsubscribeUsersToUser(api_get_user_id(), [$userId], USER_RELATION_TYPE_RRHH);
        $result = $plugin->UnsubscribeUser($subscribeSlot);
        
        if ($result) {
            Display::addFlash(Display::return_message(get_lang('UserUnsubscribed'), 'success'));
        } else {
            Display::addFlash(Display::return_message(get_lang('CannotUnsubscribeUser'), 'error'));
        }
        break;
}
exit;
