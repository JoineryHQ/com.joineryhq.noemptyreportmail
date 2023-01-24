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
![screenshot](/images/joinery-logo.png)

Joinery provides services for CiviCRM including custom extension development,
training, data migrations, and more. We aim to keep this extension in good working
order, and will do our best to respond appropriately to issues reported on its
[github issue queue](https://github.com/twomice/com.joineryhq.noemptyreportmail/issues).
In addition, if you require urgent or highly customized improvements to this
extension, we may suggest conducting a fee-based project under our standard
commercial terms.  In any case, the place to start is the
[github issue queue](https://github.com/joineryhq/com.joineryhq.noemptyreportmail/issues)
-- let us hear what you need and we'll be glad to help however we can.

And, if you need help with any other aspect of CiviCRM -- from hosting to custom
development to strategic consultation and more -- please contact us directly via
https://joineryhq.com
