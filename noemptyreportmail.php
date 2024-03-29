<?php

require_once 'noemptyreportmail.civix.php';
// phpcs:disable
use CRM_Noemptyreportmail_ExtensionUtil as E;
// phpcs:enable

define('NOEMPTYREPORTMAIL_ROWS_EMPTY_MARKER', 'NOEMPTYREPORTMAIL_ROWS_EMPTY_MARKER');

/**
 * Implements hook_civicrm_alterReportVar().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterReportVar
 */
function noemptyreportmail_civicrm_alterReportVar($varType, &$var, $reportForm) {
  if ($varType == 'rows' && empty($var)) {
    $outputMode = $reportForm->getOutputMode();
    if ($outputMode == 'pdf') {
      // When processing a pdf report with empty rows, store note this instance id
      // in a static. If we're in the midst of mailing this report instance,
      // this instance id will be detected in hook_civicrm_alterMailParams(), which
      // will prevent the email from going out.
      // (For html output, we cannot get the instance id in hook_civicrm_alterMailParams(),
      // so we use a different method, see our hook_civicrm_alterTemplateFile().
      $instanceId = $reportForm->getVar('_id');
      Civi::$statics['noemptyreportmail']['pdf_empty_report_' . $instanceId] = TRUE;
    }
  }
}

/**
 * Implements hook_civicrm_alterTemplateFile().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterTemplateFile
 */
function noemptyreportmail_civicrm_alterTemplateFile($formName, &$form, $context, &$tplName) {
  if (is_a($form, 'CRM_Report_Form')) {
    $outputMode = $form->getVar('_outputMode');
    if ($outputMode == 'print') {
      $tplVars = CRM_Core_Smarty::singleton()->get_template_vars();
      if (empty($tplVars['rows'])) {
        // Assign the original template name to a template variable. This way:
        // Our Empty.tpl (if it gets used) can include that original tpl file; and
        // Other extensions can know the original tpl name (if they know to check for it).
        $form->assign('noemptyreportmail_original_tpl', $tplName);

        // When processing a print report with empty rows, change the template to
        // our wrapper, and assign a variable indicating there are now rows.
        // Our wrapper template will just print this indicator and then include
        // the original template file.
        // If we're in the midst of mailing this report, this marker will be
        // detected in hook_civicrm_alterMailParams(), which will prevent the
        // email from going out.
        // (For pdf output, we cannot read the report content in
        // hook_civicrm_alterMailParams(), so we use a different method, see
        // our noemptyreportmail_civicrm_alterReportVar().
        $form->assign('noemptyreportmail_rows_empty_marker', NOEMPTYREPORTMAIL_ROWS_EMPTY_MARKER);
        $tplName = "CRM/noemptyreportmail/Report/Empty.tpl";
      }
    }
  }
}

/**
 * Implements hook_civicrm_alterMailParams().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterMailParams/
 */
function noemptyreportmail_civicrm_alterMailParams(&$params, $context) {
  if ($params['groupName'] == 'Report Email Sender') {
    if (strpos($params['html'], NOEMPTYREPORTMAIL_ROWS_EMPTY_MARKER) !== FALSE) {
      $params['abortMailSend'] = TRUE;
    }
    elseif (($attachment = $params['attachments'][0]) && ($attachment['cleanName'] == 'CiviReport.csv')) {
      // If the attachment has one line, that's just the header; so there are no rows.
      $lineCount = count(file($attachment['fullPath']));
      if ($lineCount == 1) {
        $params['abortMailSend'] = TRUE;
      }
    }
    elseif (($attachment = $params['attachments'][0]) && ($attachment['cleanName'] == 'CiviReport.pdf')) {
      $reportUrlTs = ts('Report URL');
      $matches = [];
      preg_match('/' . $reportUrlTs . ':\s+(http[^<\s]+)/', $params['html'], $matches);
      $reportUrl = $matches[1];
      $instanceId = CRM_Noemptyreportmail_Util::getInstanceIdFromUrl($reportUrl);

      if (Civi::$statics['noemptyreportmail']['pdf_empty_report_' . $instanceId]) {
        $params['abortMailSend'] = TRUE;
        unset(Civi::$statics['noemptyreportmail']['pdf_empty_report_' . $instanceId]);
      }
    }
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function noemptyreportmail_civicrm_config(&$config) {
  _noemptyreportmail_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function noemptyreportmail_civicrm_install(): void {
  _noemptyreportmail_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function noemptyreportmail_civicrm_enable(): void {
  _noemptyreportmail_civix_civicrm_enable();
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 */
//function noemptyreportmail_civicrm_preProcess($formName, &$form): void {
//
//}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
//function noemptyreportmail_civicrm_navigationMenu(&$menu): void {
//  _noemptyreportmail_civix_insert_navigation_menu($menu, 'Mailings', [
//    'label' => E::ts('New subliminal message'),
//    'name' => 'mailing_subliminal_message',
//    'url' => 'civicrm/mailing/subliminal',
//    'permission' => 'access CiviMail',
//    'operator' => 'OR',
//    'separator' => 0,
//  ]);
//  _noemptyreportmail_civix_navigationMenu($menu);
//}
