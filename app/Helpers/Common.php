<?php

if (!function_exists('slugify')) {
  /**
   *
   * @param string $string
   *
   * @return string A valid Instagram like username
   *
   * */
  function slugify($string) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
  }
}
