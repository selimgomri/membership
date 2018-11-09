<?

if (app('request')->hostname == 'account.chesterlestreetasc.co.uk') {
  include 'chester/footer.php';
} else if (app('request')->hostname == 'tynemouth.chesterlestreetasc.co.uk') {
  include 'tynemouth/footer.php';
}
