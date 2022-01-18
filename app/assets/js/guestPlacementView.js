window.addEventListener('load', function(){
    let viewingGuestSeatId = document.querySelector('#viewing-guest-seat-id').value;
    console.log(viewingGuestSeatId);
    let viewingGuestSeat = document.querySelectorAll(`.guest-placement__seat[data-seat-id="${viewingGuestSeatId}"]`)[0];
    console.log(viewingGuestSeat);

    viewingGuestSeat.classList.add('occupied');
});