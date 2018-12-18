<?

if (app('request')->hostname == 'account.chesterlestreetasc.co.uk') {
  include 'chester/GlobalHead.php';
} else if (app('request')->hostname == 'tynemouth.chesterlestreetasc.co.uk') {
  include 'tynemouth/header.php';
}
