function counteralert (miliseconds, msg) {
    function showalert() {
        alert(msg);
    }
    setTimeout(showalert, miliseconds);
}
