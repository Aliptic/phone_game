// Update the count down every 1 second
var x = setInterval(function() {
    // Get today's date and time
      var now = new Date().getTime();

    // Find the distance between now and the count down date
      var distance = countDownDate - now;

    // Time in seconds
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

    // Display the result in the element with id="timer"
    document.getElementById("timer").innerHTML = minutes + "m " + seconds + "s ";
    
    // Play a sound when there is less than 4 sec
    if (distance < 3000) {
        music.play()
        music.loop = false
    }
    // If the count down is finished, validate the form
    if (distance < 0) {
        clearInterval(x)
        document.getElementById("timer").innerHTML = "STOP"
        document.form.submit()
    }
}, 1000);