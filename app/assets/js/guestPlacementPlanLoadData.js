let baseUrl = `${window.location.origin}`;
 
window.addEventListener('load', function(){
    var xhr = new XMLHttpRequest();
    xhr.open('GET', `${baseUrl}/guest-placement/plan`, false);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        let initialData = JSON.parse(this.response);
        console.log(initialData)
        for (object of initialData.guests) {
            if(object.seatNumber){
                guest = document.querySelectorAll(`.guest[data-guest-id="${object.id}"]`)[0];
                guest.dataset.guestSeatId = object.seatNumber;
                document.querySelectorAll(`.guest-placement__seat[data-seat-id="${guest.dataset.guestSeatId}"]`)[0].classList.add('occupied');
            }
        }
    };
    xhr.send();  
});