// Count down milliseconds = server_end - server_now = client_end - client_now
var server_end = <?php echo $exp_date; ?> * 1000;
var server_now = <?php echo time(); ?> * 1000;
var client_now = new Date().getTime();
var end = server_end - server_now + client_now;
// this is the real end time
var _second = 1000;
var _minute = _second * 60;
var _hour = _minute * 60;
var _day = _hour *24;
var timer;

/* document.getElementById('countdown').innerHTML = 'EXPIRED!'; */

function showRemaining() {
  var now = new Date();
  var distance = end - now;
  if (distance < 0 ) {
    clearInterval( timer );
    document.getElementById('countdown').innerHTML = 'EXPIRED!';
    return;
  }
  var days = Math.floor(distance / _day);
  var hours = Math.floor( (distance % _day ) / _hour );
  var minutes = Math.floor( (distance % _hour) / _minute );
  var seconds = Math.floor( (distance % _minute) / _second );
  var countdown = document.getElementById('countdown');
  countdown.innerHTML = '';
  if (days) {
    countdown.innerHTML += days + ' Days ';
  }
  countdown.innerHTML += hours + ' Hours ';
  countdown.innerHTML += minutes + ' Mins' ;
  /* countdown.innerHTML += seconds; */
}

showRemaining();
timer = setInterval(showRemaining, 10000);
