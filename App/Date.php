<?php

namespace App;

class Date{

  public static function getCurrentMonth(){

    $startDate = date('Y-m-01');
    $endDate = date('Y-m-d');

    return array('startDate' => $startDate, 'endDate' => $endDate);
  }

  public static function getPreviousMonth(){
  
      $firstDayOfLastMonth = strtotime('first day of last month');
      $lastDayOfLastMonth = strtotime('last day of last month');
  
      $bounds = array(
          'startDate' => date('Y-m-d', $firstDayOfLastMonth),
          'endDate' => date('Y-m-d', $lastDayOfLastMonth)
      );
  
      return $bounds;
  }

  public static function getPreviousYear(){

      $startOfPreviousYear = date('Y-01-01', strtotime('last year'));
      $endOfPreviousYear = date('Y-12-31', strtotime('last year'));
  
      $bounds = array(
          'startDate' => $startOfPreviousYear,
          'endDate' => $endOfPreviousYear
      );
  
      return $bounds;
  }

  public static function getFirstAndLastDayOfMonth($date){

    list($year, $month, $day) = explode('-', $date);

    $firstDay = date('Y-m-d', strtotime("$year-$month-01"));

    $lastDay = date('Y-m-t', strtotime("$year-$month-$day"));

    return array(
        'firstDay' => $firstDay,
        'lastDay' => $lastDay
    );
  }
} 
