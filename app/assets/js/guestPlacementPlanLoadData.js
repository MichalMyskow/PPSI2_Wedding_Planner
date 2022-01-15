let json = '{"guests":[' +
'{"id":6,"seatNumber":"9" },' +
'{"id":7,"seatNumber":"4" },' +
'{"id":8,"seatNumber":"8" },' +
'{"id":9,"seatNumber":1}]}'; 

const initialData = JSON.parse(json);

window.addEventListener('load', function(){
    for (object of initialData.guests) {
        if(object.seatNumber){
            guest = document.querySelectorAll(`.guest[data-guest-id="${object.id}"]`)[0];
            guest.dataset.guestSeatId = object.seatNumber;
            document.querySelectorAll(`.guest-placement__seat[data-seat-id="${guest.dataset.guestSeatId}"]`)[0].classList.add('occupied');
        }
    }
});