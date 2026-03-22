# com.joineryhq.noemptyreportmail

## CiviCRM: No Empty Report Mail

Prevent emailing of reports that contain no rows in the output.

## Use case

Use this extension if:

* You're using CiviCRM's "Mail Reports" scheduled job to invoke a report's "Email
  Delivery" configuration, and
* You don't want the email to be sent at all if the report shows no results.

The extension is licensed under [GPL-3.0](LICENSE.txt).


## Configuration

There is no configuration. If this extension is enabled, reports will not be sent
by email if they have no output rows at the time of delivery.

## Developer concerns

If your extension implements `hook_civicrm_alterTemplateFile()`, and that hook
implementation fires after this extension's, be aware that our implementation
may have changed the template file name. You can get the original template file
name from the `noemptyreportmail_original_tpl` template variable.


## Support

Support for this package is handled under Joinery's ["Limited Support" policy](https://joineryhq.com/software-support-levels#limited-support).

Public issue queue for this package: https://github.com/JoineryHQ/com.joineryhq.noemptyreportmail/issues
