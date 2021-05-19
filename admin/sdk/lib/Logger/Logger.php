<?php

namespace Payments\Logger;

include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Handler/File.php');

use Payments\Logger\Handler\File;

class Logger{

  /**
   * List of severity levels.
   */
  const URGENT   = 0; // It's an emergency
  const ALERT    = 1; // Immediate action required
  const CRITICAL = 2; // Critical conditions
  const ERROR    = 3; // An error occurred
  const WARNING  = 4; // Something unexpected happening
  const NOTICE   = 5; // Something worth noting
  const INFO     = 6; // Information, not an error
  const DEBUG    = 7; // Debugging messages

  /**
   * The default format for log messages (machine, date, level, message)
   * written to a file. To change the order of items in the string,
   * use `%1$s` references.
   */
  public static $format = "%s - %s - %d - %s\n";

  /**
   * The default date/time format for log messages written to a file.
   * Feeds into the `$format` property.
   */
  public static $date_format = 'Y-m-d H:i:s';

  /**
   * Timezone for date/time values.
   */
  public static $timezone = 'GMT';

  /**
   * Default log level.
   */
  public static $default_level = 7;

  /**
   * The method of saving the log output. See Logger::handler()
   * for details on setting this.
   */
  private static $handler = null;

  /**
   * The name of the current machine, defaults to $_SERVER['SERVER_ADDR']
   * on first call to format_message(), or 'localhost' if $_SERVER['SERVER_ADDR']
   * is not set (e.g., during CLI use).
   */
  public static $server_name = null;

  /**
   * Handler getter/setter. If no handler is provided, it will set it to
   * sys_get_temp_dir() . '/payment.txt' as a default. Usage:
   *
   *    Logger::handler ('my_log.txt');
   *
   * Using a closure:
   *
   *     Logger::handler (function ($msg) {
   *         return error_log ($msg);
   *     });
   */
  public static function handler($handler = false){
    if ($handler) {
      self::$handler = $handler;
    } elseif (!self::$handler) {
      self::$handler = realpath(sys_get_temp_dir()) . DIRECTORY_SEPARATOR . 'payment.txt';
    }
    return self::$handler;
  }

  /**
   * Get the log info as an associative array.
   */
  private static function get_struct($message, $level){
    if (self::$server_name === null) {
      self::$server_name = (isset($_SERVER['SERVER_ADDR'])) ? $_SERVER['SERVER_ADDR'] : 'localhost';
    }

    $dt = new \DateTime('now', new \DateTimeZone(self::$timezone));

    return array(
      'machine' => self::$server_name,
      'date' => $dt->format(self::$date_format),
      'level' => $level,
      'message' => $message
    );
  }

  /**
   * Write a raw message to the log using a function or the default
   * file logging.
   */
  private static function write($struct){
    $handler = self::handler();

    if (!$handler instanceof \Closure) {
      $handler = File::init($handler);
    }
    return $handler($struct);
  }

  /**
   * This is the main function you will call to log messages.
   * Defaults to severity level Logger::ERROR, which can be
   * changed via the `$default_level` property.
   * Usage:
   *
   *     Logger::log ('Debug info', Logger::DEBUG);
   */
  public static function log($message, $level = null){
    $level = ($level !== null) ? $level : self::$default_level;
    return self::write(self::get_struct($message, $level));
  }

  public static function error($message){
    return self::log($message, self::ERROR);
  }
}
