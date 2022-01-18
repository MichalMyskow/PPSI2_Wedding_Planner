window.addEventListener('load', function(){
    let viewingGuestSeatId;
    let viewingGuestSeat;
    viewingGuestSeatId = document.querySelector('#viewing-guest-seat-id').value;
    if(viewingGuestSeatId)
        viewingGuestSeat = document.querySelectorAll(`.guest-placement__seat[data-seat-id="${viewingGuestSeatId}"]`)[0];
    if(viewingGuestSeat)
        viewingGuestSeat.classList.add('occupied');
});