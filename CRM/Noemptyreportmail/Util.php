<?php

/**
 * Utility methods for noemptyreportmail
 */
class CRM_Noemptyreportmail_Util {

  /**
   * For a given report URL, determine the instance id of the report, by
   * assuming that the URL was generated for the current userFramework.
   * (CiviCRM URLs will be formatted differently in Various userFrameworks.)
   *
   * @param string $url Assumed to be a url to a civicrm path like civicrm/report/instance/[INSTANCE_ID].
   * @return Int The numeric report instance ID
   */
  public static function getInstanceIdFromUrl(string $url) {
    switch (CRM_Core_Config::singleton()->userFramework) {
      case 'WordPress':
        // WP urls have the path passed through rawurlencode(), and the whole
        // url passed through htmlentities().
        $originalUrl = rawurldecode(html_entity_decode($url));
        $queryString = parse_url($originalUrl, PHP_URL_QUERY);
        $query = [];
        parse_str($queryString, $query);
        // WP urls store the path in query string param `q`
        $path = $query['q'];
        break;

      case 'Joomla':
        // Joomla urls have been passed through htmlentities().
        $originalUrl = html_entity_decode($url);
        $queryString = parse_url($originalUrl, PHP_URL_QUERY);
        $query = [];
        parse_str($queryString, $query);
        // Joomla urls store the path in query string param `task`
        $path = $query['task'];
        break;

      case 'Drupal':
      case 'Backdrop':
      case 'Drupal8':
        // Drupal-style urls have been passed through htmlentities().
        $originalUrl = html_entity_decode($url);
        // Drupal-style urls have the actual path.
        $path = parse_url($originalUrl, PHP_URL_PATH);
        break;

    }
    // $path is assumed to be in the format civicrm/report/instance/[INSTANCE_ID].
    $pathParts = explode('/', $path);
    $instanceId = array_pop($pathParts);
    return (int) $instanceId;
  }

}
